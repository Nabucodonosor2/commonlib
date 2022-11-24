<?php
/*
 * Created on Apr 29, 2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once dirname(__FILE__).'/../pear/XML/Parser/Simple.php';

class ReportParser extends XML_Parser_Simple
{
		public $Fonts;
		public $Fields;
		public $Header;
		public $Footer;
		public $ReportHeader;
		public $ReportFooter;
		public $FirstPageFooter;
		public $Content;
		public $ContentFont;
		public $Groups;
		public $tmpVar;
		public $tmpVar1;
		public $tmpGrp;
	
    function __construct()
    {
//        $this->XML_Parser_Simple();
        $this->XML_Parser_Simple(null, 'event', 'ISO-8859-1');	// ********* VM    	
    }
    
    public function parseRP($fn){
    	$this->Fonts=array();
    	$this->Fields = array();
    	$this->Parameters = array();
    	$this->Groups = array();
			$this->setInputFile($fn);
			$this->setMode('func');
    	$this->clearTempVars();
    	$this->clearTempGrpVars();
			return $this->parse();
    }

   /**
    * handle the category element
    *
    * The element will be handled, once it's closed
    *
    * @access   private
    * @param    string      name of the element
    * @param    array       attributes of the element
    * @param    string      character data of the element
    */
    function handleElement_Font($name, $attribs, $data){
    	$id = $attribs['ID'];
    	unset($attribs['ID']);
    	$this->Fonts[$id] = $attribs;
    }

    function handleElement_Field($name, $attribs, $data){
    	$nev = $attribs['NAME'];
    	unset($attribs['NAME']);
    	$this->Fields[$nev] = $attribs;
    }

		private function clearTempVars(){
			$this->tmpVar=array();
		}

		private function clearTempVar1(){
			$this->tmpVar1=array();
		}

		private function clearTempGrpVars(){
			$this->tmpGrp=array("vars"=>array());
		}

    function handleElement_Cell($name, $attribs, $data){
				$this->tmpVar[]=$attribs;
    }

    function handleElement_CellG($name, $attribs, $data){
				$this->tmpVar1[]=$attribs;
    }

    function handleElement_CellGroup($name, $attribs, $data){
    		$this->tmpVar[]=array_merge(array("CELLGROUP"=>$this->tmpVar1),$attribs);
    		$this->clearTempVar1();
    }

    function handleElement_PageHeader($name, $attribs, $data){
    		$this->Header=$this->tmpVar;
    		$this->clearTempVars();
    }

    function handleElement_PageFooter($name, $attribs, $data){
    		$this->Footer=$this->tmpVar;
    		$this->clearTempVars();
    }

    function handleElement_FirstPageFooter($name, $attribs, $data){
    		$this->FirstPageFooter=$this->tmpVar;
    		$this->clearTempVars();
    }

    function handleElement_Content($name, $attribs, $data){
    		$this->Content=$this->tmpVar;
    		$this->clearTempVars();
    		$this->ContentFont = null;
    		if(array_key_exists("FONT",$attribs))
    			$this->ContentFont=$attribs['FONT'];
    }

    function handleElement_ReportFooter($name, $attribs, $data){
    		$this->ReportFooter=$this->tmpVar;
    		$this->clearTempVars();
    }

    function handleElement_ReportHeader($name, $attribs, $data){
    		$this->ReportHeader=$this->tmpVar;
    		$this->clearTempVars();
    }

    function handleElement_Group($name, $attribs, $data){
    	$this->tmpGrp['attr']=$attribs;
			$this->Groups[]=$this->tmpGrp;
			$this->clearTempVars();
			$this->clearTempGrpVars();
    }

    function handleElement_GroupHeader($name, $attribs, $data){
    	$this->tmpGrp['header']=$this->tmpVar;
   		$this->clearTempVars();
    }
    
    function handleElement_GroupFooter($name, $attribs, $data){
    	$this->tmpGrp['footer']=$this->tmpVar;
   		$this->clearTempVars();
    }
    
    function handleElement_Variable($name, $attribs, $data){
    	$this->tmpGrp['vars'][]=$attribs;
    }
    
    function getHeader(){
    	return $this->Header;
    }

    function getContent(){
    	return $this->Content;
    }

    function getContentFont(){
    	return $this->ContentFont;
    }

    function getFooter(){
    	return $this->Footer;
    }

    function getFirstPageFooter(){
    	return $this->FirstPageFooter;
    }

    function getReportHeader(){
    	return $this->ReportHeader;
    }

    function getReportFooter(){
    	return $this->ReportFooter;
    }
    
    function getFonts(){
    	return $this->Fonts;
    }
    
    function getGroups(){
    	return $this->Groups;
    }

}

?>