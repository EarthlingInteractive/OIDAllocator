<?php

abstract class EarthIT_OIDAllocator_PageAction_TemplatePageAction extends EarthIT_OIDAllocator_PageAction
{
	protected function getStatusCode() { return 200; }
	protected function getHeaders() { return ['content-type'=>'text/html; charset=utf-8']; }
	protected abstract function getTemplateName();
	protected abstract function getTemplateParameters();
	
	protected $errorMessageId; // Set in constructor if your pageaction has error messages
	
	public function __construct( EarthIT_OIDAllocator_Registry $reg, $errorMessageId=null ) {
		parent::__construct($reg);
		$this->errorMessageId = $errorMessageId;
	}
	
	/** 
	 * Get standard template variables from the action context (logged in user, etc)
	 */
	protected function contextTemplateVars( EarthIT_OIDAllocator_ActionContext $actx, array $into=array() ) {
		$userId = $actx->getLoggedInUserId();
		$into['actionContext'] = $actx;
		$into['loggedInUser'] = $userId === null ? null : $this->storageHelper->getItem('user', array('ID'=>$userId));
		$into['errorMessage'] = $this->getErrorMessage($this->errorMessageId, $actx);
		return $into;
	}
	
	public function __invoke( EarthIT_OIDAllocator_ActionContext $actx ) {
		return $this->templateResponse(
			$this->getStatusCode(),
			$this->getTemplateName(),
			$this->getTemplateParameters() + $this->contextTemplateVars($actx),
			$this->getHeaders()
		);
	}
}
