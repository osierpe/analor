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

function BuildProp($pos1, $pos2, $p1, $p2, $whereclause, $init){
		if($pos1 and !$pos2){ //só o 2 definido
			$whereclause = $whereclause . " $init" . "round(PMOL," . strlen(substr(strrchr($p2, "."), 1)) . ") = $p2";
			$init = "AND ";
		}
		elseif($pos2 and !$pos1){ //só o 1 definido
			$whereclause = $whereclause . " $init" . "round(PMOL," . strlen(substr(strrchr($p1, "."), 1)) . ") = $p1";
			$init = "AND ";
		}
		elseif(!$pos2 and !$pos1){ //os dois definidos
			if($p1 > $p2){
				$whereclause = $whereclause . " $init" . "round(PMOL," . strlen(substr(strrchr($p2, "."), 1)) . ") >= $p2 AND round(PMOL," . strlen(substr(strrchr($p1, "."), 1)) . ") <= $p1";
				$init = "AND ";
			}
			elseif($p1 < $p2){
				$whereclause = $whereclause . " $init" . "round(PMOL," . strlen(substr(strrchr($p2, "."), 1)) . ") <= $p2 AND round(PMOL," . strlen(substr(strrchr($p1, "."), 1)) . ") >= $p1";
				$init = "AND ";
			}
			else{
				$whereclause = $whereclause . " $init" . "round(PMOL," . strlen(substr(strrchr($p2, "."), 1)) . ") = $p2";
				$init = "AND ";
			}
		}
		return array($whereclause,$init);
}

