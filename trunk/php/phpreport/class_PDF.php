<?php
/*
 * Created on Apr 28, 2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once(dirname(__FILE__)."/../auto_load.php");

class PDF extends FPDF {

	private $Templ;
	private $Data;
	// it is necessary due to the first page of every footer in every section
	private $PrevData;
	private $ReportData;
	private $Groups;
	private $footer;
	private $fpfooter;

private $last_rd = null;	//*********
private $prev_rd = null;	// *********
private $con_logo = true; // *********
private $pagebreak = false;
	
	static function makePDF($t,$d,$rd, $con_logo=true, $orientation='P',$unit='pt',$format='letter'){
		//Instanciation of inherited class
		$pdf=new PDF($t,$d,$rd, $con_logo, $orientation,$unit,$format);
		return $pdf;
	}
	function PDF($t,$d,$rd, $con_logo, $orientation,$unit,$format) {
		parent::FPDF($orientation,$unit,$format);
		$this->con_logo = $con_logo;
		
		$this->AliasNbPages();
		$this->SetFont("Arial","",10);
		for($i=0;$i<count($rd);$i++){
			if(is_array($t))
				$this->Templ=$t[$i];
			else
				$this->Templ=$t;
				$this->Data=$d[$i];
			$this->ReportData=$rd[$i];
			$this->ReportData->reset();
			$this->initGroups();
			$this->startSection();
			$this->footer = $this->Templ->getFooter();
			$this->fpfooter = $this->Templ->getFirstPageFooter();
			$this->PrevData=$this->Data;
			$cf = $this->Templ->getContentFont();
			if($cf == null || !$this->_SetFont($cf))
				$this->SetFont('Times','',12);
			$this->generateContentCells($this->Templ->getContent());
			$this->generateCells($this->Templ->getReportFooter());
		}		
	}

	function Header(){
		if ($this->con_logo) {
			if (session::is_set('K_CLIENTE')) {
				$file_name = session::get('K_ROOT_DIR').'images_appl/'.session::get('K_CLIENTE').'/logo_reporte.jpg';
				if (file_exists($file_name)) 
					$this->Image($file_name, 0, 0,612,792);
				else
					$this->Image(session::get('K_ROOT_DIR').'images_appl/logo_reporte.jpg', 0, 0,612,792);
			}
			else
				$this->Image(session::get('K_ROOT_DIR').'images_appl/logo_reporte.jpg', 0, 0,612,792);
		}
		if($this->SectionPageNo()==1){
			$this->generateCells($this->Templ->getReportHeader());
		}
		$this->generateCells($this->Templ->getHeader());
	}
	
	function Footer(){
		$this->generateCells($this->footer,true,true);
		if($this->SectionPageNo()==1){
			$this->generateCells($this->fpfooter,true,true);
		}

		if ($this->con_logo) {
			$p = &new ReportParser();
			if (session::is_set('K_CLIENTE'))
				$xml_footer = session::get('K_ROOT_DIR').'images_appl/'.session::get('K_CLIENTE').'/footer_reporte.xml';
			else
				$xml_footer = session::get('K_ROOT_DIR').'images_appl/footer_reporte.xml'; 
			$p->parseRP($xml_footer);
			$this->generateCells($p->getFooter(),true,true);
		}
	}
	
	public function replace($s,$rd=null,$prvdata=false){
		if($rd==null)
			$rd=$prvdata?$this->PrevData:$this->Data;
		$rv=str_replace("\\n","\n",$s);
		$c=0;
		while(($k=strpos($rv,"{",$c))!==false){
			if(($v=strpos($rv,"}",$k))!==false){
				$ss=substr($rv,$k+1,$v-$k-1);
				switch ($ss) {
					case "PageNumber":
						$rv = substr_replace($rv,$this->PageNo(),$k,$v-$k+1);
						break;
					case "SectionPageNumber":
						$rv = substr_replace($rv,$this->SectionPageNo(),$k,$v-$k+1);
						break;
					default:
						if(array_key_exists($ss,$rd)){
							$rv = substr_replace($rv,$rd[$ss],$k,$v-$k+1);
							$k=-1;
						}
						break;
				}
			}
			$c=$k+1;
		}
		return $rv;
	}
	
	private function wrapText(&$txt,$w){
		$row=1;
		$k=0;
		do{
			$sp=-1;
			$i=$k;
			for(;$i<strlen($txt);$i++){
				while($i<strlen($txt) && $txt{$i}=="\n"){
					$i++;
					$k=$i;
					$row++;
					$sp=-1;
				}
				if($i<strlen($txt)){
					$s=substr($txt,$k,$i-$k);
					if($this->GetStringWidth($s)>$w-2*$this->cMargin)
						break;
					if($txt{$i}==' ')
						$sp=$i;
				}
			}
			if($i<strlen($txt)){
				if($sp<0){
					$txt=substr($txt,0,$i)."\n".substr($txt,$i);
					$k=$i+1;
				}else{
					$txt{$sp}="\n";
					$k=$sp+1;
				}
				$row++;
			}else
				$k=$i;
		}while($k<strlen($txt));
		return $row*$this->FontSize+($row+1)*$this->cMargin;
	}
	
	private function _SetFont($font){
		if(array_key_exists($font,$this->Templ->getFonts())){
			$fonts=$this->Templ->getFonts();
			$f=$fonts[$font];
			$this->SetFont($f['NAME'],$f['STYLE'],$f['SIZE']);
			$red = isset($f['RED']) ? $f['RED'] : 0;
			$green = isset($f['GREEN']) ? $f['GREEN'] : 0;
			$blue = isset($f['BLUE']) ? $f['BLUE'] : 0;
			$this->SetTextColor($red, $green, $blue);
		} else
			return false;
		return true;
	}
	
	private function getFontSize($font){
		$size=$this->FontSize;
		if(array_key_exists($font,$this->Templ->getFonts())){
			$fonts=$this->Templ->getFonts();
			$f=$fonts[$font];
			$size=$f['SIZE']/$this->k;
		}
		return $size;
	}
	function eval_all_atribute(&$v, $rd) {
		if (count($rd)==0) 
			return;		// no hay datos para evaluar

		/* Solo son evaluables estos atributos
		*/
		$keys = array("FONT", "VISIBLE","BORDER","REDB","GREENB","BLUEB");
		for ($i=0; $i < count($keys); $i++) {
			$atribute = $keys[$i];
			if(array_key_exists($atribute, $v)) {
				$valor = $v[$atribute];
				$pos = strpos($valor, '{');
				if ($pos!==false) { 
					$valor = $this->replace($valor,$rd);
					$stmt = "\$valor = $valor;";
					$res = eval($stmt);
					$v[$atribute] = $valor;
				}
			}
		}
	}
	private function eval_atribute($atribute, &$v, $rd) {
		if (count($rd)==0) 
			return;		// no hay datos para evaluar
			
		if(array_key_exists($atribute, $v)) {
			$valor = $v[$atribute];
			$pos = strpos($valor, '{');
			if ($pos!==false) { 
				$valor = $this->replace($valor,$rd);
				
				// Si aun quedan {variable} es un errro
				$pos = strpos($valor, '{');
				if ($pos===false) { 
					eval("\$valor = ".$valor.";");
					$v[$atribute] = $valor;
				}		
				else		
					$v[$atribute] = $valor;
			}
		}
	}
	function make_row($w,$h,$txt,$ln, $v) {
		$border=(array_key_exists("BORDER",$v))?$v['BORDER']:0;
		$align=(array_key_exists("ALIGN",$v))?$v['ALIGN']:"L";
		if (array_key_exists("NEWLINE2",$v))
			$ln += $v['NEWLINE2'];
		
		$v2=array("WIDTH"=>$w,"HEIGHT"=>$h,"TEXT"=>$txt,"BORDER"=>$border,"NEWLINE"=>$ln,"ALIGN"=>$align);
		if(array_key_exists("FONT",$v))
			$v2["FONT"]=$v["FONT"];
		if(array_key_exists("ABSX",$v))
			$v2["ABSX"]=$v['ABSX'];
		if(array_key_exists("ABSY",$v))
			$v2["ABSY"]=$v['ABSY'];
		if(array_key_exists("ROWHEIGHT",$v))
			$v2["ROWHEIGHT"]=$v['ROWHEIGHT'];
		//**********
		if(array_key_exists("REDL",$v))
			$v2["REDL"]=$v['REDL'];
		if(array_key_exists("REDL",$v))
			$v2["GREENL"]=$v['GREENL'];
		if(array_key_exists("REDL",$v))
			$v2["BLUEL"]=$v['BLUEL'];
			
		if(array_key_exists("REDB",$v))
			$v2["REDB"]=$v['REDB'];
		if(array_key_exists("REDB",$v))
			$v2["GREENB"]=$v['GREENB'];
		if(array_key_exists("REDB",$v))
			$v2["BLUEB"]=$v['BLUEB'];
		if(array_key_exists("VISIBLE",$v))
			$v2["VISIBLE"]=$v['VISIBLE'];

		//**********
		return $v2;
	}
	public function generateCells($arr,$writeout=true,$prvdata=false){
		$retval=array();
		$fontsize=$this->FontSize;
		$fontsize=$this->FontSize;
		$sffamily=$this->FontFamily;
		$sfstyle=$this->FontStyle;
		for($i=0;$i<count($arr); ){
			$row=array();
			$maxHeight=-1;
			$indent=$this->lMargin;
			do{
				$v=$arr[$i];

				//*************** 
				$this->eval_all_atribute($v, $this->prev_rd);
				// Text con formula
				if(array_key_exists("TEXT2",$v)){
					$this->eval_atribute("TEXT2", $v, $this->prev_rd);
					$this->eval_atribute("TEXT2", $v, $prvdata);
					$v["TEXT"] = $v["TEXT2"];
				}
				//***************
				
				if(array_key_exists("FONT",$v)){
					$this->_SetFont($v['FONT']);
				}
				$h=max($maxHeight,(!array_key_exists("HEIGHT",$v) || $v['HEIGHT']==="-0") ? $this->FontSize+2*$this->cMargin : $v['HEIGHT']);
				$txt=(array_key_exists("TEXT",$v))?$v['TEXT']:"";
				$txt=$this->replace($txt,null,$prvdata);
//**********
$txt=$this->replace($txt,$this->prev_rd);
//**********
				if(array_key_exists("FORMAT",$v)&&strlen($txt)>0&&$v['FORMAT']{0}=="N")
					$txt=number_format($txt,$v['FORMAT']{1},$v['FORMAT']{2},$v['FORMAT']{3});
				$ln=(array_key_exists("NEWLINE",$v))?$v['NEWLINE']:0;
				$w=(array_key_exists("WIDTH",$v))?$v['WIDTH']:"100%";
				if(substr($w,-1)=="%"){
					$n=substr($w,0,strlen($w)-1);
					$w=($this->w-$this->lMargin-$this->rMargin)*$n/100;
				}
				if($w>0 && ($this->GetStringWidth($txt)>$w-2*$this->cMargin||strpos($txt,"\n")!==false)){
					// Si existe la marca NOWRAP, no realiza Warp del texto
					if(array_key_exists("NOWRAP",$v)) {
						while (($this->GetStringWidth($txt) + 2 * $this->cMargin) > $w) {
							$txt = substr($txt, 0, strlen($txt)-1);
							if (strlen($txt)==0)	
								break;
						}
					}
					else {
						if(($newHeight=$this->wrapText($txt,$w))>$maxHeight){
							$maxHeight = $newHeight;
							$h = $maxHeight;
							foreach($row as &$v1){
								$v1['HEIGHT']=$maxHeight;
							}
						}
					}
				}
				$v2 = $this->make_row($w,$h,$txt,$ln, $v);
				$indent+=$w;
				if(array_key_exists("INDENTFIX",$v)){
					$v2["INDENTFIX"]=$v['INDENTFIX'];
					$this->indent($indent);
				}
				if(array_key_exists("INDENTRELEASE",$v)){
					$v2["INDENTRELEASE"]=$v['INDENTRELEASE'];
					$this->unIndent();
				}
				
				// Enciende el flag para que si Existe un nuevo registro haga el pagebreak antes de 
				if(array_key_exists("PAGEBREAK",$v)){
					$this->pagebreak = true;					
				}
				if (!isset($v['VISIBLE']) || $v['VISIBLE'])
					$row[]=$v2;
				//************
				if (array_key_exists("NEWLINE2",$v)) {
					$this->groupCtrlAddRow($rd,$row);
					$maxHeight=-1;
					$row=array();
				}
				//************
				$i++;
			}while($ln==0 && $i<count($arr));
			
			if($writeout) {
				$this->SetFont($sffamily,$sfstyle,$this->FontSizePt);
				$this->writeCells($row);
				$sffamily=$this->FontFamily;
				$sfstyle=$this->FontStyle;
			}else{
				$retval=array_merge($retval,$row);
			}
		}
		if(!$writeout) {
			$this->SetFont($sffamily,$sfstyle,$this->FontSizePt);
			return $retval;
		}
	} 

	private function generateContentCells($arr){
		while($this->ReportData->hasMoreRow()){
			$rd=$this->ReportData->getNextRow();
			//***************
			$this->prev_rd = $this->last_rd;		//********
			$this->last_rd = $rd;					//********
			$iniRow = 0;
			$prev_is_newline2 = false;
			//***************
			$row=array();
			$maxHeight=-1;
			$i=0;
			$fontsize=$this->FontSize;
			do{
				$v=$arr[$i];
				
				//***************
				// resetea el max y deja en $iniRow desde donde se debe empezar con el nuevo height
				if ($prev_is_newline2) {
					$maxHeight = -1;
					$iniRow = $i;	
					$prev_is_newline2 = false;									
				}
				if (array_key_exists("NEWLINE2",$v))
					$prev_is_newline2 = true;
				
				$this->eval_all_atribute($v, $rd);
				
				// Text con formula
				if(array_key_exists("TEXT2",$v)){
					$this->eval_atribute("TEXT2", $v, $rd);
					$v["TEXT"] = $v["TEXT2"];
				}
				//***************
				
				if(array_key_exists("FONT",$v))
					$fontsize=$this->getFontSize($v['FONT']);
				$h=max($maxHeight,(!array_key_exists("HEIGHT",$v) || $v['HEIGHT']==="-0") ? $fontsize+2*$this->cMargin : $v['HEIGHT']);
				
				$txt=(array_key_exists("TEXT",$v))?$v['TEXT']:"";
				
				$txt=$this->replace($txt,$rd);
				
				if(array_key_exists("FORMAT",$v)&&strlen($txt)>0&&$v['FORMAT']{0}=="N")
					$txt=number_format($txt,$v['FORMAT']{1},$v['FORMAT']{2},$v['FORMAT']{3});
				$ln=(array_key_exists("NEWLINE",$v))?$v['NEWLINE']:0;

				$w=(array_key_exists("WIDTH",$v))?$v['WIDTH']:"100%";
				if(substr($w,-1)=="%"){
					$n=substr($w,0,strlen($w)-1);
					$w=($this->w-$this->lMargin-$this->rMargin)*$n/100;
				}
				if($w>0 && ($this->GetStringWidth($txt)>$w-2*$this->cMargin||strpos($txt,"\n")!==false)){
					// Si existe la marca NOWRAP, no realiza Warp del texto
					if(array_key_exists("NOWRAP",$v)) {
						while (($this->GetStringWidth($txt) + 2 * $this->cMargin) > $w) {
							$txt = substr($txt, 0, strlen($txt)-1);
							if (strlen($txt)==0)	
								break;
						}
					}
					else {
						if(($newHeight=$this->wrapText($txt,$w))>$maxHeight){
							$maxHeight = $newHeight;
							$h = $maxHeight;
							for ($j=$iniRow; $j<count($row); $j++)
								$row[$j]['HEIGHT']=$maxHeight;
						}
					}
				}
				$v2 = $this->make_row($w,$h,$txt,$ln, $v);
				if (!isset($v['VISIBLE']) || $v['VISIBLE'])
					$row[]=$v2;
				$i++;
			}while($ln==0 && $i<count($arr));			
			$this->groupCtrlAddRow($rd,$row);
		}
$this->prev_rd = $this->last_rd;		//********
		$this->groupCtrlAddRow();
	}

	private function writeCells($row){
		foreach($row as $v){
			if(array_key_exists("ABSY",$v))
				$this->SetY($v['ABSY']);
			if(array_key_exists("ABSX",$v))
				$this->SetX($v['ABSX']);
			if(array_key_exists("FONT",$v))
				$this->_SetFont($v['FONT']);
				
			//*********
			$redL = isset($v['REDL']) ? $v['REDL'] : 0;
			$greenL = isset($v['GREENL']) ? $v['GREENL'] : 0;
			$blueL = isset($v['BLUEL']) ? $v['BLUEL'] : 0;
			$this->SetDrawColor($redL, $greenL, $blueL);

			if (!isset($v['REDB'])) 
				$fill = false;
			else {
				$fill = true;
				$redB = isset($v['REDB']) ? $v['REDB'] : 255;
				$greenB = isset($v['GREENB']) ? $v['GREENB'] : 255;
				$blueB = isset($v['BLUEB']) ? $v['BLUEB'] : 255;
				$this->SetFillColor($redB, $greenB, $blueB);
			}
			//*********

			$rowheight=array_key_exists("ROWHEIGHT",$v)?$v['ROWHEIGHT']:0;
			$this->Cell($v['WIDTH'],$v['HEIGHT'],$v['TEXT'],$v['BORDER'],$v['NEWLINE'],$v['ALIGN'], $fill,'',$rowheight);
			if(array_key_exists("INDENTFIX",$v)){
				$this->indent();
			}
			if(array_key_exists("INDENTRELEASE",$v)){
				$this->unIndent();
			}
		}
	}

	private function initGroups(){
		$this->Groups=array();
		$outer=null;
		foreach($this->Templ->getGroups() as $grp){
			$this->Groups[]=new ReportGroup($this,$grp,$outer);
			if($outer)
				$outer->setInnerGroup($this->Groups[count($this->Groups)-1]);
			$outer=$this->Groups[count($this->Groups)-1];
		}
	}
	
	/**
	 * controll a the groups
	 */
	private function groupCtrlAddRow($rd=null,$row=null){
		if(count($this->Groups)>0)
			$row=$this->Groups[0]->addRow($rd,$row);
		if($row!=null){
			$this->writeCells($row);
			// Si esta encendido el flag Agrega un Page
			if ($rd && $row && $this->pagebreak) {
				$this->pagebreak = false;
				$this->AddPage($this->DefOrientation);
			}					
		}
	} 

	public function mergeData($d){
		$this->Data=array_merge($this->Data,$d);
	}

}

