<?php 
    @require 'config.php';
    session_start();
    $connessione=instauraConnessione();
    $username=$_GET['user'];

    if(isset($_POST['modifica'])){
        $nome=$_POST['nome'];
        $cognome=$_POST['cognome'];
        $email=$_POST['email'];
        $password=$_POST['password'];

        if($nome!=""){
            modificaNome($nome,$username,$connessione);
        }
        if($cognome!=""){
            modificaCognome($cognome,$username,$connessione);
        }
        if($email!=""){
            modificaEmail($email,$username,$connessione);
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
        <div class="titolo">modifica utente: <?php echo $username ?></div>
        <div class="action">
            <button onclick="location.href='adminDashboard.php?username=admin'">dashboard</button>
            <button onclick="location.href='<?php echo 'gestisci_user.php?username=admin'.$username; ?>'">torna indietro</button>
        </div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?user=".$username; ?>">
            <?php
                $stmt=$connessione->prepare("SELECT * FROM `utente` WHERE `username`=?");
                $stmt->execute([$username]);
                $result=$stmt->fetch();
            ?>
            <div class="blocco-input">
                <p>nome</p>
                <input type='text' placeholder="<?php echo $result['nome']?>" name="nome">
            </div>
            <div class="blocco-input">
                <p>cognome</p>
                <input type='text' placeholder="<?php echo $result['cognome']?>" name="cognome">
            </div>
            <div class="blocco-input">
                <p>email</p>
                <input type='text' placeholder="<?php echo $result['email']?>" name="email">
            </div>
            <div class="blocco-input">
                <p>password</p>
                <input type='text' placeholder="<?php echo $result['password']?>" name="password">
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

    function modificaNome($nome,$username,$connessione){
        $stmt=$connessione->prepare("UPDATE `utente` SET `nome` = ? WHERE `username` = ? ");
        $stmt->execute([$nome,$username]);
    }
    
    function modificaCognome($cognome,$username,$connessione){
        $stmt=$connessione->prepare("UPDATE `utente` SET `cognome` = ? WHERE `username` = ? ");
        $stmt->execute([$cognome,$username]);
    }
    
    function modificaEmail($email,$username,$connessione){
        $stmt=$connessione->prepare("UPDATE `utente` SET `email` = ? WHERE `username` = ? ");
        $stmt->execute([$email,$username]);
    }
    function modificaPassword($password,$username,$connessione){
        $stmt=$connessione->prepare("UPDATE `utente` SET `password` = ? WHERE `username` = ? ");
        $stmt->execute([$password,$username]);
    }
?>