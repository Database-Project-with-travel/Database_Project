<?php
header("Content-Type:text/html;charset=utf-8");

echo "<table style='border: solid 1px black;'>";
#echo "<tr><th>年</th><th>月</th><th>居住地</th><th>男</th><th>女
#</th></tr>";

$servername = "localhost";
$username = "project";
$password = "project";
$dbname = "project_travel";

#print_r($_POST);
#print_r($_POST['query_time']);
#echo $_POST['query_time'];

try{
	$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8",
		$username,$password);
	$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	
	#$stmt = $conn->prepare("set names 'utf8'");
	#$stmt->execute();

	$sql = "SELECT query_sql from user_history where query_time =\"".$_POST['query_time']."\";";

	#echo $sql;
	$stmt = $conn->prepare($sql);
	$stmt->execute();

	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	#print_r($result);
	foreach($result as $row){
		foreach($row as $k => $v){
			$sql = $v.";";
		}
	}

	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	#print_r($result[0]);
	echo "<tr>";
	foreach($result[0] as $k => $v){
		echo "<th>".$k."</th>";
	}
	echo "</tr>";

	foreach($result as $row){
		echo "<tr>";
		foreach($row as $k => $v){
			echo "<td style='width:150px;border:1px solid black;'>".$v."</td>";
		}
		echo "</tr>";
	}

}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
$conn = null;
echo "</table>";

?>
