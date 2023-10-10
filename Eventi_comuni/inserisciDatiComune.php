<?php
    @require 'config.php';
    session_start();
    $username=$_GET['comune'];
    $modulo=$_GET['modulo'];
    $connessione = instauraConnessione();

    if(isset($_POST['registra_evento'])){
        if(!eventoGiaEsistente($_POST['nome'],$_POST['indirizzo'],$connessione)){
            $stmt=$connessione->prepare("INSERT INTO `evento` (`Id_comune`, `nome`, `indirizzo`) VALUES (?,?,?)");
            $stmt->execute([getIdComuneByUsername($username,$connessione),$_POST['nome'],$_POST['indirizzo']]);
        }
    }else if(isset($_POST['registra_rapporto'])){
        if(!rapportoGiaEsistente($_POST['tipo'],$_POST['titolare'],$_POST['nProtocollo'],$connessione)){
            $stmt=$connessione->prepare("INSERT INTO `rapporti` (`Id_comune`, `tipo`, `titolare`, `numero_protocollo`, `importo`,`accettazione_proposta`) VALUES (?,?,?,?,?,?)");
            $stmt->execute([getIdComuneByUsername($username,$connessione),$_POST['tipo'],$_POST['titolare'],$_POST['nProtocollo'],$_POST['importo'],$_POST['accettazione_proposta']]);
        }
    }else if(isset($_POST['registra_referenze'])){
        if(!referenzeGiaEsistente($_POST['nome'],$_POST['evento'],$_POST['email'],$_POST['telefono'],$connessione)){
            $stmt=$connessione->prepare("INSERT INTO `referenze` (`Id_comune`, `nome`, `Id_evento`,`email`,`telefono`) VALUES (?,?,?,?,?)");
            $stmt->execute([getIdComuneByUsername($username,$connessione),$_POST['nome'],$_POST['evento'],$_POST['email'],$_POST['telefono']]);
        }
    }else if(isset($_POST['registra_abbonamento'])){
        if(!abbonamentoGiaEsistente($_POST['tipo'],$_POST['anno'],$connessione)){
            $stmt=$connessione->prepare("INSERT INTO `abbonamento` (`Id_comune`, `tipo`, `anno`) VALUES (?,?,?)");
            $stmt->execute([getIdComuneByUsername($username,$connessione),$_POST['tipo'],$_POST['anno']]);
        }
    }else if(isset($_POST['registra_email'])){
        if(!emailGiaEsistente($_POST['utilizzo'],$_POST['email'],$connessione)){
            $stmt=$connessione->prepare("INSERT INTO `email` (`Id_comune`, `utilizzo`, `email`) VALUES (?,?,?)");
            $stmt->execute([getIdComuneByUsername($username,$connessione),$_POST['utilizzo'],$_POST['email']]);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>inserimento dati</title>
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

            .action{
                margin-top: 2%;
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                width: 25%;
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
                width:45%;
            }

            .action>button:hover{
                background-color: var(--attivo);
            }

            form{
                margin-top: 2%;
                width: 60%;
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }

            form>input{
                background-color: var(--sfondo);
                border: 2px solid var(--secondario);
                border-radius: 5px;
                padding: 0.5% 0%;
                margin: 2% 0%;
            }

            button{
                font-weight: 600;
                position: sticky;
                margin: 2% 5%;
                padding: 0.5% 2%;
                border-radius: 5px;
                border-color: transparent;
                background-color: var(--secondario);
                cursor: pointer;
                transition-duration:0.1s;
                width:15%;
            }

            button:hover{
                background-color: var(--attivo);
            }

            #select_evento{
                font-weight: 400;
                min-width: 25%;
                max-width: 80%;
                background-color: transparent;
                border: 2px solid var(--secondario);
                border-radius: 5px;
                cursor: pointer;
                margin: 2% 0%;
            }

            @media (max-width: 1450px) {
                form{
                    flex-direction: column;
                }

                .titolo{
                    margin-top: 10%;
                }

                #select-evento{
                    width: 100%;
                }
                .action{
                    width: 60%;
                }

                button{
                    width:50%;
                }
            }
        </style>
    </head>
    <body>
        <div class="titolo">inserimento
            <?php 
                if($modulo=="evento"){
                    echo 'evento';
                }else if($modulo=="rapporto"){
                    echo 'rapporto';
                }else if($modulo=="referenza"){
                    echo 'referenza';
                }else if($modulo=="abbonamento"){
                    echo 'abbonamento';
                }
            ?>
            <?php echo $username ?>
        </div>
        <div class="action">
            <button onclick="location.href='adminDashboard.php?username=admin'">dashboard</button>
            <button onclick="location.href='<?php echo 'informazioniComune.php?comune='.$username; ?>'">torna indietro</button>
        </div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?comune=" . $username."&modulo=".$modulo; ?>">
            <?php if($modulo=="evento"){ ?>
        
        
                <input type="text" name="nome" placeholder="nome">
                <input type="text" name="indirizzo" placeholder="indirizzo">
                <input type="submit" name="registra_evento" id="submit" style="display:none;">
            
            
            <?php }else if($modulo=="rapporto"){ ?>
                
                
                <input type="text" name="tipo" placeholder="tipologia">
                <input type="text" name="titolare" placeholder="titolare">
                <input type="number" name="nProtocollo" placeholder="numero protocollo">
                <input type="number" name="importo" placeholder="importo">
                <input type="number" name="accettazione_proposta" placeholder="accettazione proposta">
                <input type="submit" name="registra_rapporto" id="submit" style="display:none;">


            <?php }else if($modulo=="referenza"){ ?>
        
        
                <input type="text" name="nome" placeholder="nome">
                <select name="evento" id="select_evento">
                <?php   $stmt = $connessione->prepare("SELECT * FROM `evento` WHERE `Id_comune`=?");
                        $stmt->execute([getIdComuneByUsername($username,$connessione)]);
                        $eventi = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($eventi as $evento){ ?>
                            <option value="<?php echo $evento['Id'] ?>"><?php echo $evento['nome'] ?></option>
                <?php   } ?>
                </select>
                <input type="text" name="email" placeholder="email">
                <input type="number" name="telefono" placeholder="telefono">
                <input type="submit" name="registra_referenze" id="submit" style="display:none;">
        
        
            <?php }else if($modulo=="abbonamento"){ ?>
       
       
                <input type="text" name="tipo" placeholder="tipo">
                <input type="text" name="anno" placeholder="anno">
                <input type="submit" name="registra_abbonamento" id="submit" style="display:none;">
       
       
            <?php }else if($modulo=="email"){ ?>
                
                
                <input type="text" name="utilizzo" placeholder="impiego">
                <input type="text" name="email" placeholder="email">
                <input type="submit" name="registra_email" id="submit" style="display:none;">


            <?php } ?>
        </form>
        <button onclick="document.getElementById('submit').click()">registra 
            <?php 
                if($modulo=="evento"){
                    echo 'evento';
                }else if($modulo=="rapporto"){
                    echo 'rapporto';
                }else if($modulo=="referenza"){
                    echo 'referenza';
                }else if($modulo=="abbonamento"){
                    echo 'abbonamento';
                }else if($modulo=="email"){
                    echo 'email';
                }
            ?>
        </button> 
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

    function rapportoGiaEsistente($tipo,$titolare,$nProtocollo,$connessione){
        $stmt=$connessione->prepare("SELECT COUNT(*) FROM `rapporti` WHERE `tipo` = ? AND `titolare` = ? AND `numero_protocollo`=? ");
        $stmt->execute([$tipo, $titolare , $nProtocollo ]);
        $count = $stmt->fetchColumn();
        return $count != 0; // true se ce gia false se non ce
    }

    function referenzeGiaEsistente($nome,$evento,$email,$telefono,$connessione){
        $stmt=$connessione->prepare("SELECT COUNT(*) FROM `referenze` WHERE `nome` = ? AND `Id_evento` = ? AND `email`=? AND `telefono`=?");
        $stmt->execute([$nome, $evento,$email,$telefono   ]);
        $count = $stmt->fetchColumn();
        return $count != 0; // true se ce gia false se non ce
    }

    function abbonamentoGiaEsistente($tipo,$anno,$connessione){
        $stmt=$connessione->prepare("SELECT COUNT(*) FROM `abbonamento` WHERE `tipo` = ? AND `anno` = ? ");
        $stmt->execute([$tipo, $anno   ]);
        $count = $stmt->fetchColumn();
        return $count != 0; // true se ce gia false se non ce
    }

    function emailGiaEsistente($utilizzo,$email,$connessione){
        $stmt=$connessione->prepare("SELECT COUNT(*) FROM `email` WHERE `utilizzo` = ? AND `email` = ? ");
        $stmt->execute([$utilizzo, $email]);
        $count = $stmt->fetchColumn();
        return $count != 0; // true se ce gia false se non ce
    }
?>