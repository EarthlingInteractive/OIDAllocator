<?php

class EarthIT_OIDAllocator_PageAction_ShowComputations extends EarthIT_OIDAllocator_PageAction_TemplatePageAction
{
	public function getTemplateName() { return 'computations'; }
	public function getTemplateParameters() {
		$computations = $this->storageHelper->queryRows("SELECT * FROM public.computation");
		return array('computations' => $computations);
	}
}
