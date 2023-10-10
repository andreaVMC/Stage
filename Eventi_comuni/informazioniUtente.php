<?php
    @require 'config.php';
    session_start();
    $username=$_GET['utente'];
    $connessione = instauraConnessione();

    if (isset($_POST['elimina'])) {
        $eliminati = isset($_POST['delete']) ? $_POST['delete'] : array();
        foreach ($eliminati as $eliminato) {
            eliminaPrenotazione($eliminato, $connessione);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gestisci iscrizioni</title>
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
            font-size: 4vw;
            font-weight: 700;
        }

        form{
            width: 60%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1% 1%;
            margin-top: 1%;
            padding-bottom: 5%;
        }

        table{
            width: 98%;
        }

        td{
            text-align: center;
        }

        input[type="checkbox"] {
            appearance: none;
            cursor: pointer;
        }
        
        /* Create a custom checkbox */
        input[type="checkbox"]::before {
            content: "";
            display: inline-block;
            width: 16px;
            height: 16px;
            background-color: var(--secondario); /* Replace with your desired color */
            border: 2px solid var(--primario); /* Replace with your desired color */
            border-radius: 3px;
            margin-right: 8px;
            vertical-align: middle;
            cursor: pointer;
        }
        
        /* Adjust the custom checkbox when checked */
        input[type="checkbox"]:checked::before {
            background-color: var(--attivo); /* Replace with your desired color */
            border-color:var(--primario);
            cursor: pointer;
        }
        
        /* Adjust the custom checkbox when disabled */
        input[type="checkbox"]:disabled::before {
            background-color: var(--secondario); /* Replace with your desired color */
            border-color: var(--primario); /* Replace with your desired color */
            opacity: 0.5;
            cursor: not-allowed;
            cursor: pointer;
        }

        .action{
            margin-top: 1%;
            width:15%;
            display: flex;
            flex-direction: row;
            justify-content: center;
        }

        .action>button{
            font-weight: 600;
            position: sticky;
            margin: 0.5% 5%;
            padding: 2% 4%;
            border-radius: 5px;
            border-color: transparent;
            background-color: var(--secondario);
            cursor: pointer;
            transition-duration:0.1s;
            width:90%;
        }

        .action>button:hover{
            background-color: var(--attivo);
        }

        .form_action{
            font-weight: 600;
            bottom: 2%;
            position: fixed;
            width: 70%;
            display: flex;
            flex-direction: row;
            justify-content: center;
        }

        button{
            font-weight: 600;
            padding: 0.5% 3%;
            margin: 0% 1%;
            border-radius: 5px;
            border-color: transparent;
            background-color: var(--secondario);
            cursor: pointer;
            transition-duration:0.1s;
        }

        button:hover{
            background-color: var(--attivo);
        }

        @media (max-width: 850px) {
            .action{
                width: 60%;
            }
            form{
                width:95%;
            }
        }
    </style>
</head>
<body>
<div class="titolo">gestisci iscrizioni</div>
<div class="action">    
<button onclick="location.href='<?php echo 'adminDashboard.php?username=admin'; ?>'">dashboard</button></div>
<button onclick="location.href='<?php echo 'gestisci_user.php?username=admin'; ?>'">torna indietro</button></div>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?username=' . $username; ?>" method="post" enctype="multipart/form-data">
    <table border="1px solid black">
        <tr>
            <th>comune</th>
            <th>Evento</th>
            <th>sede</th>
            <th>abbonamento</th>
            <th>fattura</th>
            <th>elimina prenotazione</th>
        </tr>

        <?php
        $stmt = $connessione->prepare("SELECT * FROM `registro` WHERE `Id_user` = ?");
        $stmt->execute([getIdByUsername($username, $connessione)]);

        $eventiPrenotati = $stmt->fetchAll();

        foreach ($eventiPrenotati as $evento) { ?>
            <tr>
                <td><?php echo getNomeComuneById($evento['Id_comune'], $connessione) ?></td>
                <td><?php echo getNomeEventoById($evento['Id_evento'], $connessione) ?></td>
                <td><?php echo $evento['sede_evento'] ?></td>
                <td><?php echo $evento['tipo_abbonamento'] ?></td>
                <?php if(fatturaGiaPagata($username,$connessione,$evento['Id'])){ ?>
                    <td>saldata</td>
                <?php }else{?>
                    <td>da saldare</td>
                <?php } ?>
                <td><input type="checkbox" name="delete[]" value="<?php echo $evento["Id"] ?>"></td>
            </tr>
        <?php }
        ?>
    </table>
    <input id="submit_elimina" type="submit" name="elimina" style="display:none;">
</form>
<div class="form_action">
<button onclick="document.getElementById('submit_elimina').click()">cancella</button>
</div>
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

function getIdByUsername($username, $connessione)
{
    $stmt = $connessione->prepare("SELECT `Id` FROM `utente` WHERE `username`=? ");
    $stmt->execute([$username]);
    $result = $stmt->fetch();
    if ($result) {
        return $result['Id'];
    } else {
        return null;
    }
}

function getNomeComuneById($id_comune, $connessione)
{
    $stmt = $connessione->prepare("SELECT `denominazione` FROM `Comuni` WHERE `Id` = ?");
    $stmt->execute([$id_comune]);
    $result = $stmt->fetch();
    return $result['denominazione'];
}

function getNomeEventoById($id_evento, $connessione)
{
    $stmt = $connessione->prepare("SELECT `nome` FROM `evento` WHERE `Id` = ?");
    $stmt->execute([$id_evento]);
    $result = $stmt->fetch();
    return $result['nome'];
}

function eliminaPrenotazione($eliminato, $connessione)
{
    $stmt = $connessione->prepare("DELETE FROM `registro` WHERE `Id`=?");
    $stmt->execute([$eliminato]);
}

function getIdUserByUsername($username, $connessione)
{   
    $stmt = $connessione->prepare("SELECT `Id` FROM `utente` WHERE `username`=?");
    $stmt->execute([$username]);
    $result=$stmt->fetch();
    return $result['Id'];
}

function fatturaGiaPagata($username,$connessione,$id){
    $stmt = $connessione->prepare("SELECT `fattura_pagata` FROM `registro` WHERE `Id_user`=? AND `Id`=?");
    $stmt->execute([getIdUserByUsername($username,$connessione),$id]);
    $result=$stmt->fetch();
    if($result['fattura_pagata']!=null){
        return true;
    }
    return false;
}
?>
