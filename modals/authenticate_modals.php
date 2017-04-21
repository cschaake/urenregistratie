<?php
/**
 * Login modals
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
 * @package    authenticate
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version	   1.0.9
 */
?>
    <div id="loginModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <form class="form-horizontal" role="form" novalidate name="loginForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span class="glyphicon glyphicon-log-in"></span> Login</h4>
                    </div>

                    <div class="modal-body">
                        <div ng-show="message" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>{{ message }}</div>
                        <div ng-show="spinner" class="spinner"></div>

                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="username">Gebruikersnaam of email</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="username"><span class="glyphicon glyphicon glyphicon-user"></span></span>
                                    <input id="username" name="username" type="text" ng-model="login.username" class="form-control" errorText="Een valide gebruikersnaam of email is vereist." aria-describedby="username" required/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="password">Wachtwoord</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="password"><span class="glyphicon glyphicon glyphicon-lock"></span></span>
                                    <input id="password" name="password" type="{{ showPassword ? 'text' : 'password' }}" ng-model="login.password" errorText="Een valide wachtwoord is vereist." class="form-control" aria-describedby="password" required/></input>

                                    <span class="input-group-btn" id="password">
                                        <button class="btn btn-default" type="button" ng-click="showPassword = !showPassword"><span  ng-show="showPassword" class="glyphicon glyphicon-eye-close"></span>
                                        <span ng-hide="showPassword" class="glyphicon glyphicon-eye-open"></span></button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!--<div class="form-group">
                            <div class="col-sm-4"></div>
                            <div class="col-sm-6">
                                <div class="checkbox">
                                    <label><input type="checkbox" ng-model="login.remember">Remember me</input></label>
                                </div>
                            </div>
                        </div>-->

                        <button class="btn btn-default" ng-disabled="loginForm.$invalid" ng-class="{'btn-success': loginForm.$valid}" ng-click="processLoginForm(login)">Login</button>
                        <button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Annuleer</button>
                    </div>

                    <div class="modal-footer">
                        <div class="pull-right">
                            <a href="" ng-click="showForgotPassword()">Wachtwoord vergeten</a><br/>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php
    /**
     * Login Success Modal
     *
     * Modal will be displayed on successfull login.
     */
?>
    <div id="loginSuccessModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-log-in"></span> Login succesvol</h4>
                </div>

                <div class="modal-body">
                    <p>Login was succesvol, u wordt terug verwezen naar de pagina waar u vandaan kwam.</p>
                    <br/>
                </div>
            </div>
        </div>
    </div>

<?php
    /**
     * Account Locked Modal
     *
     * Modal will be displayed on successfull login.
     */
?>
    <div id="accountLockedModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-lock"></span> Account locked</h4>
                </div>

                <div class="modal-body">
                    <p>Uw account is gelocked vanwege te veel foutieve login pogingen.</p>
                    <p>Er is een email verstuurd naar uw geregistreerde email adres met daarin instructies om uw account weer vrij te geven.</p>
                    <br/>
                </div>
            </div>
        </div>
    </div>

<?php
    /**
     * registerModal
     *
     * Modal to register a new user
     */
?>
    <div id="registerModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <form class="form-horizontal" role="form" novalidate name="registerForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span class="glyphicon glyphicon-user"></span> Registreer als nieuw gebruiker</h4>
                    </div>

                    <div class="modal-body">
                        <div ng-show="message" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>{{ message }}</div>
                        <div ng-show="spinner" class="spinner"></div>

                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="username">Gebruikersnaam</label>
                            <div class="col-sm-6">
                                <input id="username" name="username" type="text" ng-model="register.username" errorText="Gebruikersnaam moet minimaal 5 characters lang zijn." ng-minlength="5" class="form-control" required/>
                            </div>
                        </div>

                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="firstname">Voornaam</label>
                            <div class="col-sm-6">
                                <input id="firstname" name="firstname" type="text" ng-model="register.firstname" class="form-control"/>
                            </div>
                        </div>

                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="lastname">Achternaam</label>
                            <div class="col-sm-6">
                                <input id="lastname" name="lastname" type="text" ng-model="register.lastname" class="form-control"/>
                            </div>
                        </div>

                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="email">Email</label>
                            <div class="col-sm-6">
                                <input id="email" name="email" type="email" ng-model="register.email" errorText="Een valide email adres is vereist." class="form-control" required/>
                            </div>
                        </div>

                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="password1">Nieuw wachtwoord <a href="" ng-click="passwordHelp = !passwordHelp"><span class="glyphicon glyphicon-info-sign"></span></a></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input id="passworda" name="password1" type="{{ showPassword1 ? 'text' : 'password' }}"
                                        ng-model="register.password1"
                                        errorText="Een valide wachtwoord is vereist."
                                        class="form-control"
                                        ng-required="true"
                                        ng-pattern="/(?=^.{8,30}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/"/></input>
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
                            <label class="control-label col-sm-4" for="password2">Herhaal nieuw wachtwoord</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input id="password2" name="password2" type="{{ showPassword2 ? 'text' : 'password' }}" ng-model="register.password2" errorText="Wachtwoorden zijn niet gelijk" class="form-control" ng-minlength="8" required pw-check="passworda"/></input>
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
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            Het wachtwoord moet minimaal 8 tekens lang zijn en minimaal een hoofdletter, kleine letter en getal bevatten. Speciale tekens zijn optioneel, maar maken een wachtwoord veiliger.
                        </div>
                        <br/>



                        <br/>

                        <button class="btn btn-default" ng-disabled="registerForm.$invalid" ng-class="{'btn-success': registerForm.$valid}" ng-click="processRegisterForm(register)">Registreer</button>
                        <button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Cancel</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

