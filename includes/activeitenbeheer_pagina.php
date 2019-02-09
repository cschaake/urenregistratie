<?php
/**
 * Template activiteitenPanel | includes/activiteitenbeheer_pagina.php
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
 * @since      File available since Release 1.2.0
 * @version    1.2.0
 */

// Calculate date range for input (P3M means 3 months)
$startdate = new DateTime();
$startdate->sub(new DateInterval(INVOER_VOOR_HUIDIGE_DATUM));
$startdate = $startdate->format('Y-m-d');
$enddate = new DateTime();
$enddate->add(new DateInterval(INVOER_NA_HUIDIGE_DATUM));
$enddate = $enddate->format('Y-m-d');

?>
<div ng-app="myApp" ng-controller="gebuikersCtrl">
	<!-- Angular container, within this element the myApp application is active -->
	<div id="activiteitenPanel" class="panel panel-default">
		<div class="panel-body">

			<div ng-show="message" class="alert alert-danger">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>{{
				message }}
			</div>
			<div ng-show="spinner" class="spinner"></div>

			<!-- ----------------------------------------------------------
				Table controls above table - Number of displayed rows, refresh data, global filter
				-->
			<div class="row">
				<!-- For big displays -->
				<div class="col-sm-9 hidden-sm hidden-xs">
					<form role="form" class="form-inline">
						<div class="form-group">
							<!-- Number of displayed rows -->
							<select class="form-control" ng-model="itemsPerPage"
								id="numberOfRows"
								ng-options="'Toon ' + option + ' regels' for option in tableListOptions">
							</select>
						</div>

						<div class="form-group">
							<!-- Refresh data -->
							<button class="btn btn-default" ng-click="refresh()"
								id="refreshData">
								Ververs tabel <span class="glyphicon glyphicon-refresh"></span>
							</button>
						</div>

						<div class="form-group">
							<!-- Show filters -->
							<button ng-show="showFilter" class="btn btn-default"
								ng-click="showFilter = !showFilter" id="refreshData">
								Verberg filter <span class="glyphicon glyphicon-filter"></span>
							</button>

							<button ng-hide="showFilter" class="btn btn-default"
								ng-click="showFilter = !showFilter" id="refreshData">
								Toon filter <span class="glyphicon glyphicon-filter"></span>
							</button>
						</div>
					</form>
				</div>
				<!-- Global search -->
				<div class="col-sm-3 hidden-sm hidden-xs text-right">
					<form rol="form" ng-hide="search == '[object Object]'">
						<div class="form-group">
							<div class="input-group">

								<span class="input-group-addon" id="search"><span
									class="glyphicon glyphicon-search"></span></span> <input
									type="search" ng-change="onSearch()" class="form-control"
									ng-model="search" aria-describedby="search"
									placeholder="Zoek..." />

							</div>
						</div>
					</form>
					<div ng-show="search == '[object Object]'">
						<button class="btn btn-info btn-block" ng-click="resetSearch()">
							<span class="glyphicon glyphicon-search"></span> Reset filter
						</button>
						<br />
					</div>
				</div>

				<!-- for small displays -->
				<div class="col-sm-12 hidden-lg hidden-md">
					<form role="form">
						<div class="form-group">
							<div class="input-group">
								<!-- Number of displayed tables and refresh data -->
								<select class="form-control" ng-model="itemsPerPage"
									id="numberOfRows"
									ng-options="'Toon ' + option + ' regels' for option in tableListOptions">
								</select> <span class="input-group-btn">
									<button class="btn btn-default" ng-click="refresh()"
										id="refreshData">
										Ververs tabel <span class="glyphicon glyphicon-refresh"></span>
									</button>
								</span>

								<div class="input-group-btn">
									<!-- Show filters -->
									<button ng-show="showFilter" class="btn btn-default"
										ng-click="showFilter = !showFilter" id="refreshData">
										Verberg filter <span class="glyphicon glyphicon-filter"></span>
									</button>

									<button ng-hide="showFilter" class="btn btn-default"
										ng-click="showFilter = !showFilter" id="refreshData">
										Toon filter <span class="glyphicon glyphicon-filter"></span>
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
								<span class="input-group-addon" id="search"> <span
									class="glyphicon glyphicon-search"></span>
								</span> <input type="search" class="form-control"
									ng-model="search" aria-describedby="search"
									placeholder="Zoek..." />
							</div>
						</div>
					</form>
					<div ng-show="search == '[object Object]'">
						<button class="btn btn-info btn-block" ng-click="search = ''">
							<span class="glyphicon glyphicon-search"></span> Reset filter
						</button>
						<br />
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
							<th><a href="" ng-click="sortType = 'datum'">Datum</a> <a href=""
								ng-click="sortReverse = !sortReverse"> <span
									ng-show="sortType == 'datum' && !sortReverse"> <span
										class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
								</span> <span ng-show="sortType == 'datum' && sortReverse"> <span
										class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
								</span>
							</a></th>
							<th><a href="" ng-click="sortType = 'activiteit'">Activiteit</a>
								<a href="" ng-click="sortReverse = !sortReverse"> <span
									ng-show="sortType == 'activiteit' && !sortReverse"> <span
										class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
								</span> <span ng-show="sortType == 'activiteit' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
								</span>
							</a></th>
							<th class="hidden-xs"><a href="" ng-click="sortType = 'groep'">Groep</a>
								<a href="" ng-click="sortReverse = !sortReverse"> <span
									ng-show="sortType == 'groep' && !sortReverse"> <span
										class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
								</span> <span ng-show="sortType == 'groep' && sortReverse"> <span
										class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
								</span>
							</a></th>
							<th class="hidden-xs hidden-sm hidden-md">Begintijd</th>
							<th class="hidden-xs hidden-sm hidden-md">Eindtijd</th>
							<th />
						</tr>

						<!--- Filters -->
						<tr ng-show="showFilter">
							<th />
							<th>
								<form role="form" class="form-inline">
									<div class="form-group" style="width: 100%">
										<div class="input-group" style="width: 100%">
											<span class="input-group-addon hidden-xs" id="search"
												style="width: 2em"> <span class="glyphicon glyphicon-filter"></span>
											</span> <select ng-change="onSearch()" class="form-control"
												ng-model="search.activiteit" id="filterActiviteit"
												ng-options="a.activiteit as a.activiteit group by a.groep for a in activiteiten">
											</select>
										</div>
									</div>
								</form>
							</th>
							<th class="hidden-xs">
								<form role="form" class="form-inline">
									<div class="form-group" style="width: 100%">
										<div class="input-group" style="width: 100%">
											<span class="input-group-addon hidden-xs" id="search"
												style="width: 2em"> <span class="glyphicon glyphicon-filter"></span>
											</span> <select ng-change="onSearch()" class="form-control"
												ng-model="search.groep" id="filterGroep"
												ng-options="a.groep as a.groep for a in activiteitenGroepen">
											</select>
										</div>
									</div>
								</form>
							</th>
							<th class="hidden-xs hidden-sm hidden-md" />
							<th class="hidden-xs hidden-sm hidden-md" />
							<th />
						</tr>
					</thead>

					<!-- Table body -->
					<tbody>
						<tr
							ng-repeat="activiteit in activiteiten | orderBy:sortType:sortReverse | filter:search:strict | limitTo:itemsPerPage:startItem">
							<td>{{ activiteit.datum | date: "yyyy-MM-dd"}}</td>
							<td>{{ activiteit.activiteit }}</td>
							<td class="hidden-xs">{{ activiteit.groep }}</td>
							<td class="hidden-xs hidden-sm hidden-md">{{ activiteit.begintijd }}</td>
							<td class="hidden-xs hidden-sm hidden-md">{{ activiteit.eindtijd }}</td>
							<td class="text-right"
								style="width: 7em;">
								<button type="button" style="width: 3em;"
									class="btn btn-xs btn-default" ng-hide="uur.akkoord == '1'"
									data-toggle="modal" data-target="#deleterecord"
									ng-click="edit(activiteiten.indexOf(activiteit))">
									<span class="glyphicon glyphicon glyphicon-trash"></span>
								</button>&nbsp;
								<button type="button" style="width: 3em;"
									class="btn btn-xs btn-default" ng-hide="uur.akkoord == '1'"
									data-toggle="modal" data-target="#editrecord"
									ng-click="edit(activiteiten.indexOf(activiteit))">
									<span class="glyphicon glyphicon glyphicon glyphicon-pencil"></span>
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
				<div class="col-sm-6 hidden-sm hidden-xs">
					Toon {{ startItem + 1}} t/m {{ lastItemPage() }} van <span
						ng-show="totalItems != totalRecords">{{ totalItems }} gefilderde
						records. Totaal</span> {{ totalRecords }} records.
				</div>
				<div class="col-sm-6 hidden-sm hidden-xs text-right">
					<button class="btn btn-default" data-toggle="modal"
						data-target="#editrecord" ng-click="new()">
						Nieuw <span class="glyphicon glyphicon-log-in"></span>
					</button>
					<br />
				</div>
			</div>
			<!-- Pagination - Large devices -->
			<div class="row">
				<div class="col-sm-12 hidden-sm hidden-xs text-right">
					<ul class="pagination">
						<li ng-hide="startItem <= 0"><a href="" ng-click="startItem = 0">
								<span class="glyphicon glyphicon-fast-backward"></span>
						</a></li>
						<li ng-hide="startItem <= 0"><a href=""
							ng-click="startItem = startItem - itemsPerPage"> <span
								class="glyphicon glyphicon-backward"></span>
						</a></li>
						<li ng-class="{active: n == currentPage()}"
							ng-repeat="n in pagesList()"><a href=""
							ng-click="$parent.startItem = (n - 1) * $parent.itemsPerPage">{{
								n }}</a></li>
						<li ng-hide="currentPage() >= numberOfPages()"><a href=""
							ng-click="startItem = startItem + itemsPerPage"> <span
								class="glyphicon glyphicon-forward"></span>
						</a></li>
						<li ng-hide="currentPage() >= numberOfPages()"><a href=""
							ng-click="startItem = (numberOfPages() - 1) * itemsPerPage"> <span
								class="glyphicon glyphicon-fast-forward"></span>
						</a></li>
					</ul>
				</div>
			</div>

			<!-- Small devices -->
			<!-- Number of records displayed - Small devices -->
			<div class="row">
				<div class="col-sm-12 hidden-lg hidden-md text-right">
					<button class="btn btn-default" data-toggle="modal"
						data-target="#editrecord" ng-click="new()">
						Nieuw <span class="glyphicon glyphicon-log-in"></span>
					</button>
					<br />
				</div>
				<div class="col-sm-12 hidden-lg hidden-md text-center">
					Toon {{ startItem + 1 }} t/m {{ lastItemPage() }} van <span
						ng-show="totalItems != totalRecords">{{ totalItems }} gefilderde
						records. Totaal</span> {{ totalRecords }} records.
				</div>
			</div>
			<!-- Pagination - Small devices -->
			<div class="row">
				<div class="col-sm-12 hidden-lg hidden-md text-center">
					<ul class="pagination">
						<li ng-hide="startItem <= 0"><a href="" ng-click="startItem = 0">
								<span class="glyphicon glyphicon-fast-backward"></span>
						</a></li>
						<li ng-hide="startItem <= 0"><a href=""
							ng-click="startItem = startItem - itemsPerPage"> <span
								class="glyphicon glyphicon-backward"></span>
						</a></li>
						<li ng-class="{active: n == currentPage()}"
							ng-repeat="n in pagesList()"><a href=""
							ng-click="$parent.startItem = (n - 1) * $parent.itemsPerPage">{{
								n }}</a></li>
						<li ng-hide="currentPage() >= numberOfPages()"><a href=""
							ng-click="startItem = startItem + itemsPerPage"> <span
								class="glyphicon glyphicon-forward"></span>
						</a></li>
						<li ng-hide="currentPage() >= numberOfPages()"><a href=""
							ng-click="startItem = (numberOfPages() - 1) * itemsPerPage"> <span
								class="glyphicon glyphicon-fast-forward"></span>
						</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<?php
	   /**
	    * Include modals
	    */
	   include_once 'modals/activiteiten_editrecord_modal.php';
	   include_once 'modals/activiteiten_deleteconfirmation_modl.php';
	   include_once 'modals/activiteiten_help_modal.php';
	?>

</div>