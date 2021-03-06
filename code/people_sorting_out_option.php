<?php
header("Content-Type:text/html;charset=utf-8");
#此php檔為人數成長率的選項
$servername = "localhost";
$username = "project";
$password = "project";
$dbname = "project_travel";

try{
	echo "!!!若選填不完全，會重新導向至上一頁。!!!<br>";
    session_start();
    $type = $_SESSION["type"];
    $exchangerate = $_SESSION["exchangerate"];
    $filename = "outbound_".$type;
    $_SESSION["filename"] = $filename;

	$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8",
	$username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
	echo "<form method=\"post\" action=\"/people_sorting_out_sql.php\">";

	echo "請選擇統計方式：<br>";
	echo "<input type=\"radio\" name =\"time\" value=\"year\"> 年 (Year) &nbsp";
    echo "<input type=\"radio\" name =\"time\" value=\"month\"> 月 (Month) <br><br>";
	
	echo "請選擇統計的時間區段：<br>";

    $sql = "select distinct 年 from ".$filename.";";

	$stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	echo "年度";
	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			echo "<input type=\"radio\" name =\"syear\" value=\"$v\"> $v &nbsp";
		}
	}
	echo "<br>";
    $sql = "select distinct 月 from ".$filename.";";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo "月份";

	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			echo "<input type=\"radio\" name =\"smonth\" value=\"$v\"> $v &nbsp";
		}
	}
	echo "<br>";
    $sql = "select distinct 年 from ".$filename.";";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	echo "年度";
	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			echo "<input type=\"radio\" name =\"eyear\" value=\"$v\"> $v &nbsp";
		}
	}
	echo "<br>";
    $sql = "select distinct 月 from ".$filename.";";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	echo "月份";
	foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "") continue;
			echo "<input type=\"radio\" name =\"emonth\" value=\"$v\"> $v &nbsp";
		}
	}
	echo "<br><br>";

    $sql = "select * from ".$filename." where 年 = 108 AND 月 = 4 AND 國家名稱 = '日本';";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "請勾選要統計的項目 (複選)：<br>";
	foreach($result as $row){
		foreach($row as $k => $v){
            if($v == "" || $k == "年" || $k == "月" || $k == "國家名稱") continue;
			echo "<input type=\"checkbox\" name=\"$k\",value=\"$k\">";
            echo "<label for=\"$k\">".$k."</label>";
            echo "&nbsp";
		}
	}
    echo "<br><br>";

    echo "選擇由大到小排序或由小到大排序 (總人數為 0 的會排除)：<br>";
    echo "<input type=\"radio\" name =\"sorting_way\" value=\"DESC\"> 由大到小 &nbsp";
    echo "<input type=\"radio\" name =\"sorting_way\" value=\"ASC\"> 由小到大 &nbsp";

    echo "<br><br>";
    echo "選擇資料筆數 (若不足，則會顯示原本的資料排序)：<br>";
    for($i = 1; $i < 21; $i++){
        echo "<input type=\"radio\" name =\"number\" value=\"$i\"> $i &nbsp";
        if($i % 10 == 0) 
            echo "<br>";
    }

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
    echo "<form method=\"post\" action=\"/select_inbound.php\">";
    echo "<input type=\"submit\" value=\"Back\">";
	echo "</form>";
    
}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
$conn = null;
#echo "</table>";
?>