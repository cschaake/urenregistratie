<?php
/**
 * Class PuntenWaarde | objects/PuntenWaarde_obj.php
 *
 * Bevat object PuntenWaarde. 
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
 * @copyright  2020 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.2.2
 * @version    1.2.2
 */

/**
 * Class PuntenWaarde - Enkele puntenwaarde
 *
 * Bevat de waarde van een punt vanaf een bepaalde datum
 *
 * @since Object available since Release 1.2.2
 * @version 1.2.2
 */
 
/**
 * Class PuntenWaarde - Enkel puntenwaarde object
 *
 * Bevat gegevens van 1 puntenwaarde
 *
 * @since Object available since Release 1.2.2
 * @version 1.2.2
 */

class PuntenWaarde
{
    /**
     * Id van puntenwaarde record
     *
     * @int _id
     * @access private
     */
    public $id;
    
    /**
     * Datum vanaf wanneer punt geldig is
     *
     * @DateTime _datumVanaf
     * @access private
     */
    public $datumVanaf;
    
    /**
     * Waarde van de punt
     *
     * @float _waarde
     * @access private
     */
    public $waarde;

    /**
     * Method constructor
     *
     * @param int $id
     * @param string $datumVanaf
     * @param int $waarde
     * @throws Exception
     * @return bool Succes vlag
     */
    public function __construct(int $id, string $datumVanaf, float $waarde)
    {
        $this->id = (int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $this->datumVanaf = date('Y-m-d', strtotime($datumVanaf));
        $this->waarde = (float) filter_var($waarde, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
        return true;
    }
}