<?php
header("Content-Type:text/html;charset=utf-8");
try{
    session_start();
    $cnt = array();
    foreach($_POST as $k => $v){
        array_push($cnt,$k);
    }
    echo sizeof($cnt);
    if(sizeof($cnt) != 3) 
        header("Location: /select_inbound.php\n"); 
    else{
        $_SESSION["type"]= $_POST["type"];
        $_SESSION["exchangerate"]= $_POST["exchangerate"];
        if($_POST["datatype"] == "totalpeople")
            header("Location: /total_people_option.php\n");          
        else if($_POST["datatype"] == "peoplereturnrate")
            header("Location: /peoplereturnrate_option.php\n");    
        else if($_POST["datatype"] == "sorting")
            header("Location: /people_sorting_option.php\n");        
        else
            header("Location: /home_page.php\n");
    }
}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}    
?>