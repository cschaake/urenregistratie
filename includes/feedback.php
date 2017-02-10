<?php
/**
 * Feedback
 *
 * Feedback functionality
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
 * @package    Urenregistratie
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.8
 * @version    1.0.8
 */
 ?>
        
<script src="scripts/feedback.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="container">
	<div id="feedbackApp" ng-app="myApp" ng-controller="feedbackCtrl">
		<div class="hidden-sm hidden-xs">
			<a 
				href="#"
				data-toggle="modal" 
				data-target="#feedbackModal">
				<img src="images/feedback-side.png" width="30px" height="144px" style="top:50%; margin-top:-65px; position:fixed; z-index:999; right:0px"/>
			</a>
		</div>
		
		<div id="feedbackModal" class="modal" role="dialog">
			<div class="modal-dialog">
				
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-paw" style="color: #ff6600"></i> Feedback</h4>
					</div>
				
					<div class="modal-body">
						
						Wat vindt je van deze website?<br/>
						<br/>
						<div style="text-align:center">
							<a href="#" ng-click="setStar(1)"><img src="images/smily_worst.png" width="76px" height="80px"/></a>
							<a href="#" ng-click="setStar(2)"><img src="images/smily_bad.png" width="89px" height="80px" style="margin-left:20px"/></a>
							<a href="#" ng-click="setStar(3)"><img src="images/smily_ok.png" width="76px" height="80px" style="margin-left:20px"/></a>
							<a href="#" ng-click="setStar(4)"><img src="images/smily_better.png" width="108px" height="80px" style="margin-left:20px"/></a>
							<a href="#" ng-click="setStar(5)"><img src="images/smily_best.png" width="94px" height="80px" style="margin-left:20px"/></a><br/>
						</div>
						<br/>
						<div style="text-align:center">{{ starDescription }}</div>
						<hr/>
						<div class="form-group has-feedback">
							<label class="control-label" for="subject">Selecteer een onderwerp</label>
							<select 
									class="form-control"
									id="subject" 
									name="subject" 
									class="form-control" 
									ng-model="form.subject" 
									required>
								<option value="compliment">Compliment</option>
								<option value="bug">Technische vraag</option>
								<option value="question">Inhoudelijke vraag</option>
								<option value="advice">Suggestie</option>
							</select>
						</div>
						
						<label class="control-label" for="start" style="text-align:left">Wat wil je met ons delen?</label>
						<textarea 
							id="comment" 
							name="comment" 
							ng-model="form.comment"
							class="form-control" >{{ form.comment }}</textarea>
						
						<hr/>

						<button type="button" ng-click="submitFeedback()" class="btn btn-default">Verstuur</button>
					</div>
				</div>
			</div>
		</div>
		
		<div id="feedbackOkModal" class="modal" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-paw" style="color: #ff6600"></i> Feedback</h4>
					</div>
					<div class="modal-body">
					
						<?php echo $authenticate->firstName; ?> bedank voor je feedback, wij gaan er direct mee aan de slag.
						<hr/>
						
						<button 
							class="btn btn-primary center-block" 
							data-dismiss="modal">Sluiten
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
