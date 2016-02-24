<?php

class EarthIT_OIDAllocator_Util extends EarthIT_OIDAllocator_Component
{
	public function urlPath(array $path) {
		return $path == array() ? '' : implode('/',$path).'/';
	}
	
	public function getCrumz(array $path) {
		$crumz = array();
		for( $i=0; $i<count($path); ++$i ) {
			$crumPath = array_slice($path, 0, $i+1);
			$crumInfo = $this->oidAllocator->getInfo($crumPath);
			if( $crumInfo === null ) $crumInfo = array('isEmpty'=>true);
			$crumInfo['num'] = $path[$i];
			$crumInfo['urlPath'] = $this->urlPath($crumPath);
			$crumz[] = $crumInfo;
		}
		return $crumz;
	}
}