<?php
    /**
     * Register Success Modal
     *
     * Displays when logout was succesfull.
     */
?>
    <div id="registerSuccessModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-user"></span> Registreer</h4>
                </div>

                <div class="modal-body">
                    <p>Er is een email verstuurd naar {{ register.email }} om uw account te activeren.</p>
                    <br/>
                    <button class="btn btn-default" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

	<?php
    /**
     * Forgot Password Modal
     *
     * Displays password was lost.
     */
?>
    <div id="forgotPasswordModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <form class="form-horizontal" role="form" novalidate name="forgotPasswordForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span class="glyphicon glyphicon-lock"></span> Wachtwoord vergeten</h4>
                    </div>

                    <div class="modal-body">
                        <div ng-show="message" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>{{ message }}</div>
                        <div ng-show="spinner" class="spinner"></div>
                        <p>Vul uw gebruikersnaam of email adres in om een nieuw wachtwoord aan te vragen. U ontvangt een email met instructies om een nieuw wachtwoord te verkrijgen.</p>
                        <hr/>
                        <br/>
                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="email">Gebruikersnaam of email</label>
                            <div class="col-sm-6">
                                <input id="email" name="email" type="text" ng-model="forgotPassword.email" errorText="Valid username or email address required" class="form-control" required/>
                            </div>
                        </div>
                        <br/>
                        <button class="btn btn-default" ng-disabled="forgotPasswordForm.$invalid" ng-class="{'btn-success': forgotPasswordForm.$valid}" ng-click="progressForgotPassword(forgotPassword)">Verstuur</button>
                        <button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Annuleer</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

<?php
    /**
     * Forgot Password Succes Modal
     *
     * Displays when password forgot was succesfull.
     */
?>
    <div id="forgotPasswordSuccessModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-lock"></span> Wachtwoord vergeten</h4>
                </div>

                <div class="modal-body">
                    <p>Er is een email verstuurd naar {{ forgotPassword.email }} met instructies om het wachtwoord te wijzigen.</p>
                    <br/>
                    <button class="btn btn-default" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>


