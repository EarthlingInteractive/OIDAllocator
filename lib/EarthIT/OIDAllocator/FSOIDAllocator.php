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
	
	protected function lockCounterFile( array $path, $lockType ) {
		$rf = $this->counterFile($path);
		if( !file_exists($rf) ) {
			$dir = dirname($rf);
			if( !is_dir($dir) ) {
				mkdir($dir, 0755, true);
			}
		}
		$rh = fopen( $rf, 'r+' );
		flock($rh, $lockType);
		return $rh;
	}
	
	protected function updated($message) {
		foreach( $this->updateListeners as $l ) call_user_func($l, array('message'=>$message));
	}
	
	protected function allocateFromRegion( array $namespacePath, array $spaceInfo, $regionCode, $count ) {
		if( $count == 0 ) return array(); // Nothing to it!
		
		if( !isset($spaceInfo['regions'][$regionCode]) ) {
			throw new AException("No such region '$regionCode' in ".self::pathToString($namespacePath));
		}
		$regionInfo = $spaceInfo['regions'][$regionCode];
		foreach( array('bottom','top') as $requiredKey ) {
			if( !isset($regionInfo[$requiredKey]) ) {
				throw new Exception("Region '$regionCode' in ".self::pathToString($namespacePath)." doesn't indicate '$requiredKey'");
			}
		}
		
		$ch = $this->lockCounterFile($namespacePath, LOCK_EX);
		try {
			$counterFileContent = stream_get_contents($ch);
			if( $counterFileContent === '' ) $counterFileContent = '{}';
			$counters = EarthIT_JSON::decode($counterFileContent);
			// If the key exists in counters, it's the last allocated ID for that region.
			if( !isset($counters[$regionCode]) ) {
				$first = isset($regionInfo['first']) ? $regionInfo['first'] : bcadd($regionInfo['bottom'], 1);
			} else {
				$first = bcadd($counters[$regionCode], 1);
			}
			$last = bcadd($first, $count-1);
			$counters[$regionCode] = $last;
			
			ftruncate($ch,0);
			rewind($ch);
			fwrite($ch, EarthIT_JSON::prettyEncode($counters)."\n");
		} finally {
			fclose($ch);
		}
			
		$ids = array();
		for( $i=0, $j=$first; $i<$count; ++$i, $j = bcadd($j,1) ) {
			$ids[$i] = $j;
		}
		return $ids;
	}
	
	public function allocate( array $namespacePath, $count, array $options=array() ) {
		$info = $this->getInfo($namespacePath);
		$defaultRegion = isset($info['defaultRegionCode']) ? $info['defaultRegionCode'] : OIDA::REGION_PROD;
		$reg = isset($options[OIDA::REGION]) ? $options[OIDA::REGION] : $defaultRegion;
		$ids = $this->allocateFromRegion($namespacePath, $info, $reg, $count);
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
	
	public function getInfo( array $path ) {
		$c = @file_get_contents($this->infoFile($path));
		if( $c === false ) return null;
		return EarthIT_JSON::decode($c);
	}
	
	public function setInfo( array $path, array $info ) {
		$file = $this->infoFile($path);
		$dir = dirname($file);
		if( !is_dir($dir) ) mkdir($dir, 0755, true);
		if( @file_put_contents($file, EarthIT_JSON::prettyEncode($info)."\n") === false ) {
			$err = error_get_last();
			throw new Exception("Failed to write to $file: ".$err['message']);
		}
		if( !file_exists($counterFile = $this->counterFile($path)) ) {
			file_put_contents($counterFile, "{}\n");
		}
		
		$this->updated("Updated info for ".self::pathToString($path));
	}
	
	public function findInfo( array $path, array $options=array(), array &$thingsGoHere=array() ) {
		$pathString = self::pathToString($path,'');
		if( ($info = $this->getInfo($path)) !== null ) $thingsGoHere[$pathString] = $info;
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
