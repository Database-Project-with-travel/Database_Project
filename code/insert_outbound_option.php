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
    
	echo "<form method=\"post\" action=\"/insert_outbound_sql.php\">";

	echo "請選擇時間：<br>";
	$sql = "select distinct 年 from rate_to_TWD;";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo "年度";
	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			echo "<input type=\"radio\" name =\"year\" value=\"$v\"> $v &nbsp";
		}
	}
	echo "<br>";

	$sql = "select distinct 月 from rate_to_TWD;";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo "月份";
	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			echo "<input type=\"radio\" name =\"month\" value=\"$v\"> $v &nbsp";
		}
	}
	echo "<br><br>";

	echo "請選擇居住地：<br>";
	$stmt = $conn->prepare("select distinct 國家名稱 from country;");
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);	
	$cnt = 0;
	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			$cnt += 1;
			echo "<input type=\"radio\" name =\"country\" value=\"$v\"> $v &nbsp";
			if($cnt % 10 == 0) echo '<br>';
		}
	}
	echo "<br><br>";

	echo "請選擇年齡：<br>";
	$sql = "select * from outbound_age where 年 = 108 AND 月 = 4 AND 國家名稱 = '日本';";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($result as $row){
		foreach($row as $k => $v)
		{
			if($v == "" || $k == "年" || $k == "月" || $k == "國家名稱")
				continue;
			echo "<input type=\"radio\" name =\"age\" value=\"$k\"> $k &nbsp";
			echo "&nbsp";
		}
	}
	echo "<br><br>";

	echo "請選擇性別：<br>";
	echo "<input type=\"radio\" name =\"gender\" value=\"男\"> 男 &nbsp";
	echo "<input type=\"radio\" name =\"gender\" value=\"女\"> 女 <br><br>";


	echo "請選擇交通方式：<br>";
	$sql = "select * from outbound_traffic where 年 = 108 AND 月 = 4 AND 國家名稱 = '日本';";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($result as $row){
		foreach($row as $k => $v)
		{
			if($v == "" || $k == "年" || $k == "月" || $k == "國家名稱")
				continue;
			echo "<input type=\"radio\" name=\"traffic\" value=\"$k\">$k";
			echo "&nbsp";
		}
	}
	echo "<br><br>";

	echo "<br>";
	echo "<input type=\"submit\" value=\"Submit\">";
	echo "</form>";
	echo "<form method=\"post\" action=\"/insert_outbound_option.php\">";
	echo "<input type=\"submit\" value=\"Back\">";
	echo "</form>";
}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
$conn = null;
#echo "</table>";
?>
