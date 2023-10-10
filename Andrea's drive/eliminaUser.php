<?php
session_start();
require 'config.php';
$userDisponibile = true;
$login = false;
$eliminazione = false;
$error=false;
if (isset($_GET['username'])) {
    $usernameAdmin = $_GET['username'];
}

if (isset($_POST['elimina'])) {
    $adminId = getAdminIdByUsername($usernameAdmin);
    if (eliminaUser($_POST['username'], $adminId, $usernameAdmin)) {
        $eliminazione = true;
        logActivity($usernameAdmin, "ha eliminato user: ", $_POST['username']);
    } else {
        $eliminazione = false;
        $error=true;
    }
}

function getAdminIdByUsername($username) {
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
        $stmt->execute([$username]);
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

function eliminaUser($username, $adminId, $usernameAdmin) {
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Elimino l'utente corrispondente all'username e all'id_admin forniti
        $stmt = $connessione->prepare("DELETE FROM `user` WHERE `username` = ? AND `id_admin` = ?");
        $stmt->execute([$username, $adminId]);
        $rowCount = $stmt->rowCount();
        return $rowCount > 0; // Ritorna true se almeno una riga è stata eliminata
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

        .titolo {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
            color: var(--secondary-color);
        }

        .delete_form {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            margin: 0 auto;
            max-width: 400px;
        }

        .delete_form p {
            margin-bottom: 10px;
        }

        .delete_form input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--primary-color);
            border-radius: 3px;
            outline: none;
        }

        .delete_form #elimina {
            background-color: var(--secondary-color);
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .delete_form #elimina:hover {
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

        .log_inButton {
            background-color: var(--secondary-color);
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .log_inButton:hover {
            background-color: var(--hover-color);
        }
    </style>
    <title>Andrea's Drive</title>
</head>

<body>
    <?php
    // ... il codice PHP rimane invariato ...
    ?>

    <div class="titolo">Elimina User</div>
    <?php
    if (!$eliminazione) {
        ?>
        <form class="delete_form mx-auto" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?username=' . $usernameAdmin ?>">
            <p>Username: <input type="text" name="username" class="username"></p>
            <input type="submit" value="Elimina" name="elimina" id="elimina" class="disabled">
        </form>
        <div class="flex justify-center mt-6">
            <button class="log_inButton" onclick="location.href='dashboard.php?username=<?php echo $usernameAdmin ?>&admin=<?php echo $usernameAdmin ?>'">dashboard</button>
        </div>
        <?php
        if ($error) {
            echo '<p style="color:red; text-align:center;">utente non trovato</p>';
        }
    } else {
        echo '
        <div class="positiveResult">
            <p class="correct">eliminazione avvenuta correttamente</p>
            <div class="flex justify-center mt-6 space-x-4">
                <button class="log_inButton" onclick="location.href=\'dashboard.php?username=' . $usernameAdmin . '&admin=' . $usernameAdmin . '\'">dashboard</button>
                <button class="log_inButton" onclick="location.href=\'' . htmlspecialchars($_SERVER['PHP_SELF'] . '?username=' . $usernameAdmin) . '\'">Elimina nuovo utente</button>
            </div>
        </div>';
    }
    ?>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="JS/eliminaUser.js"></script>

</html>