class ReportGroup {
	
	private $inner=null;
	private $outer=null;
	private $pdf;
	private $grpdef;
	private $vars;
	private $grpvar;
	private $outbuffer;
	
	public function __construct($pdf,$grp,$outer){
		$this->outer=$outer;
		$this->pdf=$pdf;
		$this->grpdef=$grp;
		$this->vars=array();
		$this->grpvar=null;
		$this->newGroupStart();
	}
	
	public function setInnerGroup($inner){
		$this->inner=$inner;
	}
	
	private function updateVars($rd){
		foreach($this->grpdef['vars'] as $v){
			$name=$v['NAME'];
			if($rd==null){
				if($v['FUNCTION']=='GROUP')
					$this->vars[$name]=array();
				else
					$this->vars[$name]=0;
			}else{
				switch ($v['FUNCTION']) {
					case 'COUNT':
						$this->vars[$name]++;
						break;
					case 'SUM':
						$this->vars[$name]+=$rd[$v['VALUE']];
						break;
					case 'GROUP':
						$names=explode(",",$v['VALUES']);
						foreach($names as $n){
							$key=$rd[$v['KEY']];
							if(!array_key_exists($key,$this->vars[$name]))
								$this->vars[$name][$key]=array();
							if(!array_key_exists($n,$this->vars[$name][$key]))
								$this->vars[$name][$key][$n]=0;
							if($v['GROUPFUNCTION']=="SUM")
								$this->vars[$name][$key][$n]+=$rd[$n];
						}
						break;
					default:
						$this->vars[$name]=$rd[$v['VALUE']];
						break;
				}
			}
		}
	}
	
