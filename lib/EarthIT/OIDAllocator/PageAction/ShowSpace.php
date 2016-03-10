<?php

class EarthIT_OIDAllocator_PageAction_ShowSpace extends EarthIT_OIDAllocator_PageAction_TemplatePageAction
{
	protected $options;
	
	public function __construct( EarthIT_OIDAllocator_Registry $reg, array $path, array $options ) {
		parent::__construct($reg);
		$this->path = $path;
		$this->options = $options;
	}
	
	public function getTemplateName() { return 'space'; }
	public function getTemplateParameters() {
		$crumz = $this->util->getCrumz($this->path);
		
		$space = $this->oidAllocator->getInfo($this->path, array(
			EarthIT_OIDAllocator::INCLUDE_COUNTERS => true,
		));
		
		$anyRegionsAllocatable = false;
		if( isset($space['regions']) ) foreach( $space['regions'] as $reg ) {
			if( !empty($reg['allocatable']) ) $anyRegionsAllocatable = true;
		}
		
		$newItemIds = isset($this->options['newItemIds']) ? $this->options['newItemIds'] : array();
		
		$subSpaces = $this->oidAllocator->findInfo($this->path, array(
			EarthIT_OIDAllocator::INCLUDE_ROOT => false,
			EarthIT_OIDAllocator::RECURSE_PAST_INFO => false,
		));
		foreach( $subSpaces as $k=>&$subSpace ) {
			$path = $k == '' ? array() : explode('.',$k);
			$subSpace['urlPath'] = $this->util->urlPath($path);
		} unset($subSpace);
		
		if( isset($this->options['title']) ) $title = $this->options['title'];
		else if( $space ) $title = $space['name'];
		else $title = '';
		
		return [
			'pageTitle' => $title,
			'crumz' => $crumz,
			'space' => $space,
			'anyRegionsAllocatable' => $anyRegionsAllocatable,
			'subSpaces' => $subSpaces,
			'newItemIds' => $newItemIds,
		];
	}
}
