<?php
/**
 * Class Punt | objects/Punt_obj.php
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
 * @since      File available since Release 1.2.1
 * @version    1.2.1
 */

/**
 * Required files
 */

/**
 * Class Punt - Enkel punt record
 *
 * @since File available since Release 1.2.1
 * @version 1.2.1
 */
class Punt
{
    
    /**
     * Id van het uur verplicht numeriek 5 lang
     *
     * @var int Id
     * @access public
     */
    public $id;
    
    /**
     * Gebruikernaam verplicht alfanumeriek 10 lang
     *
     * @var string Gebruikersnaam
     * @access public
     */
    public $username;
    
    /**
     * Datum verplicht JSON datum formaat
     *
     * @var string Datum
     * @access public
     */
    public $datum;
    
    /**
     * Start tijd JSON tijd formaat
     *
     * @var string Start tijd
     * @access public
     */
    public $start;
    
    /**
     * Eind tijd JSON tijd formaat
     *
     * @var string Eind tijd
     * @access public
     */
    public $eind;
    
    /**
     * Id van het uur verplicht numeriek 5 lang
     *
     * @var int uur_id
     * @access public
     */
    public $uur_id;
    
    /**
     * Datum van goedkeuren verplicht JSON datum formaat
     *
     * @var string createDate
     * @access public
     */
    public $createDate;

    /**
     * Punten verplicht numeriek 5,2
     *
     * @var float Punten
     * @access public
     */
    public $punten;
    
    /**
     * Waarde punten verplicht numeriek 5,2
     *
     * @var float waardePunten
     * @access public
     */
    public $waardePunten;
    
    /**
     * puntenGebruikt optioneel numeriek 5,2
     *
     * @var float puntenGebruikt
     * @access public
     */
    public $puntenGebruikt;
    
    /**
     * Method constructor
     *
     * @param int $id
     * @param string $username
     * @param string $datum
     * @param string $start
     * @param string $eind
     * @param int $uur_id
     * @param string $createDate
     * @param float $punten
     * @param float $waardePunten
     * @param float $puntenGebruikt
     * @throws Exception
     * @return bool Succes vlag
     */
    public function __construct(int $id, string $username, string $datum, string $start, string $eind, int $uur_id, string $createDate, float $punten, float $waardePunten, float $puntenGebruikt = null)
    {
        if ($id) {
            $this->id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        } else {
            $this->id = null;
        }
        $this->id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        $this->username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->datum = date('Y-m-d', strtotime($datum));
        $this->start = date('H:i', strtotime($start));
        $this->eind = date('H:i', strtotime($eind));
        $this->uur_id = (int) filter_var($uur_id, FILTER_SANITIZE_STRING);
        $this->createDate = date('Y-m-d', strtotime($createDate));
        $this->punten = (float) filter_var($punten, FILTER_SANITIZE_STRING);
        $this->waardePunten = (float) filter_var($waardePunten, FILTER_SANITIZE_STRING);
        $this->puntenGebruikt = (float) filter_var($puntenGebruikt, FILTER_SANITIZE_STRING);
        
        if ($this->eind < $this->start) {
            throw new Exception('Eindtijd ligt voor starttijd');
        }
        
        return true;
    }
}
