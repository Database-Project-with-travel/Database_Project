<?php
header("Content-Type:text/html;charset=utf-8");
#此SQL為入境人數成長率結合匯率

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

	$sql = "select * from ".$filename." where 年 = 108 AND 月 = 4 AND 居住地 = '日本';";
	$sql_rate = "";
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
    $sql2 = "";
	$starty = "";
	$startm = "";
	$endy = "";
	$endm = "";
	$finalsql = "";
	$record = array();
	$country = array();
	$cnt = 0;//use to check if data is not enough

	if($_POST){
		foreach($_POST as $k => $v){
			if($k == "time"){
				$sql = ($v == "month") ? "( SELECT 年, 月, 居住地, " : "( SELECT 年, 居住地, ";
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
                        $sql = $sql."sum(".$k.") as ".$k.", ";
                        array_push($record,$k);
                        $check = 1;
                    }
                }
                if($check == 0)
                    array_push($country,$k);
            }

		}
		if(sizeof($country) == 0 || sizeof($record) == 0 || $cnt != 5){
            header("Location: /select_inbound.php\n");
        }
        $sql = $sql."sum(";
		foreach($record as $k => $v){
			$sql = $sql.$v."+";
		}
		$sql = rtrim($sql,"+");
		$sql = $sql.") as total_people";
		
		$sql = $sql." from ".$filename." where ( ";
		foreach($country as $k => $v){
			$sql = $sql." 居住地='".$v."' or";
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
            $sql = $sql." group by 年, 居住地 ) as tmp1";
            $sql2 = $sql2." group by 年, 居住地 ) as tmp2";
		}
		else{
            $sql = $sql." group by 年, 月, 居住地 ) as tmp1";
            $sql2 = $sql2." group by 年, 月, 居住地 ) as tmp2";		
        }	

        $newsql = ($_POST["time"] == "month") ? "( SELECT tmp1.年, tmp1.月, tmp1.居住地, " : "( SELECT tmp1.年, tmp1.居住地, ";
        foreach($record as $k => $v){
            $newsql = $newsql."tmp1.".$v.", ";   
        }
        $newsql = $newsql."tmp1.total_people, ";

		if($_POST["time"] == "year")
			$newsql = $newsql." (case when tmp2.total_people = 0 then 'NULL' 
			when tmp1.年 = 98 then 'NULL' else ((tmp1.total_people/tmp2.total_people)-1)*100 end) 
			as ratio from ".$sql.", ".$sql2." where tmp1.居住地 = tmp2.居住地 and
			(tmp1.年 = tmp2.年 + 1 or tmp1.年 = 98) group by 年, 居住地 ) as ans";
        else
			$newsql = $newsql." (case when tmp2.total_people = 0 then 'NULL' 
			when (tmp1.年 = 98 and tmp1.月 = 1) then 'NULL 'else ((tmp1.total_people/tmp2.total_people)-1)*100 end) 
			as ratio from ".$sql.", ".$sql2." where tmp1.居住地 = tmp2.居住地 
			and ((tmp1.年*12+tmp1.月-tmp2.年*12-tmp2.月) = 1 or (tmp1.年 = 98 and tmp1.月 = 1)) group by 年, 月, 居住地 ) as ans";

		if($exchangerate == "yes"){
			if($_POST["time"] == "year")
				$sql_rate = "( select rate.年, currency.國家名稱, sum(rate.對新台幣匯率) as 對新台幣匯率總和,
							 count(rate.對新台幣匯率 <> 0 or NULL) as number ";
			else
				$sql_rate = "( select rate.年, rate.月, currency.國家名稱, rate.對新台幣匯率 ";
			$sql_rate = $sql_rate."from (select 年, 月, '美元' as 幣別, 美元 as 對新台幣匯率 from rate_to_TWD union all 
							select 年, 月, '人民幣' as 幣別, 人民幣 as 對新台幣匯率 from rate_to_TWD union all 
						    select 年, 月, '歐元' as 幣別, 歐元 as 對新台幣匯率 from rate_to_TWD union all 
							select 年, 月, '日幣' as 幣別, 日幣 as 對新台幣匯率 from rate_to_TWD union all 
						    select 年, 月, '英鎊' as 幣別, 英鎊 as 對新台幣匯率 from rate_to_TWD union all 
							select 年, 月, '澳幣' as 幣別, 澳幣 as 對新台幣匯率 from rate_to_TWD union all 
						    select 年, 月, '港幣' as 幣別, 港幣 as 對新台幣匯率 from rate_to_TWD union all 
						    select 年, 月, '南非幣' as 幣別, 南非幣 as 對新台幣匯率 from rate_to_TWD union all 
						    select 年, 月, '紐幣' as 幣別, 紐幣 as 對新台幣匯率 from rate_to_TWD ) as rate, 
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
		$finalsql = ($_POST["time"] == "month") ? "SELECT ans.年, ans.月, ans.居住地, " : "SELECT ans.年, ans.居住地, ";
		foreach($record as $k => $v){
            $finalsql = $finalsql."ans.".$v.", ";
		}
        if($_POST["time"] == "year" && $exchangerate == "yes"){
			$finalsql = $finalsql."ans.total_people, ans.ratio, "."
						(case when exchange.對新台幣匯率總和 = 0 then 'NULL' else exchange.對新台幣匯率總和/exchange.number end)".  
						" from ".$newsql.", ".$sql_rate." where ans.年 = exchange.年 and ans.居住地 = exchange.國家名稱 group by ans.年, ans.居住地 order by ans.年 ASC";
		}
        else if($_POST["time"] == "month" && $exchangerate == "yes"){    
			$finalsql = $finalsql."ans.total_people, ans.ratio, "."
						exchange.對新台幣匯率 from ".$newsql.", ".$sql_rate." where ans.年 = exchange.年 
						and ans.月 = exchange.月 and ans.居住地 = exchange.國家名稱 
						group by ans.年, ans.月, ans.居住地 order by ans.年, ans.月 ASC";
		}
        else if($_POST["time"] == "year" && $exchangerate == "no")
            $finalsql = $finalsql."ans.total_people, ans.ratio from ".$newsql." group by ans.年, ans.居住地 order by ans.年 ASC";
        else
			$finalsql = $finalsql."ans.total_people, ans.ratio from ".$newsql." group by ans.年, ans.月, ans.居住地 order by ans.年, ans.月 ASC";
	}
    
    echo "<form method=\"post\" action=\"/home_page.php\">";
    echo "<input type=\"submit\" value=\"Go back to home page\">";
	echo "</form>";

	echo "<form method=\"post\" action=\"/select_inbound.php\">";
    echo "<input type=\"submit\" value=\"Go back to select_inbound\">";
	echo "</form>";

	echo "<table style='border: solid 1px black;'>";
	if($_POST["time"]=="year")
		echo "<tr><th>年</th><th>居住地";
	else
		echo "<tr><th>年</th><th>月</th><th>居住地";
	foreach($record as $k => $v){
		echo "</th><th>".$v;
	}
	if($exchangerate == "no")
		echo "</th><th>總人數</th><th>人數成長率(百分比)</th></tr>";
	else
		echo "</th><th>總人數</th><th>人數成長率(百分比)</th><th>匯率</th></tr>";
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

}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
$conn = null;
echo "</table>";

?>
