<?php
/**
 * Authenticate
 *
 * Full functional authentication module
 *
 * PHP version 5.4
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
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.0.9
 */
include_once 'includes/db_connect.php';
include_once 'includes/settings.php';
include_once 'objects/Authenticate_obj.php';
include_once 'objects/Users_obj.php';

// Start or restart session
include_once 'includes/login_functions.php';
sec_session_start ();

if (isset ( $_SERVER ['PATH_INFO'] ) && (strlen ( $_SERVER ['PATH_INFO'] ) > 1)) {
    // Find the possible reason for calling us
    $action = substr ( $_SERVER ['PATH_INFO'], 1 );
    if (strpos ( $action, "/", 1 )) {
        $action = substr ( $action, 0, strpos ( $action, "/" ) );
    }
} else {
    $action = null;
}
$authenticate = new Authenticate ( $mysqli );

// Check how we are called
if ($_SERVER ['REQUEST_METHOD'] == 'POST') {

    // Get the post info from the json call
    $postdata = file_get_contents ( 'php://input' );
    $request = json_decode ( $postdata );

    // What do we need to do
    switch ($action) {

        case 'login' :
			doLogin($request, $authenticate);
            break;

        case 'logout' :
			doLogout($authenticate);
            break;

        case 'passwordreset' :
			doPasswordReset($request, $authenticate);
            break;

        case 'passwordchange' :
			doPasswordChange($request, $authenticate);
            break;

        case 'register' :
			doRegister($request, $authenticate);
            break;

        default :
            echo json_encode ( array (
                    'success' => false,
                    'message' => 'Onbekende actie'
            ) );
    }
} elseif ($_SERVER ['REQUEST_METHOD'] == 'GET') {

    // What do we need to do
    switch ($action) {

        case "verify" :
			getVerify($authenticate);
            break;

        case 'confirmreset' :
			getConfirmReset();
            break;

        case 'groups' :
			getGroups($authenticate);
            break;

        case 'unlock' :
			getUnlock($authenticate);
            break;

        case 'self' :
			getSelf($authenticate);
            break;

        default :
			getListUsers($authenticate);
    }
} elseif ($_SERVER ['REQUEST_METHOD'] == 'PUT') {

    // Get the post info from the json call
    $postdata = file_get_contents ( 'php://input' );
    $request = json_decode ( $postdata );

	putUser($request, $authenticate);

} elseif ($_SERVER ['REQUEST_METHOD'] == 'DELETE') {

	deleteUser($authenticate);

}

/**
 * Login
 *
 * @param object $request
 * @param object $authenticate
 *
 * @return bool
*/
function doLogin($request, $authenticate)
{
    // Logon to application
    $username = filter_var ( $request->username, FILTER_SANITIZE_STRING );
    $password = filter_var ( $request->password, FILTER_SANITIZE_STRING );
    if (isset ( $_SESSION ['sessionHash'] )) {
        $sessionHash = filter_var ( $_SESSION ['sessionHash'], FILTER_SANITIZE_STRING );
    } else {
        $sessionHash = false;
    }
    if (isset ( $request->remember )) {
        if ($request->remember) {
            $remember = true;
        } else {
            $remember = false;
        }
    } else {
        $remember = false;
    }

    try {
        $session = $authenticate->login ( $username, $password, $remember, $sessionHash );
    } catch ( Exception $e ) {
        echo json_encode ( array (
            'success' => false,
            'message' => $e->getMessage ()
            ) );
        exit ();
    }

	// User is locked due to too many failed login attempts
        if (! $session) {
            echo json_encode ( array (
                'succes' => false,
                'message' => 'Gebruiker gelocked',
                'code' => 2
                ) );
            exit ();
        }
        echo json_encode ( array (
            'success' => true,
            'message' => 'Gebruiker ingelogd'
            ) );
        $_SESSION = $session;

	return true;
}

