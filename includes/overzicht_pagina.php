<?php
/**
 * Overzicht pagina
 *
 * Pagina met overzicht van gebruiker
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
 * @package    Urenverantwoording
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.0.8
 */
?>

<div ng-app="myApp" ng-controller="gebuikersCtrl" class="row"> <!-- Angular container, within this element the myApp application is active -->
                
	<!-- ------------------------------------------------------------------------------ -->
	<!-- Gebruikers panel -->
	<div id="gebruikersPanel" class="col-sm-12 col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-3 hidden-xs">
						<span class="glyphicon glyphicon-user" style="font-size:7em"></span>
					</div>
					<div class="col-sm-9 text-right">
						<table class="table">
							<tr>
								<th>Naam</th>
								<td>{{ self.firstname }} {{ self.lastname }}</td>
							</tr>
							<tr>
								<th>Login naam</th>
								<td>{{ self.username }}</td>
							</tr>
							<tr>
								<th>Email</th>
								<td>{{ self.email }}</td>
							</tr>
							<tr>
								<th>Rollen</th>
								<td>
									<div ng-repeat="certificaat in certificaten">
										{{ certificaat.rol }}
									</div>
									<div ng-if="result.length == 0">
										Geen rollen toegewezen
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
		
	<div class="col-sm-12 col-md-6">
	
		<!-- ------------------------------------------------------------------------------ -->
		<!-- Uren panelen -->
		<div id="urenPanel" class="panel panel-primary" ng-repeat="certificaat in certificaten">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-3 hidden-xs">
						<span class="glyphicon glyphicon-education" style="font-size:7em"></span>
					</div>
					<div class="col-sm-9 text-right">
						Uren benodigd voor {{ certificaat.rol }}&nbsp;&nbsp;
						<span style="font-size:4em">
							{{ urenNodig(certificaat.uren,(certificaat.ingevoerd - certificaat.afgekeurd)) | number : 1 }}
						</span>
						
						<div class="progress">
							<div 
								class="progress-bar progress-bar-success" 
								role="progressbar" 
								id="goedgekeurd"
								style="width:{{ 
									urenMax(
										certificaat.goedgekeurd * 100 / certificaat.uren,
										100
									);
								}}%">
							</div>
							
							<div 
								class="progress-bar progress-bar-warning" 
								role="progressbar" 
								id="tekeuren"
								style="width:{{ 
									urenMax(
										(certificaat.ingevoerd - certificaat.goedgekeurd - certificaat.afgekeurd) 
										* 100 / certificaat.uren, 
										100 - (certificaat.goedgekeurd * 100 / certificaat.uren)
									);
								}}%">
							</div>
							
							<div 
								class="progress-bar progress-bar-danger" 
								role="progressbar" 
								id="afgekeurd"
								style="width:{{ 
									urenMax(
										certificaat.afgekeurd * 100 / certificaat.uren,
										((certificaat.uren - certificaat.ingevoerd + certificaat.afgekeurd) * 100 / certificaat.uren)
									);
								}}%">
							</div>
						</div>
					</div>
				</div>
			</div>

			<div ng-init="showbewaking=false">
				<div ng-show="showbewaking" class="panel-body ">
					<table class="table">
						<tr>
							<th>Laatste certificering</th>
							<td>{{ certificaat.gecertificeerd }}</td>
						</tr>
						<tr>
							<th>Nieuwe certificering</th>
							<td>{{ certificaat.verloopt }}</td>
						</tr>
						<tr>
							<th>Aantal te maken uren</th>
							<td>{{ certificaat.uren | number : 1 }}</td>
						</tr>
						<tr>
							<th>Aantal ingevoerde uren</th>
							<td>{{ certificaat.ingevoerd | number : 1 }}</td>
						</tr>
						<tr>
							<th>Aantal goedgekeurde uren</th>
							<td>{{ certificaat.goedgekeurd | number : 1 }}</td>
						</tr>
						<tr>
							<th>Aantal afgekeurde uren</th>
							<td>{{ certificaat.afgekeurd | number : 1 }}</td>
						</tr>
						
					</table>
				</div>
				
				<div ng-hide="showbewaking" class="panel-footer text-primary">
					<a href="" ng-click="showbewaking = !showbewaking">Details
						<span class="pull-right glyphicon glyphicon-chevron-right"></span>
					</a>
				</div>
				<div ng-show="showbewaking" class="panel-footer text-primary">
					<a href="" ng-click="showbewaking = !showbewaking">Details
						Minder
						<span class="pull-right glyphicon glyphicon-chevron-left"></span>
					</a>
				</div>
				
			</div>
		</div>
	
		<!-- ------------------------------------------------------------------------------ -->
		<!-- Goedkeur panel -->
		<div id="goedkeurPanel" class="panel panel-danger" ng-if="showGoedtekeuren">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-3 hidden-xs">
						<span class="glyphicon glyphicon-check" style="font-size:7em"></span>
					</div>
					<div class="col-sm-9 text-right">
						Uren nog goed te keuren&nbsp;&nbsp;
						<span style="font-size:4em">
							{{ getTotal(goedtekeuren, 'uren') | number : 1 }}
						</span>
						
						<div class="progress">
							<div 
								class="progress-bar progress-bar-success" 
								role="progressbar" 
								style="width:%">
							</div>
							
							<div 
								class="progress-bar progress-bar-warning" 
								role="progressbar" 
								style="width:0%">
							</div>
							
							<div 
								class="progress-bar progress-bar-danger" 
								role="progressbar" 
								style="width:{{ 100 * getTotal(goedtekeuren, 'uren') / getTotal(goedtekeuren, 'totaaluren') }}%">
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div ng-init="showgoedkeur=false">
				<div ng-show="showgoedkeur" class="panel-body ">
					
					<table class="table">
						<tr>
							<th>Aantal goed te keuren uren</th>
							<td>{{ getTotal(goedtekeuren, 'uren') | number : 1 }}</td>
						</tr>
						<tr>
							<th>Totaal aantal ingevoerde uren</th>
							<td>{{ getTotal(goedtekeuren, 'totaaluren') | number : 1 }}</td>
						</tr>
						<tr>
							<th>Rollen</th>
							<td>
								<div ng-repeat="record in goedtekeuren">
									{{ record.rol }} {{ record.uren | number : 1 }} uren
								</div>
								<div ng-if="result.length == 0">
									Geen rollen toegewezen
								</div>
							</td>
						</tr>
						
					</table>
				</div>
				
				<div ng-hide="showgoedkeur" class="panel-footer text-danger">
					<a href="" ng-click="showgoedkeur = !showgoedkeur">
						Details
						<span class="pull-right glyphicon glyphicon-chevron-right"></span>
					</a>
				</div>
				<div ng-show="showgoedkeur" class="panel-footer text-danger">
					<a href="" ng-click="showgoedkeur = !showgoedkeur">
						Minder
						<span class="pull-right glyphicon glyphicon-chevron-left"></span>
					</a>
				</div>
				
			</div>
		</div>
		
	</div>
	<!-- ------------------------------------------------------------------------------------------
		Modal for help
	-->
	<div id="helpModal" class="modal" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><span class="glyphicon glyphicon-question-sign"></span> Help</h4>
				</div>
					
				<div class="modal-body">
					Deze pagina geeft een overzicht van de gebruikers gegevens en de geboekte uren.<br/>
					Het grijze block geeft een overzicht van de gebruikers gegevens en de huidige rollen. Voor
					deze rollen kunnen uren geboekt worden.<br/>
					<br/>
					De blauwe blokken geeft per rol de stand van de geboekte uren weer. De balk geeft aan hoeveel
					uren er geboekt zijn en hoeveel er nog nodig zijn voor de licentie. De groene balk is percentage 
					uren welke geboekt en goedgekeurd zijn. De oranje balk is het percentage geboekte uren welke nog
					goedgekeurd moeten worden. De rode balk geeft het percentage afgekeurde uren.<br/>
					<br/>
					Bij details wordt het blok geopend en worden de detail gegevens weer gegeven. Hierop staan de de exacte
					uren uitgesplitst in totaal aantal te maken uren, ingevoerde uren, goedgekeurde uren en afgekeurde uren.<br/>
					<br/>
					<div ng-show="showgoedkeur">
						Het rode blok geeft het aantal nog goed te keuren uren weer. Dit zijn uren van alle gebruikers welke
						nog op goedkeuring wachten. De balk geeft een indicatie van het aantal goed te keuren uren. In het 
						details scherm wordt een overzicht gegeven van goed te keuren uren naar rol.
						<br/>
					</div>
					Alleen uren voor de huidige certificerings periode tellen mee. De periode is van laatste certificering tot 
					nieuwe certificering.<br/>
					<br/>
					<button class="btn btn-primary center-block" data-dismiss="modal">Sluiten</button>
				</div>
			</div>	
		</div>
	</div>
</div>