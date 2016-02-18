<?php

interface EarthIT_OIDAllocator
{
	/** Option key that maps to the name of a region */
	const REGION = 'region';
	
	// Standard region codes
	const REGION_RESERVED = 'reserved';
	const REGION_PROD = 'production';
	const REGION_TEMP = 'temporary';
	
	public function allocate( array $namespacePath, $count, array $options=array() );
	public function getInfo( array $path );
	public function setInfo( array $path, array $info );
}
