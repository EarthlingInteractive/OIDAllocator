<?php

use EarthIT_OIDAllocator as OIDA;

class EarthIT_OIDAllocator_PageAction_AllocateIDs
extends EarthIT_OIDAllocator_PageAction
{
	protected $spacePath;
	protected $notes;
	protected $allocationRequests = array();
	
	public function __construct( EarthIT_OIDAllocator_Registry $reg, array $spacePath, array $params ) {
		parent::__construct($reg);
		$this->spacePath = $spacePath;
		foreach( $params['regions'] as $regionKey=>$reg ) {
			if( isset($reg['allocationRequest']) and ($r = $reg['allocationRequest']) > 0 ) {
				$this->allocationRequests[$regionKey] = $r;
			}
		}
		$this->notes = isset($params['notes']) ? trim($params['notes']) : '';
	}
	
	public function __invoke(EarthIT_OIDAllocator_ActionContext $actx) {
		$ids = array();
		foreach( $this->allocationRequests as $regionKey => $count ) {
			$_ids = $this->oidAllocator->allocate($this->spacePath, $count, array(
				OIDA::REGION => $regionKey,
				OIDA::NOTES => $this->notes
			));
			$ids = array_merge($ids, $_ids);
		}
		return $this->redirect(303, './?newItemIds='.implode(',',$ids));
	}
}
