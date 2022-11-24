function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function confirmDel() {
	var agree=confirm("¿ Esta seguro que desea eliminar el registro ?");
	if (agree) return true ;
	else return false ;
}
function trim(str) {
 str = str.replace(/^\s*|\s*$/g,"");
 return str;
}
function dlg_print() {
	/* Se llama en el botopn print de los input, cuando no esta reimplementada.
	   Si la ventana no tiene dlg de print esta es la funcion por defecto
	*/
	return true;
}
function validate() {
	/* Se llama en el botopn save de los input, cuando no esta reimplementada.
	   Si el mantendor necesita validaciones adicionales se debe reimplementar esta funcion.
	*/
	return true;
}
function validate_save() {
	if (valida_mandatory()) 
		return validate(); 
	else 
		return false;
}
function get_TR(tabla_id) {
	var aTR = Array();
	var tabla = document.getElementById(tabla_id);
	if (tabla) {   
		if (tabla.hasChildNodes()) {
			var children = tabla.childNodes;
			for (var i=0; i < children.length; i++) {
				if (children[i].nodeName=='TBODY') {
					if (children[i].hasChildNodes()) {
						var children2 = children[i].childNodes;
						for (var j=0; j<children2.length; j++) {
							if (children2[j].nodeName=='TR') {
								aTR[aTR.length] = children2[j];
	            }					
						}
					}
				}
			}
		}
	}
	return aTR;	
}
function nuevoAjax()
{ 
	/* Crea el objeto AJAX. Esta funcion es generica para cualquier utilidad de este tipo, por
	lo que se puede copiar tal como esta aqui */
	var xmlhttp=false;
	try
	{
		// Creacion del objeto AJAX para navegadores no IE
		xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
	}
	catch(e)
	{
		try
		{
			// Creacion del objet AJAX para IE
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch(E)
		{
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') xmlhttp=new XMLHttpRequest();
		}
	}
	return xmlhttp; 
}
function load_con_ajax(nom_tabla, selectDestino, id_destino, opcionSeleccionada) {
	// Obtengo el elemento del select que debo cargar
	// Creo el nuevo objeto AJAX y envio al servidor el ID del select a cargar y la opcion seleccionada del select origen
	opcionSeleccionada = URLEncode(opcionSeleccionada);

	// Mientras carga elimino la opcion "Selecciona Opcion..." y pongo una que dice "Cargando..."
	selectDestino.length=0;
	var nuevaOpcion=document.createElement("option"); 
	nuevaOpcion.value=0; 
	nuevaOpcion.innerHTML="Cargando...";
	selectDestino.appendChild(nuevaOpcion); 
	selectDestino.disabled=true;	

	var ajax=nuevoAjax();
	ajax.open("GET", "../../../../commonlib/trunk/php/drop_down_dependiente.php?nom_tabla="+nom_tabla+"&select="+id_destino+"&opcion="+opcionSeleccionada, false);
	ajax.send(null);

	var resp = ajax.responseText;
	// cuando el dropdown destino tiene a su vez un dependiente asociado el ajax responde = "/CAMPO_DEPENDIENTE/<select name=...."
	if (resp.substr(0,1)!="/")
		selectDestino.parentNode.innerHTML=resp;
	else {
		var i = 1;
		var new_id_destino = "";
		while (resp.substr(i,1)!="/") {
			new_id_destino = new_id_destino + resp.substr(i,1);
			i++;
		}
		resp = resp.substr(i + 1, resp.length - i - 1)
		selectDestino.parentNode.innerHTML=resp;

		var new_destino=document.getElementById(new_id_destino);
		load_con_ajax(nom_tabla, new_destino, new_id_destino, 0);
	}
}
function load_drop_down(nom_tabla, id_origen, id_destino) {
	var i = id_origen.length;
	while (id_origen.substr(i,1) != '_')
		i--;
	id_destino = id_destino + id_origen.substr(i, id_origen.length - i)
	
	// Obtengo el select que el usuario modifico
	var selectOrigen=document.getElementById(id_origen);
	// Obtengo la opcion que el usuario selecciono
	var opcionSeleccionada=selectOrigen.options[selectOrigen.selectedIndex].value;

	var selectDestino=document.getElementById(id_destino);
	// Si el usuario eligio la opcion "Elige", no voy al servidor y pongo los selects siguientes en estado "Selecciona opcion..."
	if(opcionSeleccionada==0)
	{
		selectDestino.length=0;
			
		var nuevaOpcion=document.createElement("option"); nuevaOpcion.value=0; nuevaOpcion.innerHTML="";
		selectDestino.appendChild(nuevaOpcion);	selectDestino.disabled=true;
	}
	load_con_ajax(nom_tabla, selectDestino, id_destino, opcionSeleccionada);
}
function load_drop_down_from_text(nom_tabla, id_origen, id_destino) {
	// ej: load_drop_down_from_text('gasto_cobranza', 'COD_DEUDOR_JUDICIAL_0', 'COD_DEMANDA');
	var record = get_num_rec_field(id_origen); 
	id_destino = id_destino + '_' + record;
	// Obtengo el select que el usuario modifico
	var selectOrigen=document.getElementById(id_origen);
	// Obtengo la opcion que el usuario selecciono
	var opcionSeleccionada=selectOrigen.value;
	
	var selectDestino=document.getElementById(id_destino);
	// Si el usuario eligio la opcion "Elige", no voy al servidor y pongo los selects siguientes en estado "Selecciona opcion..."
	if(opcionSeleccionada=='')
	{
		selectDestino.length=0;
			
		var nuevaOpcion=document.createElement("option"); nuevaOpcion.value=0; nuevaOpcion.innerHTML="";
		selectDestino.appendChild(nuevaOpcion);	selectDestino.disabled=true;
	}
	load_con_ajax(nom_tabla, selectDestino, id_destino, opcionSeleccionada);
}
function get_nom_field(field) {
// dado un nombre tipo NOM_SUCURSAL_2 retorna el nombre del campo => "NOM_SUCURSAL
	var pos = field.lastIndexOf('_');
	return field.substr(0, pos);
}
function get_num_rec_field(field) {
// dado un nombre tipo NOM_SUCURSAL_2 retorna el num del campo => "2"
	var pos = field.lastIndexOf('_');
	return field.substr(pos + 1, field.length - pos - 1);
}
function agregar_linea_html(tabla_id, ve_linea) {
	/* [0] = linea html
	   [1] = focus
	*/
	var resp = ve_linea.split('|');	
	var table_aux = document.createElement("TABLE"); 
	table_aux.innerHTML = resp[0];
 	var children = table_aux.childNodes;
	for (var i=0; i < children.length; i++) {
		if (children[i].nodeName=='TBODY') {
		  	var children2 = children[i].childNodes;
		  	for (j=0; j < children2.length; j++) {
				if (children2[j].nodeName=='TR') {
					var tr_contenido = children2[j].innerHTML;
					var tabla = document.getElementById(tabla_id); 
					
					var tbody = null; 
					var child_tabla = tabla.childNodes;
					for (k=0; k < child_tabla.length; k++)
						if (child_tabla[k].nodeName=='TBODY') {
							tbody = child_tabla[k];
							break;
						}
					if (! tbody) {
						tbody = document.createElement("TBODY"); 
						tabla.appendChild(tbody);
					}		
					tbody.appendChild(children2[j]);
					var campo_id;
					if (resp[1]!='')
						campo_id = resp[1];
					else {
						// El focus quedara en el primer campo
						pos1 = tr_contenido.search(/\bid\b/);
						pos1 = tr_contenido.indexOf('"', pos1);			// abre comilla
						pos2 = tr_contenido.indexOf('"', pos1 + 1);	// cierra comilla
						campo_id = tr_contenido.substr(pos1 + 1, pos2 - pos1 - 1);
					}
					var campo = document.getElementById(campo_id);
					if (campo) 
						campo.focus();
					return get_num_rec_field(campo_id);
				}
			}
		}
	}
}

function agregar_linea_html_ie(tabla_id, ve_linea) {
	var vl_tr = "";
	var resp = ve_linea.split('|');	
	var div_item_table = document.getElementById('DIV_' + tabla_id);
	vl_tr = resp[0];
	
	var vl_inner = div_item_table.innerHTML;
	var vl_pos = vl_inner.lastIndexOf('</TBODY></TABLE>');
	vl_inner = vl_inner.substr(0, vl_pos) + vl_tr + vl_inner.substr(vl_pos);
	div_item_table.innerHTML = vl_inner;
	
	// busca el id del TR
	vl_pos = vl_tr.indexOf('"'+tabla_id+'_');
	var vl_pos2 = vl_tr.indexOf('"', vl_pos+1);
	vl_tr_id = vl_tr.substr(vl_pos + 1, vl_pos2 - vl_pos - 1);
	return get_num_rec_field(vl_tr_id);
}

function add_line_standard(tabla_id, nom_tabla) {
	var ajax = nuevoAjax();
	ajax.open("GET", "../../../../commonlib/trunk/php/add_line.php?nom_tabla="+nom_tabla+"&label_record="+tabla_id, false);
	ajax.send(null);	

	if (navigator.appName=='Microsoft Internet Explorer')
		return agregar_linea_html_ie(tabla_id, ajax.responseText);
	else
		return agregar_linea_html(tabla_id, ajax.responseText);
}
function add_line(ve_tabla_id, ve_nom_tabla) {
/* Esta funcion se llama al agregar una linea en una dw

En los modulos donde es usada y si se desea agregar un código adiconal se debe 
reimplementar esta funcion
ver ejemplo en guia_recepcion.js
*/
	return add_line_standard(ve_tabla_id, ve_nom_tabla);
}
function recalc_sum(elem) {
	// Esta funcion es llamada cuando se elimina una linea con del_line
	if (elem.hasChildNodes()) {
  		var children = elem.childNodes;
		for (var i=0; i < children.length; i++) {
			if (children[i].id) {
				var field_id = children[i].id;
				var field = get_nom_field(field_id);
				if (field_id.lastIndexOf('_H_')!=-1) // es campo auxiliar
					continue;

				// verifica si tiene accumulate y si tiene actualiza el total
				var acum = document.getElementById('SUM_' + field + '_0'); 
				var acum_h = document.getElementById('SUM_' + field + '_H_0'); 
				if (acum) {
					var valor_sum = get_value(acum_h.id);
					
					// Si existe el campo "_H" toma el valor de este
					var record = get_num_rec_field(field_id);				
					var elem_h = document.getElementById(field + '_H_' + record);
					var valor;
					if  (elem_h)					
						valor =  to_num(get_value(field + '_H_' + record));
					else
						valor =  to_num(get_value(field_id));
					valor_sum = valor_sum - valor;
					// *** VMC
					// Se asume que se debe redondear a 0 decimales, pero esto pueed ser un error
					set_value(acum.id, valor_sum, number_format(valor_sum, 0, ',', '.'));
					set_value(acum_h.id, valor_sum, number_format(valor_sum, 0, ',', '.'));
				
					// Verifica si el campo SUM es usado en otro computed
					recalc_computed_relacionados(0, 'SUM_'+ field);
					if (acum_h.onchange) {
						var f = acum_h.onchange;
						f();
					}
 				}
			}
			else
				recalc_sum(children[i]);
 		}
 	}
}
function del_line(ve_tr_id, ve_nom_mantenedor) {
/* 
ve_tr_id: id del tr a eliminar ej: ITEM_COTIZACION_1
ve_nom_mantenedor: Nombre del mantenedor donde esta inserto la dw, ej: 'cotizacion'

Esta funcion se llama al eliminar una linea en una dw

En los modulos donde es usada y si se desea agregar un código adiconal se debe 
reimplementar esta funcion
ver ejemplo en sabbagh.item_registro_hora.js
*/
	del_line_standard(ve_tr_id, ve_nom_mantenedor);
}
function del_line_standard(tr_id, nom_tabla) {
	var tr = document.getElementById(tr_id); 
	var label_record = get_nom_field(tr_id);
	var record = get_num_rec_field(tr_id);

	var ajax = nuevoAjax();
	ajax.open("GET", "../../../../commonlib/trunk/php/del_line.php?nom_tabla="+nom_tabla+"&label_record="+label_record+"&record="+record, false);
	ajax.send(null);	
	var resp = ajax.responseText;	// no se espera respuesta
	recalc_sum(tr);
	tr.parentNode.removeChild(tr);
}
////////////// k
// El w_input crea un array de "campo_mandatory" que se llama "mandatorys" , este arreglo contiene todos los campos mandatorys del formulario

function campo_mandatory(field, nom_field) {
   this.field = field;
   this.nom_field = nom_field;
}
function is_mandatory(field_id) {
	var field = get_nom_field(field_id);
	for (var i=0; i < mandatorys.length; i++) {
   	if (mandatorys[i].field == field)
   		return i;
  }
  return false;		// not found
}
function my_tab(tab, current_tab) {
   this.tab = tab;
   this.current_tab = current_tab;
}
// valida los mandatory
function valida2(obj, tabs) {
  var myChildren = obj.childNodes; 
  for (var i=0; i < myChildren.length; i++) { 
    if (myChildren[i].nodeType == 1){ 
    	var s1 = 'TabbedPanelsContent';
    	var s2 = 'TabbedPanelsContent TabbedPanelsContentVisible';
    	var clase = myChildren[i].getAttribute('class');
    	if (myChildren[i].nodeName=='DIV' && clase=='TabbedPanels')
      		tabs[tabs.length] = new my_tab(myChildren[i], -1);
    	else if (myChildren[i].nodeName=='DIV' && (clase==s1 || clase==s2))
      		tabs[tabs.length - 1].current_tab++;
    	else if (myChildren[i].nodeName=='INPUT' || myChildren[i].nodeName=='SELECT' || myChildren[i].nodeName=='TEXTAREA') {
    		var pos = is_mandatory(myChildren[i].id);
    		if (pos===false)
    			;	// nada
    		else {
    			if (myChildren[i].value=='' || myChildren[i].value==' ') {
    				for (var j=0; j<tabs.length; j++) {
    					if (tabs[j].tab.id=='TabbedPanels1')
    						TabbedPanels1.showPanel(tabs[j].current_tab);
    					else if (tabs[j].tab.id=='TabbedPanels2')
    						TabbedPanels2.showPanel(tabs[j].current_tab);
    					else if (tabs[j].tab.id=='TabbedPanels3')
    						TabbedPanels3.showPanel(tabs[j].current_tab);
    				}
    				myChildren[i].focus();
    				alert('Debe ingresar "'+mandatorys[pos].nom_field+'" antes de grabar.');
    				return false;
    			}
    		}
      }
      if (!valida2(myChildren[i], tabs))
      	return false;
    }
  }
  return true;
}
function valida_mandatory() {
	var myForm = document.getElementById("input"); 
	var tabs = new Array();
	return valida2(myForm, tabs);
}
function URLEncode( plaintext) {
// ====================================================================
//       URLEncode and URLDecode functions
//
// Copyright Albion Research Ltd. 2002
// http://www.albionresearch.com/
//
// You may copy these functions providing that 
// (a) you leave this copyright notice intact, and 
// (b) if you use these functions on a publicly accessible
//     web site you include a credit somewhere on the web site 
//     with a link back to http://www.albionresearch.com/
//
// If you find or fix any bugs, please let us know at albionresearch.com
//
// SpecialThanks to Neelesh Thakur for being the first to
// report a bug in URLDecode() - now fixed 2003-02-19.
// And thanks to everyone else who has provided comments and suggestions.
// ====================================================================
	// The Javascript escape and unescape functions do not correspond
	// with what browsers actually do...
	var SAFECHARS = "0123456789" +					// Numeric
					"ABCDEFGHIJKLMNOPQRSTUVWXYZ" +	// Alphabetic
					"abcdefghijklmnopqrstuvwxyz" +
					"-_.!~*'()";					// RFC2396 Mark characters
	var HEX = "0123456789ABCDEF";

	var encoded = "";
	for (var i = 0; i < plaintext.length; i++ ) {
		var ch = plaintext.charAt(i);
	    if (ch == " ") {
		    encoded += "+";				// x-www-urlencoded, rather than %20
		} else if (SAFECHARS.indexOf(ch) != -1) {
		    encoded += ch;
		} else {
		    var charCode = ch.charCodeAt(0);
			if (charCode > 255) {
			    alert( "Unicode Character '" 
                        + ch 
                        + "' cannot be encoded using standard URL encoding.\n" +
				          "(URL encoding only supports 8-bit characters.)\n" +
						  "A space (+) will be substituted." );
				encoded += "+";
			} else {
				encoded += "%";
				encoded += HEX.charAt((charCode >> 4) & 0xF);
				encoded += HEX.charAt(charCode & 0xF);
			}
		}
	} // for

	return encoded;
}
function URLDecode( encoded) {
   // Replace + with ' '
   // Replace %xx with equivalent character
   // Put [ERROR] in output if %xx is invalid.
   var HEXCHARS = "0123456789ABCDEFabcdef"; 
   var plaintext = "";
   var i = 0;
   while (i < encoded.length) {
       var ch = encoded.charAt(i);
	   if (ch == "+") {
	       plaintext += " ";
		   i++;
	   } else if (ch == "%") {
			if (i < (encoded.length-2) 
					&& HEXCHARS.indexOf(encoded.charAt(i+1)) != -1 
					&& HEXCHARS.indexOf(encoded.charAt(i+2)) != -1 ) {
				plaintext += unescape( encoded.substr(i,3) );
				i += 3;
			} else {
				alert( 'Bad escape combination near ...' + encoded.substr(i) );
				plaintext += "%[ERROR]";
				i++;
			}
		} else {
		   plaintext += ch;
		   i++;
		}
	} // while
	return plaintext;
}
function set_value(field_id, valor, value_fmt) {
	var field = document.getElementById(field_id); 
	if (field) {
		if (field.value!=null)
			field.value = valor;
		else if (field.options!=null)
			field.options[field.selectedIndex].value = valor;
		else
			field.innerHTML = value_fmt;
	}
}
function set_value_dropdown(ve_drop_down_id, ve_value) {
// Busca ve_value en los options del dropdown y deja selectedIndex apuntando al que corresponde
	var dropdown = document.getElementById(ve_drop_down_id);
	for(var i=0; i < dropdown.options.length; i++)
		if (dropdown.options[i].value ==  ve_value) {
			dropdown.selectedIndex = i;
			break;
		}
}

function get_value(field_id) {
	var field = document.getElementById(field_id); 
	var valor = 0;
	if (field) {
		if (field.value!=null)
			valor = field.value;
		else if (field.options!=null)
			valor = field.options[field.selectedIndex].value;
		else
			valor = field.innerHTML;
	}
	return valor;
}
function set_drop_down_vacio(field_id) {
	var field = document.getElementById(field_id); 
	if (field)
		field.parentNode.innerHTML = '<select name="'+field_id+'" id="'+field_id+'" class = "drop_down" style="width: 150px;" ><option value=""></select>';
}
function set_drop_down(field_id, valor) {
	var field = document.getElementById(field_id); 
	if (field)
		field.parentNode.innerHTML = findAndReplace(valor, "\\'", '"');
}

////////////////////////////////////////////////////////////
/// HELP PRODUCTO
function set_values_producto(valores, record) {

	set_value('COD_PRODUCTO_' + record, valores[1], valores[1]);
	set_value('NOM_PRODUCTO_' + record, valores[2], valores[2]);
	set_value('PRECIO_' + record, valores[3], valores[3]);
}
function select_1_producto(valores, record) {
/* Esta funcion se llama cuando el usuario selecciono un producto de la lista o el dato
ingresado dio como resultado 1 producto 

En los modulos donde es usado help_producto, si se desea agregar un código adiconal se debe 
reimplementar esta funcion
ver ejmplo en produco.js
*/
	 set_values_producto(valores, record);
}
function aux_help_producto(ve_campo){
}
function help_producto_nv_biggi(campo, num_dec,ve_campo) {
	var campo_id = campo.id;
	var field = get_nom_field(campo_id);
	var record = get_num_rec_field(campo_id);

	var cod_producto = document.getElementById('COD_PRODUCTO_' + record); 
	var nom_producto = document.getElementById('NOM_PRODUCTO_' + record); 
	var precio = document.getElementById('PRECIO_' + record);
	var precio_h = document.getElementById('PRECIO_H_' + record);

	cod_producto.value = cod_producto.value.toUpperCase();
	var cod_producto_value = nom_producto_value = '';
	switch (field) {
	case 'COD_PRODUCTO': if (cod_producto.value=='TE') {
   							ingreso_TE(cod_producto);
   							return;
   						}
   						var boton_precio = document.getElementById('BOTON_PRECIO_' + record);
   						if (boton_precio)
   							boton_precio.value =  'Precio';
   						cod_producto_value = campo.value;	
   						break;
	case 'NOM_PRODUCTO': if (cod_producto.value=='T' || cod_producto.value=='TE') return;   											
   						nom_producto_value = campo.value;	
   						break;
	}
	var ajax = nuevoAjax();
	cod_producto_value = URLEncode(cod_producto_value);
	nom_producto_value = URLEncode(nom_producto_value);
	ajax.open("GET", "../../../../commonlib/trunk/php/help_producto.php?cod_producto="+cod_producto_value+"&nom_producto="+nom_producto_value, false);
	ajax.send(null);		

	var resp = URLDecode(ajax.responseText);
	var lista = resp.split('|');
	switch (lista[0]) {
  	case '0':	
				alert('El producto no existe, favor ingrese nuevamente');
			cod_producto.value = nom_producto.value = precio.innerHTML = ''; 
			campo.focus();
	   	break;
  	case '1': 				
  		select_1_producto(lista, record);
	   	break;
  	default:
		/*var args = "dialogLeft:100px;dialogTop:300px;dialogWidth:650px;dialogHeight:200px;dialogLocation:0;Toolbar:'yes';";
 			var returnVal = window.showModalDialog("../../../../commonlib/trunk/php/help_lista_producto.php?sql="+URLEncode(lista[1]), "_blank", args);
	   	if (returnVal == null) {
 				alert('El producto no existe, favor ingrese nuevamente');
				cod_producto.value = nom_producto.value = precio.innerHTML = ''; 
				campo.focus();
			}
			else {
				returnVal = URLDecode(returnVal);
			   	var valores = returnVal.split('|');
		  		select_1_producto(valores, record);
			}*/
		/*******************************NUEVO METODO*****************************/
		var id_campo = campo.id;
	   var url = "../../../../commonlib/trunk/php/help_lista_producto.php?sql="+URLEncode(lista[1]);
		$.showModalDialog({
			 url: url,
			 dialogArguments: '',
			 height: 200,
			 width: 650,
			 scrollable: false,
			 onClose: function(){ 
			 	var returnVal = this.returnValue;
			
				if (returnVal == null){		
					alert('El producto no existe, favor ingrese nuevamente');
					cod_producto.value = nom_producto.value = precio.innerHTML = ''; 
					campo.focus();	
				}else {
					returnVal = URLDecode(returnVal);
				   	var valores = returnVal.split('|');
			  		select_1_producto(valores, record);
			  		if (precio_h) {
	
						precio_h.value = findAndReplace(precio.innerHTML, '.', '');	// borra los puntos en los miles
						precio_h.value = findAndReplace(precio_h.value, ',', '.');	// cambia coma decimal por punto
					}
					
					recalc_computed_relacionados(record, 'PRECIO');
					
					var cantidad = document.getElementById('CANTIDAD_' + record);
					if (cantidad)
						cantidad.setAttribute('type', "text");				
					var item = document.getElementById('ITEM_' + record);
					if (item)
						item.setAttribute('type', "text");				
					var boton_precio = document.getElementById('BOTON_PRECIO_' + record);
					if (boton_precio)	
						boton_precio.removeAttribute('disabled');
					nom_producto.removeAttribute('disabled');
					if (cod_producto.value=='T') {
						document.getElementById('NOM_PRODUCTO_' + record).select();
						if (cantidad) {
							cantidad.setAttribute('type', "hidden");
							cantidad.value = 1;
						}		
						if (item) {
							var aTR = get_TR('ITEM_COTIZACION');
							for (var i=0; i<aTR.length; i++) {
								if (get_num_rec_field(aTR[i].id)==record)
									break;
							}
							var letra = 'A'.charCodeAt(0);
							for (i=i-1; i >=0; i--) {
								var cod_producto_value = document.getElementById('COD_PRODUCTO_' + get_num_rec_field(aTR[i].id)).value;
								if (cod_producto_value=='T') {
									letra = document.getElementById('ITEM_' + get_num_rec_field(aTR[i].id)).value.charCodeAt(0);
									if (letra >= 'A'.charCodeAt(0) && letra<='Z'.charCodeAt(0))
										letra++;
									else
										letra = 'A'.charCodeAt(0);
									break;
								}
							}	
							item.value = String.fromCharCode(letra);
						}
						if (boton_precio)	
							boton_precio.setAttribute('disabled', "");				
					}
					else if (cod_producto.value!='')
						if (cantidad)
							cantidad.focus();
						
					var cod_producto_old = document.getElementById('COD_PRODUCTO_OLD_' + record);
					if (cod_producto_old)
						cod_producto_old.value = cod_producto.value;  
					aux_help_producto(campo,ve_campo);
				}
			}
		});	
			break;
	}
	// reclacula los computed que usan precio
	
	if (precio_h) {
	
		precio_h.value = findAndReplace(precio.innerHTML, '.', '');	// borra los puntos en los miles
		precio_h.value = findAndReplace(precio_h.value, ',', '.');	// cambia coma decimal por punto
	}
	
	recalc_computed_relacionados(record, 'PRECIO');
	
	var cantidad = document.getElementById('CANTIDAD_' + record);
	if (cantidad)
		cantidad.setAttribute('type', "text");				
	var item = document.getElementById('ITEM_' + record);
	if (item)
		item.setAttribute('type', "text");				
	var boton_precio = document.getElementById('BOTON_PRECIO_' + record);
	if (boton_precio)	
		boton_precio.removeAttribute('disabled');
	nom_producto.removeAttribute('disabled');
	if (cod_producto.value=='T') {
		document.getElementById('NOM_PRODUCTO_' + record).select();
		if (cantidad) {
			cantidad.setAttribute('type', "hidden");
			cantidad.value = 1;
		}		
		if (item) {
			var aTR = get_TR('ITEM_COTIZACION');
			for (var i=0; i<aTR.length; i++) {
				if (get_num_rec_field(aTR[i].id)==record)
					break;
			}
			var letra = 'A'.charCodeAt(0);
			for (i=i-1; i >=0; i--) {
				var cod_producto_value = document.getElementById('COD_PRODUCTO_' + get_num_rec_field(aTR[i].id)).value;
				if (cod_producto_value=='T') {
					letra = document.getElementById('ITEM_' + get_num_rec_field(aTR[i].id)).value.charCodeAt(0);
					if (letra >= 'A'.charCodeAt(0) && letra<='Z'.charCodeAt(0))
						letra++;
					else
						letra = 'A'.charCodeAt(0);
					break;
				}
			}	
			item.value = String.fromCharCode(letra);
		}
		if (boton_precio)	
			boton_precio.setAttribute('disabled', "");				
	}
	else if (cod_producto.value!='')
		if (cantidad)
			cantidad.focus();
		
	var cod_producto_old = document.getElementById('COD_PRODUCTO_OLD_' + record);
	if (cod_producto_old)
		cod_producto_old.value = cod_producto.value;  
	
}
function help_producto(campo, num_dec) {
	var campo_id = campo.id;
	var field = get_nom_field(campo_id);
	var record = get_num_rec_field(campo_id);

	var cod_producto = document.getElementById('COD_PRODUCTO_' + record); 
	var nom_producto = document.getElementById('NOM_PRODUCTO_' + record); 
	var precio = document.getElementById('PRECIO_' + record);
	var precio_h = document.getElementById('PRECIO_H_' + record);

	cod_producto.value = cod_producto.value.toUpperCase();
	var cod_producto_value = nom_producto_value = '';
	switch (field) {
	case 'COD_PRODUCTO': if (cod_producto.value=='TE') {
   							ingreso_TE(cod_producto);
   							return;
   						}
   						var boton_precio = document.getElementById('BOTON_PRECIO_' + record);
   						if (boton_precio)
   							boton_precio.value =  'Precio';
   						cod_producto_value = campo.value;	
   						break;
	case 'NOM_PRODUCTO': if (cod_producto.value=='T' || cod_producto.value=='TE') return;   											
   						nom_producto_value = campo.value;	
   						break;
	}
	var ajax = nuevoAjax();
	cod_producto_value = URLEncode(cod_producto_value);
	nom_producto_value = URLEncode(nom_producto_value);
	ajax.open("GET", "../../../../commonlib/trunk/php/help_producto.php?cod_producto="+cod_producto_value+"&nom_producto="+nom_producto_value, false);
	ajax.send(null);		

	var resp = URLDecode(ajax.responseText);
	var lista = resp.split('|');
	switch (lista[0]) {
  	case '0':	
				alert('El producto no existe, favor ingrese nuevamente');
			cod_producto.value = nom_producto.value = precio.innerHTML = ''; 
			campo.focus();
	   	break;
  	case '1': 				
  		select_1_producto(lista, record);
	   	break;
  	default:
		/*var args = "dialogLeft:100px;dialogTop:300px;dialogWidth:650px;dialogHeight:200px;dialogLocation:0;Toolbar:'yes';";
 			var returnVal = window.showModalDialog("../../../../commonlib/trunk/php/help_lista_producto.php?sql="+URLEncode(lista[1]), "_blank", args);
	   	if (returnVal == null) {
 				alert('El producto no existe, favor ingrese nuevamente');
				cod_producto.value = nom_producto.value = precio.innerHTML = ''; 
				campo.focus();
			}
			else {
				returnVal = URLDecode(returnVal);
			   	var valores = returnVal.split('|');
		  		select_1_producto(valores, record);
			}*/
		/*******************************NUEVO METODO*****************************/
		var id_campo = campo.id;
	   var url = "../../../../commonlib/trunk/php/help_lista_producto.php?sql="+URLEncode(lista[1]);
		$.showModalDialog({
			 url: url,
			 dialogArguments: '',
			 height: 200,
			 width: 650,
			 scrollable: false,
			 onClose: function(){ 
			 	var returnVal = this.returnValue;
			
				if (returnVal == null){		
					alert('El producto no existe, favor ingrese nuevamente');
					cod_producto.value = nom_producto.value = precio.innerHTML = ''; 
					campo.focus();	
				}else {
					returnVal = URLDecode(returnVal);
				   	var valores = returnVal.split('|');
			  		select_1_producto(valores, record);
			  		if (precio_h) {
	
						precio_h.value = findAndReplace(precio.innerHTML, '.', '');	// borra los puntos en los miles
						precio_h.value = findAndReplace(precio_h.value, ',', '.');	// cambia coma decimal por punto
					}
					
					recalc_computed_relacionados(record, 'PRECIO');
					
					var cantidad = document.getElementById('CANTIDAD_' + record);
					if (cantidad)
						cantidad.setAttribute('type', "text");				
					var item = document.getElementById('ITEM_' + record);
					if (item)
						item.setAttribute('type', "text");				
					var boton_precio = document.getElementById('BOTON_PRECIO_' + record);
					if (boton_precio)	
						boton_precio.removeAttribute('disabled');
					nom_producto.removeAttribute('disabled');
					if (cod_producto.value=='T') {
						document.getElementById('NOM_PRODUCTO_' + record).select();
						if (cantidad) {
							cantidad.setAttribute('type', "hidden");
							cantidad.value = 1;
						}		
						if (item) {
							var aTR = get_TR('ITEM_COTIZACION');
							for (var i=0; i<aTR.length; i++) {
								if (get_num_rec_field(aTR[i].id)==record)
									break;
							}
							var letra = 'A'.charCodeAt(0);
							for (i=i-1; i >=0; i--) {
								var cod_producto_value = document.getElementById('COD_PRODUCTO_' + get_num_rec_field(aTR[i].id)).value;
								if (cod_producto_value=='T') {
									letra = document.getElementById('ITEM_' + get_num_rec_field(aTR[i].id)).value.charCodeAt(0);
									if (letra >= 'A'.charCodeAt(0) && letra<='Z'.charCodeAt(0))
										letra++;
									else
										letra = 'A'.charCodeAt(0);
									break;
								}
							}	
							item.value = String.fromCharCode(letra);
						}
						if (boton_precio)	
							boton_precio.setAttribute('disabled', "");				
					}
					else if (cod_producto.value!='')
						if (cantidad)
							cantidad.focus();
						
					var cod_producto_old = document.getElementById('COD_PRODUCTO_OLD_' + record);
					if (cod_producto_old)
						cod_producto_old.value = cod_producto.value;  
				}
			}
		});	
			break;
	}
	// reclacula los computed que usan precio
	
	if (precio_h) {
	
		precio_h.value = findAndReplace(precio.innerHTML, '.', '');	// borra los puntos en los miles
		precio_h.value = findAndReplace(precio_h.value, ',', '.');	// cambia coma decimal por punto
	}
	
	recalc_computed_relacionados(record, 'PRECIO');
	
	var cantidad = document.getElementById('CANTIDAD_' + record);
	if (cantidad)
		cantidad.setAttribute('type', "text");				
	var item = document.getElementById('ITEM_' + record);
	if (item)
		item.setAttribute('type', "text");				
	var boton_precio = document.getElementById('BOTON_PRECIO_' + record);
	if (boton_precio)	
		boton_precio.removeAttribute('disabled');
	nom_producto.removeAttribute('disabled');
	if (cod_producto.value=='T') {
		document.getElementById('NOM_PRODUCTO_' + record).select();
		if (cantidad) {
			cantidad.setAttribute('type', "hidden");
			cantidad.value = 1;
		}		
		if (item) {
			var aTR = get_TR('ITEM_COTIZACION');
			for (var i=0; i<aTR.length; i++) {
				if (get_num_rec_field(aTR[i].id)==record)
					break;
			}
			var letra = 'A'.charCodeAt(0);
			for (i=i-1; i >=0; i--) {
				var cod_producto_value = document.getElementById('COD_PRODUCTO_' + get_num_rec_field(aTR[i].id)).value;
				if (cod_producto_value=='T') {
					letra = document.getElementById('ITEM_' + get_num_rec_field(aTR[i].id)).value.charCodeAt(0);
					if (letra >= 'A'.charCodeAt(0) && letra<='Z'.charCodeAt(0))
						letra++;
					else
						letra = 'A'.charCodeAt(0);
					break;
				}
			}	
			item.value = String.fromCharCode(letra);
		}
		if (boton_precio)	
			boton_precio.setAttribute('disabled', "");				
	}
	else if (cod_producto.value!='')
		if (cantidad)
			cantidad.focus();
		
	var cod_producto_old = document.getElementById('COD_PRODUCTO_OLD_' + record);
	if (cod_producto_old)
		cod_producto_old.value = cod_producto.value;  
	
}
//////////////////////////////////////////////////////////////////////////

///////////////////// COMPUTED
function campo_computed(field, formula, num_dec) {
   this.field = field;
   this.formula = formula;
   this.num_dec = num_dec;
}
function number_format( number, decimals, dec_point, thousands_sep ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // %        note 1: For 1000.55 result with precision 1 in FF/Opera is 1,000.5, but in IE is 1,000.6
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
 
    var n = number, prec = decimals;
    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);
    var sep = (typeof thousands_sep == "undefined") ? ',' : thousands_sep;
    var dec = (typeof dec_point == "undefined") ? '.' : dec_point;
 
    var s = (prec > 0) ? n.toFixed(prec) : Math.round(n).toFixed(prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;
 
    var abs = Math.abs(n).toFixed(prec);
    var _, i;
 
    if (abs >= 1000) {
        _ = abs.split(/\D/);
        i = _[0].length % 3 || 3;
 
        _[0] = s.slice(0,i + (n < 0)) +
              _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
 
        s = _.join(dec);
    } else {
        s = findAndReplace(s, '.', dec);
    }
 
    return s;
}
function to_num(num) {
	num = findAndReplace(num, '.', '');  // borra los puntos en los miles
	num = findAndReplace(num, ',', '.'); // cambia coma decimal por punto
	
	// Borra los ceros de la izquierda, para evitar que se interprete como un nro octal
	while (num.length > 0) {
		if (num.substr(0,1)=='0')
			num = num.substr(1, num.length  -  1);
		else
			break;
	}
	if (num=='') num = 0;
	return num;
}
function roundNumber(rnum, rlength) { // Arguments: number to round, number of decimal places
  var newnumber = Math.round(rnum*Math.pow(10,rlength))/Math.pow(10,rlength);
  return newnumber;
}
function recalc_computed_relacionados(record, field_computed) {
	var expresion = /(\[[A-Z_0-9]+\])/g;
	// Verifica si el campo es usado en otro computed
	for (var i=0; i < computeds.length; i++) {
		if (computeds[i].field!=field_computed) {
		  var res = computeds[i].formula.match(expresion);
			for (var j=0; j < res.length; j++)
				if (res[j]=='['+field_computed+']') {
					computed(record, computeds[i].field);
				}
		}
	}
}
function computed(record, field_computed) {
	for (var i=0; i < computeds.length; i++)
		if (computeds[i].field==field_computed) {
			formula = computeds[i].formula;
			num_dec = computeds[i].num_dec;
			break;
		}

	var computed_field = document.getElementById(field_computed + '_' + record); 
	var expresion = /(\[[A-Z_0-9]+\])/g;
  	var res = formula.match(expresion);

	// calcula el computed		
	for (i=0; i < res.length; i++) {
		var field_formula = document.getElementById(res[i].substring(1, res[i].length - 1) + '_' + record);
		/* 
		Se implementa IF para solucionar error en IE, sobre el calculo con o sin IVA
		dentro de los Totales, Sabbagh modulo factura de honorarios
		*/
		if (navigator.appName=='Microsoft Internet Explorer'){
			valor = findAndReplace(get_value(field_formula.id), '.', '');
		}else{
			valor = get_value(field_formula.id);}
		
		valor = to_num(valor);
		formula = findAndReplace(formula, res[i], valor);
	}
	valor = eval(formula);
	valor = roundNumber(valor, num_dec);

	set_value(computed_field.id, valor, number_format(valor, num_dec, ',', '.'));
	var computed_field_h_id = field_computed + '_H_' + record;
	var computed_field_h = document.getElementById(computed_field_h_id); 
	var valor_old_computed = get_value(computed_field_h_id);
	set_value(computed_field_h_id, valor, number_format(valor, num_dec, ',', '.'));


	recalc_computed_relacionados(record, field_computed);

	// verifica si tiene accumulate y si tiene actualiza el total
	var acum = document.getElementById('SUM_' + field_computed + '_0'); 
	var acum_h = document.getElementById('SUM_' + field_computed + '_H_0'); 
	if (acum) {
		var valor_sum = acum_h.value;
		if (valor_sum=='') valor_sum = 0;
		if (valor_old_computed=='') valor_old_computed = 0;
		if (valor=='') valor = 0;
		valor_sum = parseFloat(valor_sum) - parseFloat(valor_old_computed) + parseFloat(valor);
		
		set_value(acum.id, valor_sum, number_format(valor_sum, num_dec, ',', '.'));
		set_value(acum_h.id, valor_sum, number_format(valor_sum, num_dec, ',', '.'));

		// Verifica si el campo SUM es usado en otro computed
		recalc_computed_relacionados(0, 'SUM_'+ field_computed);

		if (acum_h.onchange) {
			var f = acum_h.onchange;
			f();
		}
		
	}
}

// Usar secuencia
function item(tr, orden) {
    this.tr = tr;
    this.orden = orden;
}
function sortByOrden(a, b) {
    var x = parseInt(a.orden.value);
    var y = parseInt(b.orden.value);
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}
function usar_secuencia(ve_tabla) {
	var tabla = document.getElementById(ve_tabla);
	if (tabla.hasChildNodes()) {
  		var children = tabla.childNodes;
		var aItem = Array();
		for (var i=0; i < children.length; i++) {
			if (children[i].nodeName=='TBODY') {
				if (children[i].hasChildNodes()) {
				  	var children2 = children[i].childNodes;
				  	j=0;
				  	while (j < children2.length) {
						if (children2[j].nodeName=='TR') {
							var record = get_num_rec_field(children2[j].id);
							var orden = document.getElementById('ORDEN_' + record);
					   		aItem[aItem.length] = new item(children2[j], orden);
							children2[j].parentNode.removeChild(children2[j]);   	
						}
						else
							j++;
					}
					aItem.sort(sortByOrden);
					for (var k=0; k < aItem.length; k++) {
						children[i].appendChild(aItem[k].tr);
						aItem[k].orden.value = (k + 1) * 10;
					}
					break;
				}
			}
		}
	}
}
/*
function request(ve_prompt, ve_valor) {
	var args = "location:no;dialogLeft:100px;dialogTop:300px;dialogWidth:400px;dialogHeight:130px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("../../../../commonlib/trunk/php/request.php?prompt="+URLEncode(ve_prompt)+"&valor="+URLEncode(ve_valor), "_blank", args);
 	if (returnVal == null)		
		return false;		
	else {
		var vl_hidden;
		vl_hidden = document.getElementById('wo_hidden');
		if (vl_hidden)
			vl_hidden.value = returnVal;		
		vl_hidden = document.getElementById('wi_hidden');
		if (vl_hidden)
			vl_hidden.value = returnVal;		
	  	return true;
	}
}
*/
function request(ve_prompt, ve_valor, ve_campo) {
	var id_campo = ve_campo.id;
	var url = "../../../../commonlib/trunk/php/request.php?prompt="+URLEncode(ve_prompt)+"&valor="+URLEncode(ve_valor);
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 140,
		 width: 400,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
		 	
			if (returnVal == null)		
				return false;		
			else {
				
			
				var vl_hidden;
				vl_hidden = document.getElementById('wo_hidden');
				if (vl_hidden){
					var input = document.createElement("input");
					input.setAttribute("type", "hidden");
					input.setAttribute("name", id_campo+'_x');
					input.setAttribute("id", id_campo+'_x');
					document.getElementById("output").appendChild(input);
					vl_hidden.value = returnVal;
					document.output.submit();
				}		
				vl_hidden = document.getElementById('wi_hidden');
				if (vl_hidden){
					var input = document.createElement("input");
					input.setAttribute("type", "hidden");
					input.setAttribute("name", id_campo+'_x');
					input.setAttribute("id", id_campo+'_x');
					document.getElementById("input").appendChild(input);
					vl_hidden.value = returnVal;
					document.input.submit();
				}		
			  	return true;
			}
		}
	});
}
function valida_digito(ve_dig_verif, ve_name_rut){
	//modificaciones supervisadas por VM. para IExplorer.
	var record = get_num_rec_field(ve_dig_verif.id);
	var rut_value = document.getElementById(ve_name_rut + '_' + record).value;
  	
	var x=2;
 	var sumatorio=0;

 	for (var i=rut_value.length - 1; i>=0; i--){
 		if (x>7)
  			x=2;
  		sumatorio = sumatorio + (parseInt(rut_value.substring(i,i+1)) * x);
  		x++;
 	}
 	
 	var digito = sumatorio % 11;
 	digito = 11 - digito;
 	switch (digito){
		case 10:
  			digito="K";
    		break;	
	  	case 11:
	   	 	digito="0";
	    	break;
 	}
/*
alert (digito);
alert(ve_dig_verif.value.toUpperCase());

if (ve_dig_verif.value = 'k')
	ve_dig_verif.value := 'K'
	
flag = ve_dig_verif.value.toUpperCase();
*/
 	if (digito == ve_dig_verif.value.toUpperCase())
  		return true;
	else {
		alert('El digito verificador no corresponde al RUT ingresado ('+rut_value+')');
		ve_dig_verif.value = '';
 		return false;
 	}
}
function clear_dig_verif(ve_rut, ve_name_dig_verif) {
	var record = get_num_rec_field(ve_rut.id);
	document.getElementById(ve_name_dig_verif + '_' + record).value = '';		
}

