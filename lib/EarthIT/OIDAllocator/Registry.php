<?php

class EarthIT_OIDAllocator_Registry
{
	protected $postResponseJobs = [];
	
	protected $configDir;
	public function __construct( $configDir ) {
		$this->configDir = $configDir;
	}
	
	protected $configCache = [];
	public function getConfig( $name ) {
		$parts = explode('/', $name);
		$file = array_shift($parts);
		if( isset($this->configCache[$file]) ) {
			$c = $this->configCache[$file];
		} else {
			$cf = "{$this->configDir}/{$file}.json";
			if( !file_exists($cf) ) return null;
			$c = EarthIT_JSON::decode(file_get_contents($cf), true);
			if( $c === null ) {
				throw new Exception("Failed to load config from '{$cf}'");
			}
			$this->configCache[$file] = $c;
		}
		foreach( $parts as $p ) {
			if( isset($c[$p]) ) {
				$c = $c[$p];
			} else {
				return null;
			}
		}
		return $c;
	}
		
	protected function getViewTemplateDirectory() {
		return EarthIT_OIDAllocator_ROOT_DIR.'/views';
	}
	
	/**
	 * Components that have been explicitly configured.  Will not be
	 * wiped out by clean().
	 */
	protected $components = [];

	/**
	 * Components loaded lazily which will presumably be loaded the
	 * same way again if the the cache is cleared.  Will be emptied by
	 * clean().
	 */
	protected $cachedComponents = [];
	
	public function __isset($attrName) {
		try {
			return $this->$attrName !== null;
		} catch( Exception $e ) {
			throw $e;
			return false;
		}
	}
	
	public function __get($attrName) {
		// If something's been explicitly overridden, return that.
		if( isset($this->components[$attrName]) ) {
			return $this->components[$attrName];
		}
		
		// If there's a getter, call it and immediately return.
		$ucfAttrName = ucfirst($attrName);
		$getterMethodName = "get{$ucfAttrName}";
		if( method_exists($this, $getterMethodName) ) { 
			return $this->$getterMethodName();
		}

		// Check the cache.
		if( isset($this->cachedComponents[$attrName]) ) {
			return $this->cachedComponents[$attrName];
		}

		// If there's a loadX method, use it and cache the result.
		$creatorMethodName = "load{$ucfAttrName}";
		if( method_exists($this, $creatorMethodName) ) { 
			return $this->cachedComponents[$attrName] = $this->$creatorMethodName();
		}
		
		// If there's a class with a matching name, instantiate it and cache the instance.
		$className = "EarthIT_OIDAllocator_{$ucfAttrName}";
		if( class_exists($className,true) ) {
			return $this->cachedComponents[$attrName] = new $className($this);
		}
		
		throw new Exception("Undefined property: ".get_class($this)."#$attrName");
	}
	
	/**
	 * Use to explicitly override a component.
	 * 
	 * Don't use this directly.  Use with(...) instead to make a copy
	 * of the registry with the specified things replaced.a
	 */
	public function __set($attrName, $value) {
		$this->components[$attrName] = $value;
	}
	
	/**
	 * Don't use this directly, either.
	 * Use cleanClone() to get a copy of the registry with the cache cleared.
	 */
	protected function clean() {
		$this->cachedComponents = [];
	}

	/**
	 * Returns a copy of this Registry with the component cache cleared.
	 *
	 * This ensures that if any settings are changed on the clone that
	 * would affect how components are reloaded, their new values get
	 * used to load those components when they are requested.
	 */
	public function cleanClone() {
		$c = clone $this;
		$c->clean();
		return $c;
	}
	
	public function with(array $stuff) {
		$alt = $this->cleanClone();
		foreach( $stuff as $k=>$v ) $alt->$k = $v;
		return $alt;
	}
	
	public function withSchema(EarthIT_Schema $schema) {
		return $this->with(['schema'=>$schema]);
	}
	public function withNamedSchema($name) {
		return $this->withSchema($this->loadSchema($name));
	}
}
