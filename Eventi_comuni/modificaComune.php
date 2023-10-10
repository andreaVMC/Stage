<?php 
    @require 'config.php';
    session_start();
    $connessione=instauraConnessione();
    $username=$_GET['comune'];

    if(isset($_POST['modifica'])){
        $denominazione=$_POST['denominazione'];
        $indirizzo=$_POST['indirizzo'];
        $email=$_POST['email'];
        $referente=$_POST['referente'];
        $telefono_referente=$_POST['telefono_referente'];
        $password=$_POST['password'];

        if($denominazione!=""){
            modificaDenominazione($denominazione,$username,$connessione);
        }
        if($indirizzo!=""){
            modificaIndirizzo($indirizzo,$username,$connessione);
        }
        if($email!=""){
            modificaEmail($email,$username,$connessione);
        }
        if($referente!=""){
            modificaReferente($referente,$username,$connessione);
        }
        if($telefono_referente!=""){
            modificaTReferente($telefono_referente,$username,$connessione);
        }
        if($password!=""){
            modificaPassword($password,$username,$connessione);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>modifica comune</title>
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
                width: 15%;
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                margin-top: 2%;
            }

            button{
                cursor: pointer;
            }

            .action>button{
                font-weight: 700;
                padding: 2% 2%;
                width: 40%;
                background-color: var(--primario);
                border-color: transparent;
                border-radius: 5px;
                transition-duration: 0.2s;
            }

            .action>button:hover{
                background-color: var(--attivo);
            }

            form{
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                margin-top: 2%;
            }

            .blocco-input{
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 12%;
            }

            .blocco-input>p{
                font-weight: 600;
            }

            .blocco-input>input{
                background-color: var(--secondario);
                border: 2px solid var(--primario);
                border-radius: 5px;
                transition-duration: 0.2s;
            }

            .blocco-input>input:hover{
                background-color: var(--attivo);
            }

            .submit{
                margin-top: 2%;
                font-weight: 700;
                padding: 0.5% 2%;
                width: 10%;
                background-color: var(--primario);
                border-color: transparent;
                border-radius: 5px;
                transition-duration: 0.2s;
            }

            .submit:hover{
                background-color: var(--attivo);
            }

            @media (max-width: 1430px){
                form{
                    flex-direction: column;
                    width:50%
                }

                .action{
                    width: 60%;
                }

                .blocco-input{
                    margin-top: 5%;
                    width: 100%;
                }

                .submit{
                    width: 50%;
                    margin-top: 5%;
                }
            }
        </style>
    </head>
    <body>
        <div class="titolo">modifica comune di <?php echo $username ?></div>
        <div class="action">
            <button onclick="location.href='adminDashboard.php?username=admin'">dashboard</button>
            <button onclick="location.href='<?php echo 'gestisci_comuni.php?comune='.$username; ?>'">torna indietro</button>
        </div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?comune=".$username; ?>">
            <?php
                $stmt=$connessione->prepare("SELECT * FROM `Comuni` WHERE `username`=?");
                $stmt->execute([$username]);
                $result=$stmt->fetch();
                echo $result['denominzione'];
            ?>
            <div class="blocco-input">
                <p>denominazione</p>
                <input type='text' placeholder="<?php echo $result['denominazione']?>" name="denominazione">
            </div>
            <div class="blocco-input">
                <p>indirizzo</p>
                <input type='text' placeholder="<?php echo $result['indirizzo']?>" name="indirizzo">
            </div>
            <div class="blocco-input">
                <p>email principale</p>
                <input type='text' placeholder="<?php echo $result['email']?>" name="email">
            </div>
            <div class="blocco-input">
                <p>referente</p>
                <input type='text' placeholder="<?php echo $result['referente']?>" name="referente">
            </div>
            <div class="blocco-input">
                <p>telefono referente</p>
                <input type='text' placeholder="<?php echo $result['telefono_referente']?>" name="telefono_referente">
            </div>
            <div class="blocco-input">
                <p>password</p>
                <input type='password' placeholder="<?php echo $result['password']?>" name="password">
            </div>
            <input type="submit" name="modifica" id="submit" style="display:none;">
        </form>
        <button class="submit" onclick="document.getElementById('submit').click()">modifica</button>
        <div class="space"></div>
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

    function modificaDenominazione($denominazione,$username,$connessione){
        $stmt=$connessione->prepare("UPDATE `Comuni` SET `denominazione` = ? WHERE `username` = ? ");
        $stmt->execute([$denominazione,$username]);
    }
    
    function modificaIndirizzo($indirizzo,$username,$connessione){
        $stmt=$connessione->prepare("UPDATE `Comuni` SET `indirizzo` = ? WHERE `username` = ? ");
        $stmt->execute([$indirizzo,$username]);
    }
    
    function modificaEmail($email,$username,$connessione){
        $stmt=$connessione->prepare("UPDATE `Comuni` SET `email` = ? WHERE `username` = ? ");
        $stmt->execute([$email,$username]);
    }

    function modificaReferente($referente,$username,$connessione){
        $stmt=$connessione->prepare("UPDATE `Comuni` SET `referente` = ? WHERE `username` = ? ");
        $stmt->execute([$referente,$username]);
    }

    function modificaTReferente($telefono_referente,$username,$connessione){
        $stmt=$connessione->prepare("UPDATE `Comuni` SET `telefono_referente` = ? WHERE `username` = ? ");
        $stmt->execute([$telefono_referente,$username]);
    }
    function modificaPassword($password,$username,$connessione){
        $stmt=$connessione->prepare("UPDATE `Comuni` SET `password` = ? WHERE `username` = ? ");
        $stmt->execute([$password,$username]);
    }
?>