<?php
require "connect.php";

//função para definir filtragem por presença de elemento

function _empty($string){
     $string = trim($string);
     if(!is_numeric($string)) return empty($string);
     return FALSE;
}


function elementsvar($qtd, $possui, $nome, $nomecoluna){
	if($qtd == 0 and $possui and !_empty($qtd)){ //possui mas a quantidade é 0
		$GLOBALS["apenaspossui"] += array($nomecoluna => 1);
		echo  "<div style=\"clear:both;\">&nbsp;</div><div class=\"erro\">Erro, informações sobre $nome conflitantes.</div>";
	}		
    elseif (_empty($qtd) and $possui) {//possui e a quantidade não foi informada
		$GLOBALS["apenaspossui"] += array($nomecoluna => 1);
    } 
	elseif(!_empty($qtd)){ //quantidade informada
	    
		$GLOBALS["filtros"] += array($nomecoluna => $qtd);
	}
}

function BuildProp($pos1, $pos2, $p1, $p2, $whereclause, $init ,$type){
		if($pos1 and !$pos2){ //só o 2 definido
			$whereclause = $whereclause . " $init" . "round($type," . strlen(substr(strrchr($p2, "."), 1)) . ") = $p2";
			$init = "AND ";
		}
		elseif($pos2 and !$pos1){ //só o 1 definido
			$whereclause = $whereclause . " $init" . "round($type," . strlen(substr(strrchr($p1, "."), 1)) . ") = $p1";
			$init = "AND ";
		}
		elseif(!$pos2 and !$pos1){ //os dois definidos
			if($p1 > $p2){
				$whereclause = $whereclause . " $init" . "round($type," . strlen(substr(strrchr($p2, "."), 1)) . ") >= $p2 AND round($type," . strlen(substr(strrchr($p1, "."), 1)) . ") <= $p1";
				$init = "AND ";
			}
			elseif($p1 < $p2){
				$whereclause = $whereclause . " $init" . "round($type," . strlen(substr(strrchr($p2, "."), 1)) . ") <= $p2 AND round($type," . strlen(substr(strrchr($p1, "."), 1)) . ") >= $p1";
				$init = "AND ";
			}
			else{
				$whereclause = $whereclause . " $init" . "round($type," . strlen(substr(strrchr($p2, "."), 1)) . ") = $p2";
				$init = "AND ";
			}
		}
		return array($whereclause,$init);
}


function BuildEsqCarb($esq, $whereclause){
	$incluisimul = array();
	$excluir = array();
	$incluir = array();
	$primincsom = true;
	$primex = true;
    $priminc = true;
	
	foreach($esq as $name => $inex){
		if($inex == "incsom"){
		    
			$incluisimul += array($name => $inex);
		}
		elseif($inex == 'exclui'){
		    
			$excluir += array($name => $inex);
		}
		else{
		    
			$incluir += array($name => $inex);
		}
	}
	$qtdadeincsom = count($incluisimul);
	$qtdadeexcluir = count($excluir);
	$qtdadeinc = count($incluir);
	foreach($incluisimul as $name => $inex){
		if($primincsom){
			$whereclause = $whereclause . " AND " . "(NLIN LIKE '%$name%'";
			$qtdadeincsom = $qtdadeincsom - 1;
			$primincsom = false;
		}
		else{
			$whereclause = $whereclause . " AND " . "NLIN LIKE '%$name%'";
			$qtdadeincsom = $qtdadeincsom - 1;
		}
		if($qtdadeincsom == 0){
			$whereclause = $whereclause . ")";
		}
	}
	foreach($excluir as $name => $inex){
		if($primex){
			$whereclause = $whereclause . " AND " . "(NLIN NOT LIKE '%$name%'";
			$qtdadeexcluir = $qtdadeexcluir - 1;
			$primex = false;
		}
		else{
			$whereclause = $whereclause . " AND " . "NLIN NOT LIKE '%$name%'";
			$qtdadeexcluir = $qtdadeexcluir - 1;
		}
		if($qtdadeexcluir == 0){
			$whereclause = $whereclause . ")";
		}
	}
	foreach($incluir as $name => $inex){
		if($priminc){
			$whereclause = $whereclause . " AND " . "(NLIN LIKE '%$name%'";
			$qtdadeinc = $qtdadeinc - 1;
			$priminc = false;
		}
		else{
			$whereclause = $whereclause . " OR " . "NLIN LIKE '%$name%'";
			$qtdadeinc = $qtdadeinc - 1;
		}
		if($qtdadeinc == 0){
			$whereclause = $whereclause . ")";
		}
	}
		

	return $whereclause;
	
}



