<?php
@require "config.php";
session_start();
if(isset($_GET['comune'])){
    $username = $_GET['comune'];
}
$connessione = instauraConnessione();

if(isset($_POST['aggiorna'])){
    $eliminati = isset($_POST['elimina_eventi']) ? $_POST['elimina_eventi'] : array();
    eliminaEventi($eliminati,$connessione);
    
    $eliminati = isset($_POST['elimina_rapporti']) ? $_POST['elimina_rapporti'] : array();
    eliminaRapporti($eliminati,$connessione);
    
    $eliminati = isset($_POST['elimina_referenze']) ? $_POST['elimina_referenze'] : array();
    eliminaReferenze($eliminati,$connessione);
    
    $eliminati = isset($_POST['elimina_abbonamenti']) ? $_POST['elimina_abbonamenti'] : array();
    eliminaAbbonamenti($eliminati,$connessione);
    
    $eliminati = isset($_POST['elimina_prenotazioni']) ? $_POST['elimina_prenotazioni'] : array();
    eliminaPrenotazioni($eliminati,$connessione);

    $eliminati = isset($_POST['elimina_email']) ? $_POST['elimina_email'] : array();
    eliminaEmail($eliminati,$connessione);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
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
                scroll-behavior: smooth;
            }

            .titolo{
                font-size: 4vw;
                font-weight: 700;
            }

            .action{
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                width: 80%;
                margin-top: 2%;
            }

            .action>button{
                width: 10%;
                background-color: var(--secondario);
                font-weight: 600;
                padding: 0.5% 0%;
                cursor: pointer;
                border-radius: 5px;
                border-color: transparent;
                transition-duration: 0.2s;
            }

            .action>button:hover{
                background-color: var(--primario);
            }

            form{
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 5% 2%;
                margin-top: 2%;
                width: 90%;
            }

            form>button{
                width: 16%;
                background-color: var(--secondario);
                font-weight: 600;
                padding: 0.5% 0%;
                cursor: pointer;
                border-radius: 5px;
                border-color: transparent;
                transition-duration: 0.2s;
            }

            form>button:hover{
                background-color: var(--primario);
            }

            table{
                /*border: 1px solid greenyellow;*/
                width: 96%;
                margin: 5% 0%;
                border-collapse: collapse;
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
                border-color:var(--attivo);
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

            td{
                text-align: center;
                border-left: 2px solid black;
                border-right: 2px solid black;
            }

            th{
                text-align: center;
                border-left: 2px solid black;
                border-right: 2px solid black;
            }

            .titolo_tabella{
                font-weight: 800;
                border-left: 2px solid transparent;
                border-right: 2px solid transparent;
                padding-bottom: 2%;
            }

            .inserisci{
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 5% 2%;
                width: 90%;
            }

            .inserisci_testo{
                font-weight: 600;
                font-size: 150%;
            }

            .inserisci>.bottoni{
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                width: 60%;
                margin-top: 1%;
            }

            .inserisci>.bottoni>button{
                width: 18%;
                background-color: var(--secondario);
                font-weight: 600;
                padding: 0.5% 0%;
                cursor: pointer;
                border-radius: 5px;
                border-color: transparent;
                transition-duration: 0.2s;
            }
            .inserisci>.bottoni>button:hover{
                background-color: var(--primario);
            }

            td>button{
                width: 10%;
                background-color: var(--secondario);
                font-weight: 600;
                padding: 0.5% 0%;
                cursor: pointer;
                border-radius: 5px;
                border-color: transparent;
                transition-duration: 0.2s;
                margin-top: 2%;
            }
            td>button:hover{
                background-color: var(--primario);
            }

            .inserisci>button{
                width: 10%;
                background-color: var(--secondario);
                font-weight: 600;
                padding: 0.5% 0%;
                cursor: pointer;
                border-radius: 5px;
                border-color: transparent;
                transition-duration: 0.2s;
                margin-top: 2%;
            }
            .inserisci>button:hover{
                background-color: var(--primario);
            }

            @media (max-width: 1450px) {
                .inserisci>.bottoni{
                    width:100%;
                }
                
                .titolo{
                    margin-top: 2%;
                }

                form{
                    width:98%;
                }

                .action{
                    flex-direction: column;
                }

                .action>button{
                    margin: 2% 0%;
                    width: 50%;
                }

                .inserisci>button{
                    width:25%
                }

                td>button{
                    width: 25%;
                }
            }
        </style>
    </head>
    <body>
        <div class="titolo">informazioni comune: <?php echo $username; ?></div> <!-- Aggiunta la parentesi graffa mancante -->
        <div class="action" id="action">
            <button onclick="window.location.href='#eventi'">eventi</button> <!-- Utilizzato onclick per gestire il reindirizzamento -->
            <button onclick="window.location.href='#rapporti'">rapporti</button>
            <button onclick="window.location.href='#referenze'">referenze</button>
            <button onclick="window.location.href='#abbonamenti'">abbonamenti</button>
            <button onclick="window.location.href='#prenotazioni'">prenotazioni</button>
            <button onclick="window.location.href='#email'">email</button>
            <button onclick="location.href='adminDashboard.php?username=admin'">dashboard</button>
            <button onclick="location.href='gestisci_comuni.php?username=admin'">torna indietro</button>
        </div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?comune=" . $username; ?>">
            <table id="eventi">
                <tr>
                    <td colspan="4" class="titolo_tabella">Eventi</td>
                </tr>
                <tr>
                    <th>id</th>
                    <th>nome</th>
                    <th>indirizzo</th>
                    <th>elimina</th>
                </tr>
                <?php
                $stmt = $connessione->prepare("SELECT * FROM `evento` WHERE `Id_comune`=?");
                $stmt->execute([getIdComuneByUsername($username, $connessione)]);
                $eventi = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($eventi as $evento) { ?>
                    <tr>
                        <td><?php echo $evento['Id'] ?></td>
                        <td><?php echo $evento['nome'] ?></td>
                        <td><?php echo $evento['indirizzo'] ?></td>
                        <td><input type="checkbox" name="elimina_eventi[]" value="<?php echo $evento['Id']; ?>"></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <!------------------------------------------------------------>
            <table id="rapporti">
                <tr>
                    <td colspan="7" class="titolo_tabella">Rapporti</td>
                </tr>
                <tr>
                    <th>id</th>
                    <th>tipo</th>
                    <th>titolare</th>
                    <th>protocollo</th>
                    <th>importo</th>
                    <th>proposta</th>
                    <th>elimina</th>
                </tr>
                <?php
                $stmt = $connessione->prepare("SELECT * FROM `rapporti` WHERE `Id_comune`=?");
                $stmt->execute([getIdComuneByUsername($username, $connessione)]);
                $rapporti = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rapporti as $rapporto) { ?>
                    <tr>
                        <td><?php echo $rapporto['Id'] ?></td>
                        <td><?php echo $rapporto['tipo'] ?></td>
                        <td><?php echo $rapporto['titolare'] ?></td>
                        <td><?php echo $rapporto['numero_protocollo'] ?></td>
                        <td><?php echo $rapporto['importo'] ?></td>
                        <td>
                            <?php if($rapporto['accettazione_proposta']==1){
                                echo 'accettata';
                            }else{
                                echo 'declinata';
                            }?>
                        </td>
                        <td><input type="checkbox" name="elimina_rapporti[]" value="<?php echo $rapporto['Id']; ?>"></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <!------------------------------------------------------------>
            <table id="referenze">
                <tr>
                    <td colspan="6" class="titolo_tabella">Referenze</td>
                </tr>
                <tr>
                    <th>id</th>
                    <th>evento</th>
                    <th>nome</th>
                    <th>email</th>
                    <th>telefono</th>
                    <th>elimina</th>
                </tr>
                <?php
                $stmt = $connessione->prepare("SELECT * FROM `referenze` WHERE `Id_comune`=?");
                $stmt->execute([getIdComuneByUsername($username, $connessione)]);
                $referenze = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($referenze as $referenza) { ?>
                    <tr>
                        <td><?php echo $referenza['Id'] ?></td>
                        <td><?php echo getNomeEventoById($referenza['Id_evento'],$connessione) ?></td>
                        <td><?php echo $referenza['nome'] ?></td>
                        <td><?php echo $referenza['email'] ?></td>
                        <td><?php echo $referenza['telefono'] ?></td>
                        <td><input type="checkbox" name="elimina_referenze[]" value="<?php echo $referenza['Id']; ?>"></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <!------------------------------------------------------------>
            <table id="abbonamenti">
                <tr>
                    <td colspan="4" class="titolo_tabella">Abbonamenti</td>
                </tr>
                <tr>
                    <th>id</th>
                    <th>tipo</th>
                    <th>anno</th>
                    <th>elimina</th>
                </tr>
                <?php
                $stmt = $connessione->prepare("SELECT * FROM `abbonamento` WHERE `Id_comune`=?");
                $stmt->execute([getIdComuneByUsername($username, $connessione)]);
                $abbonamenti = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($abbonamenti as $abbonamento) { ?>
                    <tr>
                        <td><?php echo $abbonamento['Id'] ?></td>
                        <td><?php echo $abbonamento['tipo'] ?></td>
                        <td><?php echo $abbonamento['anno'] ?></td>
                        <td><input type="checkbox" name="elimina_abbonamenti[]" value="<?php echo $abbonamento['Id']; ?>"></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <!------------------------------------------------------------>
            <table id="prenotazioni">
                <tr>
                   <td colspan="7" class="titolo_tabella">Prenotazioni</td>
                </tr>
                <tr>
                    <th>id</th>
                    <th>utente</th>
                    <th>evento</th>
                    <th>f. integrata</th>
                    <th>abbonamento</th>
                    <th>fattura</th>
                    <th>elimina</th>
                </tr>
                <?php
                $stmt = $connessione->prepare("SELECT * FROM `registro` WHERE `Id_comune`=?");
                $stmt->execute([getIdComuneByUsername($username, $connessione)]);
                $prenotazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($prenotazioni as $prenotazione) { ?>
                    <tr>
                        <td><?php echo $prenotazione['Id'] ?></td>
                        <td><?php echo getUsernameByIdUser($prenotazione['Id_user'],$connessione) ?></td>
                        <td><?php echo getNomeEventoById($prenotazione['Id_evento'],$connessione) ?></td>
                        <td><?php
                            if($prenotazione['formazione_integrata']=1){
                                echo 'attiva';
                            }else{
                                echo 'disattiva';
                            }
                        ?></td>
                        <td><?php echo $prenotazione['tipo_abbonamento'] ?></td>
                        <td><?php
                            if($prenotazione['fattura_pagata']!=null){
                                echo 'saldata';
                            }else{
                                echo 'in attesa';
                            }
                        ?></td>
                        <td><input type="checkbox" name="elimina_prenotazioni[]" value="<?php echo $prenotazione['Id']; ?>"></td>
                    </tr>
                    <?php
                }
                ?>
            </table>

            <!------------------------------------------------------------>
            <table id="email">
                <tr>
                   <td colspan="7" class="titolo_tabella">email</td>
                </tr>
                <tr>
                    <th>id</th>
                    <th>impiego</th>
                    <th>email</th>
                    <th>elimina</th>
                </tr>
                <?php
                $stmt = $connessione->prepare("SELECT * FROM `email` WHERE `Id_comune`=?");
                $stmt->execute([getIdComuneByUsername($username, $connessione)]);
                $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($emails as $email) { ?>
                    <tr>
                        <td><?php echo $email['Id'] ?></td>
                        <td><?php echo $email['utilizzo']?></td>
                        <td><?php echo $email['email'] ?></td>
                        <td><input type="checkbox" name="elimina_email[]" value="<?php echo $email['Id']; ?>"></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="4"><button onclick="scaricaEmail(<?php echo htmlspecialchars(json_encode($emails)); ?>)">scarica email</button></td>
                </tr>
            </table>
            

            <input type="submit" name="aggiorna" style="display:none;" id="aggiorna">
            <button onclick="document.getElementById('aggiorna').click()">aggiorna</button>
        </form>

        <div class="inserisci">
            <div class="inserisci_testo">inserisci dati manualmente</div>
            <div class="bottoni">
                <button onclick="window.location.href='inserisciDatiComune.php?comune=<?php echo $username; ?>&modulo=evento'">eventi</button>
                <button onclick="window.location.href='inserisciDatiComune.php?comune=<?php echo $username; ?>&modulo=rapporto'">rapporti</button>
                <button onclick="window.location.href='inserisciDatiComune.php?comune=<?php echo $username; ?>&modulo=referenza'">referenze</button>
                <button onclick="window.location.href='inserisciDatiComune.php?comune=<?php echo $username; ?>&modulo=abbonamento'">abbonamenti</button>
                <button onclick="window.location.href='inserisciDatiComune.php?comune=<?php echo $username; ?>&modulo=email'">email</button>
            </div>
            <button onclick="window.location.href='#action'">torna su</button>
        </div>
        <script>
            function scaricaEmail(email) {
                // Contenuto del file
                var contenuto = "utilizzo,email\n";
                for(var i=0;i<email.length;i++){
                    contenuto+=email[i]['utilizzo']+",";
                    contenuto+=email[i]['email']+",";
                }
                // Creazione dell'elemento <a> per il download
                var downloadLink = document.createElement("a");
                downloadLink.href = "data:text/plain;charset=utf-8," + encodeURIComponent(contenuto);
                downloadLink.download = "email.csv";

                // Aggiunta dell'elemento <a> al DOM e simulazione del clic per avviare il download
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            }
        </script>
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
        echo $e->getMessage();
        return false;
    }
}

function getIdComuneByUsername($username, $connessione)
{
    $stmt = $connessione->prepare("SELECT `Id` FROM `Comuni` WHERE `username` = ?");
    $stmt->execute([$username]);
    $result = $stmt->fetch();

    if ($result) {
        return $result['Id'];
    } else {
        return null;
    }
}

function getNomeEventoById($id_evento, $connessione)
{
    $stmt = $connessione->prepare("SELECT `nome` FROM `evento` WHERE `Id` = ?");
    $stmt->execute([$id_evento]);
    $result = $stmt->fetch();
    return $result['nome'];
}

function getUsernameByIdUser($id_user,$connessione){
    $stmt = $connessione->prepare("SELECT `username` FROM `utente` WHERE `Id` = ?");
    $stmt->execute([$id_user]);
    $result = $stmt->fetch();
    return $result['username'];
}

function eliminaEventi($eliminati,$connessione){      
    foreach($eliminati as $eliminato){
        $stmt=$connessione->prepare("DELETE FROM `evento` WHERE `Id` = ?");
        $stmt->execute([$eliminato]);
    }
}

function eliminaRapporti($eliminati,$connessione){      
    foreach($eliminati as $eliminato){
        $stmt=$connessione->prepare("DELETE FROM `rapporti` WHERE `Id` = ?");
        $stmt->execute([$eliminato]);
    }
}

function eliminaReferenze($eliminati,$connessione){      
    foreach($eliminati as $eliminato){
        $stmt=$connessione->prepare("DELETE FROM `referenze` WHERE `Id` = ?");
        $stmt->execute([$eliminato]);
    }
}
function eliminaAbbonamenti($eliminati,$connessione){      
    foreach($eliminati as $eliminato){
        $stmt=$connessione->prepare("DELETE FROM `abbonamento` WHERE `Id` = ?");
        $stmt->execute([$eliminato]);
    }
}
function eliminaPrenotazioni($eliminati,$connessione){      
    foreach($eliminati as $eliminato){
        $stmt=$connessione->prepare("DELETE FROM `registro` WHERE `Id` = ?");
        $stmt->execute([$eliminato]);
    }
}

function eliminaEmail($eliminati,$connessione){      
    foreach($eliminati as $eliminato){
        $stmt=$connessione->prepare("DELETE FROM `email` WHERE `Id` = ?");
        $stmt->execute([$eliminato]);
    }
}
?>
