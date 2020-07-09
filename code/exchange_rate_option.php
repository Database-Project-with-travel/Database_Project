<?php
#此php檔為此國家幣別對新台幣或美元匯率的選項
#若無國家的幣別資料則抓美元
header("Content-Type:text/html;charset=utf-8");
$servername = "localhost";
$username = "project";
$password = "project";
$dbname = "project_travel";

try{
	$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8",
	$username,$password);
	$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	echo "<form method=\"post\" action=\"/exchange_rate_sql.php\">";

	echo "請選擇統計方式：<br>";
	echo "<input type=\"radio\" name =\"time\" value=\"year\"> 年 (Year) &nbsp";
	echo "<input type=\"radio\" name =\"time\" value=\"month\"> 月 (Month) <br><br>";
	
	echo "請選擇統計的時間區段：<br>";
	$stmt = $conn->prepare("select distinct 年 from rate_to_TWD;");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	echo "年度";
	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			echo "<input type=\"checkbox\" name=\"syear$v\",value=\"$v\">";
			echo "<label for=\"$v\">".$v."</label>";
		}
	}
	echo "<br>";

    $stmt = $conn->prepare("select distinct 月 from rate_to_TWD;");
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo "月份";

	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			echo "<input type=\"checkbox\" name=\"smonth$v\",value=\"$v\">";
			echo "<label for=\"$v\">".$v."</label>";
		}
	}
	echo "<br>";

    $stmt = $conn->prepare("select distinct 年 from rate_to_TWD;");
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	echo "年度";
	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			echo "<input type=\"checkbox\" name=\"eyear$v\",value=\"$v\">";
			echo "<label for=\"$v\">".$v."</label>";
		}
	}
	echo "<br>";

    $stmt = $conn->prepare("select distinct 月 from rate_to_TWD;");
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	echo "月份";
	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			echo "<input type=\"checkbox\" name=\"emonth$v\",value=\"$v\">";
			echo "<label for=\"$v\">".$v."</label>";
		}
	}
	echo "<br><br>";

	echo "請勾選想要此國家幣別對應哪種幣別(可複選)：<br>";
	echo "<input type=\"checkbox\" name=\"對新台幣匯率\",value=\"對新台幣匯率\">";
	echo "<label for=\"對新台幣匯率\">".國家幣別／新台幣."</label>";
	echo "<input type=\"checkbox\" name=\"對美元匯率\",value=\"對美元匯率\">";
	echo "<label for=\"對美元匯率\">".國家幣別／美元."</label>";
	echo "<br><br>";

	$stmt = $conn->prepare("select distinct 國家名稱 from country;");
	$stmt->execute();

	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	#print_r($result);	
	$cnt = 0;
    echo "請勾選要統計的國家 (複選)：<br>";
	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			$cnt += 1;
			echo "<input type=\"checkbox\" name=\"$v\",value=\"$v\">";
			echo "<label for=\"$v\">".$v."</label>";
			if($cnt % 10 == 0) echo '<br>';
		}
	}
	echo "<br>";
	echo "<input type=\"submit\" value=\"Submit\">";
	echo "</form>";
}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
$conn = null;
#echo "</table>";
?>
