<?php
/**
 * Page framework
 *
 * Framework for a page
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
 * @package    Urenregistratie
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.8
 * @version    1.0.8
 */
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
        <script src="scripts/login.js"></script>
        <script src="scripts/authenticate.js"></script>
		<?php if ($javaScript) {
        echo '<script src="' .  $javaScript . '"></script>';
		}?>
        
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
    
    <body>
        <div class="container">
            <div ng-app="myApp" ng-controller="loginCtrl"> <!-- Angular container, within this element the urenHeader application is active -->
                <?php include_once('includes/header.php'); ?>
                <?php include_once('modals/authenticate_modals.php'); ?>
                
                <?php if (isset($authenticate) || isset($anonymous)) { 
                    include_once($pagina);
                } else {
                    include_once('includes/guest_pagina.php');
                } ?>
                <?php include_once('includes/footer.php'); ?>
				
				<?php 
					if (isset($authenticate)) {
						include_once('includes/feedback.php');
					}
				?>
            </div>
        </div>
		
    </body>
</html>