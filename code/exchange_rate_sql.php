<?php
#此php檔為此國家幣別對新台幣或美元匯率的sql
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
	
	$sql = "";
	$starty = "";
	$startm = "";
	$endy = "";
	$endm = "";
	$typecurrency = array();
	$country = array();

	if($_POST)
	{
		foreach($_POST as $k => $v)
		{
			if($k == "time")
				$sql = ($v == "month") ? "select rate.年, rate.月, currency.國家名稱, currency.幣別, ":"select rate.年, currency.國家名稱, currency.幣別, ";
			else if(substr($k,0,5) == "syear")
				$starty = substr($k,5,strlen($k)-5);
			else if(substr($k,0,6) == "smonth")
				$startm = substr($k,6,strlen($k)-6);
			else if(substr($k,0,5) == "eyear")
				$endy = substr($k,5,strlen($k)-5);
			else if(substr($k,0,6) == "emonth")
				$endm = substr($k,6,strlen($k)-6);
			else if($k == "對新台幣匯率" || $k == "對美元匯率")
			{
				$sql = ($_POST["time"] == "month") ? $sql."rate.".$k.", " : $sql."avg(case when rate.".$k." = 0 then NULL else rate.".$k." end), ";
				array_push($typecurrency, $k);
			}
			else
				array_push($country,$k);

		}
		$sql = rtrim($sql,", ");

	}
	
	$sql = $sql." from (select distinct 國家名稱, 幣別 from country_currency where 國家名稱 = '";
	foreach($country as $k => $v)
	{
		$sql = $sql.$v."' or 國家名稱 = '";
	}
	$sql = rtrim($sql,"or 國家名稱 = '");
	$sql = $sql."') as currency, 
       (select 年, 月, '美元' as 幣別, 美元 as 對新台幣匯率, 美元/美元 as 對美元匯率 from rate_to_TWD
        union all
        select 年, 月, '人民幣' as 幣別, 人民幣 as 對新台幣匯率, 人民幣/美元 as 對美元匯率 from rate_to_TWD
        union all
        select 年, 月, '歐元' as 幣別, 歐元 as 對新台幣匯率, 歐元/美元 as 對美元匯率 from rate_to_TWD
        union all
        select 年, 月, '日幣' as 幣別, 日幣 as 對新台幣匯率, 日幣/美元 as 對美元匯率 from rate_to_TWD
        union all
        select 年, 月, '英鎊' as 幣別, 英鎊 as 對新台幣匯率, 英鎊/美元 as 對美元匯率 from rate_to_TWD
        union all
        select 年, 月, '澳幣' as 幣別, 澳幣 as 對新台幣匯率, 澳幣/美元 as 對美元匯率 from rate_to_TWD
        union all
        select 年, 月, '港幣' as 幣別, 港幣 as 對新台幣匯率, 港幣/美元 as 對美元匯率 from rate_to_TWD
        union all
        select 年, 月, '南非幣' as 幣別, 南非幣 as 對新台幣匯率, 南非幣/美元 as 對美元匯率 from rate_to_TWD
        union all
        select 年, 月, '紐幣' as 幣別, 紐幣 as 對新台幣匯率, 紐幣/美元 as 對美元匯率 from rate_to_TWD
        ) as rate where currency.幣別 = rate.幣別 ";

		$sql = $sql." and (( 年 > ".$starty." and 年 < ".$endy.") or ( 年 = ".$starty." and 月 >= ".$startm.") or ( 年 = ".$endy." and 月 <= ".$endm.")) "; 

		if($_POST["time"] == "year"){
			$sql = $sql." group by rate.年, currency.國家名稱";
		}
		else{
			$sql = $sql." group by rate.年, rate.月, currency.國家名稱";	
		}	
	
		$sql = $sql." order by rate.年;";


	echo "<table style='border: solid 1px black;'>";

	if($_POST["time"]=="year"){
		echo "<tr><th>年</th><th>居住地";
		foreach($typeage as $k => $v){
			echo "</th><th>".$v;
		}
		echo "</th><th>幣別";
	}
	else{
		echo "<tr><th>年</th><th>月</th><th>居住地";
		foreach($typeage as $k => $v){
			echo "</th><th>".$v;
		}
		echo "</th><th>幣別";
	}
	if($_POST["對新台幣匯率"] == on){
		echo "</th><th>對新台幣匯率";
	}
	if($_POST["對美元匯率"] == on){
		echo "</th><th>對美元匯率";
	}
	echo "<th><tr>";

	echo $sql;
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
