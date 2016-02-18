<?php

class EarthIT_OIDAllocator_FSOIDAllocatorTest extends EarthIT_OIDAllocatorTest
{
	public function createAllocator() {
		return new EarthIT_OIDAllocator_FSOIDAllocator('temp/test-'.date('Y-m-d-H-i-s').'-'.mt_rand(1000,9999));
	}
}
