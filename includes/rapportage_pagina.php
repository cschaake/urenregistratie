<?php
/**
 * Rapportage pagina
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
 * @since      File available since Release 1.0.8
 * @version    1.0.8
 */

?>
<div ng-app="myApp" ng-controller="gebuikersCtrl"> <!-- Angular container, within this element the myApp application is active -->
	<div ng-show="showRapportagePanel" id="rapportagePanel" class="panel panel-default">
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
								ng-show="showFilter" 
								class="btn btn-default" 
								ng-click="showFilter = !showFilter" 
								id="refreshData">
								Verberg filter <span class="glyphicon glyphicon-filter"></span>
							</button>
							
							<button 
								ng-hide="showFilter" 
								class="btn btn-default" 
								ng-click="showFilter = !showFilter" 
								id="refreshData">
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
								
								<span class="input-group-addon" id="search"><span class="glyphicon glyphicon-search"></span></span>
								<input type="search" ng-change="onSearch()" class="form-control" ng-model="search" aria-describedby="search" placeholder="Zoek..."/>
								
							</div>
						</div>
					</form>
					<div ng-show="search == '[object Object]'">
						<button class="btn btn-info btn-block" ng-click="resetSearch()"><span class="glyphicon glyphicon-search"></span> Reset filter</button><br/>
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
										ng-show="showFilter" 
										class="btn btn-default" 
										ng-click="showFilter = !showFilter" 
										id="refreshData">
										Verberg filter <span class="glyphicon glyphicon-filter"></span>
									</button>
									
									<button 
										ng-hide="showFilter" 
										class="btn btn-default" 
										ng-click="showFilter = !showFilter" 
										id="refreshData">
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
								<span 
									class="input-group-addon" 
									id="search">
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
						<tr>
							<th>
								<a href="" ng-click="sortType = 'username'">Gebruikersnaam</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'username' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'username' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							<th>
								<a href="" ng-click="sortType = 'voornaam'">Voornaam</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'voornaam' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'voornaam' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							<th>
								<a href="" ng-click="sortType = 'achternaam'">Achternaam</a>
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
								<a href="" ng-click="sortType = 'rol'">Certificaat</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'rol' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'rol' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							
							<th>
								<a href="" ng-click="sortType = 'gecertificeerd'">Gecertificeerd</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'gecertificeerd' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'gecertificeerd' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							
							<th>
								<a href="" ng-click="sortType = 'ingevoerd'">Ingevoerd</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'ingevoerd' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'ingevoerd' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							
							<th>
								<a href="" ng-click="sortType = 'goedgekeurd'">Goedgekeurd</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'goedgekeurd' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'goedgekeurd' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							
							<th>
								<a href="" ng-click="sortType = 'afgekeurd'">Afgekeurd</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'afgekeurd' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'afgekeurd' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							<th>
								<a href="" ng-click="sortType = 'nodig'">Nodig</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'nodig' && !sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span>
									</span>
									<span ng-show="sortType == 'nodig' && sortReverse">
										<span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span>
									</span>
								</a>
							</th>
							<th/>
						</tr>
						
						<!--- Filters -->
						<tr ng-show="showFilter">
							<th/>
							<th/>
							<th/>
							<th class="hidden-xs">
								<form role="form" class="form-inline">
									<div class="form-group" style="width:100%">
										<div 
											class="input-group" 
											style="width:100%">
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
												ng-options="a.rol as a.rol for a in rollen">
											</select>
										</div>
									</div>
								</form>
							</th>
							<th/>
							<th/>
							<th/>
							<th/>
							<th/>
							<th/>
						</tr>
					</thead>
					
					<!-- Table body -->
					<tbody>
						<tr ng-repeat="record in rapport | orderBy:sortType:sortReverse | filter:search:strict | limitTo:itemsPerPage:startItem">
							<td ng-class="{'success': record.goedgekeurd >= record.uren}">
								<a href="#" title="Laatste login {{ record.laatstelogin }}">
									{{ record.username }}
								</a>
							</td>
							<td ng-class="{'success': record.goedgekeurd >= record.uren}">{{ record.voornaam }}</td>
							<td ng-class="{'success': record.goedgekeurd >= record.uren}">{{ record.achternaam }}</td>
							<td ng-class="{'success': record.goedgekeurd >= record.uren}">
								<a href="#" title="Looptijd {{ record.looptijd }} maanden en {{ record.uren }} benodigd">
									{{ record.rol }}
								</a>
							</td>
							<td ng-class="{'success': record.goedgekeurd >= record.uren}">{{ record.gecertificeerd }}</td>
							<td ng-class="{'success': record.goedgekeurd >= record.uren}">{{ record.ingevoerd }}</td>
							<td ng-class="{'success': record.goedgekeurd >= record.uren}">{{ record.goedgekeurd }}</td>
							<td ng-class="{'success': record.goedgekeurd >= record.uren}">{{ record.afgekeurd }}</td>
							<td ng-class="{'success': record.goedgekeurd >= record.uren}">{{ record.nodig }}</td>
							<td ng-class="{'success': record.goedgekeurd >= record.uren}">
								<button 
									type="button" 
									style="width:3em;" 
									class="btn btn-xs btn-default" 
									ng-click="showDetail(record.username)">
									<span class="glyphicon glyphicon glyphicon glyphicon-search"></span>
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
					Toon {{ startItem + 1}} t/m {{ lastItemPage() }} van <span ng-show="totalItems != totalRecords">{{ totalItems }} gefilderde records. Totaal</span> {{ totalRecords }} records.
				</div>
				
			</div>
			<!-- Pagination - Large devices -->
			<div class="row">
				<div class="col-sm-12 hidden-sm hidden-xs text-right">
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
			
			<!-- Small devices -->
			<!-- Number of records displayed - Small devices -->
			<div class="row">
				
				<div class="col-sm-12 hidden-lg hidden-md text-center">
					Toon {{ startItem + 1 }} t/m {{ lastItemPage() }} van <span ng-show="totalItems != totalRecords">{{ totalItems }} gefilderde records. Totaal</span> {{ totalRecords }} records.
				</div>
			</div>
			<!-- Pagination - Small devices -->
			<div class="row">
				<div class="col-sm-12 hidden-lg hidden-md text-center">
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
	
	<div ng-show="showDetailPanel" id="detailPanel" class="panel panel-default">
		<div class="panel-heading">
			Details van {{ details[0].username }}
		</div>
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
								ng-click="refreshDetail(details[0].username)" 
								id="refreshData">
								Ververs tabel <span class="glyphicon glyphicon-refresh"></span>
								</button>
						</div>
						
						<div class="form-group"><!-- Show filters -->
							<button 
								ng-show="showFilter" 
								class="btn btn-default" 
								ng-click="showFilter = !showFilter" 
								id="refreshData">
								Verberg filter <span class="glyphicon glyphicon-filter"></span>
							</button>
							
							<button 
								ng-hide="showFilter" 
								class="btn btn-default" 
								ng-click="showFilter = !showFilter" 
								id="refreshData">
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
								
								<span class="input-group-addon" id="search"><span class="glyphicon glyphicon-search"></span></span>
								<input type="search" ng-change="onSearch()" class="form-control" ng-model="search" aria-describedby="search" placeholder="Zoek..."/>
								
							</div>
						</div>
					</form>
					<div ng-show="search == '[object Object]'">
						<button class="btn btn-info btn-block" ng-click="resetSearch()"><span class="glyphicon glyphicon-search"></span> Reset filter</button><br/>
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
										ng-show="showFilter" 
										class="btn btn-default" 
										ng-click="showFilter = !showFilter" 
										id="refreshData">
										Verberg filter <span class="glyphicon glyphicon-filter"></span>
									</button>
									
									<button 
										ng-hide="showFilter" 
										class="btn btn-default" 
										ng-click="showFilter = !showFilter" 
										id="refreshData">
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
								<span 
									class="input-group-addon" 
									id="search">
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
						<tr>
							<th>
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
							<th class="hidden-xs">
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
							<th>
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
							<th class="hidden-xs hidden-sm hidden-md">
								Start
							</th>
							<th class="hidden-xs hidden-sm hidden-md">
								Eind
							</th>
							<th>
								Aantal
							</th>
							<th>
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
												ng-model="search.activiteit" 
												id="filterActiviteit" 
												ng-options="a.activiteit as a.activiteit group by a.groep for a in activiteiten">
											</select>
										</div>
									</div>
								</form>
							</th>
							<th class="hidden-xs">
								<form role="form" class="form-inline">
									<div class="form-group" style="width:100%">
										<div 
											class="input-group" 
											style="width:100%">
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
												ng-options="a.rol as a.rol for a in rollen">
											</select>
										</div>
									</div>
								</form>
							</th>
							<th/>
							<th class="hidden-xs hidden-sm hidden-md"/>
							<th class="hidden-xs hidden-sm hidden-md"/>
							<th/>
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
											<select ng-change="onSearch()" 
												class="form-control" 
												ng-model="search.akkoord" 
												id="filterAkkoord">
												<option value="0"></option>
												<option value="1">Ja</option>
												<option value="2">Nee</option>
											</select>
										</div>
									</div>
								</form>
							</th>
						</tr>
					</thead>
					
					<!-- Table body -->
					<tbody>
						<tr ng-repeat="uur in details | orderBy:sortType:sortReverse | filter:search:strict | limitTo:itemsPerPage:startItemDetails">
							<td ng-class="{'danger': uur.akkoord == '2'}">{{ uur.activiteit }}</td>
							<td class="hidden-xs" ng-class="{'danger': uur.akkoord == '2'}">{{ uur.rol }}</td>
							<td ng-class="{'danger': uur.akkoord == '2'}">{{ uur.datum | date: "yyyy-MM-dd"}}</td>
							<td class="hidden-xs hidden-sm hidden-md" ng-class="{'danger': uur.akkoord == '2'}">{{ uur.start }}</td>
							<td class="hidden-xs hidden-sm hidden-md" ng-class="{'danger': uur.akkoord == '2'}">{{ uur.eind }}</td>
							<td ng-class="{'danger': uur.akkoord == '2'}">{{ uur.uren }}</td>
							<td ng-class="{'danger': uur.akkoord == '2'}">{{ uur.akkoord | akkoordFilter }}</td>
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
					Toon {{ startItemDetails + 1}} t/m {{ lastItemPageDetails() }} van <span ng-show="totalItemsDetails != totalRecordsDetails">{{ totalItemsDetails }} gefilderde records. Totaal</span> {{ totalRecordsDetails }} records.
				</div>
				<div class="col-sm-6 hidden-sm hidden-xs text-right">
					<button 
						class="btn btn-default" 
						ng-click="showRapport()">
						Terug <span class="glyphicon glyphicon-triangle-left"></span>
					</button>
					<br/>
				</div>
			</div>
			<!-- Pagination - Large devices -->
			<div class="row">
				<div class="col-sm-12 hidden-sm hidden-xs text-right">
					<ul class="pagination">
						<li ng-hide="startItemDetails <= 0">
							<a href="" ng-click="startItemDetails = 0">
								<span class="glyphicon glyphicon-fast-backward"></span>
							</a>
						</li>
						<li ng-hide="startItemDetails <= 0">
							<a href="" ng-click="startItemDetails = startItemDetails - itemsPerPage">
								<span class="glyphicon glyphicon-backward"></span>
							</a>
						</li>
						<li ng-class="{active: n == currentPageDetails()}" ng-repeat="n in pagesListDetails()">
							<a href="" ng-click="$parent.startItemDetails = (n - 1) * $parent.itemsPerPage">{{ n }}</a>
						</li>
						<li ng-hide="currentPageDetails() >= numberOfPagesDetails()">
							<a href="" ng-click="startItemDetails = startItemDetails + itemsPerPage">
								<span class="glyphicon glyphicon-forward"></span>
							</a>
						</li>
						<li ng-hide="currentPageDetails() >= numberOfPagesDetails()">
							<a href="" ng-click="startItemDetails = (numberOfPagesDetails() - 1) * itemsPerPage">
								<span class="glyphicon glyphicon-fast-forward"></span>
							</a>
						</li>
					</ul>
				</div>
			</div>
			
			<!-- Small devices -->
			<!-- Number of records displayed - Small devices -->
			<div class="row">
				<div class="col-sm-12 hidden-lg hidden-md text-right">
					<button 
						class="btn btn-default" 
						ng-click="showRapport()">
						Terug <span class="glyphicon glyphicon-triangle-left"></span>
					</button>
					<br/>
				</div>
				<div class="col-sm-12 hidden-lg hidden-md text-center">
					Toon {{ startItemDetails + 1 }} t/m {{ lastItemPageDetails() }} van <span ng-show="totalItemsDetails != totalRecordsDetails">{{ totalItemsDetails }} gefilderde records. Totaal</span> {{ totalRecordsDetails }} records.
				</div>
			</div>
			<!-- Pagination - Small devices -->
			<div class="row">
				<div class="col-sm-12 hidden-lg hidden-md text-center">
					<ul class="pagination">
						<li ng-hide="startItemDetails <= 0">
							<a href="" ng-click="startItemDetails = 0">
								<span class="glyphicon glyphicon-fast-backward"></span>
							</a>
						</li>
						<li ng-hide="startItemDetails <= 0">
							<a href="" ng-click="startItemDetails = startItemDetails - itemsPerPage">
								<span class="glyphicon glyphicon-backward"></span>
							</a>
						</li>
						<li ng-class="{active: n == currentPageDetails()}" ng-repeat="n in pagesListDetails()">
							<a href="" ng-click="$parent.startItemDetails = (n - 1) * $parent.itemsPerPage">{{ n }}</a>
						</li>
						<li ng-hide="currentPageDetails() >= numberOfPagesDetails()">
							<a href="" ng-click="startItemDetails = startItemDetails + itemsPerPage">
								<span class="glyphicon glyphicon-forward"></span>
							</a>
						</li>
						<li ng-hide="currentPageDetails() >= numberOfPagesDetails()">
							<a href="" ng-click="startItemDetails = (numberOfPagesDetails() - 1) * itemsPerPage">
								<span class="glyphicon glyphicon-fast-forward"></span>
							</a>
						</li>
					</ul>
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
					Rapportage voor alle gebruikers. Druk op het vergrootglas om de details voor de gebruiker in te zien.
					<br/>
					<br/>
					<button class="btn btn-primary center-block" data-dismiss="modal">Sluiten</button>
				</div>
			</div>	
		</div>
	</div>
</div>