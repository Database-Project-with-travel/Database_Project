<?php
header("Content-Type:text/html;charset=utf-8");
try{	
    session_start();
    session_destroy();
    echo "請點擊想要執行的功能 <br>";
    echo "<form method=\"post\" action = \"/midpoint_function.php\">";
    echo "<input type=\"radio\" name =\"function\" value=\"select\"> select &nbsp";
	echo "<input type=\"radio\" name =\"function\" value=\"insert\"> insert &nbsp";
	echo "<input type=\"radio\" name =\"function\" value=\"update\"> update &nbsp";
    echo "<input type=\"radio\" name =\"function\" value=\"delete\"> delete &nbsp";
    echo "<input type=\"radio\" name =\"function\" value=\"user\"> select with user &nbsp";
    echo "<input type=\"radio\" name =\"function\" value=\"history\"> history &nbsp";
    echo "<br>";
    echo "請點擊想要執行的類別 ( 當選擇 select 時，入境 & 出境才有作用，history 則不需要使用這個 ) <br>";
	echo "<input type=\"radio\" name =\"way\" value=\"Inbound\"> 入境 (Inbound) &nbsp";
    echo "<input type=\"radio\" name =\"way\" value=\"Outbound\"> 出境 (Outbound) &nbsp";
    echo "<input type=\"radio\" name =\"way\" value=\"Inbound&Outbound\"> 入境 & 出境 (Inbound & Outbound) &nbsp";
    echo "<br>";
    echo "<input type=\"submit\" value=\"submit\">";
    echo "</form>";
}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}
#echo "</table>";
?>