<?php
/**
 * Template boekersPanel | includes/boekersadmin_pagina.php
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
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2020 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.2.2
 */

/**
 * boekersPanel
 */
?>
<div ng-app="myApp" ng-controller="gebuikersCtrl"> <!-- Angular container, within this element the myApp application is active -->
	<div id="boekersPanel" class="panel panel-default">
		<div class="panel-body">

			<div ng-show="message" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>{{ message }}</div>
			<div ng-show="spinner" class="spinner"></div>

			<!-- ----------------------------------------------------------
				Table controls above table - Number of displayed rows, refresh data, global filter
				-->
			<div class="row">
				<!-- For big displays -->
				<div class="col-sm-9 hidden-sm hidden-xs">
					<form role="form" class="form-inline">
						<div class="form-group"><!-- Number of displayed rows -->
							<select class="form-control" ng-model="itemsPerPage" id="numberOfRows" ng-options="'Toon ' + option + ' regels' for option in tableListOptions"></select>
						</div>
						<div class="form-group"><!-- Refresh data -->
							<button class="btn btn-default" ng-click="refresh()" id="refreshData">Ververs tabel <span class="glyphicon glyphicon-refresh"></span></button>
						</div>
					</form>
				</div>
				<!-- Global search -->
				<div class="col-sm-3 hidden-sm hidden-xs text-right">
					<form rol="form">
						<div class="form-group">
							<div class="input-group">

								<span class="input-group-addon" id="search"><span class="glyphicon glyphicon-search"></span></span>
								<input type="search" ng-change="onSearch()" class="form-control" ng-model="search" aria-describedby="search" placeholder="Zoek..."/>

							</div>
						</div>
					</form>
				</div>

				<!-- for small displays -->
				<div class="col-sm-12 hidden-lg hidden-md">
					<form role="form">
						<div class="form-group">
							<div class="input-group"><!-- Number of displayed tables and refresh data -->
								<select class="form-control" ng-model="itemsPerPage" id="numberOfRows" ng-options="'Toon ' + option + ' regels' for option in tableListOptions"></select>
								<span class="input-group-btn">
									<button class="btn btn-default" ng-click="refresh()" id="refreshData">Ververs tabel <span class="glyphicon glyphicon-refresh"></span></button>
								</span>
								<div class="input-group-btn"><!-- Show filters -->
									<button ng-show="showFilter" class="btn btn-default" ng-click="showFilter = !showFilter" id="refreshData">Verberg filter <span class="glyphicon glyphicon-filter"></span></button>
									<button ng-hide="showFilter" class="btn btn-default" ng-click="showFilter = !showFilter" id="refreshData">Toon filter <span class="glyphicon glyphicon-filter"></span></button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<!-- Global search -->
				<div class="col-sm-12 hidden-lg hidden-md text-center">
					<form rol="form">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon" id="search"><span class="glyphicon glyphicon-search"></span></span>
								<input type="search" class="form-control" ng-model="search" aria-describedby="search" placeholder="Zoek..."/>
							</div>
						</div>
					</form>
				</div>
			</div>

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
								<a href="" ng-click="sortType = 'username'">Username</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'username' && !sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span></span>
									<span ng-show="sortType == 'username' && sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span></span>
								</a>
							</th>
							<th class="hidden-xs">
								<a href="" ng-click="sortType = 'firstname'">Voornaam</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'firstname' && !sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span></span>
									<span ng-show="sortType == 'firstname' && sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span></span>
								</a>
							</th>
							<th>
								<a href="" ng-click="sortType = 'lastname'">Achternaam</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'lastname' && !sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span></span>
									<span ng-show="sortType == 'lastname' && sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span></span>
								</a>
							</th>
							<th/>
						</tr>

					</thead>

					<!-- Table body -->
					<tbody>
						<tr ng-repeat="boeker in boekers | orderBy:sortType:sortReverse | filter:search:strict | limitTo:itemsPerPage:startItem">
							<td>{{ boeker.username }}</td>
							<td>{{ boeker.firstname }}</td>
							<td>{{ boeker.lastname}}</td>
							<td class="text-right" style="width:7em;">
								<button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#deleterecord" ng-click="edit(boekers.indexOf(boeker))"><span class="glyphicon glyphicon glyphicon-trash"></span></button>&nbsp;
								<button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#editrecord" ng-click="edit(boekers.indexOf(boeker))"><span class="glyphicon glyphicon glyphicon glyphicon-pencil"></span></button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- ----------------------------------------------------------------------------------
				Table controls below table
				-->
			<!-- Large devices -->
			<!-- Number of records displayed - Large devices -->
			<div class="row">
				<div class="col-sm-6 hidden-sm hidden-xs">
					Toon {{ startItem + 1 }} t/m {{ lastItemPage() }} van <span ng-show="totalItems != totalRecords">{{ totalItems }} gefilderde records. Totaal</span> {{ totalRecords }} records.
				</div>
				<div class="col-sm-6 hidden-sm hidden-xs text-right">
					<button class="btn btn-default" data-toggle="modal" data-target="#editrecord" ng-click="new()">Nieuw <span class="glyphicon glyphicon-log-in"></span></button><br/>
				</div>
			</div>
			<!-- Pagination - Large devices -->
			<div class="row">
				<div class="col-sm-12 hidden-sm hidden-xs text-right">
					<ul class="pagination">
						<li ng-hide="startItem <= 0"><a href="" ng-click="startItem = 0"><span class="glyphicon glyphicon-fast-backward"></span></a></li>
						<li ng-hide="startItem <= 0"><a href="" ng-click="startItem = startItem - itemsPerPage"><span class="glyphicon glyphicon-backward"></span></a></li>
						<li ng-class="{active: n == currentPage()}" ng-repeat="n in pagesList()"><a href="" ng-click="$parent.startItem = (n - 1) * $parent.itemsPerPage">{{ n }}</a></li>
						<li ng-hide="currentPage() >= numberOfPages()"><a href="" ng-click="startItem = startItem + itemsPerPage"><span class="glyphicon glyphicon-forward"></span></a></li>
						<li ng-hide="currentPage() >= numberOfPages()"><a href="" ng-click="startItem = (numberOfPages() - 1) * itemsPerPage"><span class="glyphicon glyphicon-fast-forward"></span></a></li>
					</ul>
				</div>
			</div>

			<!-- Small devices -->
			<!-- Number of records displayed - Small devices -->
			<div class="row">
				<div class="col-sm-12 hidden-lg hidden-md text-right">
					<button class="btn btn-default" data-toggle="modal" data-target="#editrecord" ng-click="new()">Nieuw <span class="glyphicon glyphicon-log-in"></span></button><br/>
				</div>
				<div class="col-sm-12 hidden-lg hidden-md text-center">
					Toon {{ startItem + 1 }} t/m {{ lastItemPage() }} van <span ng-show="totalItems != totalRecords">{{ totalItems }} gefilderde records. Totaal</span> {{ totalRecords }} records.
				</div>
			</div>
			<!-- Pagination - Small devices -->
			<div class="row">
				<div class="col-sm-12 hidden-lg hidden-md text-center">
					<ul class="pagination">
						<li ng-hide="startItem <= 0"><a href="" ng-click="startItem = 0"><span class="glyphicon glyphicon-fast-backward"></span></a></li>
						<li ng-hide="startItem <= 0"><a href="" ng-click="startItem = startItem - itemsPerPage"><span class="glyphicon glyphicon-backward"></span></a></li>
						<li ng-class="{active: n == currentPage()}" ng-repeat="n in pagesList()"><a href="" ng-click="$parent.startItem = (n - 1) * $parent.itemsPerPage">{{ n }}</a></li>
						<li ng-hide="currentPage() >= numberOfPages()"><a href="" ng-click="startItem = startItem + itemsPerPage"><span class="glyphicon glyphicon-forward"></span></a></li>
						<li ng-hide="currentPage() >= numberOfPages()"><a href="" ng-click="startItem = (numberOfPages() - 1) * itemsPerPage"><span class="glyphicon glyphicon-fast-forward"></span></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
