    <?php
        @require 'config.php';
        session_reset();
        $connessione = instauraConnessione();
        
        if(isset($_GET['username'])){
            $username=$_GET['username'];
        }

        if(isset($_POST['registra_evento'])){
            if(!eventoGiaEsistente($_POST['nome'],$_POST['indirizzo'],$connessione)){
                $stmt=$connessione->prepare("INSERT INTO `evento` (`Id_comune`, `nome`, `indirizzo`) VALUES (?,?,?)");
                $stmt->execute([getIdComuneByUsername($username,$connessione),$_POST['nome'],$_POST['indirizzo']]);
            }
        }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>aggiungi eventi</title>
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
            margin-top:2%;
            font-size: 4vw;
            font-weight: 700;
        }

        .action{
            margin: 2%;
            width: 25%;
            display: flex;
            flex-direction: row;
            justify-content: center;
        }
        button{
            font-weight: 600;
            position: sticky;
            margin: 0.5% 5%;
            padding: 0.5% 2%;
            border-radius: 5px;
            border-color: transparent;
            background-color: var(--secondario);
            cursor: pointer;
            transition-duration:0.1s;
        }

        button:hover{
            background-color: var(--attivo);
        }

        form{
            width: 25%;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            margin-bottom: 2%;
        }

        form>input{
            font-weight: 700;
            padding: 0.5% 0.5%;
            border-radius: 5px;
            border: 2px solid var(--secondario);
            background-color: transparent;
            transition-duration: 0.2s;
        }

        form>input:hover{
            background-color: var(--secondario);
        }

        @media (max-width: 1850px) {
            form{
                flex-direction: column;
                align-items: center;
            }

            form>input{
                margin: 2%
            }
        }

        @media (max-width: 520px) {
            .action{
                width: 80%;
            }
            form{
                margin-top: 5%;
            }
            form>input{
                margin: 5%
            }
        }
    </style>
    </head>
    <body>
        <div class="titolo">aggiungi evento</div>
        <div class="action">
        <button onclick="location.href='gestisci_eventi.php?username=<?php echo $username ?>'">torna indietro</button>
        <button onclick="location.href='comuniDashboard.php?username=<?php echo $username ?>'">dashboard</button>
        </div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?username=" . $username ?>">
            <input type="text" name="nome" placeholder="nome">
            <input type="text" name="indirizzo" placeholder="indirizzo">
            <input type="submit" name="registra_evento" id="submit" style="display:none;">
        </form>
        <button onclick="document.getElementById('submit').click()">registra evento</button> 
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

        function eventoGiaEsistente($nome,$indirizzo,$connessione){
            $stmt=$connessione->prepare("SELECT COUNT(*) FROM `evento` WHERE `nome` = ? AND `indirizzo` = ? ");
            $stmt->execute([$nome, $indirizzo   ]);
            $count = $stmt->fetchColumn();
            return $count != 0; // true se ce gia false se non ce
        }

        function getIdComuneByUsername($username, $connessione) {
            $stmt = $connessione->prepare("SELECT `Id` FROM `Comuni` WHERE `username` = ?");
            $stmt->execute([$username]);
            $result = $stmt->fetch();
            
            if ($result) {
                return $result['Id'];
            } else {
                return null;
            }
        }
    ?>