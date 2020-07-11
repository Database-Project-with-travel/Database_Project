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

	$stmt = $conn->prepare("select query_time from user_history order by query_time desc limit 15;");
	$stmt->execute();

	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	#print_r($result);

	echo "<form method=\"post\" action=\"/user_history_sql.php\">";
	echo "請選擇歷史搜尋時間：";
	echo "<select name=\"query_time\">";
      	#method=\"post\" action=\"/user_history_sql.php\">";
	foreach($result as $row){
		#echo "<option>";
		foreach($row as $k => $v){
			echo "<option value=\"".$v."\">";
			echo $v;
		}
		echo "</option>";
	}
	echo "<br>";
	echo "<input type=\"submit\" value=\"Submit\">";
	echo "</select>";
	echo "</form>";
	echo "<form method=\"post\" action=\"/home_page.php\">";
    echo "<input type=\"submit\" value=\"Go back to home_page\">";
	echo "</form>";

}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
$conn = null;

?>
