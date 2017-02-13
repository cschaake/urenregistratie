<?php
/**
 * Goedkeurders admin pagina
 *
 * Pagina voor het beheren van goedkeurders
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
 * @since      File available since Release 1.0.6
 * @version    1.0.7
 */
 
?>
<div ng-app="myApp" ng-controller="gebuikersCtrl"> <!-- Angular container, within this element the myApp application is active -->
	<div id="goedkeurdersPanel" class="panel panel-default">
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
						<tr ng-repeat="goedkeurder in goedkeurders | orderBy:sortType:sortReverse | filter:search:strict | limitTo:itemsPerPage:startItem">
							<td>{{ goedkeurder.username }}</td>
							<td>{{ goedkeurder.firstname }}</td>
							<td>{{ goedkeurder.lastname}}</td>
							<td class="text-right" style="width:7em;">
								<button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#deleterecord" ng-click="edit(goedkeurders.indexOf(goedkeurder))"><span class="glyphicon glyphicon glyphicon-trash"></span></button>&nbsp;    
								<button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#editrecord" ng-click="edit(goedkeurders.indexOf(goedkeurder))"><span class="glyphicon glyphicon glyphicon glyphicon-pencil"></span></button>
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

	<!-- ------------------------------------------------------------------------------------------
		Modal for new and update record
	-->
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
							<label class="control-label col-sm-2" for="username">Gebruiker</label>
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
							<label class="control-label col-sm-2" for="username">Gebruiker</label>
							<div class="col-sm-10">
								<select 
									id="username" 
									name="username" 
									class="form-control" 
									ng-model="form.username" 
									ng-options="a.username as a.firstname.concat(' ', a.lastname, ' (', a.username, ')') for a in users" 
									errorText="Username is verplicht" 
									required></select>
							</div>
						</div>
						
						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="groepen">Groepen</label>
							<div class="col-sm-10">
								<select 
									multiple 
									id="groepen" 
									name="groepen" 
									class="form-control" 
									size="{{ groepen.length }}"
									ng-model="form.groepen" 
									ng-options="b.id as b.groep for b in groepen"
									errorText="Selecteer minimaal 1 groep" 
									required></select>
								<br/>
								<div id="description-select" class="alert alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>Gebruik <kbd>ctrl</kbd> om meerder groepen te selecteren.</div>
							</div>
						</div>

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="rollen">Rollen</label>
							<div class="col-sm-10">
								<select 
									multiple 
									id="rollen" 
									name="rollen" 
									class="form-control" 
									size="{{ rollen.length }}"
									ng-model="form.rollen" 
									ng-options="c.id as c.rol for c in rollen"
									errorText="Selecteer minimaal 1 rol"
									required></select>
								<br/>
								<div id="description-select" class="alert alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>Gebruik <kbd>ctrl</kbd> om meerder rollen te selecteren.</div>
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
	
	<!-- ------------------------------------------------------------------------------------------
		Modal delete confirmation
	-->
	<div id="deleterecord" class="modal" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Regel verwijderen</h4>
				</div>
				<div class="modal-body">
					<p>Alle goedkeurders rechten voor gebruiker {{ form.username }} worden verwijderd.<br/>
					Weet u het zeker?</p><br/>
					
					<button class="btn btn-danger" data-dismiss="modal" ng-click="delete(form.index)">Verwijder</button>
					<button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Annuleer</button>
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
					Deze pagina bevat alle gebruikers welke uren kunnen goedkeuren.<br/>
					<br/>
					Gebruikers kunnen uren goedkeuren voor aangegeven groepen. Op deze pagina kunnen de gebruikers opgegeven worden
					welke uren mogen goedkeuren, en voor welke groep(en).<br/>
					<br/>
					
					<button class="btn btn-primary center-block" data-dismiss="modal">Sluiten</button>
				</div>
			</div>	
		</div>
	</div>
</div>