function validate_mail(theElement ) {
	var s = theElement.value;	
	var filter=/^[A-Za-z0-9][A-Za-z0-9_.-]*@[A-Za-z0-9_-]+\.[A-Za-z0-9_.-]+[A-za-z]$/;
	if (s.length == 0 ) return true;
	if (filter.test(s))
		return true;
	else
		alert("Ingrese una dirección de correo válida");
		theElement.value='';
		theElement.focus();
	return false;
}

function trim(str) {
		return str.replace(/^\s+|\s+$/g,"");
}

//left trim
function ltrim(str) {
	return str.replace(/^\s+/,"");
}

//right trim
function rtrim(str) {
	return str.replace(/\s+$/,"");
}

/*
function dlg_find_text(ve_nom_header, ve_valor_filtro) {
	var args = "location:no;dialogLeft:400px;dialogTop:300px;dialogWidth:470px;dialogHeight:170px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("../../../../commonlib/trunk/php/dlg_find_text.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro="+URLEncode(ve_valor_filtro), "_blank", args);
 	
 	if (navigator.appName=='Microsoft Internet Explorer'){
		if (returnVal == null){
			document.getElementById('wo_header').value = '__BORRAR_FILTRO__';
		}else{
			document.getElementById('wo_header').value = trim(returnVal);
		}
		document.forms["output"].submit();
	   	return true;
	}else{
		if (returnVal == null){
			document.getElementById('wo_hidden').value = '__BORRAR_FILTRO__';
		}else{
			document.getElementById('wo_hidden').value = trim(returnVal);
		}
		document.output.submit();
		return true;
 	}
}
function dlg_find_num(ve_nom_header, ve_valor_filtro1, ve_valor_filtro2, ve_cant_decimal, ve_solo_positivos) {
	var args = "location:no;dialogLeft:400px;dialogTop:320px;dialogWidth:550px;dialogHeight:170px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("../../../../commonlib/trunk/php/dlg_find_num.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro1="+URLEncode(ve_valor_filtro1)+"&valor_filtro2="+URLEncode(ve_valor_filtro2)+"&cant_decimal="+ve_cant_decimal+"&solo_positivos="+ve_solo_positivos, "_blank", args);
 	
 	if (navigator.appName=='Microsoft Internet Explorer'){
 		if (returnVal == null)
			document.getElementById('wo_header').value = '__BORRAR_FILTRO__';
		else
			document.getElementById('wo_header').value = returnVal;
		document.forms["output"].submit();
	   	return true;
 	}else{
 		if (returnVal == null)
			document.getElementById('wo_hidden').value = '__BORRAR_FILTRO__';
		else
			document.getElementById('wo_hidden').value = returnVal;
		document.output.submit();
	   	return true;
 	}
}
*/

