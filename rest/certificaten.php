<?php
/**
 * Service certificaten | rest/certificaten.php
 *
 * Rest service voor Certificaten
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

require_once '../objects/Certificaten_obj.php';

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
 * Function postCertificaat
 *
 * @param input $input Input object containing all input parameters (sanitized)
 * @return bool
 * 
 * @var string $json
 * @var Certificaat $certificaat_obj
 * @var Certificaten $certificaten_obj
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

	$certificaat_obj = new Certificaat(null, $json->rol_id, null, $json->looptijd, $json->uren, $json->groep_id, null);
	$certificaten_obj = new Certificaten($mysqli);

	try {
		$certificaten_obj->create($certificaat_obj);
	} catch(Exception $e) {
		http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Internal Server Error', 'code' => 500));
        exit;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($certificaten_obj);

    return true;
}

/**
 * Function getCertificaten
 *
 * @param object $request
 *
 * @return bool
 * 
 * @var Certificaten $certificaten_obj
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
 * Function putCertificaat
 *
 * Replace a record
 *
 * @param Input $input
 *
 * @return bool
 * 
 * @var string $json
 * @var Certificaat $certificaat_obj
 * @var Certificaten $certificaten_obj
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
	echo json_encode($certificaten_obj);

	return true;
}

/**
 * Function deleteCertificaten
 *
 * Delete a record
 *
 * @param Input $input
 *
 * @return bool
 * 
 * @var Certificaten $certificaten_obj
 */
function deleteCertificaten($input)
{
	global $authenticate;
	global $mysqli;

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