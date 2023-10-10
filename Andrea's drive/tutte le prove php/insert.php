<?php
    /*session_start();
    require "config.php";
    if(isset($_POST['input'])){
        try{
            $connessione = new PDO("mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],$GLOBALS['dbuser'],$GLOBALS['dbpassword']);
            $connessione -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $stmt = $connessione->prepare("INSERT INTO `test`(`campoProva`) VALUES (?)");
            $stmt->execute([$_POST['numero']]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }*/
?>