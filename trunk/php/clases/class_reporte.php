<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class reporte extends base {
	public $sql;
	public $xml;
	public $labels;
	public $con_logo;
	public $titulo;
	var		$orientation='P';
	var		$unit='pt';
	var 	$format='letter';
	var		$misma_ventana = false;
	
	function reporte($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false,$orientation='P',$unit='pt',$format='letter', $misma_ventana=false) {
		$this->sql = $sql;
		$this->xml = $xml;	// debe venir como la posicion relativa desde
		$this->labels = $labels;
		$this->titulo = str_replace("'", "", $titulo);
		$this->con_logo = $con_logo;		
		$this->orientation = $orientation;
		$this->unit = $unit;
		$this->format = $format;
		$this->misma_ventana = $misma_ventana;
		
		$token = md5(uniqid());		//  token UNICO
		session::set($token, $this);
		if ($this->misma_ventana) {
			$url = "../../../../commonlib/trunk/php/print_reporte.php?token=$token";
			header ('Location:'.$url);
		}
		else {
			if ($vuelve_a_presentacion)
				base::presentacion('', " <script>window.open('print_reporte.php?token=".$token."','".$this->titulo."')</script>");
			else 
				print " <script>window.open('../../../../commonlib/trunk/php/print_reporte.php?token=".$token."','".$this->titulo."')</script>";
		}
	}
	function modifica_pdf(&$pdf) {
		// Funcion virtual para permitir incluir directamente instrucciones sobre el PDF
		// antes de enviar el resultado a pantalla
	}
	function make_reporte() {
		$p = new ReportParser();
		$p->parseRP($this->xml);
		$rdata = new MySQLRD($this->sql);

		$pdf = PDF::makePDF(array($p), array($this->labels), array($rdata), $this->con_logo,$this->orientation,$this->unit,$this->format);		
		
		$pdf->SetTitle($this->titulo);
		$this->modifica_pdf($pdf);
		$pdf->Output($this->titulo, 'I');
	}
}
?>