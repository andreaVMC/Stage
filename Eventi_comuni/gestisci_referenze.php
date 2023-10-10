<?php
    @require 'config.php';
    session_reset();
    $connessione = instauraConnessione();
    
    if(isset($_GET['username'])){
        $username=$_GET['username'];
    }

    if(isset($_POST['aggiorna'])){
        $eliminati = isset($_POST['elimina']) ? $_POST['elimina'] : array();
        eliminaReferenze($eliminati,$connessione);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gestisci referenze</title>
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
    <div class="titolo">gestisci referenze</div>
    <div class="action">
    <button onclick="location.href='comuniDashboard.php?username=<?php echo $username ?>'">dashboard</button>
    <button onclick="location.href='aggiungi_referente.php?username=<?php echo $username ?>'">aggiungi referenza</button>
    </div>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?username=".$username; ?>">
        <table>
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
                $stmt->execute([getIdComuneByUsername($username,$connessione)]);
                $referenti = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($referenti as $referente){
                        echo "<tr>";
                        echo "<td>".$referente['Id']."</td>";
                        echo "<td>".getNomeEvetoById($referente['Id_evento'],$connessione)."</td>";  
                        echo "<td>". $referente['nome'] ."</td>";
                        echo "<td>". $referente['email'] ."</td>";
                        echo "<td>". $referente['telefono'] ."</td>";
                        ?>
                        <td><input type="checkbox" name="elimina[]" value="<?php echo $referente['Id'];?>"></td>
                        <?php
                        echo "</tr>";
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

    function eliminaReferenze($eliminati,$connessione){      
        foreach($eliminati as $eliminato){
            $stmt=$connessione->prepare("DELETE FROM `referenze` WHERE `Id` = ?");
            $stmt->execute([$eliminato]);
        }
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

    function getNomeEvetoById($id,$connessione){
        $stmt = $connessione->prepare("SELECT `nome` FROM `evento` WHERE `Id` = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        if ($result) {
            return $result['nome'];
        } else {
            return null;
        }
    }
    
?>