?>


<html>
<head>
<style>
.container {
            padding-top:20px;
            padding-left:15px;
            padding-right:15px;
        }

/* Tooltip container */
.tooltip {
	color: #005BD6;
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
  width: 220px;
  background-color: #555;
  color: #fff;
  text-align: center;
  padding: 7px 0;
  border-radius: 6px;

  /* Position the tooltip text */
  position: absolute;
  z-index: 1;
  bottom: 125%;
  left: 50%;
  margin-left: -60px;

  /* Fade in tooltip */
  opacity: 0;
  transition: opacity 0.3s;
}

/* Tooltip arrow */
.tooltip .tooltiptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
  opacity: 1;
}
input.envia {
   background-color: #4CAF50;
	border: 1px solid transparent;
  border-radius: .75rem;
  box-sizing: border-box;
  color: #FFFFFF;
  cursor: pointer;
  flex: 0 0 auto;
  font-family: "Inter var",ui-sans-serif,system-ui,-apple-system,system-ui,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
  font-size: 1.125rem;
  font-weight: 600;
  line-height: 1.5rem;
  padding: .75rem 1.2rem;
  text-align: center;
  text-decoration: none #6B7280 solid;
  text-decoration-thickness: auto;
  transition-duration: .2s;
  transition-property: background-color,border-color,color,fill,stroke;
  transition-timing-function: cubic-bezier(.4, 0, 0.2, 1);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  width: auto;
}

input.envia:hover {
  background-color: #307433;
}

input.envia:focus {
	background-color: #555555;
  box-shadow: none;
  outline: 2px solid transparent;
  outline-offset: 2px;
}


th {
	
	border: 1px solid #dddddd;
	display: table-cell;
	vertical-align: inherit;
	font-weight: bold;
	text-align: center;
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  <!---word-break: break-word; --->
}

td{
  border: 1px solid #dddddd;
  text-align: left;
}

tr:nth-child(even) {
  background-color: #dddddd;
}

.button {
	background-color: #16747A;
	border: 1px solid transparent;
  border-radius: .75rem;
  box-sizing: border-box;
  color: #FFFFFF;
  cursor: pointer;
  flex: 0 0 auto;
  font-family: "Inter var",ui-sans-serif,system-ui,-apple-system,system-ui,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
  font-size: 1.125rem;
  font-weight: 600;
  line-height: 1.5rem;
  padding: .75rem 1.2rem;
  text-align: center;
  text-decoration: none #6B7280 solid;
  text-decoration-thickness: auto;
  transition-duration: .2s;
  transition-property: background-color,border-color,color,fill,stroke;
  transition-timing-function: cubic-bezier(.4, 0, 0.2, 1);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  width: auto;;
}
.button:hover {
  background-color: #12595E;
}

.button:focus {
	background-color: #555555;
  box-shadow: none;
  outline: 2px solid transparent;
  outline-offset: 2px;
}

h1{ color: #0C424F; font-family: 'Raleway',sans-serif; 
	font-size: 62px; 
	font-weight: 800; 
	line-height: 72px; 
	margin: 0 0 24px; 
	text-align: center; 
	background-color: #dddddd;
}
</style>
</head>
	
