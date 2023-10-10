<?php
    @require 'config.php';
    $dbError=false;
    $userError=false;
    $userNonAbilitato=false;

    if(isset($_POST['login'])){
        $username=$_POST['Username'];
        $password=$_POST['Password'];
        $connessione=instauraConnessione();
        creaAdmin($connessione);
        if($connessione){
            if(utenteEsiste($connessione,$username,$password)){
                $queryString = http_build_query([
                    'username' => $username
                ]);
                if($username=='admin'){
                    header("Location: adminDashboard.php?" . $queryString);
                    exit();
                }else if(isComune($connessione,$username,$password)){
                    header("Location: comuniDashboard.php?" . $queryString);
                    exit();
                }else if(isAbilitato($connessione,$username)){
                    header("Location: userDashboard.php?" . $queryString);
                    exit();
                }else{
                    $userNonAbilitato=true;
                }
            }else{
                $userError=true;
            }
        }else{
            $dbError=true;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Eventi Comunali</title>
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

            form{
                border: 3px solid var(--primario);
                border-radius: 15px;
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 30%;
                margin: 5% 0% auto 0%;
                padding: 2% 2%;
            }

            input{
                width: 80%;
                margin: 2.5%;
                font-weight: 700;
                padding: 2% 2%;
                border-radius: 5px;
                border: 2px solid var(--secondario);
                background-color: transparent;
                transition-duration: 0.2s;
            }

            input:hover{
                background-color: var(--secondario);
            }

            button{
                font-weight: 600;
                margin: 2%;
                padding: 1% 3%;
                border-radius: 5px;
                border-color: transparent;
                background-color: var(--primario);
                cursor: pointer;
                transition-duration:0.1s;
            }

            button:hover{
                background-color: var(--attivo);
            }

            @media (max-width: 650px) {
                form{
                    width: 70%;
                }
            }
        </style>
    </head>
    <body>
        <div class="titolo">Pannello di accesso</div>
        <form method="post" name="login_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="log_in_form">
            <input type="text" placeholder="Username" name="Username" class="input_username">
            <input type="password" placeholder="Password" name="Password" class="input_password">
            <input type="submit" value="Accedi" name="login" id="submit" class="submit_button" style="display:none">
            <button onclick="document.getElementById('submit').click()" class="accedi">Accedi</button>
            <?php
                if($dbError){
                    ?><p class="errore_connessione">errore di connessione</p><?php
                }else if($userError){
                    ?><p class="errore_username">credenziali errate</p><?php
                }else if($userNonAbilitato){?>
                    <p class="errore_abilitazione">utente non abilitato</p>
                <?php }
            ?>
        </form>
    </body>
</html>

<?php
    //funzioni utili

    //creo connessione con database
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

    //controllo se eesiste un utente o un comune con queste credenziali
    function utenteEsiste($connessione,$username,$password){
        $stmt = $connessione->prepare("SELECT * FROM `utente` WHERE `username` = ? AND `password` = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user !== false){
            return true; //se trova un utente ritorna true
        }

        //altrimenti cerco tra i comuni{
        $stmt = $connessione->prepare("SELECT * FROM `Comuni` WHERE `username` = ? AND `password` = ?");
        $stmt->execute([$username, $password]);
        $comune = $stmt->fetch(PDO::FETCH_ASSOC);
        if($comune !== false){
            return true;
        }

        //altrimenti non viene trovato
        return false;
    }


    function isComune($connessione,$username,$password){
        $stmt = $connessione->prepare("SELECT * FROM `Comuni` WHERE `username` = ? AND `password` = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user !== false){
            return true; //se trova un utente ritorna true
        }
        return false;
    }

    function creaAdmin($connessione){
        $stmt = $connessione->prepare("SELECT * FROM `utente` WHERE `username` = ?");
        $stmt->execute(['admin']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user !== false){
            return true;
        }else{
            $stmt = $connessione->prepare("INSERT INTO `utente`(`nome`, `cognome`, `email`, `username`, `password`) VALUES ('admin','admin','admin','admin','admin')");
            $stmt->execute();
        }
    }

    function isAbilitato($connessione,$username){
        $stmt = $connessione->prepare("SELECT `accesso` FROM `utente` WHERE `username` = ?");
        $stmt->execute([$username]);
        $val = $stmt->fetch(PDO::FETCH_ASSOC);
        return $val['accesso'];
    }
?>