<?php
/**
 * Groep Object
 *
 * Object voor Groep
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
 * Groep object
 * 
 * @package    Urenverantwoording
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * 
 * @since      Object available since Release 1.0.7
 */ 
class Groep 
{
	
	/**
     * id verplicht numeriek 5 lang
     * 
     * @var integer		Id van de groep
     * @access public
     */
    public $id;

    /**
     * groep verplicht alfanumeriek 30 lang
     * 
     * @var string		Naam van de groep
     * @access public
     */
    public $groep;
	
	/**
     * Betreft opleidingsgroep
     * 
     * @var bool		Waar indien opleidingsgroep
     * @access public
     */
    public $opleiding;
	
	/**
     * Creeer het groep object
     *
     * @param int 	 $id 	Id van de groep
	 * @param string $groep	Naam van de groep
     *
     * @return bool 		Succes vlag
     */
    public function __construct($id, $groep) 
	{
        $this->id = $id;
		$this->groep = $groep;
		
		if ($this->id == OPLEIDINGS_GROEP_ID) {
			$this->opleiding = true;
		} else {
			$this->opleiding = false;
		}
		
        return true;
    }
}
