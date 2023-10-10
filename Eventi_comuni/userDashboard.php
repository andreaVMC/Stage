<?php
    require 'config.php';
    session_start();
    $connessione = instauraConnessione();
    $id_comune_selezionato = null;
    $errore=null;
    if(isset($_GET['username'])){
        $username = $_GET['username'];
    }

    if(isset($_POST['submit'])){
        $id_comune_selezionato = $_POST['comune'];
        if($_POST['evento']!=null && $_POST['abbonamento']!='seleziona abbonamento'){
            $evento = $_POST['evento'];
            $abbonamento = $_POST['abbonamento'];
            $formazione_integrata = $_POST['formazione_integrata'];
            registra_prenotazione($username, $id_comune_selezionato, $evento, $abbonamento, $formazione_integrata, $connessione);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User dashboard</title>
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
                margin-bottom: 10%;
                margin-top: 1%;
            }

            form{
                width: 60%;
                margin-bottom: 5%;
                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: center;
            }

            form>[id*='select']{
                font-weight: 700;
                font-size: 120%;
                width: 60%;
                background-color: transparent;
                border: 3px solid var(--primario);
                border-radius: 5px;
                padding: 0.2% 2%;
            }

            button{
                font-weight: 600;
                width: 20%;
                margin: 1%;
                padding: 0.2% 2%;
                border-radius: 5px;
                border-color: transparent;
                background-color: var(--secondario);
                cursor: pointer;
                transition-duration:0.1s;
                font-size: 120%;
            }

            .checkContainer{
                display: flex;
                flex-direction: column;
                align-items: center;
                font-size: 120%;
                font-weight: 700;
                width: 40%;
            }

            #checkbox::before {
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
            #checkbox:checked::before {
                background-color: var(--attivo); /* Replace with your desired color */
                border-color:var(--attivo);
                cursor: pointer;
            }
            
            /* Adjust the custom checkbox when disabled */
            #checkbox:disabled::before {
                background-color: var(--secondario); /* Replace with your desired color */
                border-color: var(--primario); /* Replace with your desired color */
                opacity: 0.5;
                cursor: not-allowed;
                cursor: pointer;
            }

            button:hover{
                background-color: var(--primario);
            }

            @media (max-width: 1200px) {
                form{
                    flex-direction: column;
                }
                form>button{
                    width: 60%;
                }
                form>[id*='select']{
                    width: 60%;
                }
            }

            @media (max-width: 700px) {
                button{
                    width: 80%;
                }
            }
        </style>
