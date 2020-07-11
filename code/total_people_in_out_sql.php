<?php
header("Content-Type:text/html;charset=utf-8");
#此SQL為總人數結合匯率

$servername = "localhost";
$username = "project";
$password = "project";
$dbname = "project_travel";

#print_r($_POST);

try{
	$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8",
		$username,$password);
	$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	
	#$stmt = $conn->prepare("set names 'utf8'");
	#$stmt->execute();
    
    session_start();
    $exchangerate = $_SESSION["exchangerate"];
    $filenamein = "inbound_".$_SESSION["type"];
    $filenameout = "outbound_".$_SESSION["type"];
    session_destroy();

    $sql = "select * from ".$filenamein." where 年 = 108 AND 月 = 4 AND 居住地 = '日本';";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $type = array();
    foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "" || $k == "年" || $k == "月" || $k == "居住地") continue;
            array_push($type,$k);
		}
	}

    $sql = "";
    $sql_rate = "";
    $inbound = "";
    $outbound = "";
	$starty = "";
	$startm = "";
	$endy = "";
	$endm = "";
	$record = array();
    $country = array();
    $cnt = 0;//use to check if data is not enough
    echo "YOU CHOOSE :";
	if($_POST){
		foreach($_POST as $k => $v){
			if($k == "time"){
                $sql = ($v == "month") ? "SELECT inbound.年 as 年, inbound.月 as 月, inbound.居住地 as 居住地" : "SELECT inbound.年 as 年, inbound.居住地 as 居住地";
                $cnt++;
                echo $v."&nbsp";
			}
			else if($k == "syear"){
                $starty = $v;
                $cnt++;
                echo $v."&nbsp";
			}
			else if($k == "smonth"){
                $startm = $v;
                $cnt++;
                echo $v."&nbsp";
			}		
			else if($k == "eyear"){
                $endy = $v;
                $cnt++;
                echo $v."&nbsp";
			}
			else if($k == "emonth"){
                $endm = $v;
                $cnt++;
                echo $v."&nbsp";
			}
			else{
                $check = 0;
                foreach($type as $k1 => $v1){
                    if($k == $v1){
                        $sql = $sql.", inbound.".$k." as inbound_".$k." , outbound.".$k." as outbound_".$k;
                        array_push($record,$k);
                        $check = 1;
                    }
                }
                if($check == 0)
                    array_push($country,$k);
                echo $k."&nbsp";
            }
        }
        echo "<br>";
        if(sizeof($country) == 0 || sizeof($record) == 0 || $cnt != 5 || $starty*12 + $startm > $endy*12 + $endm){
            header("Location: /select_inbound_outbound.php\n");
        }
        if($exchangerate == "no")
            $sql = $sql.", inbound.總人數 as inbound_總人數, outbound.總人數 as outbound_總人數 from ";
        else{
            $sql = $sql.", inbound.總人數 as inbound_總人數, outbound.總人數 as outbound_總人數, exchange.幣別 as 幣別, 
                (case when exchange.對新台幣匯率 IS NOT NULL then exchange.對新台幣匯率 else 'NULL' end) as 對新台幣匯率, 
                (case when exchange.對美元匯率 IS NOT NULL then exchange.對美元匯率 else 'NULL' end) as 對美元匯率 from ";
            $sql_rate = $_POST["time"] == "year" ? "(select rate.年," :"(select rate.年, rate.月,"; 
            $sql_rate = $sql_rate." currency.國家名稱, currency.幣別, 
                        avg(case when rate.對新台幣匯率 = 0 then NULL else rate.對新台幣匯率 end) as 對新台幣匯率,
                        avg(case when rate.對美元匯率 = 0 then NULL else rate.對美元匯率 end) as 對美元匯率
                        from (select distinct 國家名稱, 幣別 from country_currency where";
            foreach($country as $k => $v){
                $sql_rate = $sql_rate." 國家名稱 = '".$v."'or";
            }
            $sql_rate = rtrim($sql_rate,"or");
            $sql_rate = $sql_rate.") as currency,(
                select 年, 月, '美元' as 幣別, 美元 as 對新台幣匯率, 美元/美元 as 對美元匯率 from rate_to_TWD union all 
                select 年, 月, '人民幣' as 幣別, 人民幣 as 對新台幣匯率, 人民幣/美元 as 對美元匯率 from rate_to_TWD union all 
                select 年, 月, '歐元' as 幣別, 歐元 as 對新台幣匯率, 歐元/美元 as 對美元匯率 from rate_to_TWD union all 
                select 年, 月, '日幣' as 幣別, 日幣 as 對新台幣匯率, 日幣/美元 as 對美元匯率 from rate_to_TWD union all 
                select 年, 月, '英鎊' as 幣別, 英鎊 as 對新台幣匯率, 英鎊/美元 as 對美元匯率 from rate_to_TWD union all 
                select 年, 月, '澳幣' as 幣別, 澳幣 as 對新台幣匯率, 澳幣/美元 as 對美元匯率 from rate_to_TWD union all 
                select 年, 月, '港幣' as 幣別, 港幣 as 對新台幣匯率, 港幣/美元 as 對美元匯率 from rate_to_TWD union all 
                select 年, 月, '南非幣' as 幣別, 南非幣 as 對新台幣匯率, 南非幣/美元 as 對美元匯率 from rate_to_TWD union all 
                select 年, 月, '紐幣' as 幣別, 紐幣 as 對新台幣匯率, 紐幣/美元 as 對美元匯率 from rate_to_TWD ) as rate 
                where currency.幣別 = rate.幣別 and (( 年 > ".$starty." and 年 < ".$endy.") 
                or ( 年 = ".$starty." and 月 >= ".$startm.") or ( 年 = ".$endy." and 月 <= ".$endm."))"; 
            if($_POST["time"] == "year")
                $sql_rate = $sql_rate."group by rate.年, currency.國家名稱) as exchange, ";
            else
                $sql_rate = $sql_rate."group by rate.年, rate.月, currency.國家名稱) as exchange, ";
        }
        #echo $sql_rate;

        $inbound = ($_POST["time"] == "month") ? "(select 年, 月, 居住地, " : "(select 年, 居住地, ";
        $outbound = ($_POST["time"] == "month") ? "(select 年, 月, 國家名稱, " : "(select 年, 國家名稱, ";
        foreach($record as $k => $v){
            $inbound = $inbound."sum(".$v.") as ".$v.", ";
            $outbound = $outbound."sum(".$v.") as ".$v.", ";
        }
        $inbound = $inbound."sum(";
        $outbound = $outbound."sum(";
		foreach($record as $k => $v){
            $inbound = $inbound.$v."+";
            $outbound = $outbound.$v."+";
        }
        $inbound = rtrim($inbound,"+");
        $outbound = rtrim($outbound,"+");
        
        $inbound = $inbound.") as 總人數 from ".$filenamein." where (";
        $outbound = $outbound.") as 總人數 from ".$filenameout." where (";
        foreach($country as $k => $v){
            $inbound = $inbound." 居住地 = '".$v."' or";
            $outbound = $outbound." 國家名稱 = '".$v."' or";
        }
        $inbound = rtrim($inbound,"or");
        $outbound = rtrim($outbound,"or");

        $inbound = $inbound.") and (( 年 > ".$starty." and 年 < ".$endy.") 
        or ( 年 = ".$starty." and 月 >= ".$startm.") or ( 年 = ".$endy." and 月 <= ".$endm."))";
        $outbound = $outbound.") and (( 年 > ".$starty." and 年 < ".$endy.") 
        or ( 年 = ".$starty." and 月 >= ".$startm.") or ( 年 = ".$endy." and 月 <= ".$endm."))";        

        $inbound = ($_POST["time"] == "month") ? $inbound." group by 年, 月, 居住地) as inbound, " : $inbound." group by 年, 居住地) as inbound, ";
        $outbound = ($_POST["time"] == "month") ? $outbound." group by 年, 月, 國家名稱) as outbound " : $outbound." group by 年, 國家名稱) as outbound ";


        if($exchangerate == "yes"){
            if($_POST["time"] == "year")
                $sql = $sql.$sql_rate.$inbound.$outbound."where exchange.國家名稱 = inbound.居住地 and exchange.國家名稱 = outbound.國家名稱
                and inbound.居住地 = outbound.國家名稱 and inbound.年 = outbound.年
                and inbound.年 = exchange.年  and exchange.年 = outbound.年";
            else
                $sql = $sql.$sql_rate.$inbound.$outbound."where exchange.國家名稱 = inbound.居住地 and exchange.國家名稱 = outbound.國家名稱
                and inbound.居住地 = outbound.國家名稱 and inbound.年 = outbound.年 and inbound.月 = outbound.月
                 and inbound.年 = exchange.年  and exchange.年 = outbound.年";
        }
        else{            
            if($_POST["time"] == "month")
                $sql = $sql.$sql_rate.$inbound.$outbound."where inbound.居住地 = outbound.國家名稱 
                and inbound.年 = outbound.年 and inbound.月 = outbound.月";
            else
                $sql = $sql.$sql_rate.$inbound.$outbound."where inbound.居住地 = outbound.國家名稱 
                and inbound.年 = outbound.年";               
        }
        if($_POST["time"] == "year")
            $sql = $sql." group by inbound.年, inbound.居住地";
        else
            $sql = $sql." group by inbound.年, inbound.月, inbound.居住地";
        $sql = $sql." order by inbound.年;";
    }
    
    echo "<form method=\"post\" action=\"/home_page.php\">";
    echo "<input type=\"submit\" value=\"Go back to home page\"> &nbsp";
	echo "</form>";

	echo "<form method=\"post\" action=\"/select_inbound_outbound.php\">";
    echo "<input type=\"submit\" value=\"Go back to select_inbound_outbound\">";
	echo "</form>";

	echo "<table style='border: solid 1px black;'>";
	if($_POST["time"]=="year")
		echo "<tr><th>年</th><th>國家名稱";
	else
		echo "<tr><th>年</th><th>月</th><th>國家名稱";;
    foreach($record as $k => $v)
        echo "</th><th>入境 ".$v."</th><th>出境 ".$v;
    if($exchangerate == "no")
        echo "</th><th>入境總人數</th><th>出境總人數</th></tr>";
    else
        echo "</th><th>入境總人數</th><th>出境總人數</th><th>幣別</th><th>對新台幣匯率</th><th>對美元匯率</th></tr>";
	#echo $sql;
	$stmt = $conn->prepare($sql);
	$stmt->execute();	
	#$count = $stmt->rowCount();
	#echo "rownumber: ".$count;

	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	#foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) 
	#	as $k => $v){
	#	echo $k.":".$v;
	#}
	
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	#print_r($result);
	foreach($result as $row){
		echo "<tr>";
		foreach($row as $k => $v){
			echo "<td style='width:100px;border:1px solid black;'>".$v."</td>";
		}
		echo "</tr>";
    }
    $stmt = $conn->prepare("insert into user_history (query_sql) values (\"".$sql."\");");
    $stmt->execute();

}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
$conn = null;
echo "</table>";

?>