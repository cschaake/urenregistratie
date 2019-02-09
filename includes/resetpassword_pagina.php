<?php
/**
 * Template changePasswordModal | includes/resetpassword_pagina.php
 *
 * Pagina voor het beheren van boekers
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
 * @since      File available since Release 1.0.7
 * @version    1.2.0
 * 
 * @var User $user
 */

?>

<div class="container">
	<div ng-app="myApp" ng-controller="loginCtrl">

		<!-- ------------------------------------------------------------------------------------------
			Modal for changing password
		-->
		<div id="changePasswordModal" class="modal-dialog">
			<form class="form-horizontal" role="form" novalidate name="passwordForm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><span class="glyphicon glyphicon-lock"></span> Wijzig wachtwoord</h4>
					</div>

					<div class="modal-body">
						<div ng-show="message" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">�</a>{{ message }}</div>
						<div ng-show="spinner" class="spinner"></div>

						<div ng-init="password.username='<?php echo $user->username; ?>'"></div>
						<div ng-init="password.token='<?php echo $user->getResetToken(); ?>'"></div>

						<br/>

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-4" for="password1">Nieuw wachtwoord <a href="" ng-click="passwordHelp = !passwordHelp"><span class="glyphicon glyphicon-info-sign"></span></a></label>
							<div class="col-sm-6">
								<div class="input-group">
									<input
										id="password1"
										name="password1"
										type="{{ showPassword1 ? 'text' : 'password' }}"
										ng-model="password.password1"
										errorText="Valid password is required"
										class="form-control"
										ng-required="true"
										ng-pattern="/(?=^.{8,30}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/"/>
									<span class="input-group-btn">
										<button  class="btn btn-default" type="button" ng-click="showPassword1 = !showPassword1">
											<span ng-hide="showPassword1" class="glyphicon glyphicon-eye-open"></span>
											<span ng-show="showPassword1" class="glyphicon glyphicon-eye-close"></span>
										</button>
									</span>
								</div>
							</div>
						</div>

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-4" for="password2">Herhaal nieuw wachtwoord </label>
							<div class="col-sm-6">
								<div class="input-group">
									<input
										id="password2"
										name="password2"
										type="{{ showPassword2 ? 'text' : 'password' }}"
										ng-model="password.password2"
										errorText="Wachtwoorden zijn niet gelijk"
										class="form-control"
										ng-minlength="8"
										required
										pw-check="password1"/>
									<span class="input-group-btn">
										<button  class="btn btn-default" type="button" ng-click="showPassword2 = !showPassword2">
											<span ng-hide="showPassword2" class="glyphicon glyphicon-eye-open"></span>
											<span ng-show="showPassword2" class="glyphicon glyphicon-eye-close"></span>
										</button>
									</span>
								</div>
							</div>
						</div>

						<div ng-show="passwordHelp" class="alert alert-info">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">�</a>
							Het wachtwoord moet minimaal 8 tekens lang zijn en minimaal een hoofdletter, kleine letter en getal bevatten. Speciale tekens zijn optioneel, maar maken een wachtwoord veiliger.
						</div>

						<br/>

						<button class="btn btn-default" ng-disabled="passwordForm.$invalid" ng-class="{'btn-success': passwordForm.$valid}" ng-click="processChangePasswordForm(password)">Verstuur</button>
						<button class="btn btn-default" data-dismiss="modal" onclick="window.location='index.php';">Annuleer</button>
					</div>
				</div>
			</form>
		</div>

		<!-- ------------------------------------------------------------------------------------------
			Modal for password change success
		-->
		<div id="changePasswordSuccessModal" class="modal" role="dialog">
			<div class="modal-dialog">

				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><span class="glyphicon glyphicon-lock"></span> Wijzig Wachtwoord</h4>
					</div>

					<div class="modal-body">
						<p>Het wachtwoord is gewijzigd, u wordt doorverwezen naar de website.</p>
						<br/>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>