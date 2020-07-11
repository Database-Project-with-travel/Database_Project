<?php
header("Content-Type:text/html;charset=utf-8");
$servername = "localhost";
$username = "project";
$password = "project";
$dbname = "project_travel";

try{
	echo "!!!若沒有選填id，會重新導向至這一頁。其餘欄位不填，則維持原狀。!!!<br><br>";
	session_start();

	$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8",
	$username,$password);
	$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

	echo "<form method=\"post\" action=\"/update_inbound_sql.php\">";

	echo "請選擇想更新的資料id（必填）：<br>";
	$sql = "select id from user_inbound;";
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
			echo "<input type=\"radio\" name =\"年\" value=\"$v\"> $v &nbsp";
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
			echo "<input type=\"radio\" name =\"月\" value=\"$v\"> $v &nbsp";
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
			echo "<input type=\"radio\" name =\"居住地\" value=\"$v\"> $v &nbsp";
			if($cnt % 10 == 0) echo '<br>';
		}
	}
	echo "<br><br>";

	echo "請選擇年齡：<br>";
	$sql = "select * from inbound_age where 年 = 108 AND 月 = 4 AND 居住地 = '日本';";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($result as $row){
		foreach($row as $k => $v)
		{
			if($v == "" || $k == "年" || $k == "月" || $k == "居住地")
				continue;
			echo "<input type=\"radio\" name =\"年齡\" value=\"$k\"> $k &nbsp";
			echo "&nbsp";
		}
	}
	echo "<br><br>";

	echo "請選擇華僑／外籍：<br>";
	echo "<input type=\"radio\" name =\"華僑／外籍\" value=\"華僑\"> 華僑 &nbsp";
	echo "<input type=\"radio\" name =\"華僑／外籍\" value=\"外籍\"> 外籍 <br><br>";

	echo "請選擇性別：<br>";
	echo "<input type=\"radio\" name =\"性別\" value=\"男\"> 男 &nbsp";
	echo "<input type=\"radio\" name =\"性別\" value=\"女\"> 女 <br><br>";

	echo "請選擇職業：<br>";
	$sql = "select * from inbound_occupation where 年 = 108 AND 月 = 4 AND 居住地 = '日本';";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($result as $row){
		foreach($row as $k => $v)
		{
			if($v == "" || $k == "年" || $k == "月" || $k == "居住地")
				continue;
			echo "<input type=\"radio\" name=\"職業\" value=\"$k\">$k";
			echo "&nbsp";
		}
	}
	echo "<br><br>";

	echo "請選擇來臺原因：<br>";
	$sql = "select * from inbound_purpose where 年 = 108 AND 月 = 4 AND 居住地 = '日本';";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($result as $row){
		foreach($row as $k => $v)
		{
			if($v == "" || $k == "年" || $k == "月" || $k == "居住地")
				continue;
			echo "<input type=\"radio\" name=\"來臺原因\" value=\"$k\">$k";
			echo "&nbsp";
		}
	}
	echo "<br><br>";

	echo "請選擇交通方式：<br>";
	$sql = "select * from inbound_traffic where 年 = 108 AND 月 = 4 AND 居住地 = '日本';";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($result as $row){
		foreach($row as $k => $v)
		{
			if($v == "" || $k == "年" || $k == "月" || $k == "居住地")
				continue;
			echo "<input type=\"radio\" name=\"交通方式\" value=\"$k\">$k";
			echo "&nbsp";
		}
	}
	echo "<br><br>";

	echo "<input type=\"submit\" value=\"Submit\">";
	echo "</form>";
	echo "<form method=\"post\" action=\"/home_page.php\">";
	echo "<input type=\"submit\" value=\"Back\">";
	echo "</form>";

	echo "可更新的資料：<br>";
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

	echo "<br";

}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
$conn = null;
?>
