<?php
$servername = "localhost";
$username = "admin";
$password = "analor159";
$dbname = "test";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
else {
	echo "sucesso";
}
?>


<html>
<head>
php test
</head>
 
<body><table style ="width:100%">
<tr>
	<th>Nome comum</th>
</tr>
<?php
if ($result = $conn->query("SELECT ncom from cam151 - ic super misaman")) {
    if ($result->num_rows > 0) {
                            
                            while ($row = $result->fetch_assoc()) {
                            $ncom = $row["ncom"];
							echo "<tr>";
                            echo "<tr> $ncom </tr>";
							echo "</tr>";
							?>
							
</table>
</body>


</html>