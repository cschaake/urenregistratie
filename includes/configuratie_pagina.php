<?php
/**
 * Template configuratiePanel | includes/configuratie_pagina.php
 *
 * Pagina voor Urenregistatie configuratie
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
 * @version    1.2.0
 */

?>
<div ng-app="myApp" ng-controller="configuratieCtrl"> <!-- Angular container, within this element the myApp application is active -->
    <div id="configuratiePanel" class="panel panel-default">
        <div class="panel-body">

            <div ng-show="message" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>{{ message }}</div>
            <div ng-show="spinner" class="spinner"></div>

            <!-- ----------------------------------------------------------
                Table controls above table - Number of displayed rows, refresh data, global filter
                -->
            <div class="row">
                <!-- For big displays -->
                <div class="col-sm-9">
                    <form role="form" class="form-inline">
                        <div class="form-group"><!-- Refresh data -->
                            <button class="btn btn-default" ng-click="refresh()" id="refreshData">Ververs pagina <span class="glyphicon glyphicon-refresh"></span></button>
                        </div>
                    </form>
                </div>
                <br/><br/>
            </div>

            <div id="groepenPanel" class="panel panel-default">
                <div class="panel-heading">
                    Groepen
                </div>
                <div class="panel-body">
                    <!-- Groepen tabel -->

                    <!-- ----------------------------------------------------------
                        Table
                        -->
                    <div class="table-responsive">
                        <!-- Table list -->
                        <table class="table table-striped table-bordered">
                            <thead>
                                <!-- Table header -->
                                <!-- Header -->
                                <tr>
                                    <th>
                                        Groep
                                    </th>
                                    <th>
                                        Instructeur
                                    </th>
                                    <th/>
                                </tr>


                            </thead>

                            <!-- Table body -->
                            <tbody>
                                <tr ng-repeat="groep in groepen">
                                    <td>{{ groep.groep }}</td>
                                    <td>{{ groep.opleiding | boolFilter }}</td>
                                    <td class="text-right" style="width:7em;">
                                        <button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#deletegroep" ng-click="editgroep(groepen.indexOf(groep))"><span class="glyphicon glyphicon glyphicon-trash"></span></button>&nbsp;
                                        <button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#editgroep" ng-click="editgroep(groepen.indexOf(groep))"><span class="glyphicon glyphicon glyphicon glyphicon-pencil"></span></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ----------------------------------------------------------------------------------
                        Table controls below table
                        -->
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button class="btn btn-default" data-toggle="modal" data-target="#editgroep" ng-click="new()">Nieuw <span class="glyphicon glyphicon-log-in"></span></button><br/>
                        </div>
                    </div>

                </div>
            </div>

            <div id="rollenPanel" class="panel panel-default">
                <div class="panel-heading">
                    Rollen
                </div>
                <div class="panel-body">
                    <!-- Rollen tabel -->

                    <!-- ----------------------------------------------------------
                        Table
                        -->
                    <div class="table-responsive">
                        <!-- Table list -->
                        <table class="table table-striped table-bordered">
                            <thead>
                                <!-- Table header -->
                                <!-- Header -->
                                <tr>
                                    <th>
                                        Rol
                                    </th>
                                    <th/>
                                </tr>
                            </thead>

                            <!-- Table body -->
                            <tbody>
                                <tr ng-repeat="rol in rollen">
                                    <td>{{ rol.rol }}</td>
                                    <td class="text-right" style="width:7em;">
                                        <button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#deleterol" ng-click="editrol(rollen.indexOf(rol))"><span class="glyphicon glyphicon glyphicon-trash"></span></button>&nbsp;
                                        <button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#editrol" ng-click="editrol(rollen.indexOf(rol))"><span class="glyphicon glyphicon glyphicon glyphicon-pencil"></span></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ----------------------------------------------------------------------------------
                        Table controls below table
                        -->
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button class="btn btn-default" data-toggle="modal" data-target="#editrol" ng-click="new()">Nieuw <span class="glyphicon glyphicon-log-in"></span></button><br/>
                        </div>
                    </div>

                </div>
            </div>

            <div id="certificatenPanel" class="panel panel-default">
                <div class="panel-heading">
                    Certificaten
                </div>
                <div class="panel-body">
                    <!-- Certificaten tabel -->

                    <!-- ----------------------------------------------------------
                        Table
                        -->
                    <div class="table-responsive">
                        <!-- Table list -->
                        <table class="table table-striped table-bordered">
                            <thead>
                                <!-- Table header -->
                                <!-- Header -->
                                <tr>
                                    <th>
                                        Certificaat
                                    </th>
                                    <th>
                                        Groep
                                    </th>
                                    <th>
                                        Looptijd
                                    </th>
                                    <th>
                                        Uren
                                    </th>
                                    <th/>
                                </tr>

                            </thead>

                            <!-- Table body -->
                            <tbody>
                                <tr ng-repeat="certificaat in certificaten">
                                    <td>{{ certificaat.rol }}</td>
                                    <td>{{ certificaat.groep }}</td>
                                    <td>{{ certificaat.looptijd }}</td>
                                    <td>{{ certificaat.uren }}</td>
                                    <td ng-class="{'danger': uur.akkoord == '2'}" class="text-right" style="width:7em;">
                                        <button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#deletecertificaat" ng-click="editcertificaat(certificaten.indexOf(certificaat))"><span class="glyphicon glyphicon glyphicon-trash"></span></button>&nbsp;
                                        <button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#editcertificaat" ng-click="editcertificaat(certificaten.indexOf(certificaat))"><span class="glyphicon glyphicon glyphicon glyphicon-pencil"></span></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ----------------------------------------------------------------------------------
                        Table controls below table
                        -->
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button class="btn btn-default" data-toggle="modal" data-target="#editcertificaat" ng-click="new()">Nieuw <span class="glyphicon glyphicon-log-in"></span></button><br/>
                        </div>
                    </div>
                </div>
            </div>

            
		</div>
	</div>


	<!-- ------------------------------------------------------------------------------------------
		Modal for new and update groep record
	-->
	<div id="editgroep" class="modal" role="dialog">
		<div class="modal-dialog">
			
			<!-- Edit form -->
			<form class="form-horizontal" role="form" novalidate name="editgroepForm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 ng-show="groepen.form.edit" class="modal-title">Regel wijzigen</h4>
						<h4 ng-hide="groepen.form.edit" class="modal-title">Nieuwe regel toevoegen</h4>
					</div>

					<!-- Form -->
					<div class="modal-body">
						<div ng-show="messageGroepen" class="alert alert-danger">{{ messageGroepen }}</div>
						
						<input type="hidden" ng-model="groepen.form.edit" value="{{ groepen.form.edit }}"></input> <!-- Hidden field to set the edit variable -->
						<input type="hidden" ng-model="groepen.form.id" value="{{ groepen.form.id }}"></input> <!-- Hidden field to set record id -->

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							
							<label class="control-label col-sm-2" for="groep">Groep</label>
							<div class="col-sm-10">
								<input
									id="groep"
									name="groep"
									type="text"
									ng-model="groepen.form.groep"
									errorText="Groep is verplicht"
									class="form-control"
									required/>
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-sm-2" for="opleiding">Opleiding</label>
							<div class="col-sm-10">
								<div class="checkbox">
									<label><input id="opleiding" name="opleiding" type="checkbox" ng-model="groepen.form.opleiding" disabled/>Kan opleidingsuren boeken</label>
								</div>
							</div>
						</div>

						<br/>

						<button class="btn btn-default" ng-disabled="editgroepForm.$invalid" ng-class="{'btn-success': editgroepForm.$valid}" ng-click="insertgroep(groepen.form.index)">Update</button>
						<button class="btn btn-default" ng-click="reset()">Reset</button>
						<button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Annuleer</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- ------------------------------------------------------------------------------------------
		Modal delete groep confirmation
	-->
	<div id="deletegroep" class="modal" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Regel verwijderen</h4>
				</div>
				
				<div class="modal-body">
					<div ng-show="messageGroepen" class="alert alert-danger">{{ messageGroepen }}</div>
					<p>Groep record voor {{ groepen.form.groep }} wordt verwijderd.<br/>
					Weet u het zeker?</p><br/>

					<button class="btn btn-danger" ng-click="deletegroep(groepen.form.index)">Delete</button>
					<button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- ------------------------------------------------------------------------------------------
		Modal for new and update rol record
	-->
	<div id="editrol" class="modal" role="dialog">
		<div class="modal-dialog">
			<!-- Edit form -->
			<form class="form-horizontal" role="form" novalidate name="editrolForm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 ng-show="rollen.form.edit" class="modal-title">Regel wijzigen</h4>
						<h4 ng-hide="rollen.form.edit" class="modal-title">Nieuwe regel toevoegen</h4>
					</div>

					<!-- Form -->
					<div class="modal-body">
						<div ng-show="messageRollen" class="alert alert-danger">{{ messageRollen }}</div>
						<input type="hidden" ng-model="rollen.form.edit" value="{{ rollen.form.edit }}"></input> <!-- Hidden field to set the edit variable -->
						<input type="hidden" ng-model="rollen.form.id" value="{{ rollen.form.id }}"></input> <!-- Hidden field to set record id -->

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="groep">Rol</label>
							<div class="col-sm-10">
								<input
									id="rol"
									name="rol"
									type="text"
									ng-model="rollen.form.rol"
									errorText="Rol is verplicht"
									class="form-control"
									required/>
							</div>
						</div>
						
						<br/>

						<button class="btn btn-default" ng-disabled="editrolForm.$invalid" ng-class="{'btn-success': editrolForm.$valid}" ng-click="insertrol(rollen.form.index)">Update</button>
						<button class="btn btn-default" ng-click="reset()">Reset</button>
						<button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Annuleer</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- ------------------------------------------------------------------------------------------
		Modal delete rol confirmation
	-->
	<div id="deleterol" class="modal" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Regel verwijderen</h4>
				</div>
				<div class="modal-body">
					<div ng-show="messageRollen" class="alert alert-danger">{{ messageRollen }}</div>
					<p>Rol record voor {{ rollen.form.rol }} wordt verwijderd.<br/>
					Weet u het zeker?</p><br/>

					<button class="btn btn-danger" ng-click="deleterol(rollen.form.index)">Delete</button>
					<button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- ------------------------------------------------------------------------------------------
		Modal for new and update certificaat
	-->
	<div id="editcertificaat" class="modal" role="dialog">
		<div class="modal-dialog">
			<!-- Edit form -->
			<form class="form-horizontal" role="form" novalidate name="editcertificaatForm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 ng-show="certificaten.form.edit" class="modal-title">Regel wijzigen</h4>
						<h4 ng-hide="certificaten.form.edit" class="modal-title">Nieuwe regel toevoegen</h4>
					</div>

					<!-- Form -->
					<div class="modal-body">
						<div ng-show="messageCertificaten" class="alert alert-danger">{{ messageCertificaten }}</div>
						<input type="hidden" ng-model="certificaten.form.edit" value="{{ certificaten.form.edit }}"></input> <!-- Hidden field to set the edit variable -->
						<input type="hidden" ng-model="certificaten.form.id" value="{{ certificaten.form.id }}"></input> <!-- Hidden field to set record id -->
						
						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="certificaat">Certificaten</label>
							<div class="col-sm-10">
								<select id="certificaat" name="certificaat" class="form-control" ng-model="certificaten.form.rol_id" ng-options="a.id as a.rol group by a.groep for a in rollen" errorText="Certificaat is verplicht" required></select>
							</div>
						</div>
						
						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="groep">Groep</label>
							<div class="col-sm-10">
								<select id="groep" name="groep" class="form-control" ng-model="certificaten.form.groep_id" ng-options="a.id as a.groep for a in groepen" errorText="Groep is verplicht" required></select>
							</div>
						</div>

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="looptijd">Looptijd (maanden)</label>
							<div class="col-sm-10">
								<input
									id="looptijd"
									name="looptijd"
									type="number"
									ng-model="certificaten.form.looptijd"
									errorText="Looptijd is verplicht"
									class="form-control"
									required/>
							</div>
						</div>
						
						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="Uren">Uren</label>
							<div class="col-sm-10">
								<input
									id="uren"
									name="uren"
									type="number"
									ng-model="certificaten.form.uren"
									errorText="Uren is verplicht"
									class="form-control"
									required/>
							</div>
						</div>
						
						<br/>

						<button class="btn btn-default" ng-disabled="editcertificaatForm.$invalid" ng-class="{'btn-success': editcertificaatForm.$valid}" ng-click="insertcertificaat(certificaten.form.index)">Update</button>
						<button class="btn btn-default" ng-click="reset()">Reset</button>
						<button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Annuleer</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- ------------------------------------------------------------------------------------------
		Modal delete Certificaat confirmation
	-->
	<div id="deletecertificaat" class="modal" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Regel verwijderen</h4>
				</div>
				<div class="modal-body">
					<div ng-show="messageCertificaten" class="alert alert-danger">{{ messageCertificaten }}</div>
					<p>Certificaat record voor {{ certificaten.form.rol }} wordt verwijderd.<br/>
					Weet u het zeker?</p><br/>

					<button class="btn btn-danger" ng-click="deletecertificaat(certificaten.form.index)">Delete</button>
					<button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Cancel</button>
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
					Op deze pagina kan de configuratie van de Urenregistratie applicatie aangepast worden. Sommige onderdelen hebben speciale
					rechten nodig om gewijzigd te kunnen worden.<br/>
					<br/>
					De volgende onderdelen kunnen alleen met Admin rechten gewijzigd worden:
					<ul>
						<li>Groepen</li>
						<li>Rollen</li>
					</ul>
					De volgende onderdelen kunnen met Admin en Super rechten gewijzigd worden:
					<ul>
						<li>Certificaten</li>
						<li>Activiteiten</li>
					</ul>
					Gegevens kunnen alleen verwijderd worden wanneer deze niet gebruikt worden. Het programma controleerd dit en geeft een melding wanneer 
					het gegeven nog in gebruik is.
					<br/>
					<br/>
					<button class="btn btn-primary center-block" data-dismiss="modal">Sluiten</button>
				</div>
			</div>
		</div>
	</div>
</div>