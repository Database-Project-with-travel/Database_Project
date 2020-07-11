<?php
header("Content-Type:text/html;charset=utf-8");
try{	
    session_start();
    echo "請點擊想要執行的類別 <br>";
    echo "<form method=\"post\" action = \"/midpoint_select_with_user_outbound.php\">";
    echo "<input type=\"radio\" name =\"type\" value=\"age\"> 年齡  &nbsp";
    echo "<input type=\"radio\" name =\"type\" value=\"gender\"> 性別 &nbsp";
    echo "<input type=\"radio\" name =\"type\" value=\"traffic\"> 交通方式 &nbsp";
    echo "<br>";
    echo "請點擊想要呈現的資料方式 <br>";    
    echo "<input type=\"radio\" name =\"datatype\" value=\"totalpeople\"> 總人數 &nbsp";
    echo "<input type=\"radio\" name =\"datatype\" value=\"peoplereturnrate\"> 人數成長比例 &nbsp";
    echo "<br>";
    echo "是否要呈現匯率 (若無該國貨幣資料，則該國幣顯示美元) <br>";    
    echo "<input type=\"radio\" name =\"exchangerate\" value=\"yes\"> 是 &nbsp";
    echo "<input type=\"radio\" name =\"exchangerate\" value=\"no\"> 否 &nbsp <br>";
    echo "<input type=\"submit\" value=\"submit\">";
    echo "</form>";
    echo "<form method=\"post\" action=\"/home_page.php\">";
    echo "<input type=\"submit\" value=\"Back\">";
    echo "</form>";
    
}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
#echo "</table>";
?>