<?php

class EarthIT_OIDAllocator_PageAction_ShowSpace extends EarthIT_OIDAllocator_PageAction_TemplatePageAction
{
	protected $options;
	
	public function __construct( EarthIT_OIDAllocator_Registry $reg, array $path, array $options ) {
		parent::__construct($reg);
		$this->path = $path;
		$this->options = $options;
	}
	
	protected function urlPath(array $path) {
		return $path == array() ? '' : implode('/',$path).'/';
	}
	
	public function getTemplateName() { return 'space'; }
	public function getTemplateParameters() {
		$crumz = array();
		for( $i=0; $i<count($this->path); ++$i ) {
			$crumPath = array_slice($this->path, 0, $i+1);
			$crumInfo = $this->oidAllocator->getInfo($crumPath);
			if( $crumInfo === null ) $crumInfo = array('isEmpty'=>true);
			$crumInfo['num'] = $this->path[$i];
			$crumInfo['urlPath'] = $this->urlPath($crumPath);
			$crumz[] = $crumInfo;
		}
		
		$space = $this->oidAllocator->getInfo($this->path, array(
			EarthIT_OIDAllocator::INCLUDE_COUNTERS => true,
		));
		
		$newItemIds = isset($this->options['newItemIds']) ? $this->options['newItemIds'] : array();
		
		$subSpaces = $this->oidAllocator->findInfo($this->path, array(
			EarthIT_OIDAllocator::INCLUDE_ROOT => false,
			EarthIT_OIDAllocator::RECURSE_PAST_INFO => false,
		));
		foreach( $subSpaces as $k=>&$subSpace ) {
			$path = $k == '' ? array() : explode('.',$k);
			$subSpace['urlPath'] = $this->urlPath($path);
		} unset($subSpace);
		
		if( isset($this->options['title']) ) $title = $this->options['title'];
		else if( $space ) $title = $space['name'];
		else $title = '';

		return [
			'pageTitle' => $title,
			'crumz' => $crumz,
			'space' => $space,
			'subSpaces' => $subSpaces,
			'newItemIds' => $newItemIds,
		];
	}
}
