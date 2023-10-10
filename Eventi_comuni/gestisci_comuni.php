<?php
    @require 'config.php';
    session_reset();
    $connessione = instauraConnessione();
    
    if(isset($_GET['username'])){
        $username=$_GET['username'];
    }

    if(isset($_POST['aggiorna'])){
        $eliminati = isset($_POST['elimina']) ? $_POST['elimina'] : array();
        eliminaComuni($eliminati,$connessione);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gestisci comuni</title>
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
            border-collapse: collapse;
        }

        td{
            text-align: center;
            margin: 0;
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

        button{
            margin-top: 2%;
        }

        button{
            font-weight: 600;
            bottom: 2%;
            position: fixed;
            padding: 0.5% 3%;
            border-radius: 5px;
            border-color: transparent;
            background-color: var(--secondario);
            cursor: pointer;
            transition-duration:0.1s;
        }

        button:hover{
            background-color: var(--attivo);
        }
        tr,.change{
            transition-duration: 0.2s;
        }

        tr:hover{
            cursor: pointer;
            background-color: var(--secondario);
        }

        .change:hover{
            background-color: var(--attivo);
        }

        .download_email{
            left:2%;
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
    <div class="titolo">gestisci comuni</div>
    <div class="action">    
        <button onclick="location.href='adminDashboard.php?username=admin'">dashboard</button>
        <button onclick="location.href='aggiungi_comune.php?username=admin'">aggiungi comune</button>
    </div>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?username=".$username; ?>">
        <table>
            <tr>
                <th>id</th>
                <th>denominazione</th>
                <th>indirizzo</th>
                <th>email</th>
                <th>referente</th>
                <th>n.telefono referente</th>
                <th>username</th>
                <th>password</th>
                <th>elimina</th>
                <th>modifica</th>
            </tr>
            <?php
                $stmt = $connessione->prepare("SELECT * FROM `Comuni`");
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($users as $user){?>
                        <tr>
                            <td onclick="location.href='<?php echo 'informazioniComune.php?comune='.$user['username']; ?>'"><?php echo $user['Id'] ?></td>
                            <td onclick="location.href='<?php echo 'informazioniComune.php?comune='.$user['username']; ?>'"><?php echo $user['denominazione'] ?></td>
                            <td onclick="location.href='<?php echo 'informazioniComune.php?comune='.$user['username']; ?>'"><?php echo $user['indirizzo'] ?></td>
                            <td onclick="location.href='<?php echo 'informazioniComune.php?comune='.$user['username']; ?>'"><?php echo $user['email'] ?></td>
                            <td onclick="location.href='<?php echo 'informazioniComune.php?comune='.$user['username']; ?>'"><?php echo $user['referente'] ?></td>
                            <td onclick="location.href='<?php echo 'informazioniComune.php?comune='.$user['username']; ?>'"><?php echo $user['telefono_referente'] ?></td>
                            <td onclick="location.href='<?php echo 'informazioniComune.php?comune='.$user['username']; ?>'"><?php echo $user['username'] ?></td>
                            <td onclick="location.href='<?php echo 'informazioniComune.php?comune='.$user['username']; ?>'"><?php echo $user['password'] ?></td>
                            <td><input type="checkbox" name="elimina[]" value="<?php echo $user['username'];?>"></td>
                            <td class="change" onclick="location.href='<?php echo 'modificaComune.php?comune='.$user['username']; ?>'">modifica comune</td>
                    </tr>
                    <?php
                }
            ?>
        </table>
        <input type="submit" name="aggiorna" style="display:none;" id="aggiorna">
        <button onclick="document.getElementById('aggiorna').click()">aggiorna</button>
    </form>
    <?php
    $stmt = $connessione->prepare("SELECT * FROM `Comuni`");
    $stmt->execute();
    $comuni = $stmt->fetchAll();

    $stmt = $connessione->prepare("SELECT * FROM `email`");
    $stmt->execute();
    $emails = $stmt->fetchAll();
?>
<button class="download_email" onclick="scaricaEmail(<?php echo htmlspecialchars(json_encode($emails)); ?>,<?php echo htmlspecialchars(json_encode($comuni)); ?>)">scarica tutte le email</button>
<script>
    function scaricaEmail(emails, comuni) {
        // Contenuto del file
        var contenuto = "comune,utilizzo,email\n";
        for (let i = 0; i < comuni.length; i++) {
            for (let j = 0; j < emails.length; j++) {
                if (emails[j]['Id_comune'] == comuni[i]['Id']) {
                    contenuto += comuni[i]['denominazione'] + ", ";
                    contenuto += emails[j]['utilizzo'] + ", ";
                    contenuto += emails[j]['email'] + "\n";
                }
            }
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

    function eliminaComuni($eliminati,$connessione){      
        foreach($eliminati as $eliminato){
            $stmt=$connessione->prepare("DELETE FROM `Comuni` WHERE `username` = ?");
            $stmt->execute([$eliminato]);
        }
    }
?>