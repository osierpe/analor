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

$order = "ORDER BY NCOM";
#echo $whereclause;
if(!empty($whereclause)){
	$result = $conn->query("SELECT * FROM UNIV1_010822 $whereclause $order");
}
else{
	
	$result = $conn->query("SELECT * FROM UNIV1_010822");
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

