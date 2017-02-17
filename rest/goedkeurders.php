<?php
/**
 * Goedkeurders
 *
 * Rest service voor goedkeurders pagina
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
 * @copyright  2015 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.5
 * @version    1.0.9
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/Input_obj.php';

include_once '../objects/Groepen_obj.php';
include_once '../objects/Rollen_obj.php';
include_once '../objects/Users_obj.php';
include_once '../objects/goedkeurders_obj.php';


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
        postGoedkeurder($input);
        break;

        // Read one or all records
    case 'GET':
        getGoedkeurders();
        break;

        // Update an existing record
    case 'PUT':
        putGoedkeurder($input);
        break;

        // delete an existing record
    case 'DELETE':
        deleteGoedkeurder($input);
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
 * Post goedkeurder
 *
 * @param object $record
 *
 * @return bool
 */
function postGoedkeurder($input)
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

    $goedkeurders_obj = new Goedkeurders($mysqli);
	$goedkeurder = new Goedkeurder($json->username, $json->firstname, $json->lastname, $json->groepen, $json->rollen);

    try {
        $goedkeurders_obj->create($goedkeurder);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Internal Server Error ' . $e->getMessage(), 'code' => 500));
        exit;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($goedkeurders_obj->goedkeurders[0]);

    return true;
}

/**
 * Get goedkeurders
 *
 * @return bool
 */
function getGoedkeurders()
{
	global $authenticate;
	global $mysqli;

	$users_obj = new Users($mysqli);
	$result['users'] = $users_obj->get();

	$groepen_obj = new groepen($mysqli);
	$groepen_obj->read();
	$result['groepen'] = $groepen_obj->groepen;

	$rollen_obj = new rollen($mysqli);
	$rollen_obj->read();
	$result['rollen'] = $rollen_obj->rollen;

	$goedkeurders_obj = new Goedkeurders($mysqli);
	$goedkeurders_obj->read();
	$result['goedkeurders'] = $goedkeurders_obj->goedkeurders;

	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($result);

	return true;
}

/**
 * Put goedkeurder
 *
 * @param object $record
 *
 * @return bool
 */
function putGoedkeurder($input)
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

    $goedkeuren_obj = new goedkeuren($mysqli);

    // Update record
	//Get uren by user (if not admin or super only show own uren)
    try {
        $goedkeuren_obj->update($json);
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
        exit;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($json);

    return true;
}

/**
 * Delete goedkeurder
 *
 * @param object $request
 *
 * @return bool
 */
function deleteGoedkeurder($input)
{
	global $mysqli;
	global $authenticate;

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

    $goedkeurders_obj = new Goedkeurders($mysqli);

    // Update record
    try {
        $goedkeurders_obj->delete(array_keys($input->get_pathParams())[0]);
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 404));
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
