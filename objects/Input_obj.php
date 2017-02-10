<?php
/**
 * Input Object
 *
 * Checks the request input. PATH_INFO, POST and GET parameters are handled. And JSON and XML body content is processed. All parameters are sanitized.
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
 * @package    Tools
 * @subpackage Input
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.0.7
 */
 
/**
 * Input object
 *
 * @package    Tools
 * @subpackage Input
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @since      Class available since Release 1.0.0
 */
class Input 
{
    /**
     * HTTP method
     *
     * @var 	string 			$method        HTTP Method of request
     * @access 	private
     */
    private $method;
    
    /**
     * Path
     *
     * @var 	string 			$path        	Path of request on webserver
     * @access 	private
     */
    private $path;
    
    /**
     * Script 	name
     *
     * @var 	string 			$script        	Name of the called script (without PATH_INFO)
     * @access 	private
     */
    private $script;

    /**
     * Content Type
     *
     * @var 	string 			$contentType    Content type of the request
     * @access 	private
     */
    private $contentType;
    
    /**
     * PATH INFO parameters
     *
     * @var 	array 			$pathParams    	Associated Array containing all PATH INFO parameters (SANITIZED) 
     * @access 	public
     */
    public $pathParams;
    
    /**
     * GET parameters
     *
     * @var 	array 			$getParams        Associated Array containing all GET parameters (SANITIZED)
     * @access 	private
     */
    private $getParams;
    
    /**
     * POST 	parameters
     *
     * @var 	array 			$postParams    	Associated Array containing all POST parameters (SANITIZED)
     * @access 	private
     */
    private $postParams;
    
    /**
     * DOM document of request body
     *
     * @var 	DOMDocument 	$DOM        		DOMDocument of request body in case of XML input
     * @access 	private
     */
    private $DOM;
    
    /**
     * JSON data of request body
     *
     * @var 	array|stdClass 	$JSON    		Array or standard class in case of JSON input
     * @access 	private
     */
    private $JSON;
    
    /**
     * Create the input object
     *
     * Creates and processes the client input and fills the object
     *
	 * @access public	 
     * @throws Exception 	if input could not be converted into a DOM Document or JSON object
     * @return bool 		Success flag
     */
    public function __construct() 
	{
        // Detect content type
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $this->contentType =  $_SERVER['CONTENT_TYPE'];
        } else {
            $this->contentType = "text/plain";
        }
        
        $this->method = $_SERVER['REQUEST_METHOD'];
        $split = strrpos($_SERVER['SCRIPT_NAME'], '/');
        $this->script = substr($_SERVER['SCRIPT_NAME'],$split + 1);
        $this->path = substr($_SERVER['SCRIPT_NAME'],0,$split);
        
        // Process the request body
        switch ($this->contentType) {
            case 'application/json': 
                $contents = utf8_encode(file_get_contents('php://input'));
                if (!$this->JSON = json_decode($contents)) {
                    throw new Exception("Input is not a valid JSON object");
                }
            break;
            
            case 'application/xml': 
                $this->DOM = new DOMDocument();
                if (!$this->DOM->loadXML(file_get_contents('php://input'))) {
                    throw new Exception("Input is not a valid XML document");
                }        
            break;
            
            default:
        }
        
        // Process the Path Info
        if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        
            // Convert the extentions to an array
            $pathInfo = explode('/', substr($_SERVER['PATH_INFO'], 1));
        
            // Sanitize all input
            foreach ($pathInfo as $key => $value) {
                $value = filter_var($value, FILTER_SANITIZE_STRING);
                if (strpos($value, '=')) {
                    $param = substr($value, 0, strpos($value, '='));
                    $value = substr($value, strpos($value, '=') + 1);
                } else {
                    $param = $value;
                    $value = null;
                }
                $this->pathParams[$param] = $value;
            }
        }
        
        // Process the URL Parameters
        if (strlen($_SERVER['QUERY_STRING']) > 0) {
            foreach ($_GET as $key => $value) {
                $this->getParams[filter_var($key, FILTER_SANITIZE_STRING)] = filter_var($value, FILTER_SANITIZE_STRING);
            }
        }
        if (isset($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->postParams[filter_var($key, FILTER_SANITIZE_STRING)] = filter_var($value, FILTER_SANITIZE_STRING);
            }
        }
    }
    
    /**
     * Returns the HTTP method of the request
     *
	 * @access public
     * @return string 	The method type  
     */
    public function get_method() 
	{
        return $this->method;
    }
    
    /**
     * Returns the path called by the client
     *
	 * @access public
     * @return string 	URL Path
     */
    public function get_path() 
	{
        return $this->path;
    }
    
    /**
     * Returns the script called by the client, without the PATH_INFO extensions
     *
	 * @access public
     * @return string 	Scriptname
     */
    public function get_script() 
	{
        return $this->script;
    }
    
    /**
     * Returns Content Type of the request
     *
	 * @access public
     * @return string 	Content Type
     */
    public function get_contentType() 
	{
        return $this->contentType;
    }
    
    /**
     * Returns PATH_INFO parameters and values
     * 
     * Contains an associated array where the array keys are the names of the parameters. If values are provided, 
     * the values are inserted there respective the array keys.
     *
	 * @access public
     * @return array|null Associated Array containing all PATH_INFO parameters and values
     */
    public function get_pathParams() 
	{
        return $this->pathParams;
    }
    
    /**
     * Returns all GET parameters and values
     *
     * Contains an associated array where the array keys are the names of the parameters. If values are provided,
     * the values are inserted there respective the array keys.
     *
	 * @access public
     * @return array|null Associated Array containing all GET parameters and values
     */
    public function get_getParams() 
	{
        return $this->getParams;
    }
    
    /**
     * Returns all POST parameters and values
     *
     * Contains an associated array where the array keys are the names of the parameters. If values are provided,
     * the values are inserted there respective the array keys.
     * POST parameters not provided when request contained XML or JSON content.
     *
	 * @access public
     * @return array|null Associated Array containing all POST parameters and values
     */
    public function get_postParams() 
	{
        if ($this->postParams) {
            return $this->postParams;
        } else {
            return null;
        }
    }
    
    /**
     * Returns the XML in the body of the request
     *
     * The XML is already converted into a DOM Document object.
     *
	 * @access public
     * @return DOMDocument|null DOM Document object containing the XML document in the request
     */
    public function get_DOM() 
	{
        if ($this->DOM) {
            return $this->DOM;
        } else {
            return null;
        }
    }
    
    /**
     * Returns the JSON in the body of the request
     *
     * The JSON is already converted into an array or standard class
     *
	 * @access public
     * @return array|stdClass|null DOM Document object containing the XML document in the request
     */
    public function get_JSON() 
	{
        if ($this->JSON) {
            return $this->JSON;
        } else {
            return null;
        }
    }
	
	/**
     * Checks if Path Params are provided
     *
     * pathParams should contain an array of path parameters if any are provided.
     *
	 * @access public
     * @return 	bool	True if path parameters are provided
     */
	public function hasPathParams()
	{
		return is_array($this->pathParams);
	}
}
