<?php
/**
 * Maintenance pagina
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
 * @since      File available since Release 1.0.5
 * @version    1.0.5
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

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.js"></script>

    </head>
    
    
    <body>
        <div class="container">
				<nav class="navbar navbar-default">
					
						
						<div class="container-fluid">
							<div class="navbar-header">
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>                        
								</button>
								<a class="pull-left" href=""><img src="images/Icoon RBA.jpg" height="50px"></img></a>
								<a class="navbar-brand" href="">Urenregistratie </a>
							</div>
						</div>
				</nav>
				<div class="jumbotron text-center">
					<h1>Urenregistratie</h1>
					<hr/>
					<p>Applicatie momenteel niet beschikbaar</p>
				</div>
				<p class="text-info text-center">
				De urenregistratie applicatie van de Reddingsbrigade Apeldoorn is momenteel niet beschikbaar i.v.m. onderhoud.<br/>
				Probeer het op een later tijdstip nogmaals.<br/>
				#<?php print_r($e->getCode()); ?>
				</p><br/>
                
                <?php include_once('includes/footer.php'); ?>
        </div>
    </body>
</html>