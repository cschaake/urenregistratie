<?php
/**
 * Template unlockModal | includes/unlock_success.php
 *
 * Called when an user account unlock was successfull
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
 * @version	   1.2.3
 * 
 * @var string $username
 */
?>

<!DOCTYPE html>
<html lang="nl">
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

	</head>

	<body>
		<div class="container">
			<div>
				<div id="unlockModal" class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"><span class="glyphicon glyphicon-lock"></span> Unlock</h4>
						</div>

						<div class="modal-body">
							<p>Welkom <?php echo $username;?>, u kunt nu weer inloggen in de applicatie.</p>
							<p>Wanneer u uw wachtwoord bent vergeten, kunt u een nieuw wachtwoord aanvragen via de link wachtwoord vergeten.</p>
							<br/>
							<a href="/authenticate/index.php" class="btn btn-default">OK</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