var $dialog = null;
$( document ).ready(function() {
jQuery.showModalDialog = function(options) {

    var defaultOptns = {
        url: null,
        dialogArguments: null,
        height: 'auto',
        width: 'auto',
        position: 'center',
        resizable: true,
        scrollable: true,
        onClose: function() { },
        returnValue: null,
        doPostBackAfterCloseCallback: false,
        postBackElementId: null
    };

    var fns = {
        close: function() {
            opts.returnValue = $dialog.returnValue;
            $dialog = null;
            opts.onClose();
            if (opts.doPostBackAfterCloseCallback) {
                postBackForm(opts.postBackElementId);
            }
        },
        adjustWidth: function() { $frame.css("width", "100%"); }
    };

    // build main options before element iteration

    var opts = $.extend({}, defaultOptns, options);

    var $frame = $('<iframe id="iframeDialog" />');

    if (opts.scrollable)
        $frame.css('overflow', 'auto');

    $frame.css({
        'padding': 0,
        'margin': 0,
        'padding-bottom': 10
    });

    var $dialogWindow = $frame.dialog({
        autoOpen: true,
        modal: true,
        width: opts.width,
        height: opts.height,
        resizable: opts.resizable,
        position: opts.position,
        overlay: {
            opacity: 0.5,
            background: "black"
        },
        close: fns.close,
        resizeStop: fns.adjustWidth
    });

    $frame.attr('src', opts.url);
    fns.adjustWidth();

    $frame.load(function() {
        if ($dialogWindow) {
            var maxTitleLength = 50;
            var title = $(this).contents().find("title").html();

            if (title.length > maxTitleLength) {
                title = title.substring(0, maxTitleLength) + '...';
            }
            $dialogWindow.dialog('option', 'title', title);
        }
    });

    $dialog = new Object();
    $dialog.dialogArguments = opts.dialogArguments;
    $dialog.dialogWindow = $dialogWindow;
    $dialog.returnValue = null;
}

function postBackForm(targetElementId) {
    var theform;
    theform = document.forms[0];
    theform.__EVENTTARGET.value = targetElementId;
    theform.__EVENTARGUMENT.value = "";
    theform.submit();
}
});
// function to open THE POPUP
function dlg_find_num(ve_nom_header, ve_valor_filtro1, ve_valor_filtro2, ve_valor_filtro3, ve_cant_decimal, ve_solo_positivos, ve_campo)
{
	var id_campo = ve_campo.id;
	var url = "../../../../commonlib/trunk/php/dlg_find_num.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro1="+URLEncode(ve_valor_filtro1)+"&valor_filtro2="+URLEncode(ve_valor_filtro2)+"&cant_decimal="+ve_cant_decimal+"&solo_positivos="+ve_solo_positivos+"&valor_filtro3="+URLEncode(ve_valor_filtro3);
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 250,
		 width: 500,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
		    
		    var input = document.createElement("input");
			input.setAttribute("type", "hidden");
			input.setAttribute("name", id_campo+'_X');
			input.setAttribute("id", id_campo+'_X');
			
			document.getElementById("output").appendChild(input);
	 		
	 		if (returnVal == null)
				document.getElementById('wo_hidden').value = '__BORRAR_FILTRO__';
			else
				document.getElementById('wo_hidden').value = returnVal;
			document.output.submit();
		   	return true;
		 	
		 }
	});
	
}

