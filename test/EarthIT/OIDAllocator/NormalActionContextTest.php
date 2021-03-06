<?php

class EarthIT_OIDAllocator_NormalActionContextTest extends EarthIT_OIDAllocator_TestCase
{
	protected function _testRelativeUrl( $rel, $from, $to ) {
		$actx = new EarthIT_OIDAllocator_NormalActionContext();
		$actx = $actx->with(array('pathInfo' => $from));
		$this->assertEquals( $rel, $actx->relativeUrl($to) );
	}
	
	public function testRelativeUrl() {
		$this->_testRelativeUrl('../../fence/chicken', '/barn/cow/udder', '/fence/chicken');
		$this->_testRelativeUrl('../fence/chicken', '/barn/cow', '/fence/chicken');
		$this->_testRelativeUrl('fence/chicken', '/', '/fence/chicken');
		$this->_testRelativeUrl('./', '/fence', './');
	}
}
