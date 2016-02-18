<?php

class EarthIT_OIDAllocator_Component
{
	protected $registry;
	
	use EarthIT_OIDAllocator_ComponentGears;
	
	public function __construct( EarthIT_OIDAllocator_Registry $reg ) {
		$this->registry = $reg;
	}
}