<?php
/**
 * editrecord
 */
?>
	<div id="editrecord" class="modal" role="dialog">
		<div class="modal-dialog">
			<!-- Edit form -->
			<form class="form-horizontal" role="form" novalidate name="editForm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 ng-show="form.edit" class="modal-title">Regel wijzigen</h4>
						<h4 ng-hide="form.edit" class="modal-title">Nieuwe regel toevoegen</h4>
					</div>

					<!-- Form -->
					<div class="modal-body">

						<input type="hidden" ng-model="form.edit" value="{{ form.edit }}"></input> <!-- Hidden field to set the edit variable -->

						<div ng-if="form.edit" class="form-group has-feedback">
							<label class="control-label col-sm-2" for="username">Username</label>
							<div class="col-sm-10">
								<input
									id="username"
									name="username"
									type="text"
									ng-model="form.fullname"
									class="form-control"
									readonly>
							</div>
						</div>

						<div ng-if="!form.edit" class="form-group has-feedback">
							<label class="control-label col-sm-2" for="username">Username</label>
							<div class="col-sm-10">
								<select id="username" name="username" class="form-control" ng-model="form.username" ng-options="a.username as a.firstname.concat(' ', a.lastname, ' (', a.username, ')') for a in users" errorText="Username is verplicht" required></select>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-sm-2" for="rol">Activiteit</label>
							<div class="col-sm-10">
								<div ng-repeat="activiteit in activiteiten | groupBy: 'groep'">
									<h4 ng-show="activiteit.group_by_CHANGED">{{ activiteit.groep }}</h4>
									<input id="{{ activiteit.id }}" name="activiteit" type="checkbox" value="{{ activiteit.id }}" ng-checked="form.activiteiten.indexOf(activiteit.id) > -1" ng-click="toggleActiviteit(activiteit.id)"/>
									<label for="{{ activiteit.id }}">{{ activiteit.activiteit }}</label>
								</div>
								<!--<div ng-repeat="activiteit in activiteiten">
									<input id="{{ activiteit.id }}" name="activiteit" type="checkbox" value="{{ activiteit.id }}" ng-checked="form.activiteiten.indexOf(activiteit.id) > -1" ng-click="toggleActiviteit(activiteit.id)"/>
									<label for="{{ activiteit.id }}">{{ activiteit.activiteit }}</label>
								</div>-->
							</div>
						</div>

						<div class="table-responsive">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Rol</th>
										<th>Gecertificeerd</th>
										<th>Verloopt</th>
										<th/>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="rol in form.rollen">
										<td>{{ rol.rol }}</td>
										<td>{{ rol.gecertificeerd | date: "yyyy-MM-dd" }}</td>
										<td>{{ rol.verloopt | date: "yyyy-MM-dd" }}</td>
										<td class="text-right" style="width:7em;">
											<button type="button" style="width:3em;" class="btn btn-xs btn-default" ng-click="removeCertificaat(form.rollen.indexOf(rol))"><span class="glyphicon glyphicon glyphicon-trash"></span></button>&nbsp;
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="row">
							<div class="col-sm-6">

							</div>
							<div class="col-sm-6 text-right">
								<button class="btn btn-default" data-toggle="modal" data-target="#addcertificaat">Nieuw <span class="glyphicon glyphicon-log-in"></span></button><br/>
							</div>
						</div>
						<br/>

						<button class="btn btn-default" ng-disabled="editForm.$invalid" ng-class="{'btn-success': editForm.$valid}" ng-click="insert(form.index)">Update</button>
						<button class="btn btn-default" ng-click="reset()">Reset</button>
						<button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Annuleer</button>
					</div>
				</div>
			</form>
		</div>
	</div>

