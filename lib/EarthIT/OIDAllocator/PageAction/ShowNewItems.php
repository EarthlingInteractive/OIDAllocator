<?php

class EarthIT_OIDAllocator_PageAction_ShowNewItems extends EarthIT_OIDAllocator_PageAction_TemplatePageAction
{
	protected $newItemIds;
	
	public function __construct( EarthIT_OIDAllocator_Registry $reg, array $path, array $newItemIds ) {
		parent::__construct($reg);
		$this->path = $path;
		$this->newItemIds = $newItemIds;
	}
	
	protected function urlPath(array $path) {
		return $path == array() ? '' : implode('/',$path).'/';
	}
	
	public function getTemplateName() { return 'new-items'; }
	public function getTemplateParameters() {
		$space = $this->oidAllocator->getInfo($this->path);
		$crumz = $this->util->getCrumz($this->path);
		
		return [
			'pageTitle' => "New IDs!",
			'path' => $this->path,
			'crumz' => $crumz,
			'space' => $space,
			'newItemIds' => $this->newItemIds
		];
	}
}
