<?php

class EarthIT_OIDAllocator_PageAction_N2RGet extends EarthIT_OIDAllocator_PageAction
{
	protected $path;
	protected $req;
	
	public function __construct( EarthIT_OIDAllocator_Registry $reg, $path, EarthIT_OIDAllocator_Request $req ) {
		parent::__construct($reg);
		$this->path = $path;
		$this->req = $req;
	}
	
	public function isAllowed( EarthIT_OIDAllocator_ActionContext $actx, &$status, array &$notes=[] ) {
		return true;
	}
	
	public function __invoke( EarthIT_OIDAllocator_ActionContext $actx ) {
		// TODO: refactor N2RServer so it takes a $actx-like object.
		return $this->n2rServer->handleRequest($this->path);
	}
}
