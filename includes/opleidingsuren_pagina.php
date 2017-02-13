<?php
/**
 * Opleidingsuren pagina
 *
 * Pagina voor het beheren van vaste opleidingsuren
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
 * @since      File available since Release 1.0.5
 * @version    1.0.8
 */
?>

<div ng-app="myApp" ng-controller="gebuikersCtrl"> <!-- Angular container, within this element the myApp application is active -->
	<div id="opleidingsPanel" class="panel panel-default">
		<div class="panel-body">

			<div ng-show="message" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>{{ message }}</div>
			<div ng-show="spinner" class="spinner"></div>

			<p>Dit overzicht toont de vaste opleidingsuren voor instructeurs welke wekelijks aan het bad staan.
			</p>
			<!-- ----------------------------------------------------------
				Table controls above table - Number of displayed rows, refresh data, global filter
				-->
			<div class="row">
				<!-- For big displays -->
				<div class="col-sm-9 hidden-sm hidden-xs">
					<form role="form" class="form-inline">
						<div class="form-group"><!-- Number of displayed rows -->
							<select 
								class="form-control" 
								ng-model="itemsPerPage" 
								id="numberOfRows" 
								ng-options="'Toon ' + option + ' regels' for option in tableListOptions">
							</select>
						</div>

						<div class="form-group"><!-- Refresh data -->
							<button 
								class="btn btn-default" 
								ng-click="refresh()" 
								id="refreshData">
								Ververs tabel <span class="glyphicon glyphicon-refresh"></span>
							</button>
						</div>

						<div class="form-group"><!-- Show filters -->
							<button 
								ng-hide="showFilter" 
								class="btn btn-default" 
								ng-click="showFilter = !showFilter" 
								id="refreshData">
								Toon filter <span class="glyphicon glyphicon-filter"></span>
							</button>

							<button 
								ng-show="showFilter" 
								class="btn btn-default" 
								ng-click="showFilter = !showFilter" 
								id="refreshData">
								Verberg filter <span class="glyphicon glyphicon-filter"></span>
							</button>
						</div>
					</form>
				</div>
				<!-- Global search -->
				<div class="col-sm-3 hidden-sm hidden-xs text-right">
					<form rol="form" ng-hide="search == '[object Object]'">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon" id="search"><span class="glyphicon glyphicon-search"></span></span>
								<input 
									type="search" 
									ng-change="onSearch()" 
									class="form-control" 
									ng-model="search" 
									aria-describedby="search" 
									placeholder="Zoek..."/>
							</div>
						</div>
					</form>
					<div ng-show="search == '[object Object]'">
						<button 
							class="btn btn-info btn-block" 
							ng-click="resetSearch()">
							<span class="glyphicon glyphicon-search"></span> Reset filter
						</button>
						<br/>
					</div>
				</div>

				<!-- for small displays -->
				<div class="col-sm-12 hidden-lg hidden-md">
					<form role="form">
						<div class="form-group">
							<div class="input-group"><!-- Number of displayed tables and refresh data -->
								<select 
									class="form-control" 
									ng-model="itemsPerPage" 
									id="numberOfRows" 
									ng-options="'Toon ' + option + ' regels' for option in tableListOptions">
								</select>
								
								<span class="input-group-btn">
									<button 
										class="btn btn-default" 
										ng-click="refresh()" 
										id="refreshData">
										Ververs tabel <span class="glyphicon glyphicon-refresh"></span>
									</button>
								</span>
								
								<div class="input-group-btn"><!-- Show filters -->
									<button 
										ng-hide="showFilter" 
										class="btn btn-default" 
										ng-click="showFilter = !showFilter" 
										id="refreshData">
										Toon filter <span class="glyphicon glyphicon-filter"></span>
									</button>
									
									<button 
										ng-show="showFilter" 
										class="btn btn-default" 
										ng-click="showFilter = !showFilter" 
										id="refreshData">
										Verberg filter <span class="glyphicon glyphicon-filter"></span>
									</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<!-- Global search -->
				<div class="col-sm-12 hidden-lg hidden-md text-center">
					<form rol="form" ng-hide="search == '[object Object]'">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon" id="search">
									<span class="glyphicon glyphicon-search"></span>
								</span>
								<input 
									type="search" 
									class="form-control" 
									ng-model="search" 
									aria-describedby="search" 
									placeholder="Zoek..."/>
							</div>
						</div>
					</form>
					<div ng-show="search == '[object Object]'">
						<button 
							class="btn btn-info btn-block" 
							ng-click="search = ''">
							<span class="glyphicon glyphicon-search"></span> Reset filter
						</button>
						<br/>
					</div>
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
								<a href="" ng-click="sortType = 'voornaam'">voornaam</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'voornaam' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'voornaam' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							<th class="hidden-xs">
								<a href="" ng-click="sortType = 'achternaam'">achternaam</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'achternaam' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'achternaam' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>

							<th>
								<a href="" ng-click="sortType = 'datum'">Jaar</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'datum' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'datum' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>

							<th class="hidden-xs hidden-sm hidden-md">
								Aantal
							</th>
							<th>

							</th>
						</tr>

						<!--- Filters -->
						<tr ng-show="showFilter">
							<th>
								<form role="form" class="form-inline">
									<div class="form-group" style="width:100%">
										<div class="input-group" style="width:100%">
											<span 
												class="input-group-addon hidden-xs" 
												id="search" 
												style="width:2em">
												<span class="glyphicon glyphicon-filter"></span>
											</span>
											
											<select 
												ng-change="onSearch()" 
												class="form-control" 
												ng-model="search.voornaam" 
												id="filtervoornaam" 
												ng-options="voornaam for voornaam in urenVoorNamen">
											</select>
										</div>
									</div>
								</form>
							</th>
							<th class="hidden-xs">
								<form role="form" class="form-inline">
									<div class="form-group" style="width:100%">
										<div class="input-group" style="width:100%">
											<span 
												class="input-group-addon hidden-xs" 
												id="search" 
												style="width:2em">
												<span class="glyphicon glyphicon-filter"></span>
											</span>
											
											<select 
												ng-change="onSearch()" 
												class="form-control" 
												ng-model="search.achternaam" 
												id="filterachternaam" 
												ng-options="achternaam for achternaam in urenAchterNamen">
											</select>
										</div>
									</div>
								</form>
							</th>

							<th class="hidden-xs hidden-sm hidden-md"/>
							<th class="hidden-xs hidden-sm hidden-md"/>
							<th/>
						</tr>
					</thead>

					<!-- Table body -->
					<tbody>
						<tr ng-repeat="uur in uren | orderBy:sortType:sortReverse | filter:search:strict | limitTo:itemsPerPage:startItem">
							<td>{{ uur.voornaam }}</td>
							<td class="hidden-xs">{{ uur.achternaam }}</td>
							<td class="hidden-xs hidden-sm hidden-md">{{ uur.datum | date: "yyyy"}}</td>
							<td>{{ uur.aantal }}</td>
							<td class="text-right" style="width:7em;">
								<button
									type="button"
									style="width:3em;"
									class="btn btn-xs btn-default"
									data-toggle="modal"
									data-target="#deleterecord"
									ng-click="edit(uren.indexOf(uur))">
									<span class="glyphicon glyphicon glyphicon-trash"></span>
								</button>
								&nbsp;
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
				<div class="col-sm-8">
					Toon {{ startItem + 1 }} t/m {{ lastItemPage() }} van <span ng-show="totalItems != totalRecords">{{ totalItems }} gefilderde records. Totaal</span> {{ totalRecords }} records.
				</div>
				<div class="col-sm-4 hidden-sm hidden-xs text-right">
					<button class="btn btn-default" data-toggle="modal" data-target="#editrecord" ng-click="new()">Nieuw <span class="glyphicon glyphicon-log-in"></span></button><br/>
				</div>
			</div>
			<!-- Pagination - Large devices -->
			<div class="row">
				<div class="col-sm-12 text-right">
					<ul class="pagination">
						<li ng-hide="startItem <= 0">
							<a href="" ng-click="startItem = 0">
								<span class="glyphicon glyphicon-fast-backward"></span>
							</a>
						</li>
						<li ng-hide="startItem <= 0">
							<a href="" ng-click="startItem = startItem - itemsPerPage">
								<span class="glyphicon glyphicon-backward"></span>
							</a>
						</li>
						<li ng-class="{active: n == currentPage()}" ng-repeat="n in pagesList()">
							<a href="" ng-click="$parent.startItem = (n - 1) * $parent.itemsPerPage">{{ n }}</a>
						</li>
						<li ng-hide="currentPage() >= numberOfPages()">
							<a href="" ng-click="startItem = startItem + itemsPerPage">
								<span class="glyphicon glyphicon-forward"></span>
							</a>
						</li>
						<li ng-hide="currentPage() >= numberOfPages()">
							<a href="" ng-click="startItem = (numberOfPages() - 1) * itemsPerPage">
								<span class="glyphicon glyphicon-fast-forward"></span>
							</a>
						</li>
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
						<input type="hidden" ng-model="form.id" value="{{ form.id }}"></input> <!-- Hidden field to set record id -->

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
								<select 
									id="username" 
									name="username" 
									class="form-control" 
									ng-model="form.username" 
									ng-options="a.username as a.voornaam.concat(' ', a.achternaam, ' (', a.username, ')') for a in users" 
									errorText="Username is verplicht" 
									required>
								</select>
							</div>
						</div>


						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="datum">Jaar</label>
							<div class="col-sm-10">
								<input
									id="datum"
									name="datum"
									type="text"
									ng-pattern="/^(20)\d{2}$/"
									min="2010"
									max="2099"
									maxlength="4"
									ng-model="form.datum"
									errorText="Valide jaartal is verplicht"
									class="form-control"
									required
									placeholder="jjjj"/>
							</div>
						</div>

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="uren">Aantal uren</label>
							<div class="col-sm-10">
								<input
									id="uren"
									name="uren"
									type="number"
									maxlength="2"
									ng-model="form.uren"
									errorText="Uren is verpicht (maximaal 99 uur)"
									class="form-control"
									required
									/>
							</div>
						</div>
						<br/>

						<button 
							class="btn btn-default" 
							ng-disabled="editForm.$invalid" 
							ng-class="{'btn-success': editForm.$valid}" 
							ng-click="insert(form.index)">
							Update
						</button>
						<button 
							class="btn btn-default" 
							ng-click="reset()">
							Reset
						</button>
						<button 
							class="btn btn-default" 
							data-dismiss="modal" 
							ng-click="reset()">
							Annuleer
						</button>
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
					<button 
						type="button" 
						class="close" 
						data-dismiss="modal">
						&times;
					</button>
					
					<h4 class="modal-title">Regel verwijderen</h4>
				</div>
				<div class="modal-body">
					<p>
						Uren record voor {{ form.voornaam }} {{ form.achternaam }} op {{ form.datum | date: "yyyy" }} wordt verwijderd.<br/>
						Weet u het zeker?
					</p>
					<br/>

					<button 
						class="btn btn-danger" 
						data-dismiss="modal" 
						ng-click="delete(form.index)">
						Delete
					</button>
					
					<button 
						class="btn btn-default" 
						data-dismiss="modal" 
						ng-click="reset()">
						Cancel
					</button>
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
					<button 
						type="button" 
						class="close" 
						data-dismiss="modal">
						&times;
					</button>
					
					<h4 class="modal-title"><span class="glyphicon glyphicon-question-sign"></span> Help</h4>
				</div>

				<div class="modal-body">
					Deze pagina toont vaste opleidingsuren voor instructeurs welke vast aan het bad staan. Het vaste aantal
					opleidingsuren is bepaald in de settings van deze applicatie. Vaste opleidingsuren hoeven niet
					goedgekeurd te worden. Deze uren kunnen alleen door een goedkeurder worden ingegeven.<br/>
					<br/>
					Ingevoerde vaste opleidingsuren worden automatisch getoond in het overzicht van de gebruiker. De
					gebruikers kunnen deze uren niet zelf invoeren of wijzigen.<br/>

					<br/>

					<button class="btn btn-primary center-block" data-dismiss="modal">Sluiten</button>
				</div>
			</div>
		</div>
	</div>

</div>
