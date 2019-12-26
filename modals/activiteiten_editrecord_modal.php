<?php
/**
 * Modal editrecord | modals/activiteiten_editrecord_modal.php
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
 * @version    1.2.2
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

					
					
					<div class="form-group has-feedback"
						show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-2" for="activiteit">Activiteit</label>
						<div class="col-sm-10">
							<input id="activiteit" name="activiteit" type="text" ng-model="form.activiteit"
								errorText="Activiteit is verplicht"
								class="form-control"/>
						</div>
					</div>

					<div class="form-group has-feedback"
						show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-2" for="groep">Groep</label>
						<div class="col-sm-10">
							<select id="groep" name="groep" class="form-control"
								ng-model="form.groep_id"
								ng-options="a.id as a.groep for a in activiteitenGroepen"
								errorText="Groep is verplicht" required>
							</select>
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
					
					<div class="form-group has-feedback" ng-hide="form.nodate"
						show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-2" for="datum">Datum</label>
						<div class="col-sm-10">
							<input id="datum" name="datum" type="date" ng-model="form.datum"
								errorText="Datum is verplicht in het formaat jaar-maand-dag"
								class="form-control" required placeholder="jjjj-mm-dd" />
						</div>
					</div>

					<div class="form-group has-feedback"
						show-errors="{ showSuccess: true }">
						<div class="col-sm-2"></div>
						<div class="col-sm-10">
							<input type="checkbox" name="nodate" ng-model="form.nodate"/> Activiteit zonder datum
						</div>
					</div>

					<div class="form-group has-feedback"
						show-errors="{ showSuccess: true }">
						<div class="col-sm-2"></div>
						<div class="col-sm-10">
							<input type="checkbox" name="opmerkingVerplicht" ng-model="form.opmerkingVerplicht"/> Opmerking verplicht bij boeken
						</div>
					</div>

					<div class="form-group has-feedback" ng-hide="form.nodate"
						show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-2" for="begintijd">Begintijd</label>
						<div class="col-sm-10">
							<input id="begintijd" name="begintijd" type="text" ng-model="form.begintijd"
								errorText="Begintijd is verplicht in het formaat uren:minuten"
								class="form-control"
								ng-pattern="/^(?:\d|[01]\d|2[0-3]):[0-5]\d$/" required
								placeholder="hh:mm" />
						</div>
					</div>

					<div class="form-group has-feedback" ng-hide="form.nodate"
						show-errors="{ showSuccess: true }">
						<label class="control-label col-sm-2" for="eindtijd">Eindtijd</label>
						<div class="col-sm-10">
							<input id="eindtijd" name="eindtijd" type="text" ng-model="form.eindtijd"
								errorText="Eindtijd is verplicht is het formaat uren:minuten en moet later zijn dan de begintijd"
								class="form-control"
								ng-pattern="/^(?:\d|[01]\d|2[0-3]):[0-5]\d$/" required
								min="{{ form.begintijd }}" placeholder="hh:mm" />
						</div>
					</div>
					
					<div class="form-group has-feedback"
						show-errors="{ showSuccess: true }">
						<div class="col-sm-2"></div>
						<div class="col-sm-10">
							<input type="checkbox" name="opbouw" ng-model="form.opbouw"/> Voorbereiding en afbouw tijden toevoegen
						</div>
					</div>
					
					
					
					<?php /** @TODO voorbereiding en afbouw tijd uit parameters halen */ ?>
					<div class="alert alert-info" role="alert" ng-show="form.opbouw">
 						Begin- en eindtijden zijn exclusief 30 minuten voorbereiding en 30 minuten afbouwen van de activiteit. 
						De extra tijd wordt automatisch toegekend wanneer bij uren boeken de begin- en eindtijd wordt gehandhaaft.
					</div>
					<br />

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
