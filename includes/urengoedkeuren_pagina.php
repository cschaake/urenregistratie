<?php
/**
 * Template goedkeurenPanel | urengoedkeuren_pagina.php
 *
 * Pagina om uren goed te keuren
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
 * @version    1.2.3
 */
?>

<div ng-app="myApp" ng-controller="gebuikersCtrl"> <!-- Angular container, within this element the myApp application is active -->
	<div id="goedkeurenPanel" class="panel panel-default">
		<div class="panel-body">

			<div ng-show="message" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>{{ message }}</div>
			<div ng-show="spinner" class="spinner"></div>

			<p>
				Dit overzicht toont de nog goed te keuren bewakings- en opleidingsuren. Druk op
				<span style="color: green" class="glyphicon glyphicon glyphicon-thumbs-up"></span>
				om de regel goed te keuren, en op
				<span style="color: red" class="glyphicon glyphicon glyphicon-thumbs-down"></span>
				om de regel af te keuren.
			</p>
			<!-- ----------------------------------------------------------
				Table controls above table - Number of displayed rows, refresh data, global filter
				-->
			<div class="row">
				<!-- For big displays -->
				<div class="col-sm-9 hidden-sm hidden-xs">
					<form aria-label="filter" role="form" class="form-inline">
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
					<form aria-label="filter" role="form">
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
								<span class="input-group-addon" id="search"><span class="glyphicon glyphicon-search"></span></span>
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
				<table class="table table-striped table-bordered"><caption>Uren</caption>
					<thead>
						<!-- Table header -->
						<!-- Header -->
						<tr>
							<th scope="col">
								<a href="" ng-click="sortType = 'datum'">Datum</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'datum' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'datum' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							<th scope="col">
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
							<th scope="col" class="hidden-xs">
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
							<th scope="col">
								<a href="" ng-click="sortType = 'activiteit'">Activiteit</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'activiteit' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'activiteit' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							<th scope="col" class="hidden-xs hidden-sm">
								<a href="" ng-click="sortType = 'rol'">Rol</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'rol' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'rol' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							<th scope="col" class="hidden-xs hidden-sm hidden-md">
								Start
							</th>
							<th scope="col" class="hidden-xs hidden-sm hidden-md">
								Eind
							</th>
							<th scope="col">
								Aantal
							</th>
							<th scope="col">
								<a href="" ng-click="sortType = 'akkoord'">Akkoord</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'akkoord' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'akkoord' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
						</tr>

						<!--- Filters -->
						<tr ng-show="showFilter">
							<th scope="col"/>
							<th scope="col">
								<form  aria-label="filter" role="form" class="form-inline">
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
							<th scope="col" class="hidden-xs">
								<form aria-label="filter" role="form" class="form-inline">
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
							<th scope="col">
								<form aria-label="filter" role="form" class="form-inline">
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
												ng-model="search.activiteit"
												id="filterActiviteit"
												ng-options="activiteit for activiteit in urenActiviteiten">
											</select>
										</div>
									</div>
								</form>
							</th>
							<th scope="col" class="hidden-xs hidden-sm">
								<form aria-label="filter" role="form" class="form-inline">
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
												ng-model="search.rol"
												id="filterRol"
												ng-options="rol for rol in urenRollen">
											</select>
										</div>
									</div>
								</form>
							</th>
							<th scope="col" class="hidden-xs hidden-sm hidden-md"/>
							<th scope="col" class="hidden-xs hidden-sm hidden-md"/>
							<th scope="col"/>
							<th scope="col"/>
						</tr>
					</thead>

					<!-- Table body -->
					<tbody>
						<tr ng-repeat="uur in uren | orderBy:sortType:sortReverse | filter:search:strict | limitTo:itemsPerPage:startItem">
							<td>{{ uur.datum | date: "yyyy-MM-dd"}}</td>
							<td>{{ uur.voornaam }}</td>
							<td class="hidden-xs">{{ uur.achternaam }}</td>
							<td>{{ uur.activiteit }}</td>
							<td class="hidden-xs hidden-sm">{{ uur.rol }}</td>
							<td class="hidden-xs hidden-sm hidden-md">{{ uur.start | date: "HH:mm"}}</td>
							<td class="hidden-xs hidden-sm hidden-md">{{ uur.eind | date: "HH:mm"}}</td>
							<td>{{ uur.uren }}</td>
							<td ng-class="{'danger': uur.akkoord == '2'}" class="text-right" style="width:7em;">{{ uur.akkoord | akkoordFilter }}
								<button
									type="button"
									style="width:3em;"
									class="btn btn-xs btn-default"
									ng-show="uur.akkoord == 0"
									data-toggle="modal"
									data-target="#goedkeur"
									ng-click="edit(uren.indexOf(uur))">
									<span style="color: green" class="glyphicon glyphicon glyphicon-thumbs-up"></span>
								</button>&nbsp;
								<button
									type="button"
									style="width:3em;"
									class="btn btn-xs btn-default"
									ng-show="uur.akkoord == 0"
									data-toggle="modal"
									data-target="#afkeur"
									ng-click="edit(uren.indexOf(uur))">
									<span style="color: red" class="glyphicon  glyphicon-thumbs-down"></span>
								</button>
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
				<div class="col-sm-4">
					<button
						ng-show="opleidingBoeken"
						class="btn btn btn-default pull-right"
						onclick="location.href='opleidingsuren.php'">Opleidingsuren
					</button>
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
		Modal goedkeur confirmation
	-->
	<div id="goedkeur" class="modal" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button
						type="button"
						class="close"
						data-dismiss="modal">&times;
					</button>
					<h4 class="modal-title">Regel goedkeuren</h4>
				</div>
				<div class="modal-body">
					<p>Record voor {{ form.voornaam }} {{ form.achternaam }} bij {{ form.activiteit }} op {{ form.datum | date: "yyyy-MM-dd" }} goedkeuren?</p><br/>

					<button
						class="btn btn-success"
						data-dismiss="modal"
						ng-click="goedkeuren(form.index)">
						Goedkeuren
					</button>

					<button
						class="btn btn-default"
						data-dismiss="modal"
						ng-click="reset()">
						Annuleer
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- ------------------------------------------------------------------------------------------
		Modal afkeur confirmation
	-->
	<div id="afkeur" class="modal" role="dialog">
		<div class="modal-dialog">
		<form aria-label="Afkeuren/goedkeuren" class="form-horizontal" role="form" novalidate name="editForm">
			<div class="modal-content">
				<div class="modal-header">
					<button
						type="button"
						class="close"
						data-dismiss="modal">
						&times;
					</button>
					<h4 class="modal-title">Regel afkeuren</h4>
				</div>
				<div class="modal-body">
					<p>Record voor {{ form.voornaam }} {{ form.achternaam }} bij {{ form.activiteit }} op {{ form.datum | date: "yyyy-MM-dd" }} afkeuren?</p>
					<br/>
					<br/>

					<input
						type="hidden"
						ng-model="form.edit"
						value="{{ form.edit }}"/>

					<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
						<label
							class="control-label col-sm-12" style="text-align:left" for="reden">Reden</label>
						<div class="col-sm-12">
							<textarea
								id="reden"
								name="reden"
								class="form-control"
								ng-model="form.reden"
								errorText="Reden is verplicht"
								required>
							</textarea>
						</div>
					</div>

					<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-12" style="text-align:left" for="opmerking">Opmerking</label>
						<div class="col-sm-12">
							<textarea
								readonly
								id="opmerking"
								name="opmerking"
								class="form-control"
								ng-model="form.opmerking">
							</textarea>
						</div>
					</div>

					<button
						class="btn btn-danger"
						ng-disabled="editForm.$invalid"
						ng-class="{'btn-success': editForm.$valid}"
						ng-click="afkeuren(form.index)"
						data-dismiss="modal">
						Afkeuren
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
					Deze pagina toont alle goed te keuren uren van alle gebruikers. Door op de groene 'duim omhoog' knop
					te drukken worden de uren op de betreffende regel goedgekeurd. De rode 'duim omlaag' knop keurt de uren
					af. Afgekeurde uren moeten voorzien worden van een afkeur reden.<br/>
					<br/>

					<button
						class="btn btn-primary center-block"
						data-dismiss="modal">
						Sluiten
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
