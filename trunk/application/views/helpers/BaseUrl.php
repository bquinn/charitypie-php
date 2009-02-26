<?php
/** Zend_View_Helper_Abstract.php */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Grab the sites base url
 *
 * Author: Nathan Sudell (nathan@yura.net)
 * Company: MyCharityPie
 * Date: 2009-02-20
 * 
 */
class Zend_View_Helper_BaseUrl extends Zend_View_Helper_Abstract
{
    public function baseurl()
    {
      return Zend_Controller_Front::getInstance()->getBaseUrl();
    }
}
