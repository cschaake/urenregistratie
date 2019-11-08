<?php
/**
 * Script config | rest/config.php
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
 * @package    authenticate
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2019 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.2.1
 * @version    1.2.1
 *
 * @var mysqli $mysqli
 */

/**
 * Required files
 */
require_once '../includes/db_connect.php';

// Read all configuration
$prep_stmt = null;
$stmt = null;
$item = null;
$value = null;
$config = null;

// Select all configuration items and values
$prep_stmt = "
			SELECT
				item,
                value
			FROM
				configuration
            WHERE private = 0";

$stmt = $mysqli->prepare($prep_stmt);

if ($stmt) {
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows >= 1) {
        $stmt->bind_result($item, $value);
        
        while ($stmt->fetch()) {
            
            // Store all configuration items in constants with there values
            $config[$item] = $value;
        }
    } elseif ($stmt->num_rows == 0) {
        throw new Exception('Geen configuratie gevonden', 404);
    } else {
        $stmt->close();
        throw new Exception('Fout bij opvragen groep', 500);
    }
    $stmt->close();
} else {
    throw new Exception('Database error');
}

echo json_encode($config);