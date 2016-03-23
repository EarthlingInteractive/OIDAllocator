<?php

use EarthIT_OIDAllocator as OIDA;
use EarthIT_OIDAllocator_AllocationException as AllocException;

class EarthIT_OIDAllocator_FSOIDAllocator implements EarthIT_OIDAllocator
{
	const VALID_NAME_REGEX = '/^[a-zA-Z0-9]+$/';
	const VALID_NAME_OR_MD_FILE_REGEX = '/^([a-zA-Z0-9]+)(?:(?:\.counter)?\.json)?$/';
	
	protected $dataDir;
	protected $updateListeners = array();
	
	public function __construct( $dataDir ) {
		$this->dataDir = $dataDir;
	}
	
	public function addUpdateListener($func) {
		$this->updateListeners[] = $func;
	}
	
	protected static function writeFile($file, $content) {
		$dir = dirname($file);
		if( !empty($dir) and !is_dir($dir) ) {
			if( @mkdir($dir, 0755, true) === false ) {
				throw new Exception("Failed to create directory $dir: {$err['message']}");
			}
		}
		if( @file_put_contents($file, $content) === false ) {
			$err = error_get_last();
			throw new Exception("Failed to write to $file: {$err['message']}");
		}
	}
	
	protected static function pathToString(array $path, $rootName='root') {
		return count($path) == 0 ? $rootName : implode('.',$path);
	}
	
	protected static function last(array $stuff) {
		$thing = null;
		foreach($stuff as $thing);
		return $thing;
	}
	
	protected function spaceDir( array $path ) {
		$path = self::validatePath($path);
		$dir = $this->dataDir.'/root';
		if( count($path) > 0 ) $dir .= '/'.implode('/',$path);
		return $dir;
	}

	protected function infoFile( array $path, $ext='.json' ) {
		$path = self::validatePath($path);
		if( count($path) == 0 ) {
			return $this->dataDir.'/root'.$ext;
		} else {
			return $this->dataDir.'/root/'.implode('/',$path).$ext;
		}
	}
	
	protected function counterFile( array $path ) {
		return $this->infoFile($path, '.counters.json');
	}
	
	protected function allocationDir( array $path ) {
		return $this->infoFile($path, ".allocations");
	}
	
	protected function allocationFile( array $path, $first, $last ) {
		return $this->allocationDir($path)."/{$first}-{$last}.json";
	}
	
	protected function lockCounterFile( array $path, $lockType ) {
		$rf = $this->counterFile($path);
		if( !file_exists($rf) ) {
			if( $lockType === LOCK_SH ) return null;
			
			$dir = dirname($rf);
			if( !is_dir($dir) ) {
				mkdir($dir, 0755, true);
			}
		}
		$mode = file_exists($rf) ? 'r+' : 'w+'; // And hope nobody's racing us
		$rh = fopen( $rf, $mode );
		flock($rh, $lockType);
		return $rh;
	}
	
	protected function updated($message) {
		foreach( $this->updateListeners as $l ) call_user_func($l, array('message'=>$message));
	}
	
	protected function allocateFromRegion( array $namespacePath, array $spaceInfo, $regionCode, $count, array $options ) {
		if( $count == 0 ) return array(); // Nothing to it!
		
		if( !isset($spaceInfo['regions'][$regionCode]) ) {
			throw new AllocException("No such region '$regionCode' in ".self::pathToString($namespacePath));
		}
		$regionInfo = $spaceInfo['regions'][$regionCode];
		foreach( array('bottom','top') as $requiredKey ) {
			if( !isset($regionInfo[$requiredKey]) ) {
				throw new Exception("Region '$regionCode' in ".self::pathToString($namespacePath)." doesn't indicate '$requiredKey'");
			}
		}
		
		if( isset($regionInfo['direction']) ) switch($regionInfo['direction']) {
			case 'asc':
				$startEnd = 'bottom';
				$increment = 1;
				break;
			case 'desc':
				$startEnd = 'top';
				$increment = -1;
				break;
			default:
				throw new Exception("Unrecognized region direction: {$regionInfo['direction']}; expected 'asc' or 'desc'");
		} else {
			$startEnd = 'bottom';
			$increment = 1;
		}
		
		$ch = $this->lockCounterFile($namespacePath, LOCK_EX);
		try {
			$counterFileContent = stream_get_contents($ch);
			if( $counterFileContent === '' ) $counterFileContent = '{}';
			$counters = EarthIT_JSON::decode($counterFileContent);
			// If the key exists in counters, it's the last allocated ID for that region.
			if( !isset($counters[$regionCode]) ) {
				// bottom+1 or top-1.  Technically bottom is part of the region, but I like to skip it so IDs end with '1'.
				$first = isset($regionInfo['first']) ? $regionInfo['first'] : bcadd($regionInfo[$startEnd], $increment);
			} else {
				$first = bcadd($counters[$regionCode], $increment);
			}
			$last = bcadd($first,$increment * ($count-1));
			$counters[$regionCode] = $last;
			
			ftruncate($ch,0);
			rewind($ch);
			fwrite($ch, EarthIT_JSON::prettyEncode($counters)."\n");
		} finally {
			fclose($ch);
		}
		
		$allocationInfo = array();
		foreach( array(OIDA::NOTES,OIDA::ALLOCATING_USER_ID) as $k ) {
			if( !empty($options[$k]) ) $allocationInfo[$k] = $options[$k];
		}
		
		if( $allocationInfo ) {
			if( $increment > 0 ) {
				$min = $first; $max = $last;
			} else {
				$min = $last; $max = $first;
			}
			self::writeFile(
				$this->allocationFile($namespacePath, $min, $max),
				EarthIT_JSON::prettyEncode($allocationInfo)."\n"
			);
		}
		
		$ids = array();
		for( $i=0, $j=$first; $i<$count; ++$i, $j = bcadd($j,$increment) ) $ids[] = $j;

		return $ids;
	}
	
