<?php
/**
 * Configuratie
 *
 * Rest service voor Configuratie
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
 * @since      File available since Release 1.0.6
 * @version       1.0.9
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/Input_obj.php';

include_once '../objects/Groepen_obj.php';
include_once '../objects/Rollen_obj.php';
include_once '../objects/Activiteiten_obj.php';
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
        // Read one or all records
    case 'GET':
        getConfiguratie();
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
 * Get configuratie
 *
 * Get all configuratie records
 *
 * @param object $request
 *
 * @return bool
 */
function getConfiguratie()
{
    global $authenticate;
	global $mysqli;

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
        $groepen_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Groepen not found', 'code' => 404));
        exit;
    }

	$rollen_obj = new rollen($mysqli);
    try {
        $rollen_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Rollen not found', 'code' => 404));
        exit;
    }

	$activiteiten_obj = new Activiteiten($mysqli);
    try {
        $activiteiten_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Activiteiten not found', 'code' => 404));
        exit;
    }

	$certificaten_obj = new certificaten($mysqli);
    try {
        $certificaten_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Certificaten not found', 'code' => 404));
        exit;
    }

	$result['groepen'] = $groepen_obj->groepen;
	$result['rollen'] = $rollen_obj->rollen;
	$result['activiteiten'] = $activiteiten_obj->activiteiten;
	$result['certificaten'] = $certificaten_obj->certificaten;

	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($result);

	return true;
}

