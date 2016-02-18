<?php

class EarthIT_OIDAllocator_DataTableResultAssemblerFactory
extends EarthIT_OIDAllocator_Component
implements EarthIT_CMIPREST_RequestParser_ResultAssemblerFactory
{
	public function getResultAssembler( $actionClass ) {
		return $this->dataTableResultAssembler;
	}
}
