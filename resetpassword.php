<?php
/**
 * Page resetpassword | resetpassword.php
 *
 * Full functional authentication module
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
 * @since      File available since Release 1.0.0
 * @version    1.2.0
 * 
 * @var mysqli $mysqli
 * @var Users $users
 * @var User $user
 * @var string $token
 */

/**
 * Required files
 */
require_once 'includes/login_functions.php';
require_once 'includes/db_connect.php';

// Start session from login_functions.php
sec_session_start();

$username = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_STRING);
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

include_once('objects/Users_obj.php');

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Urenregistratie - Reddingsbrigade Apeldoorn</title>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1.0001, minimum-scale=1.0001, maximum-scale=1.0001, user-scalable=no"/> <!-- Scaleset workaround for iOS viewport bug -->

        <meta http-equiv="expires" content="Sun, 01 Jan 2014 00:00:00 GMT"/>
        <meta http-equiv="pragma" content="no-cache" />

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>

         <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js"></script>
         <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-route.js"></script>
         <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-touch.js"></script>

        <script src="scripts/showErrors.js"></script>
        <script src="scripts/resetpassword.js"></script>
        <script src="scripts/authenticate.js"></script>

        <style>
            .spinner {
                position:absolute;
                height:100px;
                width:100px;
                top: 50%;
                left: 50%;
                background: url(images/spinner.gif);
                background-size: 100%;
                z-index: 1000;
            }
        </style>
    </head>

<?php
    // Get all user info
    try {
        $users = new Users($mysqli);
        $users->read($username);
        $user = $users->users[0];
    } catch(Exception $e) {
        ?>
            <div class="modal-dialog">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Er is iets mis gegaan (error 1). Vraag een nieuw wachtwoord aan.<br/>
                    <a href="index.php">Ga terug</a>
                </div>
            </div>
        <?php
        exit;
    }

    if ($token != $user->getResetToken()) {
        ?>
            <div class="modal-dialog">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Er is iets mis gegaan (error 2). Vraag een nieuw wachtwoord aan.<br/>
                    <a href="index.php">Ga terug</a>
                </div>
            </div>
        <?php
        exit;
    }
?>

    <body>
        <?php include_once('includes/resetpassword_pagina.php'); ?>
    </body>
</html>