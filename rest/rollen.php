<?php
/**
 * Service Rollen | rest/rollen.php
 *
 * Rest service voor Rollen
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
 * @package    Urenverantwoording
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2019 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.2.1
 * 
 * @var mysqli $mysqli
 * @var Authenticate $authenticate
 * @var Input $input
 */

/**
 * Required files
 */
require_once '../includes/db_connect.php';
require_once '../includes/configuration.php';
require_once '../objects/Authenticate_obj.php';
require_once '../objects/Input_obj.php';

require_once '../objects/Rollen_obj.php';

// Start or restart session
require_once '../includes/login_functions.php';
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
        postRol($input);
        break;

        // Read one or all records
    case 'GET':
        getRollen($input);
        break;

        // Update an existing record
    case 'PUT':
        putRol($input);
        break;

        // delete an existing record
    case 'DELETE':
        deleteRol($input);
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
 * Function postRol - Post rol
 *
 * Insert a new record
 *
 * @param input $input Input object containing all input parameters (sanitized)
 * @return bool Successflag
 * 
 * @var string $json
 * @var Rol $rol_obj
 * @var Rollen $rollen_obj
 */
function postRol($input)
{
	global $authenticate;
	global $mysqli;
	$json = null;
	$rol_obj = null;
	$rollen_obj = null;

	$json = $input->get_JSON();

	// Only admin and super may update records of other users
	if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
	    http_response_code(403);
	    echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
	    exit;
	}

 	$rol_obj = new Rol(null, $json->rol);

	$rollen_obj = new Rollen($mysqli);

	try {
		$rollen_obj->create($rol_obj);
	} catch(Exception $e) {
		http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Internal Server Error', 'code' => 500));
        exit;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($rollen_obj);

    return true;
}

/**
 * Function getRollen Get rollen
 *
 * Get all records
 *
 * @param Input $input
 * @return bool
 * 
 * @var Rollen $rollen_obj
 */
function getRollen($input)
{
	global $authenticate;
	global $mysqli;
	$rollen_obj = null;

	if ($input->get_pathParams()) {
	    $rollen_obj = new Rollen($mysqli);
	    try {
	        $rollen_obj->read(array_keys($input->get_pathParams())[0]);
	    } catch(Exception $e) {
	        http_response_code(500);
	        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
	        exit;
	    }
	} else {
	    $rollen_obj = new Rollen($mysqli);
	    try {
	        $rollen_obj->read();
	    } catch(Exception $e) {
	        http_response_code(500);
	        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
	        exit;
	    }
	}

	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($rollen_obj);

	return true;
}

/**
 * Function deleteRol - Delete rol
 *
 * Delete a record
 *
 * @param Input $input
 * @return bool
 * 
 * @var Rollen $rollen_obj
 */
function deleteRol($input)
{
	global $authenticate;
	global $mysqli;
	$rollen_obj = null;

	// Only admin or super may execute this method
	if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
	    http_response_code(403);
	    echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
	    exit;
	}

	if (!$input->get_pathParams()) {
	    http_response_code(400);
	    echo json_encode(array('success' => false, 'Bad Request', 'code' => 400));
	    exit;
	}

	$rollen_obj = new Rollen($mysqli);

	try {
		$rollen_obj->delete(array_keys($input->get_pathParams())[0]);
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
 * Function putRol - Put rol
 *
 * Replace a record
 *
 * @param Input $input
 * @return bool
 * 
 * @var Rol $rol_obj
 * @var Rollen $rollen_obj
 * @var string $json
 */
function putRol($input)
{
	global $authenticate;
	global $mysqli;
	$rol_obj = null;
	$rollen_obj = null;
	$json = null;

	// Only admin or super may execute this method
	if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
	    http_response_code(403);
	    echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
	    exit;
	}

	if (!$input->get_pathParams()) {
	    http_response_code(400);
	    echo json_encode(array('success' => false, 'Bad Request', 'code' => 400));
	    exit;
	}

	$json = $input->get_JSON();

	if (array_keys($input->get_pathParams())[0] != $json->id) {
	    http_response_code(400);
	    echo json_encode(array('success' => false, 'message' => 'Selected record does not match JSON content', 'code' => 400));
	    exit;
	}

	$rol_obj = new Rol($json->id, $json->rol);
	$rollen_obj = new Rollen($mysqli);

	try {
		$rollen_obj->update($rol_obj);
	} catch(Exception $e) {
	    if ($e->getCode() == 404) {
	        http_response_code(404);
	        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 404));
	        exit;
	    }
	    http_response_code(500);
	    echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
	    exit;
	}

	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($rollen_obj);

	return true;
}