<body>
<div>
<h1>Analor</h1>
</div>
<div><center>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>"> 
	
	<div class="container" style="float:left;">
		<h2>Elementos</h2>
			<label for="scales">Possui Carbono?</label>
			<input type="checkbox" id="pca" name="pca" <?php echo isset($_POST['pca']) ? 'checked' : '' ?>>
			<label for="scales">Quantidade de Carbono:</label> 
			<input type="number" name="carbono" min="0" style="width: 4em;" value="<?php echo isset($_POST['carbono']) ? $_POST['carbono'] : '' ?>">
		
			<div style="clear:both;">&nbsp;</div>
		
			<label for="scales">Possui Oxigênio?</label>
			<input type="checkbox" id="pox" name="pox" <?php echo isset($_POST['pox']) ? 'checked' : '' ?>>
			<label for="scales">Quantidade de Oxigênio:</label> 
			<input type="number" name="oxigenio" min="0" style="width: 4em;" value="<?php echo isset($_POST['oxigenio']) ? $_POST['oxigenio'] : '' ?>">

			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Hidrogênio?</label>
			<input type="checkbox" id="phi" name="phi" <?php echo isset($_POST['phi']) ? 'checked' : '' ?>>
			<label for="scales">Quantidade de Hidrogênio:</label> 
			<input type="number" name="hidrogenio" min="0" style="width: 4em;" value="<?php echo isset($_POST['hidrogenio']) ? $_POST['hidrogenio'] : '' ?>">
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Nitrogênio?</label>
			<input type="checkbox" id="pni" name="pni" <?php echo isset($_POST['pni']) ? 'checked' : '' ?>>
			<label for="scales">Quantidade de Nitrogênio:</label> 
			<input type="number" name="nitrogenio" min="0" style="width: 4em;" value="<?php echo isset($_POST['nitrogenio']) ? $_POST['nitrogenio'] : '' ?>"> 
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Enxofre?</label>
			<input type="checkbox" id="pen" name="pen" <?php echo isset($_POST['pen']) ? 'checked' : '' ?>>
			<label for="scales">Quantidade de Enxofre:</label> 
			<input type="number" name="enxofre" min="0" style="width: 4em;" value="<?php echo isset($_POST['enxofre']) ? $_POST['enxofre'] : '' ?>">
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Cloro?</label>
			<input type="checkbox" id="pcl" name="pcl" <?php echo isset($_POST['pcl']) ? 'checked' : '' ?>>
			<label for="scales">Quantidade de Cloro:</label> 
			<input type="number" name="cloro" min="0" style="width: 4em;" value="<?php echo isset($_POST['cloro']) ? $_POST['cloro'] : '' ?>">
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Bromo?</label>
			<input type="checkbox" id="pbr" name="pbr" <?php echo isset($_POST['pbr']) ? 'checked' : '' ?>>
			<label for="scales">Quantidade de Bromo:</label> 
			<input type="number" name="bromo" min="0" style="width: 4em;" value="<?php echo isset($_POST['bromo']) ? $_POST['bromo'] : '' ?>">
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Iodo?</label>
			<input type="checkbox" id="pio" name="pio" <?php echo isset($_POST['pio']) ? 'checked' : '' ?>>
			<label for="scales">Quantidade de Iodo:</label> 
			<input type="number" name="iodo" min="0" style="width: 4em;" value="<?php echo isset($_POST['iodo']) ? $_POST['iodo'] : '' ?>">
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Fluor?</label>
			<input type="checkbox" id="pfl" name="pfl" <?php echo isset($_POST['pfl']) ? 'checked' : '' ?>>
			<label for="scales">Quantidade de Fluor:</label> 
			<input type="number" name="fluor" min="0" style="width: 4em;" value="<?php echo isset($_POST['fluor']) ? $_POST['fluor'] : '' ?>">
		
		<div style="clear:both;">&nbsp;</div>
		
		</label><div class="tooltip">Modo de uso para busca por elementos
		<span class="tooltiptext">Marque a caixa "possui" dos elementos que tenha certeza de sua presença e/ou sua quantidade se souber. Caso não saiba se há ou não presença, deixe os campos deste elemento em branco. Caso tenha certeza de que não há presença, digite sua quantidade = 0, e deixe a caixa "possui" desmarcada.</span>
		</div>
		<div style="clear:both;">&nbsp;</div>
	</div>
	
	<div class = "container" style="float:left;">
		<h2>Propriedades</h2>
		<label for="scales">Peso Molecular</label>
		<input type="number" step="0.01" min = "0" name = "pesomol1" style="width: 6em;" value="<?php echo isset($_POST['pesomol1']) ? $_POST['pesomol1'] : '' ?>"> à <input type="number" min = "0" step="0.01" name = "pesomol2" style="width: 6em;" value="<?php echo isset($_POST['pesomol2']) ? $_POST['pesomol2'] : '' ?>"> 
		<div style="clear:both;">&nbsp;</div>
	
	
		<label for="scales">Ponto de Fusão</label>
		<input type="number" step="0.01" min = "0" name = "pf1" style="width: 6em;" value="<?php echo isset($_POST['pf1']) ? $_POST['pf1'] : '' ?>"> à <input type="number" min = "0" step="0.01" name = "pf2" style="width: 6em;" value="<?php echo isset($_POST['pf2']) ? $_POST['pf2'] : '' ?>">
		<div style="clear:both;">&nbsp;</div>

		<label for="scales">Ponto de Ebulição</label>
		<input type="number" step="0.01" min = "0" name = "pe1" style="width: 6em;" value="<?php echo isset($_POST['pe1']) ? $_POST['pe1'] : '' ?>"> à <input type="number" min = "0" step="0.01" name = "pe2" style="width: 6em;"value="<?php echo isset($_POST['pe2']) ? $_POST['pe2'] : '' ?>">
		<div style="clear:both;">&nbsp;</div>
		</div>
		
		<div class = "container" style="float:left;">
		<h2>Grupo Funcional / Esqueleto de Carbono</h2>

		
		<input type="text" name="gfunc1" maxlength="10" value="<?php echo isset($_POST['gfunc1']) ? $_POST['gfunc1'] : '' ?>">
		<label><input type="radio" name="inex1" value="inclui" id="inclui" <?php echo (!isset($_POST['inex1']) or (isset($_POST['inex1']) and ($_POST['inex1'] == 'inclui'))) ? 'checked' : '' ?>>Incluir</label>
		<label><input type="radio" name="inex1" value="incsom" id="exclui" <?php echo (isset($_POST['inex1']) and ($_POST['inex1'] == 'incsom')) ? 'checked' : '' ?>>Incluir simultâneo</label>
		<label><input type="radio" name="inex1" value="exclui" id="exclui" <?php echo (isset($_POST['inex1']) and ($_POST['inex1'] == 'exclui')) ? 'checked' : '' ?>>Excluir</label>
		
		
		<div style="clear:both;">&nbsp;</div>
		
		<input type="text" name="gfunc2" maxlength="10" value="<?php echo isset($_POST['gfunc2']) ? $_POST['gfunc2'] : '' ?>">
		<label><input type="radio" name="inex2" value="inclui" id="inclui" <?php echo (!isset($_POST['inex2']) or (isset($_POST['inex2']) and ($_POST['inex2'] == 'inclui'))) ? 'checked' : '' ?>>Incluir</label>
		<label><input type="radio" name="inex2" value="incsom" id="exclui" <?php echo (isset($_POST['inex2']) and ($_POST['inex2'] == 'incsom')) ? 'checked' : '' ?>>Incluir simultâneo</label>
		<label><input type="radio" name="inex2" value="exclui" id="exclui" <?php echo (isset($_POST['inex2']) and ($_POST['inex2'] == 'exclui')) ? 'checked' : '' ?>>Excluir</label>
		
		<div style="clear:both;">&nbsp;</div>
		
		<input type="text" name="gfunc3" maxlength="10" value="<?php echo isset($_POST['gfunc3']) ? $_POST['gfunc3'] : '' ?>">
		<label><input type="radio" name="inex3" value="inclui" id="inclui" <?php echo (!isset($_POST['inex3']) or (isset($_POST['inex3']) and ($_POST['inex3'] == 'inclui'))) ? 'checked' : '' ?>>Incluir</label>
		<label><input type="radio" name="inex3" value="incsom" id="exclui" <?php echo (isset($_POST['inex3']) and ($_POST['inex3'] == 'incsom')) ? 'checked' : '' ?>>Incluir simultâneo</label>
		<label><input type="radio" name="inex3" value="exclui" id="exclui" <?php echo (isset($_POST['inex3']) and ($_POST['inex3'] == 'exclui')) ? 'checked' : '' ?>>Excluir</label>
		
		<div style="clear:both;">&nbsp;</div>
	
		<input type="text" name="gfunc4" maxlength="10" value="<?php echo isset($_POST['gfunc4']) ? $_POST['gfunc4'] : '' ?>">
		<label><input type="radio" name="inex4" value="inclui" id="inclui" <?php echo (!isset($_POST['inex4']) or (isset($_POST['inex4']) and ($_POST['inex4'] == 'inclui'))) ? 'checked' : '' ?>>Incluir</label>
		<label><input type="radio" name="inex4" value="incsom" id="exclui" <?php echo (isset($_POST['inex4']) and ($_POST['inex4'] == 'incsom')) ? 'checked' : '' ?>>Incluir simultâneo</label>
		<label><input type="radio" name="inex4" value="exclui" id="exclui" <?php echo (isset($_POST['inex4']) and ($_POST['inex4'] == 'exclui')) ? 'checked' : '' ?>>Excluir</label>
		
		<div style="clear:both;">&nbsp;</div>
		
		<input type="text" name="gfunc5" maxlength="10" value="<?php echo isset($_POST['gfunc5']) ? $_POST['gfunc5'] : '' ?>">
		<label><input type="radio" name="inex5" value="inclui" id="inclui"  <?php echo (!isset($_POST['inex5']) or (isset($_POST['inex5']) and ($_POST['inex5'] == 'inclui'))) ? 'checked' : '' ?>>Incluir</label>
		<label><input type="radio" name="inex5" value="incsom" id="exclui" <?php echo (isset($_POST['inex5']) and ($_POST['inex5'] == 'incsom')) ? 'checked' : '' ?>>Incluir simultâneo</label>
		<label><input type="radio" name="inex5" value="exclui" id="exclui" <?php echo (isset($_POST['inex5']) and ($_POST['inex5'] == 'exclui')) ? 'checked' : '' ?>>Excluir</label>
		
		<div style="clear:both;">&nbsp;</div>		
		
		<input type="text" name="gfunc6" maxlength="10" value="<?php echo isset($_POST['gfunc6']) ? $_POST['gfunc6'] : '' ?>">
		<label><input type="radio" name="inex6" value="inclui" id="inclui" <?php echo (!isset($_POST['inex6']) or (isset($_POST['inex6']) and ($_POST['inex6'] == 'inclui'))) ? 'checked' : '' ?>>Incluir</label>
		<label><input type="radio" name="inex6" value="incsom" id="exclui" <?php echo (isset($_POST['inex6']) and ($_POST['inex6'] == 'incsom')) ? 'checked' : '' ?>>Incluir simultâneo</label>
		<label><input type="radio" name="inex6" value="exclui" id="exclui" <?php echo (isset($_POST['inex6']) and ($_POST['inex6'] == 'exclui')) ? 'checked' : '' ?>>Excluir</label>
		
		<div style="clear:both;">&nbsp;</div>
				<div class="tooltip" >Modo de uso para busca por ECGF (Referencias)
			<span class="tooltiptext">Colocar na área de texto a abreviação referente ao grupo funcional (a referência a abreviação fica ao lado do botão "aplicar filtro")</span>
		</div>
		<div style="clear:both;">&nbsp;</div>
		</label><div class="tooltip" >Modo de uso para busca por ECGF (Botões)
			<span class="tooltiptext">Incluir: seleciona as moléculas com os grupos assinalados (não necessariamente na mesma molécula)<br><br>