	protected function getCounters( array $path ) {
		$ch = $this->lockCounterFile($path, LOCK_SH);
		if( $ch === null ) return array();
		try {
			$counterFileContent = stream_get_contents($ch);
			if( $counterFileContent === '' ) $counterFileContent = '{}';
			return EarthIT_JSON::decode($counterFileContent);
		} finally {
			fclose($ch);
		}
	}
	
	protected function getAllocations( array $path ) {
		$ad = $this->allocationDir($path);
		if( !is_dir($ad) ) return array();
		
		$allocations = array();
		$files = scandir($ad);
		natsort($files);
		foreach( $files as $f ) {
			if( preg_match('/^(\d+-\d+)\.json$/', $f, $bif) ) {
				$allocations[$bif[1]] = EarthIT_JSON::decode(file_get_contents("{$ad}/{$f}"));
			}
		}
		return $allocations;
	}
	
	public function allocate( array $namespacePath, $count, array $options=array() ) {
		$info = $this->getInfo($namespacePath);
		$defaultRegion = isset($info['defaultRegionCode']) ? $info['defaultRegionCode'] : OIDA::REGION_PROD;
		$reg = isset($options[OIDA::REGION]) ? $options[OIDA::REGION] : $defaultRegion;
		$ids = $this->allocateFromRegion($namespacePath, $info, $reg, $count, $options);
		$this->updated("Allocated $count from ".self::pathToString($namespacePath)." ending in ".self::last($ids));
		return $ids;
	}
	
	protected static function validatePath(array $path) {
		foreach( $path as &$seg ) {
			if( !is_scalar($seg) ) throw new Exception("Non-scalar path segment: ".gettype($seg));
			$seg = (string)$seg;
			if( strlen($seg) == 0 ) {
				throw new Exception("Path segment is empty string!");
			}
			if( !preg_match(self::VALID_NAME_REGEX,$seg) ) {
				throw new Exception("Path segment contains invalid characters: $seg");
			}
		}
		return $path;
	}
	
	public function getInfo( array $path, array $options=array() ) {
		$i = @file_get_contents($this->infoFile($path));
		if( $i === false ) return null;
		$info = EarthIT_JSON::decode($i);
		if( !empty($options[self::INCLUDE_COUNTERS]) ) {
			$info['counters'] = $this->getCounters($path);
		}
		if( !empty($options[self::INCLUDE_ALLOCATIONS]) ) {
			$info['allocations'] = $this->getAllocations($path);
		}
		// Regions always get included
		return $info;
	}
	
	public function setInfo( array $path, array $info ) {
		$infoFile = $this->infoFile($path);
		$infoJson = EarthIT_JSON::prettyEncode($info)."\n";
		self::writeFile( $file, $infoJson );
		if( !file_exists($counterFile = $this->counterFile($path)) ) {
			self::writeFile($counterFile, "{}\n");
		}
		$this->updated("Updated info for ".self::pathToString($path));
	}
	
	public function findInfo( array $path, array $options=array(), array &$thingsGoHere=array() ) {
		$pathString = self::pathToString($path,'');
		$includeRoot = !isset($options[self::INCLUDE_ROOT]) || $options[self::INCLUDE_ROOT];
		if( $includeRoot and ($info = $this->getInfo($path)) !== null ) {
			$thingsGoHere[$pathString] = $info;
			if( isset($options[self::RECURSE_PAST_INFO]) && $options[self::RECURSE_PAST_INFO] === false ) {
				return;
			}
		}
		
		$options[self::INCLUDE_ROOT] = true;
		
		$dir = $this->spaceDir($path);
		if( is_dir($dir) ) {
			$files = scandir($dir);
			foreach( $files as $fn ) {
				if( preg_match(self::VALID_NAME_OR_MD_FILE_REGEX,$fn,$bif) ) {
					$subPath = $path;
					$subPath[] = $bif[1];
					$this->findInfo($subPath, $options, $thingsGoHere);
				}
			}
		}
		return $thingsGoHere;
	}
}
