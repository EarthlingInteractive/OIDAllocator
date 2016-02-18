<?php

class EarthIT_OIDAllocator_RegistryTest extends PHPUnit_Framework_TestCase
{
	public function testRegistryRegistered() {
		global $EarthIT_OIDAllocator_Registry;
		$this->assertEquals( 'EarthIT_OIDAllocator_Registry', get_class($EarthIT_OIDAllocator_Registry) );
	}
}