Incluir simultâneo: seleciona as moléculas com os grupos assinalados (na mesma molécula)<br><br>
Excluir: elimina as moléculas com os grupos assinalados.</span>
		</div>

		</div>
			
			<div class = "container" style="float:left;">
			<h2>CAS</h2>
			<label for="scales">CAS: </label>
			<input type="text" name="cas" maxlength="15" value="<?php echo isset($_POST['cas']) ? $_POST['cas'] : '' ?>">
			
		</div>
		<div style="clear:both;">&nbsp;</div>
		<div class = "container" style="float:none;">
		<a href="/ref.php" class = "button" target = "_blank">Referencia Grup Func & Esq Carbono</a>
	
		<input type="submit" value = "Aplicar filtro" class="envia">
		<div style="clear:both;">&nbsp;</div>
		</div>

		<center>
	
	
	
</form>
</div>



<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // collect value of input field

	

	$apenaspossui = array(); //array associativo onde a chave é o nome da coluna
	$filtros = array();  //array associativo onde a chave é o nome da coluna
	$arrayescarb = array();
	$oxigenio = $_POST['oxigenio'];
	$hidrogenio = $_POST['hidrogenio'];
	$carbono = $_POST['carbono'];
	$nitrogenio = $_POST['nitrogenio'];
	$enxofre = $_POST['enxofre'];
	$cloro = $_POST['cloro'];
	$bromo = $_POST['bromo'];
	$iodo = $_POST['iodo'];
	$fluor = $_POST['fluor'];
	$pesomol1 = $_POST['pesomol1'];
	$pesomol2 = $_POST['pesomol2'];
	$pca = isset($_POST['pca']);
	$pox = isset($_POST['pox']);
	$phi = isset($_POST['phi']);
	$pni = isset($_POST['pni']);
	$pen = isset($_POST['pen']);
	$pcl = isset($_POST['pcl']);
	$pbr = isset($_POST['pbr']);
	$pio = isset($_POST['pio']);
	$pfl = isset($_POST['pfl']);
	$pf1 = $_POST['pf1'];
	$pf2 = $_POST['pf2'];
	$pe1 = $_POST['pe1'];
	$pe2 = $_POST['pe2'];
	$gf1 = $_POST['gfunc1'];
	$gf2 = $_POST['gfunc2'];
	$gf3 = $_POST['gfunc3'];
	$gf4 = $_POST['gfunc4'];
	$gf5 = $_POST['gfunc5'];
	$gf6 = $_POST['gfunc6'];
	$ie1 = $_POST['inex1'];
	$ie2 = $_POST['inex2'];
	$ie3 = $_POST['inex3'];
	$ie4 = $_POST['inex4'];
	$ie5 = $_POST['inex5'];
	$ie6 = $_POST['inex6'];
	$cas = $_POST['cas'];
	$npospm1 = false; // variavel diz se não foi especificado peso molecular no primeiro parametro
	$npospm2 = false; // variavel diz se não foi especificado peso molecular no segundo parametro
	$npf1 = false; // ponto de fusão não especificado
	$npf2 = false; // ponto de ebulição não especificado
	$npe1 = false;
	$npe2 = false;
	$pesq = false; //possui esqueleto de carbono
	$qtdesq = 0;
	elementsvar($carbono, $pca, "carbono", "CARB");
	elementsvar($oxigenio, $pox, "oxigênio", "OXIG");
	elementsvar($hidrogenio, $phi, "hidrogênio", "HIDR");
	elementsvar($nitrogenio, $pni, "nitrogênio", "NITR");
	elementsvar($enxofre, $pen, "enxofre", "ENXO");
	elementsvar($cloro, $pcl, "cloro", "CLOR");
	elementsvar($bromo, $pbr, "bromo", "BROM");
	elementsvar($iodo, $pio, "iodo", "IODO");
	elementsvar($fluor, $pfl, "fluor", "FLUO");

	if(_empty($pesomol1)){
		$npospm1 = true;
	}
	if(_empty($pesomol2)){
		$npospm2 = true;
	}
	if(_empty($pf1)){
		$npf1 = true;
	}
	if(_empty($pf2)){
		$npf2 = true;
	}
	if(_empty($pe1)){
		$npe1 = true;
	}
	if(_empty($pe2)){
		$npe2 = true;
	}
	if(!(_empty($gf1))){
		$arrayescarb += array($gf1=>$ie1);	
		$pesq = true;
	}
	if(!(_empty($gf2))){
		$arrayescarb += array($gf2=>$ie2);
		$pesq = true;
	}
	if(!(_empty($gf3))){
		$arrayescarb += array($gf3=>$ie3);
		$pesq = true;
	}
	if(!(_empty($gf4))){
		$arrayescarb += array($gf4=>$ie4);
		$pesq = true;
	}	
	if(!(_empty($gf5))){
		$arrayescarb += array($gf5=>$ie5);
		$pesq = true;
	}	
	if(!(_empty($gf6))){
		$arrayescarb += array($gf6=>$ie6);
		$pesq = true;
	}		
	if(empty($filtros) and empty($apenaspossui) and $npospm1 and $npospm2 and $npf1 and $npf2 and $npe1 and $npe2 and !$pesq and (_empty($cas))){

		$whereclause="";
	}
	//montando o whereclause
	else{
		$whereclause="WHERE";
		$init = ""; //variavel que diz que nada foi adicionado ao whereclause e serve como ligação entre os filtros
		$primesq = true; //saber se não será feita avaliação de esqueleto de carbono
		foreach($filtros as $key => $value){
				$whereclause = $whereclause . " $init". "$key = $value"; //key é o nome da coluna na base de dados
				$init = "AND ";
		}
		
		foreach($apenaspossui as $key => $value){
			$whereclause = $whereclause . " $init" . "$key > 0"; //key é o nome da coluna na base de dados
			$init = "AND ";
		}
		$arpmolar = BuildProp($npospm1, $npospm2, $pesomol1, $pesomol2, $whereclause, $init, 'PMOL');
		$arpfusao = BuildProp($npf1, $npf2, $pf1, $pf2, $arpmolar[0], $arpmolar[1],'PF');
		$arpebul = BuildProp($npe1, $npe2, $pe1, $pe2, $arpfusao[0], $arpfusao[1], 'PE');
		$whereclause = $arpebul[0];
		$whereclause = BuildEsqCarb($arrayescarb, $whereclause);
		$init = $argf6[1];

		if(!(_empty($cas))){
			$whereclause = $whereclause . " $init" . "CAS LIKE '$cas'";
		}
	}
}
?>

