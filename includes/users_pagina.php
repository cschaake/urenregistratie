<?php
/**
 * Template userPanel | includes\users_pagina.php
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
<div ng-app="myApp" ng-controller="usersCtrl">
	<div id="userPanel" class="panel panel-default">
		<div class="panel-body">
			<div ng-show="spinner" class="spinner"></div>
			<!-- ----------------------------------------------------------
				Table controls above table - Number of displayed rows, refresh data, global filter
				-->
			<div class="row">
				<!-- For big displays -->
				<div class="col-sm-9 hidden-sm hidden-xs">
					<form aria-label="filter" role="form" class="form-inline">
						<div class="form-group"><!-- Number of displayed rows -->
							<select class="form-control" ng-model="itemsPerPage" id="numberOfRows" ng-options="'Show ' + option + ' rows' for option in tableListOptions"></select>
						</div>
						<div class="form-group"><!-- Refresh data -->
							<button class="btn btn-default" ng-click="refresh()" id="refreshData">Refresh tabel <span class="glyphicon glyphicon-refresh"></span></button>
						</div>

					</form>
				</div>
				<!-- Global search -->
				<div class="col-sm-3 hidden-sm hidden-xs text-right">
					<form rol="form" ng-hide="search == '[object Object]'">
						<div class="form-group">
							<div class="input-group">

								<span class="input-group-addon" id="search"><span class="glyphicon glyphicon-search"></span></span>
								<input type="search" ng-change="onSearch()" class="form-control" ng-model="search" aria-describedby="search" placeholder="Search..."/>

							</div>
						</div>
					</form>
					<div ng-show="search == '[object Object]'">
						<button class="btn btn-info btn-block" ng-click="search = ''"><span class="glyphicon glyphicon-search"></span> Reset filter</button><br/>
					</div>
				</div>

				<!-- for small displays -->
				<div class="col-sm-12 hidden-lg hidden-md">
					<form aria-label="filter" role="form">
						<div class="form-group">
							<div class="input-group"><!-- Number of displayed tables and refresh data -->
								<select class="form-control" ng-model="itemsPerPage" id="numberOfRows" ng-options="'Show ' + option + ' rows' for option in tableListOptions"></select>
								<span class="input-group-btn">
									<button class="btn btn-default" ng-click="refresh()" id="refreshData">Refresh tabel <span class="glyphicon glyphicon-refresh"></span></button>
								</span>

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
								<input type="search" class="form-control" ng-model="search" aria-describedby="search" placeholder="Search..."/>

							</div>
						</div>
					</form>
					<div ng-show="search == '[object Object]'">
						<button class="btn btn-info btn-block" ng-click="search = ''"><span class="glyphicon glyphicon-search"></span> Reset filter</button><br/>
					</div>
				</div>

			</div>


			<!-- ----------------------------------------------------------
				Table
				-->
			<div class="table-responsive">
				<!-- Table list -->

				<table class="table table-striped table-bordered"><caption>Users</caption>
					<thead>
						<!-- Table header -->
						<!-- Header -->
						<tr>
							<th scope="col">
								<a href="" ng-click="sortType = 'username'">Username</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'username' && !sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span></span>
									<span ng-show="sortType == 'username' && sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span></span>
								</a>
							</th>
							<th scope="col" class="hidden-xs">
								<a href="" ng-click="sortType = 'firstname'">First name</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'firstname' && !sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span></span>
									<span ng-show="sortType == 'firstname' && sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span></span>
								</a>
							</th>
							<th scope="col">
								<a href="" ng-click="sortType = 'lastname'">Last name</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'lastname' && !sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span></span>
									<span ng-show="sortType == 'lastname' && sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span></span>
								</a>
							</th>
							<th scope="col">
								<a href="" ng-click="sortType = 'email'">Email</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'email' && !sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span></span>
									<span ng-show="sortType == 'email' && sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span></span>
								</a>
							</th>
							<th scope="col" class="hidden-xs hidden-sm hidden-md">
								<a href="" ng-click="sortType = 'failedLogin'">Failed login</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'failedLogin' && !sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span></span>
									<span ng-show="sortType == 'failedLogin' && sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span></span>
								</a>
							</th>
							<th scope="col" class="hidden-xs hidden-sm hidden-md">
								<a href="" ng-click="sortType = 'lastLogin'">Last login</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'lastLogin' && !sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span></span>
									<span ng-show="sortType == 'lastLogin' && sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span></span>
								</a>
							</th>
							<th scope="col" class="hidden-xs hidden-sm hidden-md">
								<a href="" ng-click="sortType = 'status'">Status</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'status' && !sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span></span>
									<span ng-show="sortType == 'status' && sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span></span>
								</a>
							</th>
							<th scope="col">
								<a href="" ng-click="sortType = 'created'">Created</a>
								<a href="" ng-click="sortReverse = !sortReverse">
									<span ng-show="sortType == 'created' && !sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet-alt pull-right"></span></span>
									<span ng-show="sortType == 'created' && sortReverse"><span class="glyphicon glyphicon-sort-by-alphabet pull-right"></span></span>
								</a>
							</th>
							<th scope="col">Groups</th>
							<th scope="col"/>
						</tr>


					</thead>

					<!-- Table body -->
					<tbody>
						<tr ng-repeat="user in users | orderBy:sortType:sortReverse | filter:search:strict | limitTo:itemsPerPage:startItem">
							<td>{{ user.username }}</td>
							<td>{{ user.firstname }}</td>
							<td>{{ user.lastname }}</td>
							<td>{{ user.email }}</td>
							<td>{{ user.failedLogin }}</td>
							<td>{{ user.lastLogin }}</td>
							<td>{{ user.status | filterStatus }}</td>
							<td>{{ user.created }}</td>
							<td>
								<a ng-show="user.groups.indexOf('admin') > -1" href="" data-toggle="tooltip" title="User has Admin role"><span class="label label-danger">A</span></a>
								<a ng-show="user.groups.indexOf('super') > -1" href="" data-toggle="tooltip" title="User has Super role"><span class="label label-info">S</span></a>
							</td>
							<td>
								<a href="" data-toggle="modal" data-target="#deleterecord" ng-click="edit(users.indexOf(user))"><span class="glyphicon glyphicon glyphicon-trash"></span></a>&nbsp;
								<a href="" data-toggle="modal" data-target="#editrecord" ng-click="edit(users.indexOf(user))"><span class="glyphicon glyphicon glyphicon glyphicon-pencil"></span></a>
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
					Show rows {{ startItem + 1 }} to {{ lastItemPage() }} of <span ng-show="totalItems != totalRecords">{{ totalItems }} filtered records. Total</span> {{ totalRecords }} records.
				</div>
				<div class="col-sm-6 hidden-sm hidden-xs text-right">
					<button class="btn btn-default" data-toggle="modal" data-target="#editrecord" ng-click="new()">New <span class="glyphicon glyphicon-log-in"></span></button><br/>
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
					<button class="btn btn-default" data-toggle="modal" data-target="#editrecord" ng-click="new()">New <span class="glyphicon glyphicon-log-in"></span></button><br/>
				</div>
				<div class="col-sm-12 hidden-lg hidden-md text-center">
					Show rows {{ startItem + 1 }} to {{ lastItemPage() }} of <span ng-show="totalItems != totalRecords">{{ totalItems }} filtered records. Total</span> {{ totalRecords }} records.
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
			<form aria-label="User wijzigen/toevoegen" class="form-horizontal" role="form" novalidate name="editForm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 ng-show="form.edit" class="modal-title">Regel wijzigen</h4>
						<h4 ng-hide="form.edit" class="modal-title">Nieuwe regel toevoegen</h4>
					</div>

					<!-- Form -->
					<div class="modal-body">

					<div ng-show="message" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">�</a>{{ message }}</div>

						<input type="hidden" ng-model="form.edit" value="{{ form.edit }}"></input><br/> <!-- Hidden field to set the edit variable -->

						<div ng-show="form.edit" class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="username">Username</label>
							<div class="col-sm-10">

								<input id="username" name="username" type="text" ng-model="form.username" errorText="Username must be at least 5 characters long." ng-minlength="5" class="form-control" readonly/>
							</div>
						</div>
						<div ng-hide="form.edit" class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="username">Username</label>
							<div class="col-sm-10">
								<input  id="username" name="username" type="text" ng-model="form.username" errorText="Username must be at least 5 characters long." ng-minlength="5" class="form-control" required/>

							</div>
						</div>

						<div ng-if="form.edit != true" class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="password1">Password</label>
							<div class="col-sm-10">
								<div class="input-group">
									<input
										id="password1"
										name="password1"
										type="{{ showPassword1 ? 'text' : 'password' }}"
										ng-model="form.password1"
										errorText="Valid password is required"
										class="form-control"
										ng-required="true"
										ng-pattern="/(?=^.{8,30}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/"/>
									<span class="input-group-btn">
										<button  class="btn btn-default" type="button" ng-click="showPassword1 = !showPassword1">
											<span ng-hide="showPassword1" class="glyphicon glyphicon-eye-open"></span>
											<span ng-show="showPassword1" class="glyphicon glyphicon-eye-close"></span>
										</button>
									</span>
								</div>
							</div>
						</div>

						<div ng-if="form.edit != true" class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="password2">Password (again)</label>
							<div class="col-sm-10">
								<div class="input-group">
									<input
										id="password2"
										name="password2"
										type="{{ showPassword2 ? 'text' : 'password' }}"
										ng-model="form.password2"
										errorText="Passwords do not match"
										class="form-control"
										ng-minlength="8"
										required/>
									<span class="input-group-btn">
										<button  class="btn btn-default" type="button" ng-click="showPassword2 = !showPassword2">
											<span ng-hide="showPassword2" class="glyphicon glyphicon-eye-open"></span>
											<span ng-show="showPassword2" class="glyphicon glyphicon-eye-close"></span>
										</button>
									</span>
								</div>
							</div>
						</div>

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="firstname">First&nbsp;Name</label>
							<div class="col-sm-10">
								<input id="firstname" name="firstname" type="text" ng-model="form.firstname" errorText="First Name is required" class="form-control" required/>
							</div>
						</div>

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="lastname">Last&nbsp;Name</label>
							<div class="col-sm-10">
								<input id="lastname" name="lastname" type="text" ng-model="form.lastname" errorText="Last Name is required" class="form-control" required/>
							</div>
						</div>

						<div class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="email">Email</label>
							<div class="col-sm-10">
								<input id="email" name="email" type="email" ng-model="form.email" errorText="A valid email adres is required" class="form-control" required/>
							</div>
						</div>

						<div ng-show="form.edit" class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="status">Status</label>
							<div class="col-sm-10">
								<select id="status" name="status" class="form-control" ng-model="form.status" ng-options="status.id as status.name for status in statusses"></select>
							</div>
						</div>

						<div  ng-show="form.edit" class="form-group has-feedback" show-errors="{ showSuccess: true }">
							<label class="control-label col-sm-2" for="groups">Groups</label>
							<div class="col-sm-10">
								<select multiple id="groups" name="groups" class="form-control" ng-model="form.groups" ng-options="group for group in groups"></select>
								<br/>
								<div id="description-select" class="alert alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">�</a>Use <kbd>ctrl</kbd> to select multiple groups</div>
							</div>
						</div>

						<button class="btn btn-default" ng-disabled="editForm.$invalid" ng-class="{'btn-success': editForm.$valid}" ng-click="insert(form.index)">Update</button>
						<button class="btn btn-default" ng-click="reset()">Reset</button>
						<button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Cancel</button>
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
					<p>Record voor {{ form.username }} wordt verwijderd.</p><br/>

					<button class="btn btn-danger" data-dismiss="modal" ng-click="delete(form.index)">Delete</button>
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
					Deze pagina toont alle gebruikers van de urenregistratie applicatie. Gebruikers kunnen nog geen
					uren boeken of goedkeuren.<br/>
					<br/>
					Het email adres wordt gebruikt om wachtwoorden te resetten etc.<br/>
					<br/>
					Er zijn 3 verschillende extra rollen gedefinieerd voor gebruikers:
					<ul>
						<li>admin - Administrator van de applicatie. Deze kan instellingen in de applicatie wijzigen</li>
						<li>super - Super gebruiker. Deze kan boekers en goedkeurders wijzigen</li>
						<li>custom - Is nog niet in gebruik</li>
					</ul>
					Normale gebruikers hebben geen groepen.<br/>
					<br/>


					<button class="btn btn-primary center-block" data-dismiss="modal">Sluiten</button>
				</div>
			</div>
		</div>
	</div>
</div>
