<?php
session_start();
require 'config.php';
$userDisponibile = true;
$login = false;
if (isset($_GET['username'])) {
    $usernameAdmin = $_GET['username'];
}

if (isset($_POST['signin'])) {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $userDisponibile = UsernameAvailable($username,$usernameAdmin);
    if ($userDisponibile) {
        if (signIn($nome, $cognome, $username, $password, $email, $usernameAdmin)) {
            $login = true;
            logActivity($usernameAdmin, "ha creato user: ", $_POST['username']);
            logActivity($usernameAdmin, "ha creato la cartella: ", $_POST['username']);
        } else {
            $login = false;
        }
    }
}

function signIn($nome, $cognome, $username, $password, $email, $usernameAdmin)
{
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Inserisco i dati del nuovo utente nella tabella `user`
        $stmt = $connessione->prepare("INSERT INTO `user` (`nome`, `cognome`, `username`, `password`, `email`, `id_admin`, `id_root`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $cognome, $username, $password, $email, getAdminIdByUsername($usernameAdmin), getAdminRootIdByUsername($usernameAdmin)]);
        // Creo la directory per il nuovo utente
        $directory = "datas/" . $usernameAdmin . "/" . $username;
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        chmod("datas/" . $usernameAdmin . "/" . $username, 0777);
        $query = $connessione->prepare("INSERT INTO `file`(`propietario`, `nome`, `path`, `id_admin`) VALUES (?,?,?,?)");
        $query->execute([$usernameAdmin,$username,"datas/" . $usernameAdmin,getAdminIdByUserName($usernameAdmin)]);
        return true;
        
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

function getAdminRootIdByUsername($username)
{
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cerco il root_id corrispondente all'username nella tabella `admin`
        $stmt = $connessione->prepare("SELECT root_id FROM `admin` WHERE `username` = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin !== false) {
            return $admin['root_id']; // Restituisco il root_id corrispondente
        } else {
            return null; // Nessun corrispondente trovato
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        return null; // Errore di connessione al database
    }
}

function getAdminIdByUsername($usernameAdmin) {
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cerco l'ID corrispondente all'username nella tabella `admin`
        $stmt = $connessione->prepare("SELECT `id_admin` FROM `admin` WHERE `username` = ?");
        $stmt->execute([$usernameAdmin]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($admin !== false) {
            return $admin['id_admin']; // Restituisco l'ID corrispondente
        } else {
            return null; // Nessun corrispondente trovato
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        return null; // Errore di connessione al database
    }
}

function UsernameAvailable($username,$adminId)
{
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verifico se il nome utente esiste già nel database
        $stmt = $connessione->prepare("SELECT COUNT(*) FROM `user` WHERE `username` = ? AND `id_admin`=? ");
        $stmt->execute([$username,getAdminIdByUsername($adminId)]);
        $count = $stmt->fetchColumn();

        return $count == 0; // Restituisce true se il nome utente non esiste nel database, altrimenti restituisce false
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

function logActivity($username, $azione, $destinazione)
{
    try {
        $adminId = getAdminIdByUsername($username);
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Andrea's Drive</title>
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

        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }

        .titolo {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: var(--secondary-color);
        }

        .signin_form {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
        }

        .signin_form p {
            margin-bottom: 10px;
        }

        .signin_form input[type="text"],
        .signin_form input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--primary-color);
            border-radius: 3px;
            outline: none;
        }

        .signin_form .error {
            color: red;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .signin_form #sign_in {
            background-color: var(--secondary-color);
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .signin_form #sign_in:hover {
            background-color: var(--hover-color);
        }

        .loginContainer {
            text-align: center;
            margin-top: 20px;
        }

        .log_inButton {
            background-color: var(--secondary-color);
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .log_inButton:hover {
            background-color: var(--hover-color);
        }

        .positiveResult {
            text-align: center;
            margin-top: 20px;
        }

        .positiveResult .correct {
            font-size: 24px;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .positiveResult>div>button{
            background-color: var(--secondary-color);
        }

        .positiveResult>div>button:hover{
            background-color: var(--hover-color);
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <?php
    if (!$login) {
        echo '
            <div class="container mx-auto">
                <div class="titolo">Pagina di registrazione User</div>
                <div class="signin_form">
                    <form method="POST" action="' . htmlspecialchars($_SERVER['PHP_SELF'] . '?username=' . $usernameAdmin) . '">
                        <div class="userDataContainer">
                            <p>nome: <input type="text" name="nome" id="nome"></p>
                            <p>cognome: <input type="text" name="cognome" id="cognome"></p>
                            <p>email: <input type="text" name="email" id="email"></p>
                            <p>username: <input type="text" name="username" id="username"></p>';
        if (!$userDisponibile) {
            echo '<p class="error">username non disponibile</p>';
        }
        echo '
                            <p>password: <input type="password" name="password" id="password"></p>
                            <input type="submit" value="Sign in" id="sign_in" name="signin" disabled>
                        </div>
                    </form>
                </div>
                <div class="loginContainer">
                    <button class="log_inButton" onclick="location.href=\'dashboard.php?username=' . $usernameAdmin . '&admin=' . $usernameAdmin . '\'">dashboard</button>
                </div>
            </div>';
    } else {
        echo '
            <div class="container mx-auto">
                <div class="positiveResult">
                    <p class="correct">registrazione avvenuta correttamente</p>
                    <div class="flex justify-center mt-6 space-x-4">
                        <button class="px-4 py-2 font-bold text-white bg-secondary-color rounded hover:bg-hover-color focus:outline-none" onclick="location.href=\'index.php\'">Log in</button>
                        <button class="px-4 py-2 font-bold text-white bg-secondary-color rounded hover:bg-hover-color focus:outline-none" onclick="location.href=\'' . htmlspecialchars($_SERVER['PHP_SELF'] . '?username=' . $usernameAdmin) . '\'">Crea nuovo utente user</button>
                        <button class="px-4 py-2 font-bold text-white bg-secondary-color rounded hover:bg-hover-color focus:outline-none" onclick="location.href=\'dashboard.php?username=' . $usernameAdmin . '&admin=' . $usernameAdmin . '\'">Torna alla dashboard</button>
                    </div>
                </div>
            </div>';
    }
    ?>


<script>
    // Ottieni riferimenti agli elementi del modulo
    const nomeInput = document.getElementById('nome');
    const cognomeInput = document.getElementById('cognome');
    const emailInput = document.getElementById('email');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const signInButton = document.getElementById('sign_in');

    // Aggiungi un gestore di eventi all'input di ogni campo
    nomeInput.addEventListener('input', checkFormValidity);
    cognomeInput.addEventListener('input', checkFormValidity);
    emailInput.addEventListener('input', checkFormValidity);
    usernameInput.addEventListener('input', checkFormValidity);
    passwordInput.addEventListener('input', checkFormValidity);

    // Funzione per controllare la validità del modulo e abilitare/disabilitare il pulsante "Sign in"
    function checkFormValidity() {
        const nomeValue = nomeInput.value.trim();
        const cognomeValue = cognomeInput.value.trim();
        const emailValue = emailInput.value.trim();
        const usernameValue = usernameInput.value.trim();
        const passwordValue = passwordInput.value.trim();

        const isFormValid = nomeValue !== '' && cognomeValue !== '' && emailValue !== '' && usernameValue !== '' && passwordValue !== '';

        signInButton.disabled = !isFormValid;
    }
</script>

</body>

</html>
