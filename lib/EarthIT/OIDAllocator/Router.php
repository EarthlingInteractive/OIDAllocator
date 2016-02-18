<?php

class EarthIT_OIDAllocator_Router extends EarthIT_OIDAllocator_Component
{
	public function apiRequestToAction( $method, $path, $queryString, Nife_Blob $content=null ) {
		throw new Exception("No worky yet!");
	}
	
	protected function createPageAction( $actionName /* followed by action-specific arguments */ ) {
		$args = func_get_args();
		/* $actionName = */ array_shift($args);
		array_unshift($args, $this->registry);
		$className = "EarthIT_OIDAllocator_PageAction_{$actionName}";
		$rc = new ReflectionClass($className);
		return $rc->newInstanceArgs($args);
	}
	
	public function requestToAction( EarthIT_OIDAllocator_Request $req ) {
		$path = $req->getPathInfo();
		if( $path == '/' ) {
			return $this->createPageAction('ShowHello');
		} else if( $path == '/login' ) {
			switch( $req->requestMethod ) {
			case 'GET' : return $this->createPageAction('ShowLoginForm', $req->getParam('error-message-id'));
			case 'POST': return $this->createPageAction('LogIn', $req->getParam('username'), $req->getParam('password'));
			}
		} else if( $path == '/logout' ) {
			return function(EarthIT_OIDAllocator_ActionContext $actx) {
				if( $actx->sessionExists() ) $actx->destroySession();
				return Nife_Util::httpResponse(303, 'Log you out!', ['location'=>'./']);
			};
		} else if( $path == '/register' ) {
			switch( $req->getRequestMethod() ) {
			case 'GET':
				return $this->createPageAction('ShowRegistrationForm', $req->getParam('error-message-id'));
			case 'POST':
				return $this->createPageAction('Register', $req->getParams());
			}
		} else if(
			preg_match('#^/api([;/].*)#',$path,$bif) and
			($apiAction = $this->apiRequestToAction(
				$req->getRequestMethod(),
				$bif[1], $req->queryString,
				$req->getRequestContentBlob())
			 ) !== null
		) {
			return $apiAction;
		}
		
		return function(EarthIT_OIDAllocator_ActionContext $actx) use ($req, $path) {
			// These are here because they haven't yetb been converted to
			// the newer interpret-then-execute style, though it'd be
			// easy to do.
			if( preg_match('<^/hello/(.*)$>', $path, $matchData) ) {
				return Nife_Util::httpResponse( 200, "Hello, ".rawurldecode($matchData[1]).'!' );
			} else if( $path == '/error' ) {
				trigger_error( "An error occurred for demonstrative porpoises.", E_USER_ERROR );
			} else if( $path == '/exception' ) {
				throw new Exception( "You asked for an exception and this is it." );
			} else {
				return Nife_Util::httpResponse( 404, "I don't know about $path!" );
			}
		};
	}
	
	public function doAction($action, EarthIT_OIDAllocator_ActionContext $actx) {
		if( is_callable($action) ) {
			return call_user_func($action, $actx);
		} else {
			throw new Exception("I don't know how to run ".EarthIT_OIDAllocator_Debug::describe($action)." as an action");
		}
	}
	
	/**
	 * Check a few different ways that a user could be authenticated
	 * and return a new ActionContext that reflects any logged-in-ness
	 * 
	 * @return EarthIT_OIDAllocator_ActionContext with loggedInUserId set, if someone was authenticated
	 * @throws EarthIT_OIDAllocator_AuthenticationFailure
	 */
	public function authenticate( EarthIT_OIDAllocator_Request $req, EarthIT_OIDAllocator_ActionContext $actx ) {
		$auth = $req->getAuthUserPw();
		if( $auth['username'] !== null ) {
			$loginResult = $this->userModel->checkLogin( $auth['username'], $auth['password'] );
			if( $loginResult['success'] ) {
				return $actx->with(array('loggedInUserId'=>$loginResult['userId']));
			} else {
				throw new EarthIT_OIDAllocator_AuthenticationFailure($loginResult['message']);
			}
		}
		
		if( ($sessionUserId = $actx->getSessionVariable('userId')) !== null ) {
			return $actx->with(array('loggedInUserId'=>$sessionUserId));
		}
		
		return $actx;
	}
	
	public function handleRequest( EarthIT_OIDAllocator_Request $req, EarthIT_OIDAllocator_ActionContext $actx ) {
		try {
			$actx = $this->authenticate( $req, $actx );
		} catch( EarthIT_OIDAllocator_AuthenticationFailure $f ) {
			return Nife_Util::httpResponse( 401, $f->getMessage() );
		}
		
		$action = $this->requestToAction($req);
		return $this->doAction($action, $actx);
	}
}