<?php
	/**
	 * Modals for succesfully logged in users only
	 */

	if(isset($authenticate)) {
?>

<?php
    /**
     * Logout Modal
     *
     * Displays the modal to logout.
     */
?>
    <div id="logoutModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-log-out"></span> Logout</h4>
                </div>

                <div class="modal-body">
                    <div ng-show="spinner" class="spinner"></div>
                    <p>Weet u zeker dat uw wilt uitloggen?</p>
                    <br/>
                    <button id="logoutbutton" class="btn btn-danger" ng-click="processLogoutForm()">Logout</button>
                    <button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Annuleer</button>
                </div>

            </div>
        </div>
    </div>

<?php
    /**
     * Logout Success Modal
     *
     * Displays when logout was succesfull.
     */
?>
    <div id="logoutSuccessModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-log-out"></span> Logout succesvol</h4>
                </div>

                <div class="modal-body">
                    <p>Logout was succesvol, u wordt doorverwezen naar de begin pagina.</p>
                    <br/>
                </div>
            </div>
        </div>
    </div>

<?php
    /**
     * ChangePasswordModal
     *
     * Displays modal to change password.
     */
?>
    <div id="changePasswordModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <form class="form-horizontal" role="form" novalidate name="passwordForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span class="glyphicon glyphicon-lock"></span> Wijzig wachtwoord</h4>
                    </div>

                    <div class="modal-body">
                        <div ng-show="message" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>{{ message }}</div>
                        <div ng-show="spinner" class="spinner"></div>
                        <div ng-init="password.username='<?php echo $authenticate->username; ?>'"></div>

                        <br/>
                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="password">Oud wachtwoord</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input id="password" name="password" type="{{ showPassword ? 'text' : 'password' }}" ng-model="password.password" errorText="Wachtwoord is vereist" class="form-control" required/></input>
                                    <span class="input-group-btn">
                                        <button  class="btn btn-default" type="button" ng-click="showPassword = !showPassword">
                                            <span ng-hide="showPassword" class="glyphicon glyphicon-eye-open"></span>
                                            <span ng-show="showPassword" class="glyphicon glyphicon-eye-close"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="password1">Nieuw wachtwoord <a href="" ng-click="passwordHelp = !passwordHelp"><span class="glyphicon glyphicon-info-sign"></span></a></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input id="password1" name="password1" type="{{ showPassword1 ? 'text' : 'password' }}"
                                        ng-model="password.password1"
                                        errorText="Een valide wachtwoord is vereist"
                                        class="form-control"
                                        ng-required="true"
                                        ng-pattern="/(?=^.{8,30}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/"/></input>
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
                            <label class="control-label col-sm-4" for="password2">Herhaal nieuw wachtwoord</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input id="password2" name="password2" type="{{ showPassword2 ? 'text' : 'password' }}" ng-model="password.password2" errorText="Wachtwoorden zijn niet gelijk" class="form-control" ng-minlength="8" required pw-check="password1"/></input>
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
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            Het wachtwoord moet minimaal 8 tekens lang zijn en minimaal een hoofdletter, kleine letter en getal bevatten. Speciale tekens zijn optioneel, maar maken een wachtwoord veiliger.
                        </div>

                        <br/>

                        <button class="btn btn-default" ng-disabled="passwordForm.$invalid" ng-class="{'btn-success': passwordForm.$valid}" ng-click="processChangePasswordForm(password)">Verstuur</button>
                        <button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Annuleer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php
    /**
     * changePasswordSucessModal
     *
     * Displays password was changes successfully.
     */
?>
    <div id="changePasswordSuccessModal" class="modal" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-lock"></span> Wijzig wachtwoord</h4>
                </div>

                <div class="modal-body">
                    <p>Uw wachtwoord is gewijzigd, u wordt doorgestuurd naar de pagina waar u vandaan komt.</p>
                    <br/>
                </div>
            </div>
        </div>
    </div>

<?php
    /**
     * profileModal
     *
     * Modal to edit user profile information
     */
?>
    <div id="profileModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <form class="form-horizontal" role="form" novalidate name="profileForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span class="glyphicon glyphicon-user"></span> Mijn profiel</h4>
                    </div>

                    <div class="modal-body">
                        <div ng-show="message" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>{{ message }}</div>
                        <div ng-show="spinner" class="spinner"></div>

                        <div class="form-group">
                            <label class="control-label col-sm-4" for="username">Gebruikersnaam</label>
                            <div class="col-sm-6">
                                <input id="username" name="username" type="text" ng-model="profile.username" class="form-control" readonly/>
                            </div>
                        </div>

                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="firstname">Voornaam</label>
                            <div class="col-sm-6">
                                <input id="firstname" name="firstname" type="text" ng-model="profile.firstname" class="form-control"/>
                            </div>
                        </div>

                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="lastname">Achternaam</label>
                            <div class="col-sm-6">
                                <input id="lastname" name="lastname" type="text" ng-model="profile.lastname" class="form-control"/>
                            </div>
                        </div>

                        <div class="form-group has-feedback" show-errors="{ showSuccess: true }">
                            <label class="control-label col-sm-4" for="email">Email</label>
                            <div class="col-sm-6">
                                <input id="email" name="email" type="email" ng-model="profile.email" errorText="Valid email address required" class="form-control" required/>
                            </div>
                        </div>

                        <button class="btn btn-default" ng-disabled="profileForm.$invalid" ng-class="{'btn-success': profileForm.$valid}" ng-click="processProfileForm(profile)">Update</button>
                        <button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Annuleer</button>
                    </div>
                    {{ profile }}
                </div>
            </form>
        </div>
    </div>


<?php
    /**
     * profileSuccessModal
     *
     * Shown when profile was updated successfully
     */
?>
    <div id="profileSuccessModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-user"></span> Mijn profiel</h4>
                </div>

                <div class="modal-body">
                    <p>Uw profiel is geupdate.</p>
                    <br/>
                    <button class="btn btn-default" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

<?php
	}
