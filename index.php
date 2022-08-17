<?php
require "connect.php";
include_once("script.php");
?>


<html>
<head>
<link rel="stylesheet" href="style.css">
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
</table>
</body>


</html>