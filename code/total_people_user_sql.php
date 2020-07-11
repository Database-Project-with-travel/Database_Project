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
    $user = "";
    $user_to_people = "( select 年, 月, 居住地";
	$starty = "";
	$startm = "";
	$endy = "";
	$endm = "";
	$record = array();
    $country = array();
    $cnt = 0;//use to check if data is not enough

    if($_SESSION["type"] == "age"){
        foreach($type as $k => $v){
            $user_to_people = $user_to_people.", count(年齡 = '".$v."' or NULL) as ".$v;
        }
        $user_to_people = $user_to_people." from user_inbound group by 年, 月, 居住地 ) as user";
    }
    else if($_SESSION["type"] == "chinese"){
        foreach($type as $k => $v){
            $user_to_people = $user_to_people.", count(華僑／外籍 = '".$v."' or NULL) as ".$v;
        }
        $user_to_people = $user_to_people." from user_inbound group by 年, 月, 居住地 ) as user";
    }
    else if($_SESSION["type"] == "gender"){
        foreach($type as $k => $v){
            $user_to_people = $user_to_people.", count(性別 = '".$v."' or NULL) as ".$v;
        }
        $user_to_people = $user_to_people." from user_inbound group by 年, 月, 居住地 ) as user";
    }
    else if($_SESSION["type"] == "occupation"){
        foreach($type as $k => $v){
            $user_to_people = $user_to_people.", count(職業 = '".$v."' or NULL) as ".$v;
        }
        $user_to_people = $user_to_people." from user_inbound group by 年, 月, 居住地 ) as user";
    }
    else if($_SESSION["type"] == "purpose"){
        foreach($type as $k => $v){
            $user_to_people = $user_to_people.", count(來臺原因 = '".$v."' or NULL) as ".$v;
        }
        $user_to_people = $user_to_people." from user_inbound group by 年, 月, 居住地 ) as user";
    }
    else{
        foreach($type as $k => $v){
            $user_to_people = $user_to_people.", count(交通方式 = '".$v."' or NULL) as ".$v;
        }
        $user_to_people = $user_to_people." from user_inbound group by 年, 月, 居住地 ) as user";
    }

	if($_POST){
		foreach($_POST as $k => $v){
			if($k == "time"){
                $sql = ($v == "month") ? "SELECT inbound.年, inbound.月, inbound.居住地" : "SELECT inbound.年, inbound.居住地";
                $cnt++;
			}
			else if($k == "syear"){
                $starty = $v;
                $cnt++;
			}
			else if($k == "smonth"){
                $startm = $v;
                $cnt++;
			}		
			else if($k == "eyear"){
                $endy = $v;
                $cnt++;
			}
			else if($k == "emonth"){
                $endm = $v;
                $cnt++;
			}
			else{
                $check = 0;
                foreach($type as $k1 => $v1){
                    if($k == $v1){
                        $sql = $sql.", inbound.".$k." as inbound_".$k." , user.".$k." as user_".$k;
                        array_push($record,$k);
                        $check = 1;
                    }
                }
                if($check == 0)
                    array_push($country,$k);
            }

        }
        if(sizeof($country) == 0 || sizeof($record) == 0 || $cnt != 5){
            header("Location: /select_with_user_inbound.php\n");
        }
        if($exchangerate == "no")
            $sql = $sql.", inbound.總人數 as inbound_總人數, user.總人數 as user_總人數
                        , user.總人數+inbound.總人數 as 總人數 from ";
        else{
            $sql = $sql.", inbound.總人數 as inbound_總人數, user.總人數 as user_總人數
                ,user.總人數+inbound.總人數 as 總人數, exchange.幣別, 
                (case when exchange.對新台幣匯率 IS NOT NULL then exchange.對新台幣匯率 else 'NULL' end), 
                (case when exchange.對美元匯率 IS NOT NULL then exchange.對美元匯率 else 'NULL' end) from ";
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
        $user = ($_POST["time"] == "month") ? "(select 年, 月, 居住地, " : "(select 年, 居住地, ";
        foreach($record as $k => $v){
            $inbound = $inbound."sum(".$v.") as ".$v.", ";
            $user = $user."sum(".$v.") as ".$v.", ";
        }
        $inbound = $inbound."sum(";
        $user = $user."sum(";
		foreach($record as $k => $v){
            $inbound = $inbound.$v."+";
            $user = $user.$v."+";
        }
        $inbound = rtrim($inbound,"+");
        $user = rtrim($user,"+");
        
        $inbound = $inbound.") as 總人數 from ".$filenamein." where (";
        $user = $user.") as 總人數 from ".$user_to_people." where (";
        foreach($country as $k => $v){
            $inbound = $inbound." 居住地 = '".$v."' or";
            $user = $user." 居住地 = '".$v."' or";
        }
        $inbound = rtrim($inbound,"or");
        $user = rtrim($user,"or");

        $inbound = $inbound.") and (( 年 > ".$starty." and 年 < ".$endy.") 
        or ( 年 = ".$starty." and 月 >= ".$startm.") or ( 年 = ".$endy." and 月 <= ".$endm."))";
        $user = $user.") and (( 年 > ".$starty." and 年 < ".$endy.") 
        or ( 年 = ".$starty." and 月 >= ".$startm.") or ( 年 = ".$endy." and 月 <= ".$endm."))";        

        $inbound = ($_POST["time"] == "month") ? $inbound." group by 年, 月, 居住地) as inbound, " : $inbound." group by 年, 居住地) as inbound, ";
        $user = ($_POST["time"] == "month") ? $user." group by 年, 月, 居住地) as user " : $user." group by 年, 居住地) as user ";


        if($exchangerate == "yes"){
            if($_POST["time"] == "year")
                $sql = $sql.$sql_rate.$inbound.$user."where exchange.國家名稱 = inbound.居住地 and exchange.國家名稱 = user.居住地
                and inbound.居住地 = user.居住地 and inbound.年 = user.年
                and inbound.年 = exchange.年  and exchange.年 = user.年";
            else
                $sql = $sql.$sql_rate.$inbound.$user."where exchange.國家名稱 = inbound.居住地 and exchange.國家名稱 = user.居住地
                and inbound.居住地 = user.居住地 and inbound.年 = user.年 and inbound.月 = user.月
                 and inbound.年 = exchange.年  and exchange.年 = user.年";
        }
        else{            
            if($_POST["time"] == "month")
                $sql = $sql.$sql_rate.$inbound.$user."where inbound.居住地 = user.居住地 
                and inbound.年 = user.年 and inbound.月 = user.月";
            else
                $sql = $sql.$sql_rate.$inbound.$user."where inbound.居住地 = user.居住地 
                and inbound.年 = user.年";               
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

	echo "<form method=\"post\" action=\"/select_with_user_inbound.php\">";
    echo "<input type=\"submit\" value=\"Go back to select_with_user_inbound\">";
	echo "</form>";

	echo "<table style='border: solid 1px black;'>";
	if($_POST["time"]=="year")
		echo "<tr><th>年</th><th>國家名稱";
	else
		echo "<tr><th>年</th><th>月</th><th>國家名稱";;
    foreach($record as $k => $v)
        echo "</th><th>入境 ".$v."</th><th>新增 ".$v;
    if($exchangerate == "no")
        echo "</th><th>入境總人數</th><th>新增總人數</th></tr>";
    else
        echo "</th><th>入境總人數</th><th>新增總人數</th><th>總人數</th><th>幣別</th><th>對新台幣匯率</th><th>對美元匯率</th></tr>";
	echo $sql;
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

}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
$conn = null;
echo "</table>";

?> 
