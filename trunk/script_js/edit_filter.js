function filter_edit_mail(valor)  { 
	var len = valor.length; 
	var res = "";
	var arroba = false;
	for (x=0; x<len; x++) { 
		v1=valor.substr(x,1); 
		if (/^([0-9a-zA-Z._-])$/.test(v1)) {
			res = res + v1;
		}
		else if (v1=="@") {
			if (arroba) continue;
			res = res + v1;
			arroba = true;
		}
	} 
	return res.toLowerCase();
}
function posicionCursor(ve_obj) {
	 var cursor = -1;
	
	 // IE
	 if (document.selection && (document.selection != 'undefined'))
	 {
	     var _range = document.selection.createRange();
	     var contador = 0;
	     while (_range.move('character', -1))
	         contador++;
	     cursor = contador;
	 }
	// FF
	 else if (ve_obj.selectionStart >= 0)
	     cursor = ve_obj.selectionStart;
	
	return cursor;
}
function onlyNumbers(ve_obj, ve_evt, ve_max_num_dec, ve_solo_positivo){
	var keynum;
	
	if(window.event) // IE
		keynum = ve_evt.keyCode;
	else if(ve_evt.which) // Netscape/Firefox/Opera
		keynum = ve_evt.which;
	else // Para FF los carcaters tab, end, etc
		keynum = ve_evt.keyCode;
		
	var vl_reg_coma = /\,/;
	if (keynum==8	// backspace
		|| keynum==9	// tab
		|| keynum==35	// end
		|| keynum==36	// home
		|| keynum==37	// left
		|| keynum==39	// right
		|| keynum==13	// enter
		)
		return true;
	else if (keynum==46 && ve_evt.charCode==0)	// sumpirmir
		return true;
	if ((keynum==99	// ctrl C
		|| keynum==118	// ctrl V
		|| keynum==120)	// ctrl X
		&& ve_evt.ctrlKey)
		return true;
	else if((keynum >= 48 && keynum <= 57)) { // numeros
		if (ve_max_num_dec > 0 && vl_reg_coma.test(ve_obj.value)) { // permite decimales y ya tiene la coma
			var vl_pos = ve_obj.value.lastIndexOf(',');
			var vl_pos_cursor = posicionCursor(ve_obj);
			if (vl_pos_cursor <= vl_pos)	// se esta antes de la coma
				return true;
			var vl_cant_dec = ve_obj.value.length - vl_pos - 1;
			if (vl_cant_dec >= ve_max_num_dec)
				return false;
		}
		return true;
	}
	else if (keynum==45) { // minus
		if (ve_solo_positivo)	// no permite negativos
			return false;
		else if (ve_obj.value.substr(0,1)=="-") // ya esta el caracter menos
			return false
		else if (posicionCursor(ve_obj)==0)
			return true;
	}
	else if (keynum==44) { // coma
		if (ve_max_num_dec ==0)	// no permite decimales
			return false;
		
		if (vl_reg_coma.test(ve_obj.value))	// ya tiene la coma
			return false;
		else
			return true;
	}
	else {
		return false;
	}
}
function IsNumeric(valor) { 
	var len=valor.length; 
	var sw="S"; 
	for (x=0; x<len; x++) { 
		v1=valor.substr(x,1); 
		v2 = parseInt(v1); 
		//Compruebo si es un valor numérico 
		if (isNaN(v2))
			sw= "N";
	} 
	if (sw=="S")
		return true;
	else
		return false;
} 