/**
 * Logout
 *
 * @param object $authenticate
 *
 * @return bool
*/
function doLogout($authenticate)
{
    // Logout from application
    $username = filter_var ( $_SESSION ['username'], FILTER_SANITIZE_STRING );
    $sessionHash = filter_var ( $_SESSION ['sessionHash'], FILTER_SANITIZE_STRING );

    try {
        $authenticate->logout ( $username, $sessionHash );
    } catch ( Exception $e ) {
        echo json_encode ( array (
            'success' => false,
            'message' => $e->getMessage ()
            ) );
        exit ();
    }

    session_destroy ();
    echo json_encode ( array (
        'success' => true,
        'message' => 'Gebruker uitgelogd'
        ) );

	return true;
}

/**
 * Request password reset
 *
 * @param object $request
 * @param object $authenticate
 *
 * @return bool
*/
function doPasswordReset($request, $authenticate)
{
    $email = filter_var ( $request->email, FILTER_SANITIZE_EMAIL );

    try {
        $authenticate->passwordreset ( $email );
    } catch ( Exception $e ) {
        echo json_encode ( array (
        'success' => false,
        'message' => 'Gebruiker niet gevonden'
        ) );
        exit ();
    }

    echo json_encode ( array (
        'success' => true,
        'message' => 'Wachtwoord reset verzonden'
        ) );

	return true;
}

/**
 * Password change
 *
 * @param object $request
 * @param object $authenticate
 *
 * @return bool
*/
function doPasswordChange($request, $authenticate)
{
            // Change user password
            $username = filter_var ( $request->username, FILTER_SANITIZE_STRING );
            if (isset ( $request->token )) {
                $token = filter_var ( $request->token, FILTER_SANITIZE_STRING );
            } else {
                $token = null;
            }
            if (isset ( $request->password )) {
                $password = filter_var ( $request->password, FILTER_SANITIZE_STRING );
            } else {
                $password = null;
            }
            $password1 = filter_var ( $request->password1, FILTER_SANITIZE_STRING );
            $password2 = filter_var ( $request->password2, FILTER_SANITIZE_STRING );

            try {
                $authenticate->change_password ( $username, $password, $password1, $password2, $token );
            } catch ( Exception $e ) {
                echo json_encode ( array (
                        'success' => false,
                        'message' => $e->getMessage ()
                ) );
                exit ();
            }

            echo json_encode ( array (
                    'success' => true,
                    'message' => 'Wachtwoord gewijzigd'
            ) );

			return true;
}

/**
 * Register new user
 *
 * @param object $request
 * @param object $authenticate
 *
 * @return bool
 */
function doRegister($request, $authenticate)
{
            // Sanitize our input
            $username = filter_var ( $request->username, FILTER_SANITIZE_STRING );
            $password1 = filter_var ( $request->password1, FILTER_SANITIZE_STRING );
            $password2 = filter_var ( $request->password2, FILTER_SANITIZE_STRING );
            $email = filter_var ( $request->email, FILTER_SANITIZE_EMAIL );
            if (isset ( $request->firstname )) {
                $firstname = filter_var ( $request->firstname, FILTER_SANITIZE_STRING );
            } else {
                $firstname = filter_var ( $request->firstName, FILTER_SANITIZE_STRING );
            }
            if (isset ( $request->lastname )) {
                $lastname = filter_var ( $request->lastname, FILTER_SANITIZE_STRING );
            } else {
                $lastname = filter_var ( $request->lastName, FILTER_SANITIZE_STRING );
            }

            try {
                $authenticate->register ( $username, $password1, $password2, $email, $firstname, $lastname );
            } catch ( Exception $e ) {
                echo json_encode ( array (
                        'success' => false,
                        'message' => $e->getMessage ()
                ) );
                exit ();
            }

            echo json_encode ( array (
                    'success' => true,
                    'message' => 'Gebruiker aangemaakt'
            ) );

			return true;
}

