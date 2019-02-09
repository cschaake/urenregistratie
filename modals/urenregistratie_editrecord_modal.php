<?php
/**
 * Modal editrecord | modals/urenregistratie_editrecord_modal.php
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
 * 
 * @var string $startdate
 * @var string $enddate
 */
?>

<?php
    /**
     * Modal editrecord
     */
?>
<div id="editrecord" class="modal" role="dialog">
	<div class="modal-dialog">

		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 ng-show="form.edit" class="modal-title">Regel wijzigen</h4>
				<h4 ng-hide="form.edit" class="modal-title">Nieuwe regel toevoegen</h4>
			</div>

			<div class="modal-body">
				<div ng-show="messagelocal" class="alert alert-danger">{{
					messagelocal }}</div>

				<form class="form-horizontal" role="form" novalidate name="editForm">
					<input type="hidden" ng-model="form.edit" value="{{ form.edit }}"></input>
					<!-- Hidden field to set the edit variable -->
					<input type="hidden" ng-model="form.id" value="{{ form.id }}"></input>
					<!-- Hidden field to set record id -->
					<input type="hidden" ng-model="form.username"
						value="{{ form.username }}"></input><br />
					<!-- Hidden field to set record id -->
					
					<div class="form-group has-feedback"
						show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-2" for="datum">Datum</label>
						<div class="col-sm-10">
							<input id="datum" name="datum" type="date" ng-model="form.datum"
								errorText="Datum is verplicht in het formaat jaar-maand-dag en moet tussen <?= $startdate ?> en <?= $enddate ?> liggen"
								class="form-control" required min="<?= $startdate ?>"
								max="<?= $enddate ?>" placeholder="jjjj-mm-dd" />
						</div>
					</div>

					<div class="form-group has-feedback"
						show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-2" for="activiteit">Activiteit</label>
						<div class="col-sm-10">
							<select id="activiteit" name="activiteit" class="form-control"
								ng-model="form.activiteit_id"								
								ng-options="a.id as a.activiteit for a in urenActiviteitenFiltered"
								errorText="Activiteit is verplicht, selecteer eerst een datum" required>
							</select>
						</div>
					</div>

					<div class="form-group has-feedback"
						show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-2" for="rol">Rol</label>
						<div class="col-sm-10">
							<select id="rol" name="rol" class="form-control"
								ng-model="form.rol_id"
								ng-options="a.id as a.rol for a in urenRollen"
								errorText="Rol is verplicht" required>
							</select>
						</div>
					</div>

					<div class="form-group has-feedback"
						show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-2" for="start">Starttijd</label>
						<div class="col-sm-10">
							<input id="start" name="start" type="text" ng-model="form.start"
								errorText="Starttijd is verplicht in het formaat uren:minuten en moet tussen {{ begintijd }} en {{ eindtijd }} liggen"
								class="form-control"
								ng-pattern="/^(?:\d|[01]\d|2[0-3]):[0-5]\d$/" required check-Begintijd
								placeholder="hh:mm" />
						</div>
					</div>

					<div class="form-group has-feedback"
						show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-2" for="eind">Eindtijd</label>
						<div class="col-sm-10">
							<input id="eind" name="eind" type="text" ng-model="form.eind"
								errorText="Eindtijd is verplicht in het formaat uren:minuten en moet tussen {{ form.start }} en {{ eindtijd }} liggen"
								class="form-control"
								ng-pattern="/^(?:\d|[01]\d|2[0-3]):[0-5]\d$/" required check-Eindtijd
								placeholder="hh:mm" />
						</div>
					</div>

					<div ng-show="form.reden" class="form-group">
						<label class="control-label col-sm-12" for="start"
							style="text-align: left">Reden van afkeur</label>
						<div class="col-sm-12">
							<textarea id="reden" name="reden" type="time" readonly
								class="form-control">{{ form.reden }}</textarea>
						</div>
					</div>

					<div class="form-group has-feedback"
						show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-2" for="datum">Opmerking</label>
						<div class="col-sm-10">
							<input id="opmerking" name="opmerking" type="text"
								ng-model="form.opmerking" class="form-control"
								errorText="Bij deze geselecteerde activiteit is opmerking verplicht"
								ng-required="form.activiteit_id == opmerkingRequired" />
						</div>
					</div>
					
					<div class="form-group">
    					<label class="col-sm-2"></label>
    					<div class="col-sm-10">
        					<table class="table table-bordered">
        						<tr>
        							<td>Aantal uren</td>
        							<td>{{ calculateTime(form.eind, form.start, false) }}</td>
        						</tr>
        						<tr>
        							<td>Extra opbouw uren</td>
        							<td>{{ form.start == begintijd ? configuratieExtraBeginUren : '0' }}</td>
        						</tr>
        						<tr>
        							<td>Extra opruim uren</td>
        							<td>{{ form.eind == eindtijd ? configuratieExtraEindUren : '0' }}</td>
        						</tr>
        						<tr>
        							<td><strong>Totaal uren</strong></td>
        							<td><strong>{{ calculateTime(form.eind, form.start, true) }}</strong></td>
        						</tr>
        					</table>
        				</div>
					</div>
					
					
					<button class="btn btn-default" ng-disabled="editForm.$invalid"
						ng-class="{'btn-success': editForm.$valid}"
						ng-click="insert(form.index)">Update</button>

					<button class="btn btn-default" ng-click="reset()">Reset</button>

					<button class="btn btn-default" data-dismiss="modal"
						ng-click="reset()">Annuleer</button>
				</form>
			</div>
		</div>
	</div>
</div>
