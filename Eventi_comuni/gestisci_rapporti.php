<?php
    require 'config.php';
    session_start();
    $connessione = instauraConnessione();
    
    if (isset($_GET['username'])) {
        $username = $_GET['username'];
    }

    if (isset($_POST['aggiorna'])) {
        $eliminati = isset($_POST['elimina']) ? $_POST['elimina'] : array();
        eliminaRapporti($eliminati, $connessione);
    }

    if (isset($_POST['carica'])) {
        $stmt = $connessione->prepare("SELECT * FROM `rapporti` WHERE `Id_comune` = ?");
        $stmt->execute([getIdComuneByUsername($username, $connessione)]);
        $rapporti = $stmt->fetchAll();

        foreach ($rapporti as $rapporto) {
            $nome= "fattura".$rapporto['Id'];
            $file = $_FILES[$nome];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileDestination = 'rapporti/' . $fileName;
            move_uploaded_file($fileTmpName, $fileDestination);
            chmod($fileDestination, 0666);
            
            if($fileName!=""){
                $stmt = $connessione->prepare("UPDATE `rapporti` SET `fattura_pagata` = ?, `fattura_emessa` = ? WHERE `Id` = ?");
                $stmt->execute([$fileDestination, 1, $rapporto['Id']]);
            }
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gestisci rapporti</title>
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
            width:25%;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
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
    <div class="titolo">gestisci rapporti</div>
    <div class="action">
    <button onclick="location.href='comuniDashboard.php?username=<?php echo htmlspecialchars($username); ?>'">dashboard</button>
    <button onclick="location.href='aggiungi_rapporto.php?username=<?php echo htmlspecialchars($username); ?>'">aggiungi rapporto</button>
    </div>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?username=".htmlspecialchars($username); ?>" enctype="multipart/form-data">
        <table>
            <tr>
                <th>id</th>
                <th>tipo</th>
                <th>titolare</th>
                <th>n. protocollo</th>
                <th>importo</th>
                <th>proposta accettata</th>
                <th>fattura</th>
                <th>carica fattura</th>
                <th>ricevuta</th>
                <th>elimina</th>
            </tr>
            <?php
                $stmt = $connessione->prepare("SELECT * FROM `rapporti` WHERE `Id_comune` = ?");
                $stmt->execute([getIdComuneByUsername($username, $connessione)]);
                $rapporti = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rapporti as $rapporto) {
            ?>
            <tr>
                <td><?php echo $rapporto['Id']; ?></td>
                <td><?php echo $rapporto['tipo']; ?></td>
                <td><?php echo $rapporto['titolare']; ?></td>
                <td><?php echo $rapporto['numero_protocollo']; ?></td>
                <td><?php echo $rapporto['importo']; ?></td>
                <td><?php echo $rapporto['accettazione_proposta']; ?></td>
                <td>
                    <button onclick="scarica_fattura(
                        '<?php echo $rapporto['Id']; ?>',
                        '<?php echo htmlspecialchars($username); ?>',
                        '<?php echo $rapporto['tipo']; ?>',
                        '<?php echo $rapporto['titolare']; ?>',
                        '<?php echo $rapporto['numero_protocollo']; ?>',
                        '<?php echo $rapporto['importo']; ?>'
                    )">scarica fattura</button>
                </td>
                <td>
                    <input type="file" name="<?php echo "fattura".$rapporto['Id'] ?>" id="<?php echo "fattura".$rapporto['Id'] ?>" style="display:none;">
                    <button type="button" onclick="document.getElementById('<?php  echo 'fattura'.$rapporto['Id'] ?>').click()">carica fattura</button>
                </td>
                <td>
                    <?php if (fatturaGiaPagata($rapporto['Id'], $connessione)) { ?>
                        <button onclick="scarica_ricevuta(
                            '<?php echo $rapporto['Id']; ?>',
                            '<?php echo htmlspecialchars($username); ?>',
                            '<?php echo $rapporto['tipo']; ?>',
                            '<?php echo $rapporto['titolare']; ?>',
                            '<?php echo $rapporto['numero_protocollo']; ?>',
                            '<?php echo $rapporto['importo']; ?>'
                        )">scarica ricevuta</button>
                    <?php } ?>
                </td>
                <td><input type="checkbox" name="elimina[]" value="<?php echo $rapporto['Id']; ?>"></td>
            </tr>
            <?php } ?>
        </table>
        <input type="submit" name="aggiorna" style="display:none;" id="aggiorna">
        <input id="submit_carica" type="submit" name="carica" style="display:none;">
    </form>
    <div class="form_action"><button onclick="document.getElementById('aggiorna').click()">aggiorna</button>
    <button onclick="document.getElementById('submit_carica').click()">carica</button></div>


    <script>
        function scarica_fattura(Id, nomeComune, tipo, titolare, n_protocollo, importo) {
            // Contenuto del file
            var contenuto = "Fattura: il comune di " + nomeComune + " si impegna nel contratto di " + tipo + ",\ncon il titolare " + titolare + " per l' importo complessivo di " + importo + "$\nnumero protocollo: " + n_protocollo + "\nfirme titolare e comune:_______________________";
            // Creazione dell'elemento <a> per il download
            var downloadLink = document.createElement("a");
            downloadLink.href = "data:text/plain;charset=utf-8," + encodeURIComponent(contenuto);
            downloadLink.download = Id + "_rapporto_" + nomeComune + "_" + titolare + "_" + n_protocollo + ".txt";

            // Aggiunta dell'elemento <a> al DOM e simulazione del clic per avviare il download
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }

        function scarica_ricevuta(Id, nomeComune, tipo, titolare, n_protocollo, importo) {
            // Contenuto del file
            var contenuto = "Ricevuta: il comune di " + nomeComune + " si impegna nel contratto di " + tipo + ",\ncon il titolare " + titolare + " per l' importo complessivo di " + importo + "$\nnumero protocollo: " + n_protocollo;
            // Creazione dell'elemento <a> per il download
            var downloadLink = document.createElement("a");
            downloadLink.href = "data:text/plain;charset=utf-8," + encodeURIComponent(contenuto);
            downloadLink.download = Id + "_ricevuta_" + nomeComune + "_" + titolare + "_" + n_protocollo + ".txt";

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

    function eliminaRapporti($eliminati, $connessione)
    {
        foreach ($eliminati as $eliminato) {
            $stmt = $connessione->prepare("DELETE FROM `rapporti` WHERE `Id` = ?");
            $stmt->execute([$eliminato]);
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

    function getIdByFilename($fileName)
    {
        // Estrarre la cifra all'inizio del nome del file
        $id = intval($fileName);
        return $id;
    }

    function fatturaGiaPagata($id, $connessione)
    {
        $stmt = $connessione->prepare("SELECT `fattura_pagata` FROM `rapporti` WHERE `Id` = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        if ($result && $result['fattura_pagata'] !== null) {
            return true;
        } else {
            return false;
        }
    }
?>