/**
 * Verify user
 *
 * @param object $authenticate
 *
 * @return bool
 */
function getVerify($authenticate)
{
            // Validate user from email validation request
                        // Find the username in the URL
            $username = substr ( $_SERVER ['PATH_INFO'], 1 );
            $username = filter_var ( substr ( $username, strpos ( $username, "/" ) + 1 ), FILTER_SANITIZE_STRING );
            $token = filter_input ( INPUT_GET, 'token', FILTER_SANITIZE_STRING );

            try {
                $authenticate->validate ( $username, $token );
            } catch ( Exception $e ) {
                $errormessage = $e->getMessage ();
                include_once ('includes/errormessage.php');
                exit ();
            }

            include_once ('includes/validate_success.php');

			return true;
}

/**
 * Cofirm password reset
 *
 * @return bool
 */
function getConfirmReset()
{
            // Execute password reset from email
            require_once ('confirmreset.php');

			return true;
}

/**
 * Get Groups of user
 *
 * @todo Groups verplaatsen naar groups rest service
 * @param object $authenticate
 *
 * @return bool
 */
function getGroups($authenticate)
{
            // Get all groups
            if ($authenticate->authorisation_check ( false )) {
                try {
                    $groups = $authenticate->get_groups ();
                } catch ( Exception $e ) {
                    echo json_encode ( array (
                            'success' => false,
                            'message' => $e->getMessage (),
                            'code' => 500
                    ) );
                    exit ();
                }
                echo json_encode ( $groups );
            }

			return true;
}

/**
 * Unlock user account
 *
 * @param object $authenticate
 *
 * @return bool
 */
function getUnlock($authenticate)
{
            // Execute account unlock from email
                        // Find the username in the URL
            $username = substr ( $_SERVER ['PATH_INFO'], 1 );
            $username = filter_var ( substr ( $username, strpos ( $username, "/" ) + 1 ), FILTER_SANITIZE_STRING );
            $token = filter_input ( INPUT_GET, 'token', FILTER_SANITIZE_STRING );

            try {
                $authenticate->unlock ( $username, $token );
            } catch ( Exception $e ) {
                $errormessage = $e->getMessage ();
                include_once ('includes/errormessage.php');
                exit ();
            }

            include_once ('includes/unlock_success.php');

			return true;
}

/**
 * Get information of current user
 *
 * @param object $authenticate
 *
 * @return bool
 */
function getSelf($authenticate)
{
            // Get information about current user
            if ($authenticate->authorisation_check ( false )) {
                $self = array (
                        'username' => $authenticate->username,
                        'firstname' => $authenticate->firstName,
                        'lastname' => $authenticate->lastName,
                        'email' => $authenticate->email,
                        'ipaddres' => $authenticate->ip
                );
                echo json_encode ( $self );
            } else {
                http_response_code ( 401 );
                echo json_encode ( array (
                        'success' => false,
                        'message' => 'Unauthorized',
                        'code' => 401
                ) );
            }

			return true;
}

/**
 * Get all information of users
 *
 * @todo Verplaats naar rest users
 * @param object $authenticate
 *
 * @return bool
 */