</head>
<body>
    <div class="titolo">dashboard utente</div>
    <?php if ($id_comune_selezionato) {
            $stmt = $connessione->prepare('SELECT * FROM `referenze` WHERE `Id_comune` = ?'); 
            $stmt->execute([$id_comune_selezionato]); 
            $eventi = $stmt->fetchAll(PDO::FETCH_ASSOC); 
            foreach ($eventi as &$evento) {
                $evento['Id_evento'] = getNameEvento($evento['Id_evento'], $connessione);
            }
        ?>
            <button onclick="scaricaReferenze(<?php echo htmlspecialchars(json_encode($eventi)); ?>)">scarica referenze</button>
        <?php }
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?username=".$username; ?>">
        <?php if($id_comune_selezionato != null){ ?>
            <button onclick="location.href='<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?username=".$username; ?>'">cambia comune</button>
        <?php } ?>
        <select name="comune" id="select_comune" <?php if($id_comune_selezionato != null){ ?> style="display:none;" <?php } ?>>
            <option id="option" default <?php if($id_comune_selezionato != null){ ?> value="<?php echo $id_comune_selezionato ?> " <?php } ?>></option>
            <?php
                $stmt = $connessione->prepare('SELECT * FROM `Comuni`');
                $stmt->execute();
                $comuni = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($comuni as $comune) {
                    echo '<option value="'.$comune['Id'].'">'.$comune['denominazione'].'</option>';
                }
            ?>
        </select>

        <?php if($id_comune_selezionato != null){ ?>
            <select name="evento" id="select_evento">
                <option default>seleziona evento</option>
                <?php
                    $stmt = $connessione->prepare('SELECT * FROM `evento` WHERE `Id_comune` = ?');
                    $stmt->execute([$id_comune_selezionato]);
                    $eventi = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($eventi as $evento) {
                        echo '<option value="'.$evento['Id'].'">'.$evento['nome'].'</option>';
                    }
                ?>
            </select>

            <select name="abbonamento" id="select_abbonamento">
                <option default>seleziona abbonamento</option>
                <?php
                    $stmt = $connessione->prepare('SELECT * FROM `abbonamento` WHERE `Id_comune` = ?');
                    $stmt->execute([$id_comune_selezionato]);
                    $abbonamenti = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($abbonamenti as $abbonamento) {
                        echo '<option value="'.$abbonamento['tipo'].'">'.$abbonamento['tipo'].'</option>';
                    }
                ?>
            </select>
            <div class="checkContainer">
                <div class="checkTesto">f. integrata</div>
                <input type="checkbox" name="formazione_integrata" value="1" id="checkbox">
            </div>
        <?php } 
        if($errore){?>
            <div class="errore">errore: prenotazione già registrata</div>
        <?php } ?>

        <input type="submit" id="submit" name="submit" style="display:none;">
        <button onclick="document.getElementById('submit').click()">
            <?php
                if($id_comune_selezionato == null){
                    echo 'fitra ricerca per comune';
                } else {
                    echo "registrati";
                }
            ?>
        </button>
    </form>

    <button onclick="location.href='<?php echo 'gestisci_iscrizioni.php?username='.$username ?>'">gestisci iscrizioni</button>
    <button onclick="location.href='index.php'">log out</button>
    <script>
        function scaricaReferenze(referenze) {
            // Contenuto del file
            var contenuto = "evento,nome,email,telefono\n";
            for(var i=0;i<referenze.length;i++){
                contenuto+=referenze[i]['Id_evento']+",";
                contenuto+=referenze[i]['nome']+",";
                contenuto+=referenze[i]['email']+",";
                contenuto+=referenze[i]['telefono']+"\n";
            }
            // Creazione dell'elemento <a> per il download
            var downloadLink = document.createElement("a");
            downloadLink.href = "data:text/plain;charset=utf-8," + encodeURIComponent(contenuto);
            downloadLink.download = "refernze.csv";

            // Aggiunta dell'elemento <a> al DOM e simulazione del clic per avviare il download
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
</body>
</html>

<?php
    function instauraConnessione(){
        try {
            // Stabilisco la connessione al database
            $connessione = new PDO(
                "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
                $GLOBALS['dbuser'],
                $GLOBALS['dbpassword']
            );
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connessione;
        } catch(PDOException $e) {
            echo "Errore di connessione al database: " . $e->getMessage();
            return false;
        }
    }

    function registra_prenotazione($username, $id_comune, $evento, $abbonamento, $formazione_integrata, $connessione) {
        try {
            $stmt = $connessione->prepare("SELECT * FROM `utente` WHERE `username` = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            if($formazione_integrata==null){
                $formazione_integrata=0;
            }
            if(!eventoGiaPrenotato($id_comune, $user['Id'], $evento, $user['nome'], $user['cognome'], $user['email'], getIndirizzo($evento, $connessione), $formazione_integrata, getNameEvento($evento, $connessione), $abbonamento,$connessione)){
                $stmt = $connessione->prepare("INSERT INTO `registro`(`Id_comune`, `Id_user`, `Id_evento`, `nome_user`, `cognome_user`, `email_user`, `sede_evento`, `formazione_integrata`, `singoli_eventi`, `tipo_abbonamento`,`fattura_emessa`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$id_comune, $user['Id'], $evento, $user['nome'], $user['cognome'], $user['email'], getIndirizzo($evento, $connessione), $formazione_integrata, getNameEvento($evento, $connessione), $abbonamento,1]);
                global $errore;
                $errore = false;
            }else{
                return;
            }
        } catch(PDOException $e) {
            echo "Errore durante l'esecuzione della query: " . $e->getMessage();
        }
    }
    
    function getIndirizzo($evento, $connessione) {
        $stmt = $connessione->prepare("SELECT `indirizzo` FROM `evento` WHERE `Id` = ? ");
        $stmt->execute([$evento]);
        $result = $stmt->fetch();
        return $result['indirizzo'];
    }
    
    function getNameEvento($evento, $connessione) {
        $stmt = $connessione->prepare("SELECT `nome` FROM `evento` WHERE `Id` = ? ");
        $stmt->execute([$evento]);
        $result = $stmt->fetch();
        return $result['nome'];
    }

    function eventoGiaPrenotato($id_comune, $user_id, $evento, $user_nome, $user_cognome, $user_email, $indirizzo_evento, $formazione_integrata, $nome_evento, $abbonamento, $connessione) {
        $stmt = $connessione->prepare("SELECT * FROM `registro` WHERE `Id_comune` = ? AND `Id_user` = ? AND `Id_evento` = ? AND `nome_user` = ? AND `cognome_user` = ? AND `email_user` = ? AND `sede_evento` = ? AND `formazione_integrata` = ? AND `singoli_eventi` = ? AND `tipo_abbonamento` = ?");
        $stmt->execute([$id_comune, $user_id, $evento, $user_nome, $user_cognome, $user_email, $indirizzo_evento, $formazione_integrata, $nome_evento, $abbonamento]);
        $result = $stmt->rowCount();
        global $errore;
        $errore = true;

        if ($result > 0) {
            return true; // Il record esiste
        } else {
            return false; // Il record non esiste
        }
    }
    
    
?>