<?php
/**
 * addcertificaat
 */
?>
	<div id="addcertificaat" class="modal" role="dialog">
		<div class="modal-dialog">
			<!-- Edit form -->
			<form class="form-horizontal" role="form" novalidate name="certForm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Certificaat toevoegen</h4>
					</div>

					<!-- Form -->
					<div class="modal-body">

						<div class="form-group has-feedback">
							<label class="control-label col-sm-4" for="username">Username</label>
							<div class="col-sm-8">
								<input id="username" name="username" type="text" ng-model="form.username" class="form-control" readonly/>
							</div>
						</div>

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-4" for="rol">Certificaat</label>
							<div class="col-sm-8">
								<select id="rol" name="rol" class="form-control" ng-model="certificaat.id" ng-options="a.id as a.rol for a in rollen" errorText="Rol is verplicht" required></select>
							</div>
						</div>

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-4" for="gecertificeerd">Datum gecertificeerd</label>
							<div class="col-sm-8">
								<input 
									id="gecertificeerd" 
									ng-change="calcCertificaat()" 
									name="gecertificeerd" 
									type="date" 
									ng-model="certificaat.gecertificeerd" 
									errorText="Datum is verplicht in het formaat jaar-maand-dag" 
									class="form-control" 
									required placeholder="jjjj-mm-dd"/>
							</div>
						</div>


						<div class="form-group has-feedback">
							<label class="control-label col-sm-4" for="verloopt">Datum verloopt</label>
							<div class="col-sm-8">
								<input id="rol" name="rol" type="text" ng-model="certificaat.verloopt" class="form-control" readonly/>
							</div>
						</div>

						<div class="form-group has-feedback">
							<label class="control-label col-sm-4" for="uren">Aantal uren</label>
							<div class="col-sm-8">
								<input id="rol" name="rol" type="text" ng-model="certificaat.uren" class="form-control" readonly/>
							</div>
						</div>

						<input type="hidden" id="group_id" ng-model="certificaat.group_id" readonly/>

						<button class="btn btn-default" ng-disabled="certForm.$invalid" ng-class="{'btn-success': certForm.$valid}" ng-click="addCertificaat(certificaat)">Update</button>
						<button class="btn btn-default" data-dismiss="modal" ng-click="cancelCertificaat()">Annuleer</button>

					</div>
				</div>
			</form>
		</div>
	</div>