function getListUsers($authenticate)
{
	global $mysqli;
            // Get all information of the user or users

            if ($authenticate->authorisation_check ( false )) {

                // Check if we are called for one user or all users
                if (isset ( $_SERVER ['PATH_INFO'] ) && (strlen ( $_SERVER ['PATH_INFO'] ) > 1)) {
                    // We are called for one user

                    // Check if username is same or got the right permissions
                    $request_user = substr ( filter_var ( $_SERVER ['PATH_INFO'], FILTER_SANITIZE_STRING ), 1 );

                    if ($request_user != $authenticate->username &&
				    ((! is_array ( $authenticate->group )) || ! (in_array ( 'admin', $authenticate->group ) || in_array ( 'super', $authenticate->group )))) {
                        http_response_code ( 403 );
                        echo json_encode ( array (
                            'success' => false,
                            'message' => 'Forbidden',
                            'code' => 403
                        ));
                        exit ();

                    }

                    $user = new Users($mysqli);
                    try {
                        $userinfo = $user->read($request_user);
                    } catch ( Exception $e ) {
                        http_response_code ( 404 );
                        echo json_encode ( array (
                                'success' => false,
                                'message' => 'Not found',
                                'code' => 404
                        ) );
                        exit ();
                    }
                    echo json_encode($user);
                } else {
                    // We are called for all users

                    // Check if we have the right permissions
                    if ((! is_array ( $authenticate->group )) || !(in_array ( 'admin', $authenticate->group ) || in_array ( 'super', $authenticate->group ))) {
                        http_response_code ( 403 );
                        echo json_encode ( array (
                                'success' => false,
                                'message' => 'Forbidden',
                                'code' => 403
                        ) );
                        exit ();
                    }

                    try {
                        $users_obj = new Users($mysqli);
                        $users_obj->read();
                    } catch ( Exception $e ) {
                        http_response_code ( 404 );
                        echo json_encode ( array (
                                'success' => false,
                                'message' => 'Not found',
                                'code' => 404
                        ) );
                        exit ();
                    }

                    echo json_encode($users_obj);
                }
            }
			return true;
}

/**
 * Update user information
 *
 * @todo Verplaats naar rest users
 * @param object $request
 * @param object $authenticate
 *
 * @return bool
 */
function putUser($request, $authenticate)
{
    global $mysqli;

    if ($authenticate->authorisation_check(false)) {
        // Check if we are a super or admin user
        if (is_array($authenticate->group) && (in_array ( 'admin', $authenticate->group ) || in_array ( 'super', $authenticate->group ))) {
            $super = true;
        } else {
            $super = false;
        }

        $users_obj = new Users($mysqli);
        $user_obj = new User($request->username, $request->firstname, $request->lastname, $request->groepen, $request->rollen, $request->email, null, null, null, null, $request->status);

        // Update user information
        try {
            $users_obj->update($user_obj, $super);
        } catch ( Exception $e ) {
            echo json_encode ( array (
                    'success' => false,
                    'message' => $e->getMessage ()
            ) );
            exit ();
        }

        echo json_encode ( array (
                'success' => true,
                'message' => 'Gebruikers informatie geupdate'
        ) );
    } else {
        http_response_code ( 401 );
        echo json_encode ( array (
                'success' => false,
                'message' => 'Unauthorized',
                'code' => 401
        ) );
    }

	return true;
}

/**
 * Delete user information
 *
 * @todo Verplaats naar rest users
 * @param object $authenticate
 *
 * @return bool
 */
function deleteUser($authenticate)
{
    global $mysqli;

    // Delete user information
    if ($authenticate->authorisation_check ( true )) {
        // Check if we are called for one user or all users
        if (isset ( $_SERVER ['PATH_INFO'] ) && (strlen ( $_SERVER ['PATH_INFO'] ) > 1)) {
            // We are called for one user

            $request_user = substr ( filter_var ( $_SERVER ['PATH_INFO'], FILTER_SANITIZE_STRING ), 1 );

            try {
                $users = new Users($mysqli);
                $users->delete($request_user);
            } catch ( Exception $e ) {
                // Proberbly session mismatch (session hijacking?)
                echo json_encode ( array (
                        'success' => false,
                        'message' => 'Kan gebruiker niet verwijderen',
                        'code' => 401
                ) );
                exit ();
            }

            echo json_encode ( array (
                    'success' => true,
                    'message' => 'Gebruiker verwijderd',
                    'code' => 200
            ) );
        } else {
            // No user is specified, this is not allowed
            http_response_code ( 400 );
            echo json_encode ( array (
                    'success' => false,
                    'message' => 'Bad request',
                    'code' => 400
            ) );
            exit ();
        }
    }

	return true;
}
