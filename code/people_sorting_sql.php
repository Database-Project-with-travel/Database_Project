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
	$starty = "";
	$startm = "";
	$endy = "";
	$endm = "";
	$record = array();
    $country = array();
    $cnt = 0;//use to check if data is not enough

	if($_POST){
		foreach($_POST as $k => $v){
			if($k == "time"){
                $sql = ($v == "month") ? "( SELECT tmp.年, tmp.月, tmp.居住地, " : "( SELECT tmp.年, tmp.居住地, ";
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
            else if($k == "number" || $k == "sorting_way"){
                $cnt++;
                continue;
            }
			else{
                $check = 0;
                foreach($type as $k1 => $v1){
                    if($k == $v1){
                        $sql = $sql."sum(tmp.".$k.") as ".$k.", ";
                        array_push($record,$k);
                        $check = 1;
                    }
                }
                if($check == 0)
                    array_push($country,$k);
            }

        }
        if(sizeof($country) == 0 || sizeof($record) == 0 || $cnt != 7){
            header("Location: /select_inbound.php\n");
        }
		$sql = $sql."sum(";
		foreach($record as $k => $v){
			$sql = $sql."tmp.".$v."+";
		}
        $sql = rtrim($sql,"+");
        $sql = $exchangerate == "no" ? $sql.") as total_people" 
                                     : $sql.") as total_people, (case when exchange.對新台幣匯率 = 0 then 'NULL' else exchange.對新台幣匯率 end) as 對新台幣匯率";
        if($_POST["time"]=="year" && $exchangerate == "yes")
            $sql = $sql.", sum(exchange.對新台幣匯率) as 對新台幣匯率總和, count(exchange.對新台幣匯率 <> 0 or NULL) as number";
        
        if($exchangerate == "yes"){
            $sql_rate = "( select rate.年, rate.月, currency.國家名稱, rate.對新台幣匯率
                     from (select 年, 月, '美元' as 幣別, 美元 as 對新台幣匯率 from rate_to_TWD union all
                           select 年, 月, '人民幣' as 幣別, 人民幣 as 對新台幣匯率 from rate_to_TWD union all
                           select 年, 月, '歐元' as 幣別, 歐元 as 對新台幣匯率 from rate_to_TWD union all
                           select 年, 月, '日幣' as 幣別, 日幣 as 對新台幣匯率 from rate_to_TWD union all
                           select 年, 月, '英鎊' as 幣別, 英鎊 as 對新台幣匯率 from rate_to_TWD union all
                           select 年, 月, '澳幣' as 幣別, 澳幣 as 對新台幣匯率 from rate_to_TWD union all
                           select 年, 月, '港幣' as 幣別, 港幣 as 對新台幣匯率 from rate_to_TWD union all
                           select 年, 月, '南非幣' as 幣別, 南非幣 as 對新台幣匯率 from rate_to_TWD union all
                           select 年, 月, '紐幣' as 幣別, 紐幣 as 對新台幣匯率 from rate_to_TWD ) as rate,
                          (select distinct country_currency.國家名稱, country_currency.幣別 from country_currency where";
            foreach($country as $k => $v){
                $sql_rate = $sql_rate." country_currency.國家名稱='".$v."' or";
            }                   
            $sql_rate = rtrim($sql_rate,"or");
            $sql_rate = $sql_rate.") as currency where currency.幣別 = rate.幣別 and (( 年 > ".$starty." and 年 < ".$endy.") 
                     or ( 年 = ".$starty." and 月 >= ".$startm.") or ( 年 = ".$endy." and 月 <= ".$endm."))) as exchange"; 
        }
        #echo $sql_rate;

        $sql = $exchangerate == "yes" ? $sql." from ".$filename." as tmp, ".$sql_rate." where ( " 
                                      : $sql." from ".$filename." as tmp where ( ";
		foreach($country as $k => $v){
			$sql = $sql." tmp.居住地='".$v."' or";
		}
		$sql = rtrim($sql,"or");
        $sql = $sql.") and (( tmp.年 > ".$starty." and tmp.年 < ".$endy.") or ( tmp.年 = ".$starty." 
                    and tmp.月 >= ".$startm.") or ( tmp.年 = ".$endy." and tmp.月 <= ".$endm.")) ";
        if($exchangerate == "yes") 
            $sql = $sql."and tmp.年 = exchange.年 and tmp.月 = exchange.月 and tmp.居住地 = exchange.國家名稱 ";
		if($_POST["time"] == "year"){
			$sql = $sql." group by tmp.年, tmp.居住地 order by tmp.年 ASC) as ans";
		}
		else{
			$sql = $sql." group by tmp.年, tmp.月, tmp.居住地 order by tmp.年, tmp.月 ASC) as ans";	
        }		

        $newsql = "";

        if($_POST["time"] == "month")
            $newsql = "( SELECT ans.年, ans.月, ans.居住地, ";
        else
            $newsql = "( SELECT ans.年, ans.居住地, ";

        foreach($record as $k => $v){
            $newsql = $newsql."sum(ans.".$v."), ";
        }
        $newsql = $newsql."sum(";
		foreach($record as $k => $v){
			$newsql = $newsql."ans.".$v."+";
		}
        $newsql = rtrim($newsql,"+");
        if($_POST["time"] == "year" && $exchangerate == "yes")
            $newsql = $newsql.") as total_people, (case when ans.對新台幣匯率總和 = 0 then 'NULL' else ans.對新台幣匯率總和/ans.number end) 
                        from ".$sql." group by ans.年, ans.居住地";
        else if($_POST["time"] == "month" && $exchangerate == "yes")    
            $newsql = $newsql.") as total_people, ans.對新台幣匯率 from ".$sql.
                                  " group by ans.年, ans.月, ans.居住地";
        else if($_POST["time"] == "year" && $exchangerate == "no")
            $newsql = $newsql.") as total_people from ".$sql." group by ans.年, ans.居住地";
        else
            $newsql = $newsql.") as total_people from ".$sql." group by ans.年, ans.月, ans.居住地";
        $newsql = $newsql." ) as total ";
        $finalsql = "SELECT * from  ".$newsql."where total.total_people <> 0 order by total.total_people "
                    .$_POST["sorting_way"]." limit ".$_POST["number"].";";
    }
    
    echo "<form method=\"post\" action=\"/home_page.php\">";
    echo "<input type=\"submit\" value=\"Go back to home page\"> &nbsp";
	echo "</form>";

	echo "<form method=\"post\" action=\"/select_inbound.php\">";
    echo "<input type=\"submit\" value=\"Go back to select_inbound\">";
	echo "</form>";

	echo "<table style='border: solid 1px black;'>";
	if($_POST["time"]=="year")
		echo "<tr><th>年</th><th>居住地";
	else
		echo "<tr><th>年</th><th>月</th><th>居住地";;
    foreach($record as $k => $v)
        echo "</th><th>".$v;
    if($exchangerate == "no")
        echo "</th><th>總人數</th></tr>";
    else
        echo "</th><th>總人數</th><th>匯率</th></tr>";
	echo $finalsql;
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