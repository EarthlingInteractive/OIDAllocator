<?php

class EarthIT_OIDAllocator_PageUtil extends EarthIT_OIDAllocator_Component
{
	protected $params;
	
	public function __construct( EarthIT_OIDAllocator_Registry $reg, array $params=[] ) {
		parent::__construct($reg);
		$this->params = $params;
	}
	
	public static function jsonResponse($status, $data, $headers=[]) {
		$headers += ['content-type'=>'application/json'];
		return Nife_Util::httpResponse($status, new EarthIT_JSON_PrettyPrintedJSONBlob($data), $headers);
	}
	
	public function emitHtmlBoilerplate($title, array $params=array()) {
		$this->emitView('html-boilerplate', array('title'=>$title) + $params);
	}
	public function emitHtmlFooter( array $params=array() ) {
		$this->emitView('html-footer', $params);
	}
	public function emitErrorMessageBlock( array $params=array() ) {
		$this->emitView('error-message-block', $params);
	}
	
	protected function fortifyViewParams(array $params) {
		return $params + $this->params + ['PU' => $this];
	}
	
	public function linkHtml($target, $text) {
		if( $target[0] == '/' ) {
			$target = $this->params['actionContext']->relativeUrl($target);
		}
		return "<a href=\"".htmlspecialchars($target)."\">".htmlspecialchars($text)."</a>";
	}
	
	protected function templateFile($viewName) {
		$file = $this->registry->viewTemplateDirectory.'/'.$viewName.".php";
		if( !file_exists($file) ) {
			throw new Exception("Template file for '$viewName', '$file', does not exist");
		}
		return $file;
	}
	
	public function viewBlob($viewName, $params=array()) {
		$params = $this->fortifyViewParams($params);
		$params['params'] = $params;
		return new EarthIT_FileTemplateBlob($this->templateFile($viewName), $params);
	}
	
	public function emitView($viewName, $params=array()) {
		$params = $this->fortifyViewParams($params);
		extract($params);
		include $this->templateFile($viewName);;
	}
	
	public function emitSelectOptions( array $options, $selectedValue ) {
		foreach( $options as $k => $v ) {
			echo "<option value=\"", htmlspecialchars($k), "\"",
				($selectedValue == $k ? ' selected' : ''), ">",
				htmlspecialchars($v), "</option>\n";
		}
	}
}
