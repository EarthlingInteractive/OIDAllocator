<?php

class EarthIT_OIDAllocator_PageAction_SayHelloTo extends EarthIT_OIDAllocator_PageAction_TemplatePageAction
{
	protected $name;
	public function __construct(EarthIT_OIDAllocator_Registry $reg, $name) {
		parent::__construct($reg);
		$this->name = $name;
	}
	protected function getTemplateName() { return 'say-hi-to'; }
	protected function getTemplateParameters() { return ['name'=>$this->name]; }
}
