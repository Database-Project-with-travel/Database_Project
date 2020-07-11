<?php
header("Content-Type:text/html;charset=utf-8");
#此SQL為人數成長率結合匯率

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
    $filename = $_SESSION["filename"];
    session_destroy();

	$sql = "select * from ".$filename." where 年 = 108 AND 月 = 4 AND 國家名稱 = '日本';";
	$sql_rate = "";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $type = array();
    foreach($result as $row){
		foreach($row as $k => $v){
			if($v == "" || $k == "年" || $k == "月" || $k == "國家名稱") continue;
            array_push($type,$k);
		}
	}

    $sql = "";
    $sql2 = "";
	$starty = "";
	$startm = "";
	$endy = "";
	$endm = "";
	$finalsql = "";
	$record = array();
	$country = array();
	$cnt = 0;//use to check if data is not enough
    echo "YOU CHOOSE :";
	if($_POST){
		foreach($_POST as $k => $v){
			if($k == "time"){
				$sql = ($v == "month") ? "( SELECT 年, 月, 國家名稱, " : "( SELECT 年, 國家名稱, ";
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
                        $sql = $sql."sum(".$k.") as ".$k.", ";
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
            header("Location: /select_outbound.php\n");
        }
        $sql = $sql."sum(";
		foreach($record as $k => $v){
			$sql = $sql.$v."+";
		}
		$sql = rtrim($sql,"+");
		$sql = $sql.") as total_people";
		
		$sql = $sql." from ".$filename." where ( ";
		foreach($country as $k => $v){
			$sql = $sql." 國家名稱='".$v."' or";
		}
        $sql = rtrim($sql,"or");
        
        $sql2 = $sql;
        $sql = $sql.") and (( 年 > ".$starty." and 年 < ".$endy.") or ( 年 = ".$starty." and 月 >= ".$startm.") or ( 年 = ".$endy." and 月 <= ".$endm.")) ";
        if($startm > 1 && $endm > 1)
            $sql2 = $sql2.") and (( 年 > ".$starty." and 年 < ".$endy.") or ( 年 = ".
                    $starty." and 月 >= ".($startm-1).") or ( 年 = ".$endy." and 月 <= ".($endm-1).")) ";
        else if($startm > 1 && $endm = 1)
            $sql2 = $sql2.") and (( 年 > ".$starty." and 年 < ".($endy-1).") or ( 年 = ".
                    $starty." and 月 >= ".($startm-1).") or ( 年 = ".($endy-1)." and 月 <= 12)) "; 
        else if($startm = 1 && $endm > 1)
            $sql2 = $sql2.") and (( 年 > ".($starty-1)." and 年 < ".$endy.") or ( 年 = ".
                    ($starty-1)." and 月 >= 12 ) or ( 年 = ".$endy." and 月 <= ".($endm-1).")) ";
        else
            $sql2 = $sql2.") and (( 年 > ".($starty-1)." and 年 < ".($endy-1).") or ( 年 = ".
                    ($starty-1)." and 月 >= 12 ) or ( 年 = ".($endy-1)." and 月 <= 12)) ";         
		if($_POST["time"] == "year"){
            $sql = $sql." group by 年, 國家名稱 ) as tmp1";
            $sql2 = $sql2." group by 年, 國家名稱 ) as tmp2";
		}
		else{
            $sql = $sql." group by 年, 月, 國家名稱 ) as tmp1";
            $sql2 = $sql2." group by 年, 月, 國家名稱 ) as tmp2";		
        }	

        $newsql = ($_POST["time"] == "month") ? "( SELECT tmp1.年, tmp1.月, tmp1.國家名稱, " : "( SELECT tmp1.年, tmp1.國家名稱, ";
        foreach($record as $k => $v){
            $newsql = $newsql."tmp1.".$v.", ";   
        }
        $newsql = $newsql."tmp1.total_people, ";

		if($_POST["time"] == "year")
			$newsql = $newsql." (case when tmp2.total_people = 0 then 'NULL' 
			when tmp1.年 = 98 then 'NULL' else ((tmp1.total_people/tmp2.total_people)-1)*100 end) 
			as ratio from ".$sql.", ".$sql2." where tmp1.國家名稱 = tmp2.國家名稱 and
			(tmp1.年 = tmp2.年 + 1 or tmp1.年 = 98) group by 年, 國家名稱 ) as ans";
        else
			$newsql = $newsql." (case when tmp2.total_people = 0 then 'NULL' 
			when (tmp1.年 = 98 and tmp1.月 = 1) then 'NULL 'else ((tmp1.total_people/tmp2.total_people)-1)*100 end) 
			as ratio from ".$sql.", ".$sql2." where tmp1.國家名稱 = tmp2.國家名稱 
			and ((tmp1.年*12+tmp1.月-tmp2.年*12-tmp2.月) = 1 or (tmp1.年 = 98 and tmp1.月 = 1)) group by 年, 月, 國家名稱 ) as ans";

		if($exchangerate == "yes"){
			if($_POST["time"] == "year")
				$sql_rate = "( select rate.年, currency.國家名稱, sum(rate.對新台幣匯率) as 對新台幣匯率總和,
							 count(rate.對新台幣匯率 <> 0 or NULL) as number, rate.幣別,
							  sum(rate.對美元匯率) as 對美元匯率總和,
							 count(rate.對美元匯率 <> 0 or NULL) as number1 ";
			else
				$sql_rate = "( select rate.年, rate.月, currency.國家名稱, rate.對新台幣匯率, rate.對美元匯率, rate.幣別 ";
			$sql_rate = $sql_rate."from (select 年, 月, '美元' as 幣別, 美元 as 對新台幣匯率, 美元/美元 as 對美元匯率 from rate_to_TWD union all
										select 年, 月, '人民幣' as 幣別, 人民幣 as 對新台幣匯率, 人民幣/美元 as 對美元匯率 from rate_to_TWD union all
										select 年, 月, '歐元' as 幣別, 歐元 as 對新台幣匯率, 歐元/美元 as 對美元匯率 from rate_to_TWD union all
										select 年, 月, '日幣' as 幣別, 日幣 as 對新台幣匯率, 日幣/美元 as 對美元匯率 from rate_to_TWD union all
										select 年, 月, '英鎊' as 幣別, 英鎊 as 對新台幣匯率, 英鎊/美元 as 對美元匯率 from rate_to_TWD union all
										select 年, 月, '澳幣' as 幣別, 澳幣 as 對新台幣匯率, 澳幣/美元 as 對美元匯率 from rate_to_TWD union all
										select 年, 月, '港幣' as 幣別, 港幣 as 對新台幣匯率, 港幣/美元 as 對美元匯率 from rate_to_TWD union all
										select 年, 月, '南非幣' as 幣別, 南非幣 as 對新台幣匯率, 南非幣/美元 as 對美元匯率 from rate_to_TWD union all
										select 年, 月, '紐幣' as 幣別, 紐幣 as 對新台幣匯率, 紐幣/美元 as 對美元匯率 from rate_to_TWD ) as rate, 
									 ( select distinct country_currency.國家名稱, country_currency.幣別 from country_currency where";
			foreach($country as $k => $v){
				$sql_rate = $sql_rate." country_currency.國家名稱='".$v."' or";
			}                   
			$sql_rate = rtrim($sql_rate,"or");
			$sql_rate = $sql_rate.") as currency where currency.幣別 = rate.幣別 and (( 年 > ".$starty." and 年 < ".$endy.") 
					 or ( 年 = ".$starty." and 月 >= ".$startm.") or ( 年 = ".$endy." and 月 <= ".$endm.")) ";
			if($_POST["time"] == "year")
				$sql_rate = $sql_rate."group by rate.年, currency.國家名稱) as exchange";		 
			else
				$sql_rate = $sql_rate."group by rate.年, rate.月, currency.國家名稱) as exchange";
		}
		$finalsql = ($_POST["time"] == "month") ? "SELECT ans.年 as 年, ans.月 as 月, ans.國家名稱 as 國家名稱, " : "SELECT ans.年 as 年, ans.國家名稱 as 國家名稱, ";
		foreach($record as $k => $v){
			$finalsql = $finalsql."ans.".$v." as ".$v.", ";
		}
        if($_POST["time"] == "year" && $exchangerate == "yes"){
			$finalsql = $finalsql."ans.total_people as 總人數, ans.ratio as 人數成長率, "."exchange.幣別 as 幣別, 
						(case when exchange.對新台幣匯率總和 = 0 then 'NULL' else exchange.對新台幣匯率總和/exchange.number end) as 對新台幣匯率,
						(case when exchange.對美元匯率總和 = 0 then 'NULL' else exchange.對美元匯率總和/exchange.number1 end) as 對美元匯率".  
						" from ".$newsql.", ".$sql_rate." where ans.年 = exchange.年 and ans.國家名稱 = exchange.國家名稱 group by ans.年, ans.國家名稱 order by ans.年 ASC";
		}
        else if($_POST["time"] == "month" && $exchangerate == "yes"){    
			$finalsql = $finalsql."ans.total_people as 總人數, ans.ratio as 人數成長率, "."
						exchange.幣別 as 幣別, (case when exchange.對新台幣匯率 = 0 then 'NULL' else exchange.對新台幣匯率 end) as 對新台幣匯率,
						(case when exchange.對美元匯率 = 0 then 'NULL' else exchange.對美元匯率 end) as 對美元匯率 from ".$newsql.", ".$sql_rate." where ans.年 = exchange.年 
						and ans.月 = exchange.月 and ans.國家名稱 = exchange.國家名稱 
						group by ans.年, ans.月, ans.國家名稱 order by ans.年, ans.月 ASC";
		}
        else if($_POST["time"] == "year" && $exchangerate == "no")
            $finalsql = $finalsql."ans.total_people as 總人數, ans.ratio as 人數成長率 from ".$newsql." group by ans.年, ans.國家名稱 order by ans.年 ASC";
        else
			$finalsql = $finalsql."ans.total_people as 總人數, ans.ratio as 人數成長率 from ".$newsql." group by ans.年, ans.月, ans.國家名稱 order by ans.年, ans.月 ASC";
	}
    
    echo "<form method=\"post\" action=\"/home_page.php\">";
    echo "<input type=\"submit\" value=\"Go back to home page\">";
	echo "</form>";

	echo "<form method=\"post\" action=\"/select_outbound.php\">";
    echo "<input type=\"submit\" value=\"Go back to select_outbound\">";
	echo "</form>";

	echo "<table style='border: solid 1px black;'>";
	if($_POST["time"]=="year")
		echo "<tr><th>年</th><th>國家名稱";
	else
		echo "<tr><th>年</th><th>月</th><th>國家名稱";
	foreach($record as $k => $v){
		echo "</th><th>".$v;
	}
	if($exchangerate == "no")
		echo "</th><th>總人數</th><th>人數成長率(百分比)</th></tr>";
	else
		echo "</th><th>總人數</th><th>人數成長率(百分比)</th><th>幣別</th><th>對新台幣匯率</th><th>對美元匯率</th></tr>";
	#echo $finalsql;
	echo "人數成長比例 = (某期間人數/前期間人口)*100% <br>";
	echo "!!!當前期間人口總數為 0 時，則顯示'NULL'!!!<br>";
	$stmt = $conn->prepare($finalsql);
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
			echo "<td style='width:150px;border:1px solid black;'>".$v."</td>";
		}
		echo "</tr>";
    }
    $stmt = $conn->prepare("insert into user_history (query_sql) values (\"".$finalsql."\");");
    $stmt->execute();
}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
$conn = null;
echo "</table>";

?>