	private function newGroupStart(){
		$this->outbuffer=array();
		$this->updateVars(null);
	}
	
	public function addRow($rd=null,$row=null){
		$rv=null;
		
		/**
		 * I. ki az a legalacsonyabb csoport, aki lezarja a csoportot
		 * II. ha nincs csoport valtas, akkor indulhat a feldolgozas
		 * III. a legalacsonyabb csoport a gyerekektol elkeri az adatokat es elvegzi a csoport
		 * 	lezarasat
		 * IV. adatelkereskor a csoport lezartnak tekintendo
		 * 
		 * adat elkeres
		 * 	igen / adatlekeres
		 * 		elkeri az alsobbakat
		 * 		visszaadja az adatokat
		 * 	nem /adatfeldolgozas
		 * 		le kell-e zarni( mas jott es elotte volt mar adat)
		 * 			igen /adatfeldolgozas lezarassal 
		 * 				elkeri az alsobbakat
		 * 				lezarja, ujat kezd
		 * 				feldolgozza
		 * 			nem / adatfeldolgozas lezaras nelkul
		 * 				feldolgozas
		 * 
		 */
		
		if($rd==null){
			// adatok elkerese
			if($this->inner){
				$backrow=$this->inner->addRow();
				if($backrow&&count($backrow)>0)
					$this->outbuffer=array_merge($this->outbuffer,$backrow);
			}
			$this->finishGroup();
			$rv=$this->outbuffer;
			$this->newGroupStart();
		}else{
			// adatfeldolgozas
			if(array_key_exists('LOCAL',$this->grpdef['attr']) 
					&& $this->grpvar!=null 
					&& $this->grpvar!=$rd[$this->grpdef['attr']['LOCAL']]){
				// csoport lezarasa valtozas miatt
				if($this->inner){
					$backrow=$this->inner->addRow($rd,$row);
					if($backrow&&count($backrow)>0)
						$this->outbuffer=array_merge($this->outbuffer,$backrow);
				}
				$this->finishGroup();
				$rv=$this->outbuffer;
				$this->newGroupStart();
			}
			$backrow=null;
			if($this->inner)
				$backrow=$this->inner->addRow($rd,$row);
			else
				$this->outbuffer=array_merge($this->outbuffer,$row);
				
			if (isset($this->grpdef['attr']['LOCAL'])) {
				$this->grpvar=$rd[$this->grpdef['attr']['LOCAL']];
			}
				
			$this->updateVars($rd);
			if($backrow&&count($backrow)>0)
				$this->outbuffer=array_merge($this->outbuffer,$backrow);
		}
		return $rv;
		
		/*if(array_key_exists('LOCAL',$this->grpdef['attr']) 
				&& $this->grpvar !=null 
				&& ($rd==null || $this->grpvar!=$rd[$this->grpdef['attr']['LOCAL']])){
			if($rd==null && $row!=null&& count($row)>0){
				$this->outbuffer=array_merge($this->outbuffer,$row);
				$row=null;
			}
			$this->finishGroup();
			$rv=$this->outbuffer;
			$forceout=false;
			$this->newGroupStart();
		}
		if($rd){
			$this->grpvar=$rd[$this->grpdef['attr']['LOCAL']];
			$this->updateVars($rd);
		}
		if($row!=null&& count($row)>0){
			$this->outbuffer=array_merge($this->outbuffer,$row);
		}
		if($forceout){
			if(!array_key_exists('LOCAL',$this->grpdef['attr']))
				$this->finishGroup();
			$rv=$this->outbuffer;
			$this->outbuffer=array();
		}
		return $rv;*/
	}
	