<?php

$order = "ORDER BY NCOM";
#echo $whereclause;
if(!empty($whereclause)){
	$result = $conn->query("SELECT * FROM UNIV1_090722 $whereclause $order");
}
else{
	
	$result = $conn->query("SELECT * FROM UNIV1_090722");
}
if(empty($result->num_rows)){
	echo"<div style=\"clear:both;\">&nbsp;</div>Numero de registros na tabela = 0";
}
else{
	echo"<div style=\"clear:both;\">&nbsp;</div>Numero de registros na tabela = $result->num_rows";
}
echo"<table>
<tr>
	<th colspan=\"7\">Identificadores</th>
	<th colspan=\"3\">Propriedades físicas</th>
	<th colspan=\"2\">Dados Espectometricos</th>
</tr>
<tr>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">Nome comum</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">CAS</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">SMILES</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">Nome Linear(ECGF)</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">Formula Linear</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">FM</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">PM</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">PF</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">Peb</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">Pfder</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">IV</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">EM</th>
</tr>";
if ($result) {
	if ($result->num_rows > 0) {
							
		while ($row = $result->fetch_assoc()) {	
			$ncom = $row["NCOM"];
			$cas = $row["CAS"];
			$smiles = $row["SMILES"]; 
			$nlin = $row["NLIN"];
			$flin = $row["FLIN"];
			$fmol = $row["FMOL"];
			$pmol = $row["PMOL"];
			$pf = $row["PF"];
			$pe = $row["PE"];
			$pfder = $row['PFDER'];
			//$iv = $row['iv'];
			//$em = $row['em'];
			echo "<tr>";
			echo "<td> $ncom </td>";
			echo "<td> $cas </td>";
			echo "<td> $smiles </td>";
			echo "<td> $nlin </td>";
			echo "<td> $flin </td>";
			echo "<td> $fmol </td>";
			echo "<td> $pmol </td>";
			echo "<td> $pf </td>";
			echo "<td> $pe </td>";
			echo "<td> $pfder </td>";
			echo "<td> info </td>";
			echo "<td> info </td>";
			echo "</tr>";
		}
	}
	else{
		echo "<p> Não foram encontrados registros para a filtragem </p>";
	}
}


?>							
</table>
</body>


</html>