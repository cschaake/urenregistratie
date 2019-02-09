<?php
/**
 * Template footer | includes/footer.php
 *
 * Footer for all pages
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
 * @version    1.2.0
 */
?>
<div class="panel panel-default">
    <div class="panel-footer">
        <span class="glyphicon glyphicon-copyright-mark"></span> Copyrights Reddingsbrigade Apeldoorn
        <a href="" data-toggle="modal" data-target="#about">
			<span style="color: #4D94B8;" class="glyphicon glyphicon-knight pull-right">v<?php echo APPLICATION_VERSION ?></span>
		</a>
    </div>
</div>

<div id="about" class="modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Over Urenregistratie</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3 hidden-xs"><br/>
                        <span class="glyphicon glyphicon-knight" style="color: #4D94B8; font-size:7em"></span>
                    </div>
                    <div class="col-sm-9">
                        <h1>Urenregistratie</h1>
                        <p class="small">Versie <?php echo APPLICATION_VERSION ?></p>
                        <p>Urenregistratie is ontworpen door <a href="http://www.schaake.nu">Christiaan Schaake</a>,
                        in opdracht van <a href="http://www.reddingsbrigadeapeldoorn.nl">Reddingsbrigade Apeldoorn</a>.</p>
                        <p>Alle rechten zijn voorbehouden aan Reddingsbrigade Apeldoorn.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="licentie.php">Licentie-informatie</a> |
                <a href="privacy.php">Privacybeleid</a>
            </div>
        </div>
    </div>
</div>