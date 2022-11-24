<?php
require_once(dirname(__FILE__)."/../auto_load.php");

class nodo {
	var $text;
	var $id;
	var $imagen;
	var $is_folder;
	var $leafs = array();
	
	function nodo($text, $id, $imagen, $is_folder = false, $leafs = array()) {
		$this->text = $text;
		$this->id = $id;
		$this->imagen = $imagen;
		$this->is_folder = $is_folder;
		$this->leafs = $leafs;
	}
	function draw_nodo($nivel) {
		if ($this->is_folder)  {
			$tree = '<tr>';
			if ($nivel > 1 )
				$tree .= '<td width="15"></td>';
			$tree .='<td><a onClick="toggle(this)" class="folder"><img src="../../../../commonlib/trunk/images/plus.gif"><img src="'.$this->imagen.'"/>'.$this->text.'</a> 
					 <div style="display:none;">
					 <table border="0" cellspacing="0" cellpadding="0">';
			for ($i=0 ; $i < count($this->leafs); $i++)  {
				$nodo = $this->leafs[$i];
				$tree .= $nodo->draw_nodo($nivel + 1);
			}
			$tree .= '</table>
					  </div>
					  </td>
					  </tr>';
			return $tree;
		}
		else 
			return '<tr> 
						<td width="15"></td>
						<td><a class="leaf" onClick="selectLeaf('.$this->id.')"><img src="../../../../commonlib/trunk/images/link.gif"/><img src="'.$this->imagen.'"/>'.$this->text.'</a></td>
					</tr>';
	}
}

class tree_view {
	var $titulo;
	var $var_tree;
	
	function tree_view($titulo, $var_tree = 'TREE_VIEW') {
		$this->titulo = $titulo;
		$this->var_tree = $var_tree;
	}
	function load_tree() {	// funcion virtual
		/* ejemplo
		$arch1 = new nodo('arch1', 1, $this->image_leaf);
		$arch2 = new nodo('arch2', 2, $this->image_leaf);
		$folder1 = new nodo('folder1', 11, $this->image_folder, true, array($arch1, $arch2));

		$arch3 = new nodo('arch3', 3, $this->image_leaf);
		$arch773 = new nodo('arch777', 3, $this->image_leaf);
		$arch7 = new nodo('arch7', 7, $this->image_leaf);
		$folder4 = new nodo('folder4', 44, $this->image_folder, true, array($arch7, $arch3));		
		$folder2 = new nodo('folder2', 22, $this->image_folder, true, array( $folder4, $arch3,$arch773));
		
		$arch4 = new nodo('arch4', 4, $this->image_leaf);
		$arch5 = new nodo('arch5', 5, $this->image_leaf);
		$arch6 = new nodo('arch6', 6, $this->image_leaf);
		$folder3 = new nodo('folder3', 33, $this->image_folder, true, array($arch4, $arch5, $arch6));		

		
		$proyecto1 = new nodo('proyecto1', 111, $this->image_proyecto, true, array($folder1, $folder2, $folder3));		
		$proyecto2 = new nodo('proyecto2', 222, $this->image_proyecto, true, array($folder1, $folder2, $folder3));		
		
		$this->tree = array($proyecto1, $proyecto2);
		*/
	}
	function select_leaf() {// funcion virtual
		/* ejemplo
		return '<SCRIPT Language="javascript">
		    		function selectLeaf(code) {
		        		alert("You just clicked on code = " + code);
		    		}
				</SCRIPT>';
		*/
		return '';
	}
	function draw_tree(&$temp) {
		$tree = $this->select_leaf();
		
		// draw
		$tree .= '<table border="0" cellspacing="0" cellpadding="0">
				  <tr> 
			       <td> 
				    <table border="0" cellspacing="0" cellpadding="0">
				     <tr> 
				      <td>'.$this->titulo.'</td>
				     </tr>';
		for ($i=0 ; $i < count($this->tree); $i++)
			$tree .= $this->tree[$i]->draw_nodo(1);
		$tree .=   '</table>
				   </td>
				  </tr>
				 </table>';

		$temp->setVar($this->var_tree, $tree);
	}
}
?>