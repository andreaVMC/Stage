<?php 
@require 'config.php';

if (isset($_GET['username'])) {
    $username = $_GET['username'];
} else {
    echo "Username non specificato.";
}

if (isset($_GET['admin'])) {
    $adminId = $_GET['admin'];
} else {
    echo "ID admin non specificato.";
}

if (isset($_GET['subject'])) {
    $subject = $_GET['subject'];
} else {
    echo "subject non specificato.";
}

if (isset($_POST['privilegio'])) {
    changePrivilegio($subject);
}

function changePrivilegio($subject) {
    try {
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cerca l'utente nella tabella user
        $statement = $connessione->prepare("SELECT * FROM user WHERE username = :username");
        $statement->execute([':username' => $subject]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $newPrivilegio = ($user['privilegio'] == 1) ? 0 : 1;

            // Aggiorna il campo privilegio
            $statement = $connessione->prepare("UPDATE user SET privilegio = :privilegio WHERE username = :username");
            $statement->execute([':privilegio' => $newPrivilegio, ':username' => $subject]);
        } else {
            echo "errore";
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}


function takeInfos($subject){
    try{
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cerca l'username nella tabella admin
        $statement = $connessione->prepare("SELECT * FROM admin WHERE username = :username");
        $statement->execute([':username' => $subject]);
        $admin = $statement->fetch(PDO::FETCH_ASSOC);

        if($admin){
            // Memorizza i campi dell'admin in variabili globali
            $GLOBALS['id'] = $admin['id_admin'];
            $GLOBALS['id_admin'] = $admin['id_admin'];
            $GLOBALS['Username'] = $admin['username'];
            $GLOBALS['Password'] = $admin['password'];
            $GLOBALS['email'] = $admin['email'];
            $GLOBALS['nome'] = $admin['nome'];
            $GLOBALS['cognome'] = $admin['cognome'];
        } else {
            // Cerca l'username nella tabella user
            $statement = $connessione->prepare("SELECT * FROM user WHERE username = :username");
            $statement->execute([':username' => $subject]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if($user){
                // Memorizza i campi dell'utente in variabili globali
                $GLOBALS['id'] = $user['id'];
                $GLOBALS['id_admin'] = $user['id_admin'];
                $GLOBALS['Username'] = $user['username'];
                $GLOBALS['Password'] = $user['password'];
                $GLOBALS['email'] = $user['email'];
                $GLOBALS['nome'] = $user['nome'];
                $GLOBALS['cognome'] = $user['cognome'];
                $GLOBALS['privilegio'] = $user['privilegio'];
                // ... Memorizza gli altri campi desiderati dell'utente
            } else {
                // Nessun record corrispondente trovato
                return false;
            }
        }
                
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
    
    return true;
}

function isAdmin($username,$adminId){
    if($username==$adminId){
        return true;
    }
    return false;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #110E35;
            --secondary-color: #413A86;
            --background-color: #C9C7EE;
            --text-color: #374151;
            --hover-color: #404E78;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: Arial, sans-serif;
        }

        .intestazione {
            background-color: var(--primary-color);
            color: #fff;
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(3,1fr);
        }

        .user {
            font-size: 18px;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .titolo {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }

        .actions {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            width: 100%;
            position: fixed;
            bottom: 20px;
            gap: 10px;
        }

        .actions>button {
            background-color: var(--secondary-color);
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .actions>button:hover {
            background-color: var(--hover-color);
        }

        .bordo_tabella{
            background-color: #fff;
            width: 450px;
            padding: 10px 0px 10px 0px;
            border-radius: 5px;
            margin: 2% auto 2% auto;
        }

        table {
            width: 100%;
            max-width: 350px;
            margin: 2% auto;
            border-collapse: collapse;
            margin-bottom: 20px;
            
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid var(--primary-color);
        }

        table td:first-child {
            font-weight: bold;
            width: 120px;
        }

        .password {
            color: var(--secondary-color);
        }

        .eliminato {
            text-align: center;
            margin-bottom: 20px;
            color: red;
        }

        .form {
            text-align: center;
        }

        .privilegio {
            background-color: var(--secondary-color);
            color: #fff;
            padding: 0px 20px 22px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .privilegio:hover {
            background-color: var(--hover-color);
        }

        td>button{
            background-color: var(--secondary-color);
            color: white;
            padding: 0 2%;
            border-radius: 5px;
        }

        @media (max-width: 480px){
            .bordo_tabella{
                width: 80%;
                padding: 10px 2px;
            }
        
            table {
                width: 95%;
                margin: 2% auto;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            table td {
                width:min-content;

                padding: 10px 2px;
                border-bottom: 1px solid var(--primary-color);
            }

            table td:first-child {
                font-weight: bold;
                width: 120px;
            }

        }
    </style>
    <title>Andrea's Drive</title>
</head>

<body>

    <div class="intestazione">
        <div class="user">
            <div class="content">
                <?php 
                    if(isAdmin($username,$adminId)){
                        echo "admin: ".$adminId;
                    }else{
                        echo "user: ".$username."<br>";
                        echo "admin: ".$adminId;
                    }
                ?>
            </div>
        </div>
        <div class="titolo">
            <?php
                takeInfos($subject);
                echo $subject;
            ?>
        </div>
        <div style="width:100%;"></div>
    </div>
    <?php if($id!=""){?>
    <div class="bordo_tabella">
    <table>
        <tr>
            <td>Admin ID:</td>
            <td>
                <?php
                    echo $id_admin;
                ?>
            </td>
        </tr>
        <tr>
            <td>Utente ID:</td>
            <td>
                <?php
                    echo $id;
                ?>
            </td>
        </tr>
        <tr>
            <td>Nome:</td>
            <td>
                <?php
                    echo $nome;
                ?>
            </td>
        </tr>
        <tr>
            <td>Cognome:</td>
            <td>
                <?php
                    echo $cognome;
                ?>
            </td>
        </tr>
        <tr>
            <td>Email:</td>
            <td>
            <?php echo strlen($email) > 20 ? substr($email, 0, 14) . "..." : $email; ?>
                <button onclick="copyEmail()" >Copy</button>
            </td>
        </tr>
        <tr>
            <td style="border-bottom-color:transparent;">Password:</td>
            <td style="border-bottom-color:transparent;" class="password">
                <?php
                    if(isAdmin($username,$adminId) || $username==$subject){
                        echo $Password;
                    }else{
                        echo "Dati riservati";
                    }
                ?>
            </td>
        </tr>
    </table>
    </div>
    <?php }else{ ?>
        <div class="eliminato">Utente non più disponibile</div>
    <?php } ?>
    <?php if(isAdmin($username,$adminId) && $subject!=$adminId && $id!="") { ?>
        <form class="form" method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF'])."?username=".$username."&admin=".$adminId."&subject=".$subject?>">
            <input type="submit" value="
                <?php
                    if(!$privilegio){
                        echo "Dai privilegi file";
                    }else{
                        echo "Rimuovi privilegi file";
                    }
                ?>
            " class="privilegio" name="privilegio">
        </form>
    <?php } ?>

    <div class="actions">
        <button onclick="window.history.back()">Go Back</button>
        <button class="log_inButton ml-4" onclick="location.href='dashboard.php?username=<?php echo $username; ?>&admin=<?php echo $adminId; ?>'">Dashboard</button>
    </div>

    <script>

    function copyEmail() {
    var email = "<?php echo $email; ?>";
    navigator.clipboard.writeText(email)
        .then(function() {
        alert("Email copied to clipboard!");
        })
        .catch(function(error) {
        console.error("Unable to copy email: ", error);
        });
    }


    </script>
</body>

</html>
