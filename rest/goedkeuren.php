<?php
/**
 * Goedkeuren
 *
 * Rest service voor goedkeuren van uren
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
 * @package    Urenverantwoording
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.0.9
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/Input_obj.php';

include_once '../objects/Uren_obj.php';
include_once '../objects/Activiteiten_obj.php';

// Start or restart session
include_once '../includes/login_functions.php';
sec_session_start();

$authenticate = new Authenticate($mysqli);

// Check if we are authorized
if (!$authenticate->authorisation_check(false)) {
    http_response_code(401);
	header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Unauthorized', 'code' => 401));
    exit;
}
// We do have a valid user

// Get all input (sanitized)
$input = new Input();

switch ($input->get_method()) {
     // Insert a new record
    case 'POST':
        postUrengoedkeuren($input);
        break;

    // Read one or all records
    case 'GET':
        getUrengoedkeuren();
        break;

    default:
        http_response_code(501);
		header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
}

/**
 * Post Urengoedkeuren
 *
 * Goedkeuren van uren
 *
 * @param 	input 	$input	Input object containing all input parameters (sanitized)
 *
 * @return 	bool	Successflag
 */
function postUrengoedkeuren($input) {
	global $mysqli;
	global $authenticate;

	// Admin of Super mag alle uren af/goedkeuren. Anders geldend de goedkeurders rollen, wordt doorgegeven via eigen username
	if ((is_array($authenticate->group)) && (in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
	    $username = null;
	} else {
	    $username = $authenticate->username;
	}

	$json = $input->get_JSON();
	$goedkeuren_obj = new uren($mysqli);

	if (array_key_exists('afkeuren', $input->get_pathParams())) {
        try {
            $goedkeuren_obj->afkeuren(array_keys($input->get_pathParams())[0], $json->reden);
        } catch(Exception $e) {
            http_response_code(404);
			header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Not found ' . $e->getMessage(), 'code' => 404));
            exit;
        }
        http_response_code(200);
		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Record succesvol afgekeurd', 'code' => 200));

	} elseif (array_key_exists('goedkeuren', $input->get_pathParams())) {
        try {
            $goedkeuren_obj->goedkeuren(array_keys($input->get_pathParams())[0]);
        } catch(Exception $e) {
            http_response_code(404);
			header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Not found ' . $e->getMessage(), 'code' => 404));
            exit;
        }
        http_response_code(200);
		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Record succesvol goedgekeurd', 'code' => 200));

	} else {
		http_response_code(501);
		header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
        exit;
	}

	return true;
}

/**
 * Get Urengoedkeuren
 *
 * Lees alle goed te keuren uren
 *
 * @param 	input 	$input	Input object containing all input parameters (sanitized)
 *
 * @return 	bool	Successflag
 */
function getUrengoedkeuren() {
	global $mysqli;
	global $authenticate;

	// Admin of Super mag alle uren af/goedkeuren. Anders geldend de goedkeurders rollen, wordt doorgegeven via eigen username
    if ((is_array($authenticate->group)) && (in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
        $username = null;
    } else {
		$username = $authenticate->username;
	}

    $goedkeuren_obj = new uren($mysqli);
    try {
        $goedkeuren_obj->readGoedTeKeuren($username);
    } catch(Exception $e) {
		http_response_code(400);
		header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 400));
        exit;
    }
	$result['uren'] = $goedkeuren_obj->uren;

	$activiteiten = new activiteiten($mysqli);
	try {
		$activiteiten->read();
	} catch(Exception $e) {
		http_response_code(400);
		header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 400));
        exit;
    }
	$result['activiteiten'] = $activiteiten->activiteiten;

   	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($result);

    return true;
}
