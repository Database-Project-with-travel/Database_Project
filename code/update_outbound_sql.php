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
	$cnt = 0;
	$sql = "update user_outbound set ";
#print_r($_POST);
	
	if($_POST)
	{
		foreach($_POST as $k => $v)
		{

			if($k == "id")
		                $cnt++;
			else if ($k == "年" || $k == "月")
				$sql = $sql.$k." = ".$v.", ";
			else
				$sql = $sql.$k." = '".$v."', ";
            	}
	}

	if ($cnt == 0)
		header("Location: /update_outbound_option.php\n");
	$sql = rtrim($sql,"', ");
	
	$sql = $sql."' where id = ".$_POST["id"].";";

	echo $sql;
	$stmt = $conn->prepare($sql);
	$stmt->execute();

	echo "<form method=\"post\" action=\"/home_page.php\">";
	echo "<input type=\"submit\" value=\"Go back to home page\">";
	echo "</form>";

	echo "<form method=\"post\" action=\"/update_outbound_option.php\">";
	echo "<input type=\"submit\" value=\"Go back to update_outbound_option\">";
	echo "</form>";

	echo "<table style='border: solid 1px black;'>";
	echo "<tr><th>ID</th><th>年</th><th>月</th><th>國家名稱</th><th>年齡</th><th>性別</th><th>交通方式</th><th>";

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