function filter_edit_date(fecha) {
	var primerslap=false; 
	var segundoslap=false; 
	var long = fecha.length; 
	var dia; 
	var mes; 
	var ano; 
	
	if ((long >= 2) && (primerslap==false)) { 
		dia=fecha.substr(0,2); 
		if ((IsNumeric(dia)==true) && (dia<=31) && (dia!="00")) {
			fecha=fecha.substr(0,2)+"/"+fecha.substr(3,7); 
			primerslap=true; 
		} 
		else { 
			dia=fecha.substr(0,1); 
			if ((IsNumeric(dia)==true) && (fecha.substr(1,1)=="/")) {
				fecha="0"+fecha.substr(0,1)+"/"+fecha.substr(2,7); 
				primerslap=true; 
			} 
			else {
				fecha=""; 
				primerslap=false;
			} 
		} 
	} 
	else { 
		dia=fecha.substr(0,1); 
		if (IsNumeric(dia)==false)
			fecha="";
		if ((long<=2) && (primerslap=true)) {
			fecha=fecha.substr(0,1); 
			primerslap=false; 
		} 
	} 
	if ((long>=5) && (segundoslap==false)) { 
		mes=fecha.substr(3,2); 
		if ((IsNumeric(mes)==true) &&(mes<=12) && (mes!="00")) { 
			fecha=fecha.substr(0,5)+"/"+fecha.substr(6,4); 
			segundoslap=true; 
		} 
		else { 
			mes=fecha.substr(3,1); 
			if ((IsNumeric(mes)==true) &&(fecha.substr(4,1)=="/")) { 
				fecha=fecha.substr(0,3)+"0"+fecha.substr(3,1)+"/"+fecha.substr(5,4); 
				segundoslap=true; 
			} 
			else { 
				fecha=fecha.substr(0,3);; 
				segundoslap=false;
			} 
		} 
	} 
	else { 
		if ((long<=5) && (segundoslap=true)) { 
			fecha=fecha.substr(0,4); 
			segundoslap=false; 
		} 
	} 
	if (long>=7) { 
		ano=fecha.substr(6,4); 
		if (IsNumeric(ano)==false)
			fecha=fecha.substr(0,6); 
		else { 
			if (long==10) { 
				if ((ano==0) || (ano<1900) || (ano>2100))
					fecha=fecha.substr(0,6); 
			} 
		} 
	} 
	if (long>=10) { 
		fecha=fecha.substr(0,10); 
		dia=fecha.substr(0,2); 
		mes=fecha.substr(3,2); 
		ano=fecha.substr(6,4); 
		// Año no viciesto y es febrero y el dia es mayor a 28 
		if (mes ==02)
			if (((ano%4 != 0) && (dia > 28)) || ((ano%4 == 0) && (dia > 29)))
				fecha=fecha.substr(0,2)+"/"; 
	} 
	return (fecha); 
}	
function filter_edit_dig_verif(ve_obj, ve_evt) {
//modificaciones supervisadas por VM. para IExplorer.
	var keynum;
	
	if(window.event) // IE
		keynum = ve_evt.keyCode;
	else if(ve_evt.which) // Netscape/Firefox/Opera
		keynum = ve_evt.which;
	else // Para FF los carcaters tab, end, etc
		keynum = ve_evt.keyCode;
		
	if (keynum==8	// backspace
		|| keynum==9	// tab
		|| keynum==35	// end
		|| keynum==36	// home
		|| keynum==37	// left
		|| keynum==39	// right
		|| keynum==13	// enter
		)
		return true;
	else if (keynum==46 && ve_evt.charCode==0)	// sumpirmir
		return true;
	if ((keynum==99	// ctrl C
		|| keynum==118	// ctrl V
		|| keynum==120)	// ctrl X
		&& ve_evt.ctrlKey)
		return true;
	else if((keynum >= 48 && keynum <= 57)) { // numeros
		return true;
	}
	else if((keynum == 75)) { // "K"
		return true;
	}
	else if((keynum == 107)) { // "k"
		return true;
	}
	else {
		return false;
	}
}
function filter_edit_time(ve_time) {
	var primerslap=false; 
	var long = ve_time.length; 
	var hora; 
	var minuto; 
	
	if ((long >= 2) && (primerslap==false)) { 
		hora = ve_time.substr(0,2); 
		if ((IsNumeric(hora)==true) && (hora<=23)) {
			ve_time = ve_time.substr(0,2)+":"+ve_time.substr(3,3); 
			primerslap=true; 
		} 
		else { 
			hora = ve_time.substr(0,1); 
			if ((IsNumeric(hora)==true) && (ve_time.substr(1,1)==":")) {
				ve_time = "0"+ve_time.substr(0,1)+":"+ve_time.substr(2,3); 
				primerslap=true; 
			} 
			else {
				ve_time=""; 
				primerslap=false;
			} 
		} 
	} 
	else { 
		hora = ve_time.substr(0,1); 
		if (IsNumeric(hora)==false)
			ve_time="";
		if ((long<=2) && (primerslap=true)) {
			ve_time = ve_time.substr(0,1); 
			primerslap=false; 
		} 
	}
	if (primerslap) {
		minuto = ve_time.substr(3,2); 
		if (minuto > 59)
			ve_time = ve_time.substr(0,3) + '59'; 
	} 
	return (ve_time); 
}

function onlyNumbersSpecial(ve_obj, ve_evt, ve_max_num_dec, ve_solo_positivo){
	var keynum;
	
	if(window.event) // IE
		keynum = ve_evt.keyCode;
	else if(ve_evt.which) // Netscape/Firefox/Opera
		keynum = ve_evt.which;
	else // Para FF los carcaters tab, end, etc
		keynum = ve_evt.keyCode;
		
	var vl_reg_coma = /\,/;
	if (keynum==8	// backspace
		|| keynum==9	// tab
		|| keynum==35	// end
		|| keynum==36	// home
		|| keynum==37	// left
		|| keynum==39	// right
		|| keynum==13	// enter
		)
		return true;
	else if (keynum==46 && ve_evt.charCode==0)	// sumpirmir
		return true;
	if ((keynum==99	// ctrl C
		|| keynum==118	// ctrl V
		|| keynum==120)	// ctrl X
		&& ve_evt.ctrlKey)
		return true;
	else if((keynum >= 48 && keynum <= 57)) { // numeros
		if (ve_max_num_dec > 0 && vl_reg_coma.test(ve_obj.value)) { // permite decimales y ya tiene la coma
			var vl_pos = ve_obj.value.lastIndexOf(',');
			var vl_pos_cursor = posicionCursor(ve_obj);
			if (vl_pos_cursor <= vl_pos)	// se esta antes de la coma
				return true;
			var vl_cant_dec = ve_obj.value.length - vl_pos - 1;
		}
		return true;
	}
	else if (keynum==45) { // minus
		if (ve_solo_positivo)	// no permite negativos
			return false;
		else if (ve_obj.value.substr(0,1)=="-") // ya esta el caracter menos
			return false
		else if (posicionCursor(ve_obj)==0)
			return true;
	}
	else if (keynum==44) { // coma
		if (ve_max_num_dec ==0)	// no permite decimales
			return false;
	}
	else {
		return false;
	}
}