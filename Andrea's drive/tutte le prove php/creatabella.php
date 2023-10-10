<?php
    session_start();
    require "config.php";
        try{
            $connessione = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],$GLOBALS['dbuser'],$GLOBALS['dbpassword']);
            $connessione -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $stmt = $connessione->prepare("CREATE TABLE utenti (
              id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              nome VARCHAR(30) NOT NULL,
              cognome VARCHAR(30) NOT NULL,
              email VARCHAR(50),
              data_registrazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            if($stmt->execute([])){
              echo "a buon fine";
            }else{
              echo "non a buon fine";
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
?>