function dlg_find_text(ve_nom_header, ve_valor_filtro, ve_campo)
{
	var id_campo = ve_campo.id;
	var url = "../../../../commonlib/trunk/php/dlg_find_text.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro="+URLEncode(ve_valor_filtro);
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 170,
		 width: 510,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
			
			var input = document.createElement("input");
			input.setAttribute("type", "hidden");
			input.setAttribute("name", id_campo+'_X');
			input.setAttribute("id", id_campo+'_X');
			
			document.getElementById("output").appendChild(input);
		
			//document.getElementById('wo_hidden_find').name = id_campo+'_X';
			
			if (returnVal == null){
				document.getElementById('wo_hidden').value = '__BORRAR_FILTRO__';
			}else{
				document.getElementById('wo_hidden').value = trim(returnVal);
			}
			document.output.submit();
			return true;
		 	
		 }
	});
	
}

function dlg_find_date(ve_nom_header, ve_valor_filtro1, ve_valor_filtro2, ve_campo)
{	
	var id_campo = ve_campo.id;
	var url = "../../../../commonlib/trunk/php/dlg_find_date.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro1="+URLEncode(ve_valor_filtro1)+"&valor_filtro2="+URLEncode(ve_valor_filtro2);
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 320,
		 width: 580,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
			
			var input = document.createElement("input");
			input.setAttribute("type", "hidden");
			input.setAttribute("name", id_campo+'_X');
			input.setAttribute("id", id_campo+'_X');
			
			document.getElementById("output").appendChild(input);
		
			//document.getElementById('wo_hidden_find').name = id_campo+'_X';
			
			if (returnVal == null){
				document.getElementById('wo_hidden').value = '__BORRAR_FILTRO__';
			}else{
				document.getElementById('wo_hidden').value = returnVal;
			}
			document.output.submit();
			return true;
		 	
		 }
	});
	
}
function dlg_find_drop_down(ve_nom_header, ve_valor_filtro, ve_sql, ve_campo)
{
	var id_campo = ve_campo.id;
	var url = "../../../../commonlib/trunk/php/dlg_find_drop_down.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro="+URLEncode(ve_valor_filtro)+"&sql="+URLEncode(ve_sql);
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 170,
		 width: 500,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
			
			var input = document.createElement("input");
			input.setAttribute("type", "hidden");
			input.setAttribute("name", id_campo+'_X');
			input.setAttribute("id", id_campo+'_X');
			
			document.getElementById("output").appendChild(input);
		
			//document.getElementById('wo_hidden_find').name = id_campo+'_X';
			
			if (returnVal == null){
				document.getElementById('wo_hidden').value = '__BORRAR_FILTRO__';
			}else{
				document.getElementById('wo_hidden').value = returnVal;
			}
			document.output.submit();
			return true;
		 	
		 }
	});
	
}
function dlg_find_mes(ve_nom_header, ve_valor_filtro1, ve_valor_filtro2, ve_campo) 
{
	var id_campo = ve_campo.id;
	var url = "../../../../commonlib/trunk/php/dlg_find_mes.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro1="+URLEncode(ve_valor_filtro1)+"&valor_filtro2="+URLEncode(ve_valor_filtro2);
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 300,
		 width: 500,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
			
			var input = document.createElement("input");
			input.setAttribute("type", "hidden");
			input.setAttribute("name", id_campo+'_X');
			input.setAttribute("id", id_campo+'_X');
			
			document.getElementById("output").appendChild(input);
		
			//document.getElementById('wo_hidden_find').name = id_campo+'_X';
			
			if (returnVal == null){
				document.getElementById('wo_hidden').value = '__BORRAR_FILTRO__';
			}else{
				document.getElementById('wo_hidden').value = returnVal;
			}
			document.output.submit();
			return true;
		 	
		 }
	});
	
}
function personalizar_mod_documento(returnValue){

}
function mod_documento(ve_tabla, ve_cod, ve_cod_item_menu, ve_mod) {
	var url = "../../../../commonlib/trunk/php/mod_documento.php?tabla="+ve_tabla+"&cod="+ve_cod+"&cod_item_menu="+ve_cod_item_menu+"&mod="+ve_mod;
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 720,
		 width: 1100,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
		 	// Hace un unlock del documento	
			var ajax = nuevoAjax();
			ajax.open("GET", "../../../../commonlib/trunk/php/unlock_documento.php?tabla="+ve_tabla+"&cod="+ve_cod, false);
			ajax.send(null);	
			personalizar_mod_documento(returnVal);
			return returnVal;
		}
	});
}
function personalizar_add_documento(returnVal)
{
}
function add_documento(ve_tabla, ve_cod_item_menu) {
	var url = "../../../../commonlib/trunk/php/add_documento.php?tabla="+ve_tabla+"&cod_item_menu="+ve_cod_item_menu;
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 720,
		 width: 1100,
		 scrollable: false,
		 onClose: function(){ 	 	
		 	var ajax = nuevoAjax();
			ajax.open("GET", "../../../../commonlib/trunk/php/get_key_new_record.php?tabla="+ve_tabla, false);
			ajax.send(null);	

			var returnVal = ajax.responseText;
			personalizar_add_documento(returnVal) 
			return returnVal;
			
		}
	});
}
/*
function dlg_find_date(ve_nom_header, ve_valor_filtro1, ve_valor_filtro2) {
	var args = "location:no;dialogLeft:400px;dialogTop:300px;dialogWidth:470px;dialogHeight:300px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("../../../../commonlib/trunk/php/dlg_find_date.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro1="+URLEncode(ve_valor_filtro1)+"&valor_filtro2="+URLEncode(ve_valor_filtro2), "_blank", args);
 	
 	if (navigator.appName=='Microsoft Internet Explorer'){
 		if (returnVal == null)
			document.getElementById('wo_header').value = '__BORRAR_FILTRO__';
		else
			document.getElementById('wo_header').value = returnVal;
		document.forms["output"].submit();
	   	return true;
 	}else{
 		if (returnVal == null)
			document.getElementById('wo_hidden').value = '__BORRAR_FILTRO__';
		else
			document.getElementById('wo_hidden').value = returnVal;
		document.output.submit();
	   	return true;
 	}
}
function dlg_find_drop_down(ve_nom_header, ve_valor_filtro, ve_sql) {
	var args = "location:no;dialogLeft:400px;dialogTop:300px;dialogWidth:470px;dialogHeight:170px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("../../../../commonlib/trunk/php/dlg_find_drop_down.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro="+URLEncode(ve_valor_filtro)+"&sql="+URLEncode(ve_sql), "_blank", args);
 	
 	if (navigator.appName=='Microsoft Internet Explorer'){
 		if (returnVal == null)
			document.getElementById('wo_header').value = '__BORRAR_FILTRO__';
		else
			document.getElementById('wo_header').value = returnVal;
		document.forms["output"].submit();
	   	return true;
 	}else{
 		if (returnVal == null)
			document.getElementById('wo_hidden').value = '__BORRAR_FILTRO__';
		else
			document.getElementById('wo_hidden').value = returnVal;
		document.output.submit();
	   	return true;
 	}
}
function dlg_find_mes(ve_nom_header, ve_valor_filtro1, ve_valor_filtro2) {
	var args = "location:no;dialogLeft:400px;dialogTop:300px;dialogWidth:470px;dialogHeight:170px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("../../../../commonlib/trunk/php/dlg_find_mes.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro1="+URLEncode(ve_valor_filtro1)+"&valor_filtro2="+URLEncode(ve_valor_filtro2), "_blank", args);
 	
 	if (navigator.appName=='Microsoft Internet Explorer'){
 		if (returnVal == null)
			document.getElementById('wo_header').value = '__BORRAR_FILTRO__';
		else
			document.getElementById('wo_header').value = returnVal;
		document.forms["output"].submit();
	   	return true;
 	}else{
 		if (returnVal == null)
			document.getElementById('wo_hidden').value = '__BORRAR_FILTRO__';
		else
			document.getElementById('wo_hidden').value = returnVal;
		document.output.submit();
	   	return true;
 	}
}
function mod_documento(ve_tabla, ve_cod, ve_cod_item_menu, ve_mod) {
	var args = "location:no;dialogLeft:50px;;dialogWidth:1100px;dialogHeight:720px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("../../../../commonlib/trunk/php/mod_documento.php?tabla="+ve_tabla+"&cod="+ve_cod+"&cod_item_menu="+ve_cod_item_menu+"&mod="+ve_mod, "_blank", args);

	// Hace un unlock del documento	
	var ajax = nuevoAjax();
	ajax.open("GET", "../../../../commonlib/trunk/php/unlock_documento.php?tabla="+ve_tabla+"&cod="+ve_cod, false);
	ajax.send(null);	
	
	return returnVal;
}
function add_documento(ve_tabla, ve_cod_item_menu) {
	var args = "location:no;dialogLeft:50px;;dialogWidth:1100px;dialogHeight:720px;dialogLocation:0;Toolbar:no;";
	window.showModalDialog("../../../../commonlib/trunk/php/add_documento.php?tabla="+ve_tabla+"&cod_item_menu="+ve_cod_item_menu, "_blank", args);

	var ajax = nuevoAjax();
	ajax.open("GET", "../../../../commonlib/trunk/php/get_key_new_record.php?tabla="+ve_tabla, false);
	ajax.send(null);	

	var returnVal = ajax.responseText; 
	return returnVal;
}
*/
function calc_dscto() {
	/* Usada para reclacular los descuentos, cuando se reclacula un SUM_TOTAL_H,
	   se usa en cotizacion, NV, factura, etc
	*/
	var porc_dscto1 = document.getElementById('PORC_DSCTO1_0');
	var monto_dscto1 = document.getElementById('MONTO_DSCTO1_0');
	var ingreso_usuario_dscto1 = document.getElementById('INGRESO_USUARIO_DSCTO1_0');
	var porc_dscto2 = document.getElementById('PORC_DSCTO2_0');
	var monto_dscto2 = document.getElementById('MONTO_DSCTO2_0');
	var ingreso_usuario_dscto2 = document.getElementById('INGRESO_USUARIO_DSCTO2_0');
	
	var f;
	if (ingreso_usuario_dscto1.value=='P') {
		f = porc_dscto1.onchange;
		f();
	}
	else { // 'M'
		f = monto_dscto1.onchange;
		if (f)
			f();
	}

	if (ingreso_usuario_dscto2.value=='P') {
		f = porc_dscto2.onchange;
		f();
	}
	else { // 'M'
		f = monto_dscto2.onchange;
		if (f)
			f();
	}
}
function sprintf ( ) {
    // Return a formatted string  
    // 
    // version: 909.322
    // discuss at: http://phpjs.org/functions/sprintf
    // +   original by: Ash Searle (http://hexmen.com/blog/)
    // + namespaced by: Michael White (http://getsprink.com)
    // +    tweaked by: Jack
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Paulo Ricardo F. Santos
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: sprintf("%01.2f", 123.1);
    // *     returns 1: 123.10
    // *     example 2: sprintf("[%10s]", 'monkey');
    // *     returns 2: '[    monkey]'
    // *     example 3: sprintf("[%'#10s]", 'monkey');
    // *     returns 3: '[####monkey]'
    var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
    var a = arguments, i = 0, format = a[i++];

    // pad()
    var pad = function (str, len, chr, leftJustify) {
        if (!chr) {chr = ' ';}
        var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
        return leftJustify ? str + padding : padding + str;
    };

    // justify()
    var justify = function (value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
        var diff = minWidth - value.length;
        if (diff > 0) {
            if (leftJustify || !zeroPad) {
                value = pad(value, minWidth, customPadChar, leftJustify);
            } else {
                value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
            }
        }
        return value;
    };

    // formatBaseX()
    var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
        // Note: casts negative numbers to positive ones
        var number = value >>> 0;
        prefix = prefix && number && {'2': '0b', '8': '0', '16': '0x'}[base] || '';
        value = prefix + pad(number.toString(base), precision || 0, '0', false);
        return justify(value, prefix, leftJustify, minWidth, zeroPad);
    };

    // formatString()
    var formatString = function (value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
        if (precision != null) {
            value = value.slice(0, precision);
        }
        return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
    };

    // doFormat()
    var doFormat = function (substring, valueIndex, flags, minWidth, _, precision, type) {
        var number;
        var prefix;
        var method;
        var textTransform;
        var value;

        if (substring == '%%') {return '%';}

        // parse flags
        var leftJustify = false, positivePrefix = '', zeroPad = false, prefixBaseX = false, customPadChar = ' ';
        var flagsl = flags.length;
        for (var j = 0; flags && j < flagsl; j++) {
            switch (flags.charAt(j)) {
                case ' ': positivePrefix = ' '; break;
                case '+': positivePrefix = '+'; break;
                case '-': leftJustify = true; break;
                case "'": customPadChar = flags.charAt(j+1); break;
                case '0': zeroPad = true; break;
                case '#': prefixBaseX = true; break;
            }
        }

        // parameters may be null, undefined, empty-string or real valued
        // we want to ignore null, undefined and empty-string values
        if (!minWidth) {
            minWidth = 0;
        } else if (minWidth == '*') {
            minWidth = +a[i++];
        } else if (minWidth.charAt(0) == '*') {
            minWidth = +a[minWidth.slice(1, -1)];
        } else {
            minWidth = +minWidth;
        }

        // Note: undocumented perl feature:
        if (minWidth < 0) {
            minWidth = -minWidth;
            leftJustify = true;
        }

        if (!isFinite(minWidth)) {
            throw new Error('sprintf: (minimum-)width must be finite');
        }

        if (!precision) {
            precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : undefined;
        } else if (precision == '*') {
            precision = +a[i++];
        } else if (precision.charAt(0) == '*') {
            precision = +a[precision.slice(1, -1)];
        } else {
            precision = +precision;
        }

        // grab value using valueIndex if required?
        value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

        switch (type) {
            case 's': return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
            case 'c': return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
            case 'b': return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'o': return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'x': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'X': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
            case 'u': return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'i':
            case 'd':
                number = parseInt(+value, 10);
                prefix = number < 0 ? '-' : positivePrefix;
                value = prefix + pad(String(Math.abs(number)), precision, '0', false);
                return justify(value, prefix, leftJustify, minWidth, zeroPad);
            case 'e':
            case 'E':
            case 'f':
            case 'F':
            case 'g':
            case 'G':
                number = +value;
                prefix = number < 0 ? '-' : positivePrefix;
                method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
                textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
                value = prefix + Math.abs(number)[method](precision);
                return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
            default: return substring;
        }
    };

    return findAndReplace(format, regex, doFormat);
}
function set_order(ve_field_name) {
	document.getElementById('wo_hidden').value = ve_field_name;
	document.output.submit();
   	return true;
}

