<?php
/**
 * Groepen
 *
 * Rest service voor Groepen
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

include_once '../objects/Groepen_obj.php';

// Start or restart session
include_once '../includes/login_functions.php';
sec_session_start();

$authenticate = new Authenticate($mysqli);

// Check if we are authorized
if (! $authenticate->authorisation_check(false)) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => false,
        'message' => 'Unauthorized',
        'code' => 401
    ));
    exit();
}
// We do have a valid user

// Get all input (sanitized)
$input = new Input();

switch ($input->get_method()) {
    // Insert a new record
    case 'POST':
        postGroep($input);
        break;

        // Read one or all records
    case 'GET':
        getGroepen($input);
        break;

        // Update an existing record
    case 'PUT':
        putGroep($input);
        break;

        // delete an existing record
    case 'DELETE':
        deleteGroep($input);
        break;

    default:
        http_response_code(501);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Not implemented',
            'code' => 501
        ));
}

/**
 * Post groep
 *
 * Insert a new record
 *
 * @param object $request
 *
 * @return bool
 */
function postGroep($input)
{
	global $authenticate;
	global $mysqli;

	$json = $input->get_JSON();

	// Only admin and super may update records
	if (! ($authenticate->checkGroup('admin') || $authenticate->checkGroup('super'))) {
	    http_response_code(403);
	    header('Content-Type: application/json');
	    echo json_encode(array(
	        'success' => false,
	        'message' => 'Forbidden',
	        'code' => 403
	    ));
	    exit();
	}

	$groepen_obj = new groepen($mysqli);
	$groep = new groep(null, $json->groep);

	try {
		$groepen_obj->create($groep);
	} catch(Exception $e) {
		http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Internal Server Error', 'code' => 500));
        exit;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($groepen_obj->groepen); // @todo alleen object terug geven

    return true;
}

/**
 * Get Groepen
 *
 * @param input $input
 *            Input object containing all input parameters (sanitized)
 * @return bool Successflag
 */
function getGroepen(input $input = null)
{
    global $mysqli;
    global $authenticate;

    if ($input->hasPathParams()) {
        // We are called for one record

        $groepen_obj = new groepen($mysqli);
        try {
            $groep = $groepen_obj->get(array_keys($input->get_pathParams())[0]);
        } catch(Exception $e) {
            http_response_code(404);
            echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
            exit;
        }
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($groep);
    } else {
        // We are called for all records

        $groepen_obj = new groepen($mysqli);
        try {
            $groepen_obj->read();
        } catch(Exception $e) {
            http_response_code(404);
            echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
            exit;
        }
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($groepen_obj);
    }

    return true;
}

/**
 * Delete groep
 *
 * Delete a record
 *
 * @param object $request
 *
 * @return bool
 */
function deleteGroep($input)
{
	global $authenticate;
	global $mysqli;

	// No record to delete was provided
	if (! $input->get_pathParams()) {
	    http_response_code(400);
	    header('Content-Type: application/json');
	    echo json_encode(array(
	        'success' => false,
	        'message' => 'Bad Request',
	        'code' => 400
	    ));
	    exit();
	}

	// Only admin and super may update records of other users
	if (! $authenticate->checkGroup('admin') || ! $authenticate->checkGroup('super')) {
	    $username = $authenticate->username;
	} else {
	    $username = null;
	}

	$groepen_obj = new groepen($mysqli);

	try {
		$groepen_obj->delete(array_keys($input->get_pathParams())[0]);
	} catch(Exception $e) {
		http_response_code($e->getCode());
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()));
        exit;
	}

	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode(array(
	    'success' => true,
	    'message' => 'Record successfully deleted',
	    'code' => 200
	));

	return true;
}

/**
 * Put groep
 *
 * Replace a record
 *
 * @param object $request
 *
 * @return bool
 */
function putGroep($input)
{
	global $authenticate;
	global $mysqli;

	$json = $input->get_JSON();

	// Only admin and super may update records
	if (! ($authenticate->checkGroup('admin') || $authenticate->checkGroup('super'))) {
	    http_response_code(403);
	    header('Content-Type: application/json');
	    echo json_encode(array(
	        'success' => false,
	        'message' => 'Forbidden',
	        'code' => 403
	    ));
	    exit();
	}

	$groepen_obj = new groepen($mysqli);

	try {
		$groepen_obj->update($json);
	} catch(Exception $e) {
		http_response_code(500);
		echo json_encode(array('success' => false, 'message' => 'Internal Server Error, ' . $e->getMessage(), 'code' => 500));
		exit;
	}

	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($groepen_obj->groepen);

	return true;
}
