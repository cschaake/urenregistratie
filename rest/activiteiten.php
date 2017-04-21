<?php
/**
 * Activiteiten
 *
 * Rest service voor Activiteiten
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
 * @since      File available since Release 1.0.0
 * @version    1.0.9
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/Input_obj.php';
include_once '../objects/Activiteiten_obj.php';

// Start or restart session
include_once '../includes/login_functions.php';
sec_session_start();

$authenticate = new Authenticate($mysqli);

// Check if we are authorized
if (!$authenticate->authorisation_check(false)) {
    http_response_code(401);
    echo json_encode(array('success' => false, 'message' => 'Unauthorized', 'code' => 401));
    exit;
}
// We do have a valid user

$input = new input();

switch ($input->get_method()) {
    /**
     * POST method (CREATE)
     *
     * We need to insert a new record
     */
    case 'POST':
        postActiviteit($input);
        break;

    /**
     * GET method (READ)
     *
     * We need to read one or all records
     */
    case 'GET':
        getActiviteiten($input);
        break;

    /**
     * PUT method (UPDATE)
     *
     * We need to update a record
     */
    case 'PUT':
        putActiviteit($input);
        break;

    /**
     * DELETE method (DELETE)
     *
     * We need to delete a record
     */
    case 'DELETE':
        deleteActiviteit($input);
        break;

    /**
     * Other methods are not implemented
     *
     * Just return an error message
     */
    default:
        http_response_code(501);
        echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
}

/**
 * Post Activiteit
 *
 * Create a new Activiteit
 *
 * @param input $input
 *
 * @return bool
 */
function postActiviteit($input)
{
    global $mysqli;
	global $authenticate;

	// Only admin or super may execute this method
    if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
        exit;
    }

    $json = $input->get_JSON();

    $activiteit_obj = new Activiteit(null, $json->activiteit, $json->groep_id);
    $activiteiten_obj = new Activiteiten($mysqli);
    try {
        $activiteiten_obj->create($activiteit_obj);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
        exit;
    }

	http_response_code(200);
	header('Content-Type: application/json');
    echo json_encode($activiteiten_obj);

    return true;
}

/**
 * Get Activiteiten
 *
 * Read one or all activiteiten
 *
 * @param input $input
 *
 * @return bool
 */
function getActiviteiten($input)
{
    global $mysqli;

    if ($input->get_pathParams()) {

        $activiteiten_obj = new Activiteiten($mysqli);
        try {
            $activiteiten_obj->read(array_keys($input->get_pathParams())[0]);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
            exit;
        }

    } else {
        $activiteiten_obj = new Activiteiten($mysqli);
        try {
            $activiteiten_obj->read();
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
            exit;
        }

    }

	http_response_code(200);
	header('Content-Type: application/json');
    echo json_encode($activiteiten_obj);

    return true;
}

/**
 * Put Activiteit
 *
 * Update an existing Activiteit
 *
 * @param input $input
 *
 * @return bool
 */
function putActiviteit($input)
{
	global $mysqli;
	global $authenticate;

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

	$activiteit_obj = new Activiteit($json->id, $json->activiteit, $json->groep_id, $json->groep);
	$activiteiten_obj = new Activiteiten($mysqli);

	try {
		$activiteiten_obj->update($activiteit_obj);
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
    echo json_encode($activiteiten_obj);

	return true;
}

/**
 * Delete Activiteit
 *
 * Delete an existing Activiteit
 *
 * @param input $input
 *
 * @return bool
 */
function deleteActiviteit($input)
{
	global $mysqli;
	global $authenticate;

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

	$activiteiten_obj = new Activiteiten($mysqli);

	try {
		$activiteiten_obj->delete(array_keys($input->get_pathParams())[0]);
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