	private function finishGroup(){
		$this->pdf->mergeData($this->vars);
		if(array_key_exists('header',$this->grpdef)){
			$cells=$this->replaceGroupCells($this->grpdef['header']);
			$this->outbuffer=array_merge($this->pdf->generateCells($cells,false), $this->outbuffer);
			//***************  aui VM ********************
		}
		if(array_key_exists('footer',$this->grpdef)){
			$cells=$this->replaceGroupCells($this->grpdef['footer']);
			$this->outbuffer=array_merge(
				$this->outbuffer
				,$this->pdf->generateCells($cells,false)
			);
		}
	}
	
	private function replaceGroupCells($c){
		$rv=array();
		foreach($c as $cell){
			if(array_key_exists("CELLGROUP",$cell)){
				$name=$cell['NAME'];
				$font=$cell['FONT'];
				$first=true;
				$var=$this->vars[$name];
				foreach($var as $key=>$rowvalues){
					foreach($cell['CELLGROUP'] as $row){
						if($first){
							$row['FONT']=$font;
							$first=false;
						}
						$rd=array('$key'=>$key);
						foreach($rowvalues as $k=>$v)
							$rd['$key::'.$k]=$v;
						$row['TEXT']=$this->pdf->replace($row['TEXT'],$rd);
						$rv[]=$row;
					}
				}
			}else
				$rv[]=$cell;
		}
		return $rv;
	}
}

function dumpCells($c){
	print "-----START----------".BR;
	print count($c).BR;
	if(is_array($c))
		foreach($c as $cell){
			print $cell['TEXT'].BR;
		}
	print "-----END----------".BR;
	
}
?>