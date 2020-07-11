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

	$sql = "insert into user_inbound (年, 月, 居住地, 年齡, 華僑／外籍, 性別, 職業, 來臺原因, 交通方式) values (";
	
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

		if($cnt != 9)
		    header("Location: /insert_inbound_option.php\n");

	}
	$sql = rtrim($sql,", ");
	$sql = $sql.");";

	echo $sql;
	$stmt = $conn->prepare($sql);
	$stmt->execute();

	echo "<table style='border: solid 1px black;'>";
	echo "<tr><th>ID</th><th>年</th><th>月</th><th>居住地</th><th>年齡</th><th>華僑／外籍</th><th>性別</th><th>職業</th><th>來臺原因</th><th>交通方式</th><th>";

	$sql = "select * from user_inbound;";
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
