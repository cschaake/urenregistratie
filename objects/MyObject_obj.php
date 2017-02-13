<?php

/**
 * @author chris_cvpz4xj
 *
 */
class MyObject
{

    
    /**
     * Description
     * 
     * @var number
     * @access public
     */
    public $var1;

    
    /**
     * Description
     * 
     * @var string
     * @access private
     */
    private $var2;

    /**
     *
     * @param number $var1            
     * @return number
     */
    public function myFunction($var1)
    {
        $this->myPrivateFunction('help');
        return $var1 + 1;
    }

    
    /**
     * Description
     *
     * @param string $var2
     * @throws Exception
     * @return boolean
     */
    private function myPrivateFunction($var2)
    {
        $this->var2 = $var2;
        if ($this->var2 == 'help') {
            throw new Exception();
        }
        return true;
    }
}