function BuildEsqCarb($primesq, $ie, $gf, $qtdesq, $whereclause, $init){
	if(!(_empty($gf))){
		if($primesq){
			if($ie == 'exclui'){
				$whereclause = $whereclause . " $init" . "(NLIN NOT LIKE '%$gf%'";
			}
			else{
				$whereclause = $whereclause . " $init" . "(NLIN LIKE '%$gf%'";
			}
			$primesq = false;
			$qtdesq = $qtdesq - 1;
			if($qtdesq == 0){
				$whereclause = $whereclause . ")";
			}
		}
		else{
			if($ie == 'inclui'){
				$whereclause = $whereclause . " OR " . "NLIN LIKE '%$gf%'";
			}
			elseif($ie == "incsom"){
				$whereclause = $whereclause . " AND " . "NLIN LIKE '%$gf%' ";
			}
			else{
				$whereclause = $whereclause . " AND " . "NLIN NOT LIKE '%$gf%'";
			}
			$qtdesq = $qtdesq - 1;
			if($qtdesq == 0){
				$whereclause = $whereclause . ")";
			}
		}
	}
	$init = "AND ";
	return array($whereclause, $init, $primesq, $qtdesq);
	
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
			<input type="checkbox" id="pca" name="pca">
			<label for="scales">Quantidade de Carbono:</label> 
			<input type="number" name="carbono" min="0" style="width: 4em;">
		
			<div style="clear:both;">&nbsp;</div>
		
			<label for="scales">Possui Oxigênio?</label>
			<input type="checkbox" id="pox" name="pox">
			<label for="scales">Quantidade de Oxigênio:</label> 
			<input type="number" name="oxigenio" min="0" style="width: 4em;">

			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Hidrogênio?</label>
			<input type="checkbox" id="phi" name="phi">
			<label for="scales">Quantidade de Hidrogênio:</label> 
			<input type="number" name="hidrogenio" min="0" style="width: 4em;">
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Nitrogênio?</label>
			<input type="checkbox" id="pni" name="pni">
			<label for="scales">Quantidade de Nitrogênio:</label> 
			<input type="number" name="nitrogenio" min="0" style="width: 4em;">
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Enxofre?</label>
			<input type="checkbox" id="pen" name="pen">
			<label for="scales">Quantidade de Enxofre:</label> 
			<input type="number" name="enxofre" min="0" style="width: 4em;">
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Cloro?</label>
			<input type="checkbox" id="pcl" name="pcl">
			<label for="scales">Quantidade de Cloro:</label> 
			<input type="number" name="cloro" min="0" style="width: 4em;">
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Bromo?</label>
			<input type="checkbox" id="pbr" name="pbr">
			<label for="scales">Quantidade de Bromo:</label> 
			<input type="number" name="bromo" min="0" style="width: 4em;">
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Iodo?</label>
			<input type="checkbox" id="pio" name="pio">
			<label for="scales">Quantidade de Iodo:</label> 
			<input type="number" name="iodo" min="0" style="width: 4em;">
			
			<div style="clear:both;">&nbsp;</div>
			
			<label for="scales">Possui Fluor?</label>
			<input type="checkbox" id="pfl" name="pfl">
			<label for="scales">Quantidade de Fluor:</label> 
			<input type="number" name="fluor" min="0" style="width: 4em;">
		
		<div style="clear:both;">&nbsp;</div>
		
		</label><div class="tooltip">Modo de uso para busca por elementos
		<span class="tooltiptext">Marque a caixa "possui" dos elementos que tenha certeza de sua presença e/ou sua quantidade se souber. Caso não saiba se há ou não presença, deixe os campos deste elemento em branco. Caso tenha certeza de que não há presença, digite sua quantidade = 0, e deixe a caixa "possui" desmarcada.</span>
		</div>
		<div style="clear:both;">&nbsp;</div>
	</div>
	
	<div class = "container" style="float:left;">
		<h2>Propriedades</h2>
		<label for="scales">Peso Molecular</label>
		<input type="number" step="0.01" min = "0" name = "pesomol1" style="width: 6em;"> à <input type="number" min = "0" step="0.01" name = "pesomol2" style="width: 6em;"> 
		<div style="clear:both;">&nbsp;</div>
	
	
		<label for="scales">Ponto de Fusão</label>
		<input type="number" step="0.01" min = "0" name = "pf1" style="width: 6em;"> à <input type="number" min = "0" step="0.01" name = "pf2" style="width: 6em;">
		<div style="clear:both;">&nbsp;</div>

		<label for="scales">Ponto de Ebulição</label>
		<input type="number" step="0.01" min = "0" name = "pe1" style="width: 6em;"> à <input type="number" min = "0" step="0.01" name = "pe2" style="width: 6em;">
		<div style="clear:both;">&nbsp;</div>
		</div>
		
		<div class = "container" style="float:left;">
		<h2>Grupo Funcional / Esqueleto de Carbono</h2>

		
		<input type="text" name="gfunc1" maxlength="10">
		<label><input type="radio" name="inex1" value="inclui" id="inclui" checked>Incluir</label>
		<label><input type="radio" name="inex1" value="incsom" id="exclui">Incluir simultâneo</label>
		<label><input type="radio" name="inex1" value="exclui" id="exclui">Excluir</label>
		
		
		<div style="clear:both;">&nbsp;</div>
		
		<input type="text" name="gfunc2" maxlength="10">
		<label><input type="radio" name="inex2" value="inclui" id="inclui" checked>Incluir</label>
		<label><input type="radio" name="inex2" value="incsom" id="exclui">Incluir simultâneo</label>
		<label><input type="radio" name="inex2" value="exclui" id="exclui">Excluir</label>
		
		<div style="clear:both;">&nbsp;</div>
		
		<input type="text" name="gfunc3" maxlength="10">
		<label><input type="radio" name="inex3" value="inclui" id="inclui" checked>Incluir</label>
		<label><input type="radio" name="inex3" value="incsom" id="exclui">Incluir simultâneo</label>
		<label><input type="radio" name="inex3" value="exclui" id="exclui">Excluir</label>
		
		<div style="clear:both;">&nbsp;</div>
	
		<input type="text" name="gfunc4" maxlength="10">
		<label><input type="radio" name="inex4" value="inclui" id="inclui" checked>Incluir</label>
		<label><input type="radio" name="inex4" value="incsom" id="exclui">Incluir simultâneo</label>
		<label><input type="radio" name="inex4" value="exclui" id="exclui">Excluir</label>
		
		<div style="clear:both;">&nbsp;</div>
		
		<input type="text" name="gfunc5" maxlength="10">
		<label><input type="radio" name="inex5" value="inclui" id="inclui" checked>Incluir</label>
		<label><input type="radio" name="inex5" value="incsom" id="exclui">Incluir simultâneo</label>
		<label><input type="radio" name="inex5" value="exclui" id="exclui">Excluir</label>
		
		<div style="clear:both;">&nbsp;</div>		
		
		<input type="text" name="gfunc6" maxlength="10">
		<label><input type="radio" name="inex6" value="inclui" id="inclui" checked>Incluir</label>
		<label><input type="radio" name="inex6" value="incsom" id="exclui">Incluir simultâneo</label>
		<label><input type="radio" name="inex6" value="exclui" id="exclui">Excluir</label>
		
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
			<input type="text" name="cas" maxlength="15">
			
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
		$pesq = true;
		$qtdesq = $qtdesq + 1;
	}
	if(!(_empty($gf2))){
		$pesq = true;
		$qtdesq = $qtdesq + 1;
	}
	if(!(_empty($gf3))){
		$pesq = true;
		$qtdesq = $qtdesq + 1;
	}
	if(!(_empty($gf4))){
		$pesq = true;
		$qtdesq = $qtdesq + 1;
	}	
	if(!(_empty($gf5))){
		$pesq = true;
		$qtdesq = $qtdesq + 1;
	}	
	if(!(_empty($gf6))){
		$pesq = true;
		$qtdesq = $qtdesq + 1;
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
		$arpmolar = BuildProp($npospm1, $npospm2, $pesomol1, $pesomol2, $whereclause, $init);
		$arpfusao = BuildProp($npf1, $npf2, $pf1, $pf2, $arpmolar[0], $arpmolar[1]);
		$arpebul = BuildProp($npe1, $npe2, $pe1, $pe2, $arpfusao[0], $arpfusao[1]);
		$argf1 = BuildEsqCarb($primesq, $ie1, $gf1, $qtdesq, $arpebul[0], $arpebul[1]);
		$argf2 = BuildEsqCarb($argf1[2], $ie2, $gf2, $argf1[3], $argf1[0], $argf1[1]);
		$argf3 = BuildEsqCarb($argf2[2], $ie3, $gf3, $argf2[3], $argf2[0], $argf2[1]);
		$argf4 = BuildEsqCarb($argf3[2], $ie4, $gf4, $argf3[3], $argf3[0], $argf3[1]);
		$argf5 = BuildEsqCarb($argf4[2], $ie5, $gf5, $argf4[3], $argf4[0], $argf4[1]);
		$argf6 = BuildEsqCarb($argf5[2], $ie6, $gf6, $argf5[3], $argf5[0], $argf5[1]);
		$whereclause = $argf6[0];
		$init = $argf6[1];

		if(!(_empty($cas))){
			$whereclause = $whereclause . " $init" . "CAS LIKE '$cas'";
		}
	}
}
?>

<?php



if(!empty($whereclause)){
	$result = $conn->query("SELECT * FROM UNIV1_160222 $whereclause");
	echo "$whereclause";
}
else{
	
	$result = $conn->query("SELECT * FROM UNIV1_160222");
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