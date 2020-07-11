<?php
header("Content-Type:text/html;charset=utf-8");
try{
    session_start();	
    $_SESSION["type"]= $_POST["type"];
    $_SESSION["exchangerate"]= $_POST["exchangerate"];
    if($_POST["datatype"] == "totalpeople")
        header("Location: /total_people_user_option.php\n");          
    else if($_POST["datatype"] == "peoplereturnrate")
        header("Location: /peoplereturnrate_user_option.php\n");    
    else if($_POST["datatype"] == "sorting")
        header("Location: /people_sorting_user_option.php\n");             
}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}    
?>