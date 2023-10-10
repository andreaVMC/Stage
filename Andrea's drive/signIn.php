<?php
session_start();
require 'config.php';
$userDisponibile = true;

// Variabile per tenere traccia dello stato di compilazione
$formCompleted = false;

if (isset($_POST['signin'])) {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userDisponibile = UsernameAvailable($username);
    if (signIn($nome, $cognome, $username, $password, $email)) {
        $login = true;
        // Store activity in the attivita table
        logActivity($username, "ha registrato", "il suo account admin");
    } else {
        $login = false;
    }
}

function getAdminId($adminId) {
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Recupera il valore id_admin dal record con lo stesso username di $adminId
        $stmt = $connessione->prepare("SELECT id_admin FROM admin WHERE username = ?");
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin !== false) {
            return $admin['id_admin'];
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
    
    return false;
}

function logActivity($username, $azione, $destinazione)
{
    try {
        $adminId = getAdminId($username);
        $timestamp = date("Y-m-d H:i:s");

        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Memorizzo l'attività nella tabella attivita
        $stmt = $connessione->prepare("INSERT INTO `attivita`(`sorgente`, `azione`, `destinazione`, `data`, `id_admin`) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $azione, $destinazione, $timestamp, $adminId]);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

function signIn($nome, $cognome, $username, $password, $email)
{
    try {
        if (UsernameAvailable($username)) {
            // Stabilisco la connessione al database
            $connessione = new PDO(
                "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
                $GLOBALS['dbuser'],
                $GLOBALS['dbpassword']
            );
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Creo la cartella per il nuovo utente admin
            $stmt = $connessione->prepare("INSERT INTO `directorys`(`nome`) VALUES (?)");
            $stmt->execute([$username]);
            mkdir("datas/" . $username);
            chmod("datas/" . $username, 0777);

            // Ottengo l'ID della directory appena creata
            $stmt = $connessione->prepare("SELECT `id` FROM `directorys` WHERE nome = ?");
            $stmt->execute([$username]);
            $id_directory = $stmt->fetchColumn();

            // Memorizzo il nuovo utente admin
            $stmt = $connessione->prepare("INSERT INTO `admin`(`nome`, `cognome`, `email`, `password`, `username`, `root_id`) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $cognome, $email, $password, $username, $id_directory]);

            // Ottengo l'id dell'admin
            $stmt = $connessione->prepare("SELECT `id_admin` FROM `admin` WHERE `root_id` = ?");
            $stmt->execute([$id_directory]);
            $id_admin = $stmt->fetchColumn();

            // Assegno l'id admin alla cartella appena creata
            $stmt = $connessione->prepare("UPDATE `directorys` SET `admin_id`=? WHERE `id`=?");
            $stmt->execute([$id_admin, $id_directory]);
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

function UsernameAvailable($username)
{
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verifico se il nome utente esiste già nel database
        $stmt = $connessione->prepare("SELECT COUNT(*) FROM `admin` WHERE `username` = ?");
        $stmt->execute([$username]);
        $count = $stmt->fetchColumn();
        return $count == 0; // Restituisce true se il nome utente non esiste nel database, altrimenti restituisce false
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
            font-family: 'Roboto', sans-serif;
        }

        .titolo {
            text-align: center;
            font-size: 24px;
            margin-top: 20px;
            font-weight: 500;
            color: var(--primary-color);
        }

        .signin_form {
            max-width: 400px;
            margin: 0 auto;
            margin-top: 50px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
        }

        .userDataContainer {
            margin-bottom: 20px;
        }

        .userDataContainer p {
            margin-bottom: 10px;
            color: var(--text-color);
        }

        .userDataContainer input {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid var(--secondary-color);
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        .userDataContainer input[type="submit"] {
            background-color: var(--secondary-color);
            border: none;
            color: white;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .userDataContainer input[type="submit"]:hover {
            background-color: var(--hover-color);
        }

        .userDataContainer p > p {
            color: red;
            font-size: 1vw;
            height: 1vh;
        }

        .loginContainer {
            text-align: center;
            margin-top: 20px;
        }

        .titoletto {
            font-size: 16px;
        }

        .log_inButton {
            background-color: var(--secondary-color);
            border: none;
            color: white;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .log_inButton:hover {
            background-color: var(--hover-color);
        }

        .positiveResult {
            text-align: center;
            background-color: white;
            padding: 2% 2%;
            border-radius: 10px;
            width: 30%;
            margin: 50px auto 0px auto;
        }

        .positiveResult p {
            font-size: 20px;
        }
    </style>
    <title>Andrea's Drive</title>
    <script>
        // Funzione per verificare lo stato di compilazione
        function checkFormCompletion() {
            const inputs = document.querySelectorAll('.userDataContainer input');
            let isCompleted = true;
            inputs.forEach(input => {
                if (input.value === '') {
                    isCompleted = false;
                }
            });

            const signInButton = document.getElementById('sign_in');
            signInButton.disabled = !isCompleted;
        }
    </script>
</head>

<body>
    <?php
    if (!$login) {
        echo '
            <div class="titolo">Pagina di registrazione</div>
            <div class="signin_form">
                <form method="POST" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '">
                    <div class="userDataContainer">
                        <p>nome: <input type="text" name="nome" id="nome" oninput="checkFormCompletion()"></p>
                        <p>cognome: <input type="text" name="cognome" id="cognome" oninput="checkFormCompletion()"></p>
                        <p>email: <input type="text" name="email" id="email" oninput="checkFormCompletion()"></p>
                        <p>username: <input type="text" name="username" id="username" oninput="checkFormCompletion()"></p>';
        if (!$userDisponibile) {
            echo '<p><span class="error-text">Username non disponibile</span></p>';
        }
        echo '
                        <p>password: <input type="password" name="password" id="password" oninput="checkFormCompletion()"></p>
                        <input type="submit" value="Sign in" id="sign_in" name="signin" class="disabled" disabled>
                    </div>
                </form>
                <div class="loginContainer">
                    <p class="titoletto">accedi</p>
                    <button class="log_inButton" onclick="location.href=\'index.php\'">Log in</button>
                </div>
            </div>
        ';
    } else {
        echo '
            <div class="positiveResult">
                <p>Registrazione avvenuta correttamente<br>Vai alla pagina di login per continuare</p>
                <button class="log_inButton" onclick="location.href=\'index.php\'">Log in</button>
            </div>
        ';
    } ?>
</body>

</html>
