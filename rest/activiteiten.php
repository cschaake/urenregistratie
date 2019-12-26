<?php
/**
 * Service activiteiten | rest/activiteiten.php
 *
 * Rest service voor Activiteiten
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
 * @var input $input
 */

/**
 * required files
 */

require_once '../includes/db_connect.php';
require_once '../includes/configuration.php';
require_once '../objects/Authenticate_obj.php';
require_once '../objects/Input_obj.php';

require_once '../objects/Activiteiten_obj.php';
require_once '../objects/Groepen_obj.php';
require_once '../objects/Rollen_obj.php';

// Start or restart session
require_once '../includes/login_functions.php';
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
 * Function postActiviteit
 *
 * Create a new Activiteit
 *
 * @param input $input
 *
 * @return bool
 * 
 * @var string $json
 * @var Activiteit $activiteit_ojb
 * @var Activiteiten $activiteiten_obj
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
    // @TODO opmerkingVerplicht en opbouw kunnen ontbreken in de JSON
    $json = $input->get_JSON();
    $activiteit_obj = new Activiteit(null, $json->datum, $json->begintijd, $json->eindtijd, $json->activiteit, $json->rollen, $json->groep_id, null, $json->opmerkingVerplicht, $json->opbouw);
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
 * function getActiviteiten
 *
 * Read one or all activiteiten
 *
 * @param input $input
 *
 * @return bool
 * 
 * @var array $result
 *      @param Activiteiten "activiteiten"
 *      @param Groepen "groepen"
 * @var Activiteiten $activiteiten_obj
 * @var Groepen $groepen_obj
 * @var Rollen $rollen_obj
 */
function getActiviteiten($input)
{
    /**
     * @var array $result Resultaat van bevragingen
     */
    $result = null;
    
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
    $result['activiteiten'] = $activiteiten_obj->activiteiten;
    
    $groepen_obj = new Groepen($mysqli);
    try {
        $groepen_obj->read();
    } catch (Exception $e) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => $e->getMessage(),
            'code' => 400
        ));
        exit();
    }
    
    $result['groepen'] = $groepen_obj->groepen;
    
    $rollen_obj = new Rollen($mysqli);
    try {
        $rollen_obj->read();
    } catch (Exception $e) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => $e->getMessage(),
            'code' => 400
        ));
        exit();
    }
    
    $result['rollen'] = $rollen_obj->rollen;

	http_response_code(200);
	header('Content-Type: application/json');
    echo json_encode($result);

    return true;
}

/**
 * function puActiviteit
 *
 * Update an existing Activiteit
 *
 * @param input $input
 *
 * @return bool
 * 
 * @var string $json
 * @var Activiteit $activititeit_obj
 * @var Activiteiten $activiteiten_obj
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

	$activiteit_obj = new Activiteit($json->id, $json->datum, $json->begintijd, $json->eindtijd, $json->activiteit, $json->rollen, $json->groep_id, $json->groep, $json->opmerkingVerplicht, $json->opbouw);
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
 * Function deleteActiviteit
 *
 * Delete an existing Activiteit
 *
 * @param input $input
 *
 * @return bool
 * 
 * @var Activiteiten $activiteiten_obj
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
