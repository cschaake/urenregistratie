<?php
/**
 * Template header | includes/header.php
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
 */

/** 
 * @var Authenticate $authenticate Bevat het authenticatie object uit includes/login_functions.
 * @var mysqli $mysqli Represents a connection between PHP and a MySQL database.
 */
?>
<nav class="navbar navbar-default">
	<div id="mainMenu" class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>                        
			</button>
			<a class="pull-left" href=""><img src="images/Icoon RBA.jpg" height="50px"></img></a>
			<a class="navbar-brand" href="/urenregistratie/index.php">Urenregistratie </a>
		</div>
		<div class="collapse navbar-collapse" id="myNavbar">
			<?php if(isset($authenticate)) { ?>
				<ul class="nav navbar-nav">
					<li 
						<?php 
							if(stripos($_SERVER['REQUEST_URI'],'index.php') !== false) { 
								echo 'class="active"';
							}
						?>>
						<a href="index.php">Overzicht</a>
					</li>
					<li 
						<?php 
							if(stripos($_SERVER['REQUEST_URI'],'urenregistratie.php') !== false) {
								echo 'class="active"';
							}
						?>>
						<a href="urenregistratie.php">Registreren</a>
					</li>
					
					<?php 
						include_once 'objects/Users_obj.php';
						
						$users_obj = new Users($mysqli); 
						
						if ($users_obj->kanGoedkeuren($authenticate->username)) {
								?><li 
									<?php 
										if(stripos($_SERVER['REQUEST_URI'],'urengoedkeuren.php') !== false) {
											echo 'class="active"';
										}
									?>>
									<a href="urengoedkeuren.php">Goedkeuren</a>
								</li><?php
						}
						
						if ($authenticate->checkGroup('admin') || $authenticate->checkGroup('super')) { 
								?><li 
									<?php 
										if(stripos($_SERVER['REQUEST_URI'],'activiteitenbeheer.php') !== false) {
											echo 'class="active hidden-xs hidden-sm"';
										} else {
										    echo 'class="hidden-xs hidden-sm"';
										}
									?>>
									<a href="activiteitenbeheer.php">Activiteiten</a>
								</li><?php
						}
						
						if ($authenticate->checkGroup('admin') || $authenticate->checkGroup('super')) {
						    ?><li 
									<?php 
										if(stripos($_SERVER['REQUEST_URI'],'rapportage.php') !== false) {
											echo 'class="active hidden-xs hidden-sm"';
										} else {
										    echo 'class="hidden-xs hidden-sm"';
										}
									?>>
									<a href="rapportage.php">Rapportage</a>
								</li><?php
						}
					?>
				</ul>
			
			<?php } ?>
			
			<ul class="nav navbar-nav navbar-right">
				<?php if (isset($authenticate->username)) { ?>
					<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href=""><?php echo $authenticate->firstName . ' ' . $authenticate->lastName; ?><span class="caret"></span></a>
						<ul class="dropdown-menu" aria-labelledby="dropdownMenuDivider">
							<li><a href="" ng-click="showProfileModal('<?php echo $authenticate->username; ?>')">Mijn profiel</a></li>
							<li><a href="" data-toggle="modal" data-target="#changePasswordModal">Wijzig wachtwoord</a></li>
							<?php if (is_array($authenticate->group) && in_array('admin',$authenticate->group)) {
								?>
									<li role="separator" class="divider"></li>
									<li><a href="users.php">Gebruikers</a></li>
									
									<li><a href="configuratie.php">Configuratie</a></li>
									
							<?php } ?>
							
							<?php if (is_array($authenticate->group) && in_array('super',$authenticate->group)) {
								?>
									<li role="separator" class="divider"></li>
									<li><a href="boekersadmin.php">Boekers</a></li>
									<li><a href="goedkeurdersadmin.php">Goedkeurders</a></li>
									
							<?php } ?>
						</ul>
					</li>
					<li><a href="" data-toggle="modal" data-target="#helpModal"><span class="glyphicon glyphicon-question-sign"></span>  Help</a></li>
					<li><a href="" data-toggle="modal" data-target="#logoutModal"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
				<?php } else { ?>
					<li><a href="" data-toggle="modal" data-target="#loginModal"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</nav>
