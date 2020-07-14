<?php
header("Content-Type:text/html;charset=utf-8");
try{
     session_start();
     $_SESSION["way"] = $_POST["way"];
     print_r($_POST);
     if($_POST["function"] == "select"){
          if($_POST["way"] == "Inbound")
               header("Location: /select_inbound.php\n");
          else if($_POST["way"] == "Outbound")
               header("Location: /select_outbound.php\n");
          else if($_POST["way"] == "Inbound&Outbound")
               header("Location: /select_inbound_outbound.php\n"); 
          else
               header("Location: /home_page.php\n");          
     }
     else if($_POST["function"] == "insert"){
          if($_POST["way"] == "Inbound")
               header("Location: /insert_inbound_option.php\n");
          else if($_POST["way"] == "Outbound")
               header("Location: /insert_outbound_option.php\n");
          else
               header("Location: /home_page.php\n");                  
     }
     else if($_POST["function"] == "update"){
          if($_POST["way"] == "Inbound")
               header("Location: /update_inbound_option.php\n");
          else if($_POST["way"] == "Outbound")
               header("Location: /update_outbound_option.php\n");  
          else
               header("Location: /home_page.php\n");              
     }
     else if($_POST["function"] == "delete"){
          if($_POST["way"] == "Inbound")
               header("Location: /delete_inbound_option.php\n");
          else if($_POST["way"] == "Outbound")
               header("Location: /delete_outbound_option.php\n");
          else
               header("Location: /home_page.php\n");                
     }
     else if($_POST["function"] == "user"){
          if($_POST["way"] == "Inbound")
               header("Location: /select_with_user_inbound.php\n");
          else if($_POST["way"] == "Outbound")
               header("Location: /select_with_user_outbound.php\n");
          else
               header("Location: /home_page.php\n");      
     }
     else if($_POST["function"] == "history"){
          header("Location: /user_history_option.php\n");
     }
     else
          header("Location: /home_page.php\n");

}catch(PDOException $e){
	echo "Error: " . $e->getMessage();
}    
?>