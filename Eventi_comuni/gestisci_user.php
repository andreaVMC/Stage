<?php
    @require 'config.php';
    session_reset();
    $connessione = instauraConnessione();
    
    if(isset($_GET['username'])){
        $username=$_GET['username'];
    }

    if(isset($_POST['aggiorna'])){
        $accessi = isset($_POST['accesso']) ? $_POST['accesso'] : array();
        $eliminati = isset($_POST['elimina']) ? $_POST['elimina'] : array();
        eliminaUser($eliminati,$connessione);
        aggiornaAccessi($accessi,$connessione);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gestione utenti</title>
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
        tr,.change{
            transition-duration: 0.2s;
        }

        button:hover{
            background-color: var(--attivo);
        }

        tr:hover{
            cursor: pointer;
            background-color: var(--secondario);
        }
        .change:hover{
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
    <div class="titolo">Gestisci user</div>
    <div class="action">
        <button onclick="location.href='aggiungi_user.php?username=admin'">aggiungi user</button>
        <button onclick="location.href='adminDashboard.php?username=admin'">dashboard</button>
    </div>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?username=".$username; ?>">
        <table>
            <tr>
                <th>id</th>
                <th>nome</th>
                <th>cognome</th>
                <th>email</th>
                <th>username</th>
                <th>password</th>
                <th>accesso</th>
                <th>elimina</th>
                <th>modifica</th>
            </tr>
            <?php
                $stmt = $connessione->prepare("SELECT * FROM `utente`");
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($users as $user){
                    if($user['username']!='admin'){
                        echo '<tr>';
                        echo '<td onclick="location.href=\'informazioniUtente.php?utente=' . $user['username'] . '\'">' . $user['Id'] . '</td>';
                        echo '<td onclick="location.href=\'informazioniUtente.php?utente=' . $user['username'] . '\'">' .$user['nome']."</td>";  
                        echo '<td onclick="location.href=\'informazioniUtente.php?utente=' . $user['username'] . '\'">' . $user['cognome'] ."</td>";
                        echo '<td onclick="location.href=\'informazioniUtente.php?utente=' . $user['username'] . '\'">'  . (strlen($user['email']) > 15 ? substr($user['email'], 0, 10) . "..." : $user['email']) . "</td>";
                        echo '<td onclick="location.href=\'informazioniUtente.php?utente=' . $user['username'] . '\'">' .$user['username']."</td>";
                        echo '<td onclick="location.href=\'informazioniUtente.php?utente=' . $user['username'] . '\'">' .$user['password']."</td>";
                        ?><td><input type="checkbox" class="checkbox" name="accesso[]" value="<?php echo $user['username'] ?>" <?php
                            if($user['accesso']){
                                echo 'checked';
                            }
                        ?>></td>
                        <td><input type="checkbox" class="checkbox" name="elimina[]" value="<?php echo $user['username'];?>"></td>
                        <td class="change" onclick="location.href='<?php echo 'modificaUser.php?user='.$user['username']; ?>'">modifica user</td>
                        <?php
                        echo "</tr>";
                    }
                }
            ?>
        </table>
        <input type="submit" name="aggiorna" style="display:none;" id="aggiorna">
    </form>
    <button onclick="document.getElementById('aggiorna').click()">aggiorna</button>
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



    function aggiornaAccessi($accessi,$connessione){
        $stmt=$connessione->prepare("UPDATE `utente` SET `accesso`='0' WHERE 1");
        $stmt->execute();
        foreach($accessi as $accesso){
            $stmt=$connessione->prepare("UPDATE `utente` SET `accesso`='1' WHERE `username` = ?");
            $stmt->execute([$accesso]);
        }
    }

    function eliminaUser($eliminati,$connessione){      
        foreach($eliminati as $eliminato){
            $stmt=$connessione->prepare("DELETE FROM `utente` WHERE `username` = ?");
            $stmt->execute([$eliminato]);
        }
    }
?>