<?php
{ // page main
  require("./security_check.php");
  
  if(!isset($_POST["action"]) || $_POST["action"]=="login") {
    header("location:cotacao.php");
  }
}
?>
