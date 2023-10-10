<?php
    @require 'config.php';
    session_start();
    $connessione=  instauraConnessione();
    $username=$_GET['username'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard comunale</title>
    <style>
            @import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap');

            :root{
                --sfondo:#ECF2FF;
                --secondario:#E3DFFD;
                --primario:#E5D1FA;
                --attivo:#FFF4D2;
                --testo:#000;
            }

            body{
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100vh;
                background-color: var(--sfondo);
                color: var(--testo);
                display: flex;
                flex-direction: column;
                align-items: center;
                font-family: 'Lato', sans-serif;
            }

            .titolo{
                font-size: 200%;
                font-weight: 700;
                margin-bottom: 5%;
                margin-top: 1%;
            }

            button{
                font-weight: 600;
                width: 60%;
                margin: 1%;
                padding: 1% 3%;
                border-radius: 5px;
                border-color: transparent;
                background-color: var(--secondario);
                cursor: pointer;
                transition-duration:0.1s;
                font-size: 120%;
            }

            button:hover{
                background-color: var(--primario);
            }

            @media (max-width: 650px) {
                button{
                    margin: 2%;
                    padding: 3% 9%;
                }
            }
    </style>
</head>
<body>
    <div class="titolo">dashboard comuni</div>
    <button onclick="location.href='<?php echo 'gestisci_eventi.php?username=' . $username ?>'">gestisci eventi</button>
    <button onclick="location.href='<?php echo 'gestisci_rapporti.php?username=' . $username ?>'">gestisci rapporti extra</button>
    <button onclick="location.href='<?php echo 'gestisci_abbonamenti.php?username=' . $username ?>'">gestisci abbonamenti</button>
    <button onclick="location.href='<?php echo 'gestisci_referenze.php?username=' . $username ?>'">gestisci refenze</button>
    <button onclick="location.href='<?php echo 'gestisci_prenotazioni.php?username=' . $username ?>'">gestisci prenotazioni</button>
    <button onclick="location.href='index.php'">log out</button>
</body>
</html>

<?php
    function instauraConnessione()
    {
        try {
            // Stabilisco la connessione al database
            $connessione = new PDO(
                "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
                $GLOBALS['dbuser'],
                $GLOBALS['dbpassword']
            );
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connessione;
        } catch (PDOException $e) {
            echo "Errore di connessione al database: " . $e->getMessage();
            return false;
        }
    }

?>