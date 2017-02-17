<?php
/**
 * Certificaten
 *
 * Rest service voor Certificaten
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

include_once '../objects/Certificaten_obj.php';

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
        postCertificaat($input);
        break;

        // Read one or all records
    case 'GET':
        getCertificaten();
        break;

        // Update an existing record
    case 'PUT':
        putCertificaat($input);
        break;

        // delete an existing record
    case 'DELETE':
        deleteCertificaten($input);
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
 * Post certificaat
 *
 * @param input $input
 *            Input object containing all input parameters (sanitized)
 * @return bool
 */
function postCertificaat($input)
{
    global $authenticate;
	global $mysqli;

	$json = $input->get_JSON();

	// Only admin and super may update records of other users
	if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
	    http_response_code(403);
	    echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
	    exit;
	}

	$certificaten_obj = new Certificaten($mysqli);

	try {
		$certificaten_obj->create($json);
	} catch(Exception $e) {
		http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Internal Server Error', 'code' => 500));
        exit;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($certificaten_obj->certificaten); // @todo Object terug geven

    return true;
}

/**
 * Get certificaten
 *
 * @param object $request
 *
 * @return bool
 */
function getCertificaten()
{
	global $mysqli;

    $certificaten_obj = new Certificaten($mysqli);
    try {
        $certificaten_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
        exit;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($certificaten_obj);

    return true;
}

/**
 * Put certificaat
 *
 * Replace a record
 *
 * @param object $request
 *
 * @return bool
 */
function putCertificaat($input)
{
	global $authenticate;
	global $mysqli;

	$json = $input->get_JSON();

	// Only admin and super may update records of other users
	if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
	    http_response_code(403);
	    echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
	    exit;
	}

	$certificaten_obj = new Certificaten($mysqli);
	$certificaat = new Certificaat($json->id, $json->rol_id, $json->rol, $json->looptijd, $json->uren,
	    $json->groep_id, $json->groep);

	try {
		$certificaten_obj->update($certificaat);
	} catch(Exception $e) {
		http_response_code(500);
		echo json_encode(array('success' => false, 'message' => 'Internal Server Error, ' . $e->getMessage(), 'code' => 500));
		exit;
	}

	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($certificaten_obj->certificaten); // @todo object terug geven

	return true;
}

/**
 * Delete certificaat
 *
 * Delete a record
 *
 * @param object $request
 *
 * @return bool
 */
function deleteCertificaten($input)
{
	global $authenticate;
	global $mysqli;

	$json = $input->get_JSON();

	// Only admin and super may update records of other users
	if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
	    http_response_code(403);
	    echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
	    exit;
	}

	$certificaten_obj = new Certificaten($mysqli);

	try {
		$certificaten_obj->delete(array_keys($input->get_pathParams())[0]);
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