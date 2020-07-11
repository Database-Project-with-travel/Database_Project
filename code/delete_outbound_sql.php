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

	$sql = "delete from user_outbound where id = ";
	
	if($_POST)
	{

		foreach($_POST as $k => $v)
		{
			$sql = $sql.$v.";";
			$cnt++;
            	}

		if($cnt != 1)
		    header("Location: /delete_outbound_option.php\n");

	}

	#echo $sql;
	$stmt = $conn->prepare($sql);
	$stmt->execute();

	echo "<form method=\"post\" action=\"/home_page.php\">";
	echo "<input type=\"submit\" value=\"Go back to home page\">";
	echo "</form>";

	echo "<form method=\"post\" action=\"/delete_outbound_option.php\">";
	echo "<input type=\"submit\" value=\"Go back to delete_outbound_option\">";
	echo "</form>";

	echo "<table style='border: solid 1px black;'>";
	echo "<tr><th>ID</th><th>年</th><th>月</th><th>居住地</th><th>年齡</th><th>華僑／外籍</th><th>性別</th><th>職業</th><th>來臺原因</th><th>交通方式</th><th>";

	$sql = "select * from user_outbound;";
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
