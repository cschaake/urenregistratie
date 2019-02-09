<?php
/**
 * Service configuratie | rest/configuratie.php
 *
 * Rest service voor Configuratie
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
 * @since      File available since Release 1.0.6
 * @version    1.2.0
 * 
 * @var mysqli $mysqli
 * @var Authenticate $authenticate
 * @var Input $input
 */

/**
 * Required files
 */
require_once '../includes/db_connect.php';
require_once '../includes/settings.php';
require_once '../objects/Authenticate_obj.php';
require_once '../objects/Input_obj.php';

require_once '../objects/Groepen_obj.php';
require_once '../objects/Rollen_obj.php';
require_once '../objects/Certificaten_obj.php';

// Start or restart session
require_once '../includes/login_functions.php';
sec_session_start();

global $mysqli;

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

if ($input->get_method()) {
    // Read one or all records
    getConfiguratie();
} else {
    http_response_code(501);
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => false,
        'message' => 'Not implemented',
        'code' => 501
    ));
}

/**
 * fucntion getConfiguratie 
 *
 * Get all configuratie records
 *
 * @param object $request
 *
 * @return bool
 * 
 * @var array $result
 *      @param Groepen "groepen"
 *      @param Rollen "rollen"
 *      @param Certificaten "certificaten"
 * @var mysqli $mysqli
 * @var Authenticate $authenticate
 * @var Groepen $groepen_obj
 * @var Rollen $rollen_obj
 * @var Certificaten $certificaten_obj
 */
function getConfiguratie()
{
    global $authenticate;
	global $mysqli;

	$result = null;
	
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

    $groepen_obj = new Groepen($mysqli);
    try {
        $groepen_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Groepen not found', 'code' => 404));
        exit;
    }

	$rollen_obj = new Rollen($mysqli);
    try {
        $rollen_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Rollen not found', 'code' => 404));
        exit;
    }

	$certificaten_obj = new Certificaten($mysqli);
    try {
        $certificaten_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Certificaten not found', 'code' => 404));
        exit;
    }

	$result['groepen'] = $groepen_obj->groepen;
	$result['rollen'] = $rollen_obj->rollen;
	$result['certificaten'] = $certificaten_obj->certificaten;

	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($result);

	return true;
}

