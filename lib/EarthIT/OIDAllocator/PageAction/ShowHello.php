<?php

class EarthIT_OIDAllocator_PageAction_ShowHello extends EarthIT_OIDAllocator_PageAction_TemplatePageAction
{
	public function getTemplateName() { return 'hello'; }
	public function getTemplateParameters() {
		$spaces = $this->oidAllocator->findInfo(array());
		foreach( $spaces as $k=>&$space ) {
			$path = $k == '' ? array() : explode('.',$k);
			$space['urlPath'] = $path == array() ? '' : implode('/',$path).'/';
		} unset($space);
		return [
			'spaces' => $spaces
		];
	}
}
