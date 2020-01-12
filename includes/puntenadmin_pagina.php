<?php
/**
 * Template puntenadmin | includes/puntenadmin_pagina.php
 *
 * Pagina voor Urenregistatie punten admin
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
 * @since      File available since Release 1.2.2
 * @version    1.2.2
 */

?>
<div ng-app="myApp" ng-controller="puntenadminCtrl"> <!-- Angular container, within this element the myApp application is active -->
    <div id="puntenadminPanel" class="panel panel-default">
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
            
            <!--  Punten waardes tabel -->

            <div id="puntenwaardesPanel" class="panel panel-default">
                <div class="panel-heading">
                    Punten waardes
                </div>
                <div class="panel-body">
                    <!-- Puntenwaaardes tabel -->

                    <!-- ----------------------------------------------------------
                        Table
                        -->
                    <div class="table-responsive">
                        <!-- Table list -->
                        <table class="table table-striped table-bordered">
                        	<caption>Punten waardes</caption>
                            <thead>
                                <!-- Table header -->
                                <!-- Header -->
                                <tr>
                                    <th scope="row">
                                        Datum vanaf
                                    </th>
                                    <th scope="row">
                                        Waarde
                                    </th>
                                    <th scope="row"/>
                                </tr>

                            </thead>

                            <!-- Table body -->
                            <tbody>
                                <tr ng-repeat="puntenwaarde in puntenwaardes">
                                    <td>{{ puntenwaarde.datumVanaf }}</td>
                                    <td>€ {{ puntenwaarde.waarde | number : 2 }}</td>
                                    <td ng-class="{'danger': uur.akkoord == '2'}" class="text-right" style="width:7em;">
                                        <button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#deletepuntenwaarde" ng-click="editpuntenwaarde(puntenwaardes.indexOf(puntenwaarde))"><span class="glyphicon glyphicon glyphicon-trash"></span></button>&nbsp;
                                        <button type="button" style="width:3em;" class="btn btn-xs btn-default" data-toggle="modal" data-target="#editpuntenwaarde" ng-click="editpuntenwaarde(puntenwaardes.indexOf(puntenwaarde))"><span class="glyphicon glyphicon glyphicon glyphicon-pencil"></span></button>
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
			<div id="puntenwaardesPanel" class="panel panel-default">
                <div class="panel-heading">
                    Acties
                </div>
                <div class="panel-body">
                	<!-- -----------------------------------------------------------------------------------
                        Bereken buttons
                        -->
                    <div class="row">
                        <div class="col-sm-12 text-left">
                            <button class="btn btn-default" data-toggle="modal" data-target="#herberekenpunten">Herbereken punten <span class="glyphicon glyphicon-grain"></span></button><br/>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>

    <!-- ------------------------------------------------------------------------------------------
		Modal herberekenpunten
	-->
	<div id="herberekenpunten" class="modal" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Herbereken punten</h4>
				</div>
				
				<div class="modal-body">
					<div ng-show="messageGroepen" class="alert alert-danger">{{ messageGroepen }}</div>
					<p>Herberekenen van punten van afgelopen 12 maanden. Voor alle goedgekeurede uren wordt gecontroleerd of er punten zijn toegewezen. Indien er voor een uren record
					geen punten zijn toegewezen worden de alsnog toegewezen mag als verwerkingsdatum de huidige datum.<br/>
					Er wordt een verslag getoond met alle resultaten van de herberekening.</p><br/>
					
					<div class="form-group">
						<label for="report">Herberekening rapport</label>
						<textarea class="form-control" readonly rows="10" cols="80" id="report">{{ log }}</textarea>
					</div>

					<button class="btn btn-danger" ng-click="herberekenPunten()">Herbereken punten <span class="glyphicon glyphicon-grain"></span></button>
					<button class="btn btn-default" data-dismiss="modal" ng-click="reset()">Sluiten</button>
				</div>
			</div>
		</div>
	</div>

</div>