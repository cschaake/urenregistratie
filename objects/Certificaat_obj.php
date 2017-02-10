<?php
/**
 * Certificaten Object
 *
 * Object voor Certificaten tabel
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
 * @since      File available since Release 1.0.7
 * @version    1.0.7
 */
 
 /**
 * Certificaat object
 *
 * @package    Urenverantwoording
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @since      Object available since Release 1.0.7
 */
class Certificaat {
	/**
     * id verplicht numeriek 5 lang
     * 
     * @var 	integer
     * @access 	public
     */
    public $id;
	
	/**
     * rol_id verplicht numeriek 5 lang
     * 
     * @var 	int
     * @access 	public
     */
    public $rol_id;

    /**
     * rol verplicht alfanumeriek 30 lang
     * 
     * @var 	string
     * @access 	public
     */
    public $rol;
	
	/**
     * Looptijd verplicht numeriek 3 lang
     * 
     * @var 	int
     * @access 	public
     */
    public $looptijd;
	
	/**
     * Uren verplicht numeriek 3 lang
     * 
     * @var 	int
     * @access 	public
     */
    public $uren;

    /**
     * Groep_id verplicht numeriek 5 lang
     * 
     * @var 	int
     * @access 	public
     */
    public $groep_id;
	
	/**
     * Groep alfanumeriek 30 lang
     * 
     * @var 	string
     * @access  public
     */
    public $groep;
	
	/**
     * Creeer certificaat object
     *
	 * @access public
     * @param  int 		$id
	 * @param  int 		$rol_id
	 * @param  string 	$rol
	 * @param  int 		$looptijd
	 * @param  int 		$uren
	 * @param  int 		$groep_id
	 * @param  string 	$groep
     * @return bool 	Succes vlag
     */
    public function __construct($id, $rol_id, $rol, $looptijd, $uren, $groep_id, $groep) {
		if ($id) {
			$this->id = (int) filter_var($id, FILTER_SANITIZE_STRING);
		} else {
			$this->id = null;
		}
		$this->rol_id = (int) filter_var($rol_id, FILTER_SANITIZE_STRING);
		$this->rol = filter_var($rol, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
		$this->looptijd = (int) filter_var($looptijd, FILTER_SANITIZE_STRING);
		$this->uren = (int) filter_var($uren, FILTER_SANITIZE_STRING);
		$this->groep_id = (int) filter_var($groep_id, FILTER_SANITIZE_STRING);
		$this->groep = filter_var($groep, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
		
        return true;
    }
} 
