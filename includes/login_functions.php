<?php
/**
 * Script login functies | includes/login_functions.php
 *
 * Full functional authentication module
 *
 * PHP version 7.2
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/mit-license.html  MIT License.  
 * If you did not receive a copy of the MIT License and are unable to 
 * obtain it through the web, please send a note to license@php.net so 
 * we can mail you a copy immediately.
 *
 * @package    authenticate
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2019 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.2.0
 */
include_once 'settings.php';

if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
    header('Location: https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], true, 301);
    die();
}

/**
 * Function sec_session_start
 *
 * Starts or renews PHP session, set new session-id and extent time for cookie
 *
 * @return bool success
 * 
 * @var string $session_name
 * @var string $secure
 * @var bool $httponly
 * @var array $cookieParams
 */
function sec_session_start() 
{
    $session_name = SESSIONNAME;   
    $secure = SECURE;
    $httponly = true; 
    
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === false) {
        throw new Exception('Schakel cookies in om deze applicatie te kunnen gebruiken.');
    }
    
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    
    if (isset($_SESSION['remember'])) {
		// Set lifetime to 30 days
        $cookieParams["lifetime"] = 2592000; 
    }
    
    // Sets the new cookies params
    session_set_cookie_params(
	    //        $cookieParams["lifetime"],
	'2592000',
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    
    // Sets the session name to the one set above.
    session_name($session_name);
    //session_set_cookie_params('2592000');
    session_start();
    
	// regenerated the session, delete the old one. 
    session_regenerate_id(true);    
    
    return true;
}

/**
 * Function checkAuthenticate
 *
 * Check if the user is logged in
 * 
 * @param mysqli $mysqli
 * @return Authenticate $authenticate object
 * 
 * @var string $username
 * @var string $sessionHash
 * @var string $remember
 * @var Authenticate $authenticate
 */
function checkAuthenticate($mysqli) 
{
	sec_session_start();

	/** 
	 * Check if session contains username and sessionHash. If these parameters are available, check them against the database and retrieve all user relevant info.
	 * On every check the session is renewed.
	 *
	 * The object $authenticate contains all user information.
	 * The returned object $session contains all new session info.
	 */
	if (isset($_SESSION['username'], $_SESSION['sessionHash'])) {
		
		$username = filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
		$sessionHash = filter_var($_SESSION['sessionHash'], FILTER_SANITIZE_STRING);
		$remember = isset($_SESSION['remember']);
		
		include_once 'objects/Authenticate_obj.php';
		$authenticate = new Authenticate($mysqli);
				
		try {
			$session = $authenticate->login_check($username, $sessionHash, $remember);
		} catch(Exception $e) {
			$authenticate->logout($username, $sessionHash);
			session_destroy();
		}
		if (isset($authenticate->username)) {
			$_SESSION = $session;
			// User is logged in
		} 
	} else {
		$authenticate = null;
	}
	
	return $authenticate;
}
