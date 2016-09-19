<?php

use EarthIT_OIDAllocator as OIDA;

class EarthIT_OIDAllocator_PageAction_AllocateIDs
extends EarthIT_OIDAllocator_PageAction
{
	protected $spacePath;
	protected $notes;
	protected $allocationRequests = array();
	const REG_DEFAULT = '.DEFAULT';
	const RS_PAGE = 'page';
	const RS_JSON = 'json';
	protected $returnStyle;
	
	public function __construct( EarthIT_OIDAllocator_Registry $reg, array $spacePath, array $allocationRequests, $returnStyle=self::RS_PAGE ) {
		parent::__construct($reg);
		$this->spacePath = $spacePath;
		$this->allocationRequests = $allocationRequests;
		$this->returnStyle = $returnStyle;
	}
	
	public static function parseForm( EarthIT_OIDAllocator_Registry $reg, array $spacePath, array $params ) {
		$allocationRequests = array();
		$notes = isset($params['notes']) ? trim($params['notes']) : '';
		foreach( $params['regions'] as $regionKey=>$region ) {
			if( isset($region['allocationRequest']) and ($r = $region['allocationRequest']) > 0 ) {
				$allocationRequests[] = array('size'=>$r, 'regionCode'=>$regionKey, 'notes'=>$notes);
			}
		}
		return new self( $reg, $spacePath, $allocationRequests, self::RS_PAGE );
	}
	
	public static function parseJso( EarthIT_OIDAllocator_Registry $reg, array $spacePath, array $allocationRequests ) {
		foreach( $allocationRequests as &$alReq ) {
			if( !isset($alReq['size']) ) $alReq['size'] = 1;
		}
		return new self( $reg, $spacePath, $allocationRequests, self::RS_JSON );
	}
	
	public function __invoke(EarthIT_OIDAllocator_ActionContext $actx) {
		$ids = array();
		foreach( $this->allocationRequests as $alReq ) {
			$opts = array();
			$opts[OIDA::ALLOCATION_TIME] = date('c');
			if( isset($alReq['notes']) ) $opts[OIDA::NOTES] = $alReq['notes'];
			if( isset($alReq['regionCode']) ) $opts[OIDA::REGION] = $alReq['regionCode'];
			$_ids = $this->oidAllocator->allocate($this->spacePath, $alReq['size'], $opts);
			$ids = array_merge($ids, $_ids);
		}
		if( $this->returnStyle == self::RS_PAGE ) {
			return $this->redirect(303, './?newItemIds='.implode(',',$ids));
		} else {
			return $this->jsonResponse(200, array('newItemIds'=>array_values($ids)));
		}
	}
}