<?php
/**
 * deleterecord
 */
?>
	<div id="deleterecord" class="modal" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Regel verwijderen</h4>
				</div>
				<div class="modal-body">
					<p>Uren record voor {{ form.activiteit }} op {{ form.datum | date: "yyyy-MM-dd" }} wordt verwijderd.<br/>
					Weet u het zeker?</p><br/>

					<button class="btn btn-danger" data-dismiss="modal" ng-click="delete(form.index)">Delete</button>
					<button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Cancel</button>
				</div>
			</div>
		</div>
	</div>

<?php
/**
 * helpModal
 */
?>
	<div id="helpModal" class="modal" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><span class="glyphicon glyphicon-question-sign"></span> Help</h4>
				</div>

				<div class="modal-body">
					Deze pagina bevat alle gebruikers welke uren kunnen boeken.<br/>
					<br/>
					Gebruikers hebben één of meer certificaten. Elk certificaat heeft een certificerings eis in de vorm
					van minimaal aantal uren. Bij de gebruiker dient een certificaat opgevoerd te worden met een begin
					datum voor geldigheid van het certificaat. Het systeem berekend zelf de einddatum voor de certificering.<br/>
					<br/>

					<button class="btn btn-primary center-block" data-dismiss="modal">Sluiten</button>
				</div>
			</div>
		</div>
	</div>

</div>