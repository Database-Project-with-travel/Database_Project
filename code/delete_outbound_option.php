<?php
header("Content-Type:text/html;charset=utf-8");
$servername = "localhost";
$username = "project";
$password = "project";
$dbname = "project_travel";

try{
	echo "!!!若選填不完全，會重新導向至上一頁。!!!<br><br>";
	session_start();

	$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8",
	$username,$password);
	$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    


	echo "<form method=\"post\" action=\"/delete_outbound_sql.php\">";

	echo "請選擇想刪除的資料id：<br>";
	$sql = "select id from user_outbound;";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	foreach($result as $row){
		foreach($row as $k => $v)
		{
			if($v == "") 
				continue;
			$cnt += 1;
			echo "<input type=\"radio\" name=\"id\" value=\"$v\">$v";
			echo "&nbsp";
			if($cnt % 10 == 0)
				echo '<br>';
		}
	}
	echo "<br><br>";

	echo "<br>";
	echo "<input type=\"submit\" value=\"Submit\">";
	echo "</form>";
	echo "<form method=\"post\" action=\"/home_page.php\">";
	echo "<input type=\"submit\" value=\"Back\">";
	echo "</form>";

	echo "可刪除的資料：<br>";
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

	echo "<br";

}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
$conn = null;
#echo "</table>";
?>
