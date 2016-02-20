<?php

class EarthIT_OIDAllocator_PageAction_ShowSpace extends EarthIT_OIDAllocator_PageAction_TemplatePageAction
{
	public function __construct( EarthIT_OIDAllocator_Registry $reg, array $path ) {
		parent::__construct($reg);
		$this->path = $path;
	}
	
	public function getTemplateName() { return 'space'; }
	public function getTemplateParameters() {
		$space = $this->oidAllocator->getInfo($this->path, array(
			EarthIT_OIDAllocator::INCLUDE_COUNTERS => true
		));
		
		$subSpaces = $this->oidAllocator->findInfo($this->path, array(
			EarthIT_OIDAllocator::INCLUDE_ROOT => false
		));
		foreach( $subSpaces as $k=>&$subSpace ) {
			$path = $k == '' ? array() : explode('.',$k);
			$subSpace['urlPath'] = $path == array() ? '' : implode('/',$path).'/';
		} unset($subSpace);
		return [
			'space' => $space,
			'subSpaces' => $subSpaces
		];
	}
}
