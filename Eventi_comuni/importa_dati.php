<?php
    @require 'config.php';
    session_start();

    if(isset($_GET['username'])){
        $username = $_GET['username'];
    }

    if(isset($_POST['submit'])){
        $utenti = $_FILES['utenti']['tmp_name'];
        $comuni = $_FILES['comuni']['tmp_name'];
        $eventi = $_FILES['eventi']['tmp_name'];
        $abbonamenti = $_FILES['abbonamenti']['tmp_name'];
        $rapporti = $_FILES['rapporti']['tmp_name'];
        $contatto = $_FILES['contatti']['tmp_name'];
        $registro = $_FILES['registro']['tmp_name'];
        $connessione=instauraConnessione();

        caricaUtenti($utenti,$connessione);
        caricaComuni($comuni,$connessione);
        caricaEventi($eventi, $connessione);
        caricaAbbonamenti($abbonamenti, $connessione);
        caricaRapporti($rapporti,$connessione);
        caricaContatti($contatto,$connessione);
        caricaRegistro($registro,$connessione);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>importa dati</title>
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

        .templates{
            width: 80%;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            margin-top: 2%;
        }

        button{
            padding: 0.5% 1.5%;
            border-color: transparent;
            background-color: var(--secondario);
            border-radius: 5px;
            transition-duration: 0.1s;
            cursor:pointer;
            font-weight: 400;
        }

        button:hover{
            background-color: var(--attivo);
        }

        form{
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 30%;
            margin-top: 2%;
            margin-bottom: 2%;
        }

        form>button{
            margin: 1% auto;
            width: 40%;
            padding: 1% 3%;
        }

        form>.carica{
            width: 30%;
        }

        .carica,.dashboard{
            background-color: var(--primario);
            font-weight: 700;
        }

        .avvertimento{
            border: 1px solid red;
            height: 6vh;
            display: flex;
            flex-direction: row;
            justify-content: center;
            width: 100%;
            background-color: #F6BD60;
            margin-top: auto;
        }

        .avvertimento>p{
            font-weight: 700;
        }

        .pericolo{
            height: 4vh;
            margin: auto 0% auto 0%;
            margin-right: 0.5%;
        }

        @media (max-width: 1380px) {
            .templates{
                width: 30%;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding-bottom: 2%;
                border-bottom: 3px solid var(--primario);
            }

            .templates>button{
                margin: 1% auto;
                width: 40%;
                padding: 1% 3%;
            }

            form{
                margin-top: 1%;
            }
        }

        @media (max-width: 1080px) {
            .templates>button, form>button{
                width:60%
            }

            .dashboard{
                width: 15%;
            }
        }

        @media (max-width: 680px) {

            .templates{
                margin-top:7%;
                padding-bottom: 5%;
            }

            .templates>button, form>button{
                width:100%
            }

            .carica{
                margin-top: 15%;
            }

            .dashboard{
                width: 15%;
            }
        }

        @media (max-width: 500px) {

            .templates{
                margin-top:7%;
                padding-bottom: 5%;
            }

            .templates, form{
                width:60%
            }

            .dashboard{
                width: 25%;
                margin-top: 5%;
            }
        }
    </style>
</head>
<body>
    <div class="titolo">Importazione dati</div>
    <div class="templates">
        <button onclick="downloadFile('template/template_utenti.csv')">template utenti</button>
        <button onclick="downloadFile('template/template_comuni.csv')">template comuni</button>
        <button onclick="downloadFile('template/template_eventi.csv')">template eventi</button>
        <button onclick="downloadFile('template/template_abbonamenti.csv')">template abbonamenti</button>
        <button onclick="downloadFile('template/template_extraRapporti.csv')">template rapporti</button>
        <button onclick="downloadFile('template/template_contatti.csv')">template contatti</button>
        <button onclick="downloadFile('template/template_registroIscrizioni.csv')">template registro</button>
    </div>
    <hr>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?username=".$username?>" method="post" enctype="multipart/form-data">
        <input type="file" name="utenti" id="utenti" style="display:none;">
        <button type="button" onclick="document.getElementById('utenti').click()">carica utenti</button>

        <input type="file" name="comuni" id="comuni" style="display:none;">
        <button type="button" onclick="document.getElementById('comuni').click()">carica comuni</button>
        
        <input type="file" name="eventi" id="eventi" style="display:none;">
        <button type="button" onclick="document.getElementById('eventi').click()">carica eventi</button>
        
        <input type="file" name="abbonamenti" id="abbonamenti" style="display:none;">
        <button type="button" onclick="document.getElementById('abbonamenti').click()">carica abbonamenti</button>
        
        <input type="file" name="rapporti" id="rapporti" style="display:none;">
        <button type="button" onclick="document.getElementById('rapporti').click()">carica rapporti</button>
        
        <input type="file" name="contatti" id="contatti" style="display:none;">
        <button type="button" onclick="document.getElementById('contatti').click()">carica contatti</button>
        
        <input type="file" name="registro" id="registro" style="display:none;">
        <button type="button" onclick="document.getElementById('registro').click()">carica prenotazioni</button>
        
        <input type="submit" name="submit" id="submit" style="display:none">
        <button onclick="document.getElementById('submit').click()" class="carica">carica</button>
    </form>

    <button onclick="location.href='adminDashboard.php?username=admin'" class="dashboard">dashboard</button>
    
    <div class="avvertimento">
        <img src="IMG/danger.png" class="pericolo">
        <p>attenzione non eliminare la prima riga dei template con i nomi dei campi</p>
    </div>

    <script>

        //gestione files
        function downloadFile(filePath) {
            const link = document.createElement('a');
            link.href = filePath;
            link.download = getFileNameFromPath(filePath);
            link.target = "_blank";
            link.style.display = "none";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function getFileNameFromPath(filePath) {
            const parts = filePath.split('/');
            return parts[parts.length - 1];
        }


        document.getElementById('utenti_btn').addEventListener('click', function() {
            document.getElementById('utenti').click(); // Simula il clic sull'input file
        });

        document.getElementById('comuni_btn').addEventListener('click', function() {
            document.getElementById('comuni').click(); // Simula il clic sull'input file
        });
        document.getElementById('eventi_btn').addEventListener('click', function() {
            document.getElementById('eventi').click(); // Simula il clic sull'input file
        });
        document.getElementById('abbonamenti_btn').addEventListener('click', function() {
            document.getElementById('abbonamenti').click(); // Simula il clic sull'input file
        });
        document.getElementById('rapporti_btn').addEventListener('click', function() {
            document.getElementById('rapporti').click(); // Simula il clic sull'input file
        });
        document.getElementById('contatti_btn').addEventListener('click', function() {
            document.getElementById('contatti').click(); // Simula il clic sull'input file
        });
        document.getElementById('registro_btn').addEventListener('click', function() {
            document.getElementById('registro').click(); // Simula il clic sull'input file
        });

        // Aggiungi gli altri eventi click per gli altri bottoni

        // Mostra il nome del file selezionato
        document.getElementById('utenti').addEventListener('change', function(e) {
            var fileInput = e.target;
            var fileName = fileInput.files[0].name;
            fileInput.parentNode.querySelector('span').innerText = fileName;
        });
        document.getElementById('comuni').addEventListener('change', function(e) {
            var fileInput = e.target;
            var fileName = fileInput.files[0].name;
            fileInput.parentNode.querySelector('span').innerText = fileName;
        });
        document.getElementById('eventi').addEventListener('change', function(e) {
            var fileInput = e.target;
            var fileName = fileInput.files[0].name;
            fileInput.parentNode.querySelector('span').innerText = fileName;
        });
        document.getElementById('abbonamenti').addEventListener('change', function(e) {
            var fileInput = e.target;
            var fileName = fileInput.files[0].name;
            fileInput.parentNode.querySelector('span').innerText = fileName;
        });
        document.getElementById('rapporti').addEventListener('change', function(e) {
            var fileInput = e.target;
            var fileName = fileInput.files[0].name;
            fileInput.parentNode.querySelector('span').innerText = fileName;
        });
        document.getElementById('contatti').addEventListener('change', function(e) {
            var fileInput = e.target;
            var fileName = fileInput.files[0].name;
            fileInput.parentNode.querySelector('span').innerText = fileName;
        });
        document.getElementById('registro').addEventListener('change', function(e) {
            var fileInput = e.target;
            var fileName = fileInput.files[0].name;
            fileInput.parentNode.querySelector('span').innerText = fileName;
        });

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
        }catch(PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    function caricaUtenti($utenti, $connessione) {
        if ($utenti == null) {
            return;
        }

        $ctr=1;
        
        $file = fopen($utenti, 'r'); // Apri il file in modalità di lettura ('r')
        
        if ($file) {
            while (($data = fgetcsv($file)) !== false) {
                if(!utenteEsistente($data,$connessione) && usernameUtenteDisponibile($data,$connessione) && $ctr==0){
                    memorizzaUtente($data,$connessione);
                }else{
                    $ctr--;
                }
            }
            
            fclose($file);
        } else {
            echo "Impossibile aprire il file.";
        }
    }
    function utenteEsistente($data,$connessione){
        $stmt = $connessione->prepare("SELECT * FROM `utente` WHERE `nome` = ? AND `cognome` = ? AND `email` = ? AND `username` = ? AND `password` = ?");
        $stmt->execute([$data[0], $data[1],$data[2],$data[3], $data[4]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user !== false){
            return true;
        }
        return false;
    }
    function usernameUtenteDisponibile($data,$connessione){
        $stmt = $connessione->prepare("SELECT * FROM `utente` WHERE `username` = ?");
        $stmt->execute([$data[3]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user == false){
            return true;
        }
        return false;
    }
    function memorizzaUtente($data,$connessione){
        $stmt = $connessione->prepare("INSERT INTO `utente`(`nome`, `cognome`, `email`, `username`, `password`) VALUES (?,?,?,?,?)");
        $stmt->execute([$data[0], $data[1],$data[2],$data[3], $data[4]]);
        return;
    }

    function caricaComuni($comuni, $connessione) {
        if ($comuni == null) {
            return;
        }
        $ctr=1;
        
        $file = fopen($comuni, 'r'); // Apri il file in modalità di lettura ('r')
        
        if ($file) {
            while (($data = fgetcsv($file)) !== false) {
                if(!comuneEsistente($data,$connessione) && usernameComuneDisponibile($data,$connessione) && $ctr==0){
                    memorizzaComune($data,$connessione);
                }else{
                    $ctr--;
                }
            }
            
            fclose($file);
        } else {
            echo "Impossibile aprire il file.";
        }
    }

    function comuneEsistente($data,$connessione){
        $stmt = $connessione->prepare("SELECT * FROM `Comuni` WHERE `denominazione` = ? AND `indirizzo` = ? AND `email` = ? AND `referente` = ? AND `telefono_referente` = ? AND `username` = ? AND `password` = ?");
        $stmt->execute([$data[0], $data[1],$data[2],$data[3], $data[4], $data[5], $data[6]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user !== false){
            return true;
        }
        return false;
    }

    function usernameComuneDisponibile($data,$connessione){
        $stmt = $connessione->prepare("SELECT * FROM `Comuni` WHERE `username` = ?");
        $stmt->execute([$data[5]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user == false){
            return true;
        }
        return false;
    }

    function memorizzaComune($data,$connessione){
        $stmt = $connessione->prepare("INSERT INTO `Comuni`(`denominazione`, `indirizzo`, `email`, `referente`, `telefono_referente`, `username`, `password`) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$data[0], $data[1],$data[2],$data[3], $data[4], $data[5], $data[6]]);
        return;
    }

    function caricaEventi($eventi, $connessione) {
        if ($eventi == null) {
            return;
        }
        $ctr=1;
        $file = fopen($eventi, 'r'); // Apri il file in modalità di lettura ('r')
        
        if ($file) {
            while (($data = fgetcsv($file)) !== false) {
                if(!eventoEsistente($data,$connessione) && $ctr==0){
                    memorizzaEvento($data,$connessione);
                }else{
                    $ctr--;
                }
            }
            
            fclose($file);
        } else {
            echo "Impossibile aprire il file.";
        }
    }

    function eventoEsistente($data,$connessione){
        $stmt = $connessione->prepare("SELECT * FROM `evento` WHERE `Id_comune` = ? AND `nome` = ? AND `indirizzo` = ?");
        $stmt->execute([$data[0], $data[1],$data[2]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user !== false){
            return true;
        }
        return false;
    }

    function memorizzaEvento($data,$connessione){
        $stmt = $connessione->prepare("INSERT INTO `evento`(`Id_comune`, `nome`, `indirizzo`) VALUES (?,?,?)");
        $stmt->execute([$data[0], $data[1],$data[2]]);
        return;
    }

    function caricaAbbonamenti($abbonamenti, $connessione) {
        if ($abbonamenti == null) {
            return;
        }
        $ctr=1;
        $file = fopen($abbonamenti, 'r'); // Apri il file in modalità di lettura ('r')
        
        if ($file) {
            while (($data = fgetcsv($file)) !== false) {
                if(!abbonamentoEsistente($data,$connessione) && $ctr==0){
                    memorizzaAbbonamenti($data,$connessione);
                }else{
                    $ctr--;
                }
            }
            
            fclose($file);
        } else {
            echo "Impossibile aprire il file.";
        }
    }

    function abbonamentoEsistente($data,$connessione){
        $stmt = $connessione->prepare("SELECT * FROM `abbonamento` WHERE `Id_comune` = ? AND `tipo` = ? AND `anno` = ?");
        $stmt->execute([$data[0], $data[1],$data[2]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user !== false){
            return true;
        }
        return false;
    }

    function memorizzaAbbonamenti($data,$connessione){
        $stmt = $connessione->prepare("INSERT INTO `abbonamento`(`Id_comune`, `tipo`, `anno`) VALUES (?,?,?)");
        $stmt->execute([$data[0], $data[1],$data[2]]);
        return;
    }


    function caricaRapporti($rapporti, $connessione) {
        if ($rapporti == null) {
            return;
        }
        $ctr=1;
        $file = fopen($rapporti, 'r'); // Apri il file in modalità di lettura ('r')
        
        if ($file) {
            while (($data = fgetcsv($file)) !== false) {
                if(!rapportoEsistente($data,$connessione) && $ctr==0){
                    memorizzaRapporto($data,$connessione);
                }else{
                    $ctr--;
                }
            }
            
            fclose($file);
        } else {
            echo "Impossibile aprire il file.";
        }
    }

    function rapportoEsistente($data,$connessione){
        $stmt = $connessione->prepare("SELECT * FROM `rapporti` WHERE `Id_comune` = ? AND `tipo` = ? AND `titolare` = ? AND `numero_protocollo` = ?");
        $stmt->execute([$data[0], $data[1],$data[2],$data[3]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user !== false){
            return true;
        }
        return false;
    }

    function memorizzaRapporto( $data,$connessione){
        $stmt = $connessione->prepare("INSERT INTO `rapporti`(`Id_comune`, `tipo`, `titolare`, `numero_protocollo`, `importo`,`accettazione_proposta`) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$data[0], $data[1],$data[2],$data[3],$data[4],$data[5]]);
        return;
    }

    function caricaContatti($contatti, $connessione) {
        if ($contatti == null) {
            return;
        }
        $ctr=1;
        $file = fopen($contatti, 'r'); // Apri il file in modalità di lettura ('r')
        
        if ($file) {
            while (($data = fgetcsv($file)) !== false) {
                if(!contattoEsistente($data,$connessione) && $ctr==0){
                    memorizzaContatto($data,$connessione);
                }else{
                    $ctr--;
                }
            }
            
            fclose($file);
        } else {
            echo "Impossibile aprire il file.";
        }
    }

    function contattoEsistente($data,$connessione){
        $stmt = $connessione->prepare("SELECT * FROM `referenze` WHERE `Id_comune` = ? AND `Id_evento` = ? AND `nome` = ? AND `email` = ? AND `telefono` = ?");
        $stmt->execute([$data[0], $data[1],$data[2],$data[3],$data[4]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user !== false){
            return true;
        }
        return false;
    }

    function memorizzaContatto( $data,$connessione){
        $stmt = $connessione->prepare("INSERT INTO `referenze`(`Id_comune`, `Id_evento`, `nome`, `email`, `telefono`) VALUES (?,?,?,?,?)");
        $stmt->execute([$data[0], $data[1],$data[2],$data[3],$data[4]]);
        return;
    }


    function caricaRegistro($registro, $connessione) {
        if ($registro == null) {
            return;
        }
        $ctr=1;
        $file = fopen($registro, 'r'); // Apri il file in modalità di lettura ('r')
        
        if ($file) {
            while (($data = fgetcsv($file)) !== false) {
                if(!registroEsistente($data,$connessione) && $ctr==0){
                    memorizzaRegistro($data,$connessione);
                }else{
                    $ctr--;
                }
            }
            
            fclose($file);
        } else {
            echo "Impossibile aprire il file.";
        }
    }

    function registroEsistente($data,$connessione){
        $stmt = $connessione->prepare("SELECT * FROM `registro` WHERE `Id_comune` = ? AND `Id_user` = ? AND `nome_user` = ? AND `cognome_user` = ? AND `email_user` = ? AND `sede_evento` = ? AND `formazione_integrata` = ? AND `singoli_eventi` = ? AND `tipo_abbonamento` = ?");
        $stmt->execute([$data[0], $data[1],$data[2],$data[3],$data[4], $data[5],$data[6],$data[7],$data[8]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user !== false){
            return true;
        }
        return false;
    }

    function memorizzaRegistro( $data,$connessione){
        $stmt = $connessione->prepare("INSERT INTO `registro`(`Id_comune`, `Id_user`, `nome_user`, `cognome_user`, `email_user`, `sede_evento`, `formazione_integrata`, `singoli_eventi`, `tipo_abbonamento`) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$data[0], $data[1],$data[2],$data[3],$data[4], $data[5],$data[6],$data[7],$data[8]]);
        return;
    }
?>