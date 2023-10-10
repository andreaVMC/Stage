<?php
  require "config.php";
  foreach(getusers() as $user){
    echo "<p>".$user."</p>";
  }

  function getusers(){
    $utenti = array();
    try{
      $connessione = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],$GLOBALS['dbuser'],$GLOBALS['dbpassword']);
      $connessione -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $stmt = $connessione->prepare("SELECT `campoProva` FROM `test` WHERE 1");
      $stmt->execute([]);
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        array_push($utenti,$row['campoProva']);
      }
    }catch(PDOException $e){
      echo $e->getMessage();
    }
    return $utenti;
  }
?>