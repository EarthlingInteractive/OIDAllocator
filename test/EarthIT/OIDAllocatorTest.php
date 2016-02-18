<?php

abstract class EarthIT_OIDAllocatorTest extends EarthIT_OIDAllocator_TestCase
{
	protected abstract function createAllocator();
	
	public function setUp() {
		$this->allocator = $this->createAllocator();
		$this->testInfo = array(
			'name' => 'Test!',
			'regions' => array(
				EarthIT_OIDAllocator::REGION_RESERVED => array(
					'bottom' =>            '0',
					'top'    =>         '1000',
					'first'  =>            '1',
				),
				EarthIT_OIDAllocator::REGION_PROD => array(
					'bottom' =>         '1000',
					'top'    =>  '10000000000',
					'first'  =>  '10000000001',
				),
				EarthIT_OIDAllocator::REGION_TEMP => array(
					'bottom' =>  '90000000000',
					'top'    => '100000000000',
					'first'  =>  '90000000005',
				),
			)
		);

	}

	public function testSetGetInfo() {
		$this->allocator->setInfo(array(900,800,700), $this->testInfo);
		$this->assertEquals($this->testInfo, $this->allocator->getInfo(array(900,800,700)));
	}
	
	public function testAllocateSome() {
		$this->allocator->setInfo(array(1,2,3), $this->testInfo);
		
		$ids = $this->allocator->allocate(array(1,2,3), 4, array(
			EarthIT_OIDAllocator::REGION => EarthIT_OIDAllocator::REGION_TEMP
		));
		$this->assertEquals(
			array('90000000005','90000000006','90000000007','90000000008'),
			$ids);
		
		$ids = $this->allocator->allocate(array(1,2,3), 3, array(
			EarthIT_OIDAllocator::REGION => EarthIT_OIDAllocator::REGION_TEMP
		));
		$this->assertEquals(
			array('90000000009','90000000010','90000000011'),
			$ids);
	}
}
