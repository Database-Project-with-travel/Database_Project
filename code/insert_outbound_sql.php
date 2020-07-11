<?php
header("Content-Type:text/html;charset=utf-8");

$servername = "localhost";
$username = "project";
$password = "project";
$dbname = "project_travel";

try{
	$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8",
		$username,$password);
	$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	
	$sql = "";
    	$cnt = 0; //use to check if data is not enough

	$sql = "insert into insert_outbound (年, 月, 國家名稱, 年齡, 性別, 交通方式) values (";
	
	if($_POST)
	{

		foreach($_POST as $k => $v)
		{
			if ($k == "year" || $k == "month")
				$sql = $sql.$v.", ";
			else
				$sql = $sql."'".$v."', ";
			$cnt++;
            	}

		if($cnt != 6)
		    header("Location: /insert_outbound_option.php\n");

	}
	$sql = rtrim($sql,", ");
	$sql = $sql.");";

	echo $sql;
	$stmt = $conn->prepare($sql);
	$stmt->execute();

	echo "<table style='border: solid 1px black;'>";
	echo "<tr><th>ID</th><th>年</th><th>月</th><th>國家名稱</th><th>年齡</th><th>性別</th><th>交通方式</th><th>";

	$sql = "select * from insert_outbound;";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