function my_alert(ve_mensaje) {
	alert(ve_mensaje);
}

// nuevas funciones usadas para que al volver al listado deje marcado en cual se ingreso
function haga_scroll(ve_puntodescroll) {
	var vl_scroll =  document.getElementById('wo_scroll');
	vl_scroll.scrollTop = ve_puntodescroll;
}
function graba_scroll(ve_nom_tabla) {
	var vl_scroll =  document.getElementById('wo_scroll');

	var ajax = nuevoAjax();
	ajax.open("GET", "../../../../commonlib/trunk/php/ajax_set_scroll.php?scroll="+vl_scroll.scrollTop+"&nom_tabla="+ve_nom_tabla, false);
	ajax.send(null);
}
//verifica que el numero que se alla copiado en el header del filtro(output) sean solo numeros
function compruebanumeros(ve_valor){
    var vl_valor1 = ve_valor.value;
	var numeros="0123456789";
	for(i=0; i<vl_valor1.length; i++){
      if (numeros.indexOf(vl_valor1.charAt(i),0)==-1){
		alert('Solo puede ingresar Numeros');
		ve_valor.value ='';
		return; 
      }
   }
}

function findAndReplace(ve_string, ve_valor_a_reemplazar, ve_valor_x_reemplazar){
      var i = 0, length = ve_string.length;

      for (i; i < length; i++) {
            ve_string = ve_string.replace(ve_valor_a_reemplazar, ve_valor_x_reemplazar);
      }
      return ve_string;
}
function redFocus(ve_html){
	ve_html.focus();
	ve_html.style.border='1px solid #FF0000';
}