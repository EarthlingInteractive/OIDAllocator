<?php

interface EarthIT_OIDAllocator
{
	/** Option key that maps to the name of a region */
	const REGION = 'region';
	
	// Standard region codes
	const REGION_RESERVED = 'reserved';
	const REGION_PROD = 'production';
	const REGION_TEMP = 'temporary';
	
	const INCLUDE_COUNTERS = 'include-counters';
	// These are also used as keys in allocation info, which is stored,
	// which is why they are camelCase:
	const NOTES = 'notes';
	const ALLOCATING_USER_ID = 'allocatingUserId';
	
	public function allocate( array $namespacePath, $count, array $options=array() );
	public function getInfo( array $path, array $options=array() );
	public function setInfo( array $path, array $info );
	
	const INCLUDE_ROOT = 'include-root';
	const RECURSE_PAST_INFO = 'recurse-past-info';
	
	public function findInfo( array $path, array $options=array(), array &$thingsGoHere=array() );
}
