<?php
require "connect.php";

?>
<html>
<head>
<style>
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
}

td{
  border: 1px solid #dddddd;
  text-align: left;
}

tr:nth-child(even) {
  background-color: #dddddd;
}

</style>
</head>
	
<body>

<?php


echo"<table>

<tr>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">Nome</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">NLin</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">Elemento</th>
	<th style = \"top: 0; position: -webkit-sticky; position: sticky;background-color: #B1CBCE;\">P Chave</th>
</tr>";

$result = $conn->query("SELECT * FROM abrev");
if ($result) {
	if ($result->num_rows > 0) {
							
		while ($row = $result->fetch_assoc()) {	
			$nome = $row["NOME"];
			$nlin = $row["NLIN"];
			$elemento = $row["ELEMENTO"]; 
			$pchave = $row["PCHAVE"];

			echo "<tr>";
			echo "<td> $nome </td>";
			echo "<td> $nlin </td>";
			echo "<td> $elemento </td>";
			echo "<td> $pchave </td>";
			echo "</tr>";
		}
	}
}

?>							
</table>

</body>


</html>

