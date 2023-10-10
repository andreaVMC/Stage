<?php
    require 'config.php';
    $error=false;
    $errore_connessione=false;
    $adminOption="";
    if (isset($_POST['login'])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $adminOption = $_POST["admin"];
        $adminOption = controllaAdmin($username,$password,$adminOption);

        if (utente_corretto($username, $password)) {
            $queryString = http_build_query([
                'username' => $username,
                'admin' => $adminOption
            ]);
            header("Location: dashboard.php?" . $queryString);
            exit();
        } else {
            $error = true;
        }
    }

    function controllaAdmin($username, $password, $adminOption) {
        try {
            // Stabilisco la connessione al database
            $connessione = new PDO(
                "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
                $GLOBALS['dbuser'],
                $GLOBALS['dbpassword']
            );
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Controllo nella tabella `admin`
            $stmt = $connessione->prepare("SELECT * FROM `admin` WHERE `username` = ? AND `password` = ?");
            $stmt->execute([$username, $password]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($admin !== false) {
                return $username;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        return $adminOption;
    }
    

    function utente_corretto($username, $password) {
        try {
            // Stabilisco la connessione al database
            $connessione = new PDO(
                "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
                $GLOBALS['dbuser'],
                $GLOBALS['dbpassword']
            );
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Controllo nella tabella `admin`
            $stmt = $connessione->prepare("SELECT * FROM `admin` WHERE `username` = ? AND `password` = ?");
            $stmt->execute([$username, $password]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($admin !== false) {
                return true; // Lo username e la password corrispondono nella tabella `admin`
            }
    
            // Controllo nella tabella `user`
            $admin_id = $_POST['admin'];
            $admin_id=getAdminIdByUsername($admin_id);
            if ($admin_id !== null){
                $stmt = $connessione->prepare("SELECT * FROM `user` WHERE `username` = ? AND `password` = ? AND `id_admin` = ?");
                $stmt->execute([$username, $password, $admin_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return ($user !== false); // Restituisce true se lo username, la password e l'admin_id corrispondono nella tabella `user`, altrimenti restituisce false
            }else{
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    function getAdminIdByUsername($admin_id) {
        try {
            // Stabilisco la connessione al database
            $connessione = new PDO(
                "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
                $GLOBALS['dbuser'],
                $GLOBALS['dbpassword']
            );
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Cerco l'ID corrispondente all'username nella tabella `admin`
            $stmt = $connessione->prepare("SELECT id_admin FROM `admin` WHERE `username` = ?");
            $stmt->execute([$admin_id]);
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

        .title {
            text-align: center;
            font-size: 24px;
            margin-top: 20px;
            font-weight: 500;
            color: var(--primary-color);
        }

        .login-form {
            max-width: 400px;
            margin: 0 auto;
            margin-top: 50px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            margin-bottom: 10px;
            display: block;
            color: var(--text-color);
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid var(--secondary-color);
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        .form-group input:hover {
            border-color: var(--hover-color);
        }

        .form-group select {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid var(--secondary-color);
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        .form-group select:hover {
            border-color: var(--hover-color);
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .disabled {
            opacity: 0.6;
            pointer-events: none;
        }

        .signin-container {
            text-align: center;
            margin-top: 20px;
        }

        .subtitle {
            font-size: 16px;
        }

        .sign-in-button {
            background-color: var(--primary-color);
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

        .sign-in-button:hover {
            background-color: var(--hover-color);
        }

        @media (max-width: 768px) {
            .login-form {
                max-width: 100%;
                margin: 20px;
            }
        }
    </style>
    <title>Andrea's Drive</title>
</head>
<body>
    <div class="title">Pagina di accesso</div>
    <div class="login-form">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="admin">Azienda:</label>
                <?php
                // Stabilire la connessione al database
                $conn = new PDO(
                    "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
                    $GLOBALS['dbuser'],
                    $GLOBALS['dbpassword']
                );
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Query per selezionare gli username dalla tabella admin
                $stmt = $conn->query("SELECT username FROM admin");
                $usernames = $stmt->fetchAll(PDO::FETCH_COLUMN);

                // Creazione del menu a tendina
                echo "<select name='admin' class='form-control'>";
                foreach ($usernames as $username) {
                    echo "<option value='$username'>$username</option>";
                }
                echo "</select>";

                // Chiudere la connessione al database
                $conn = null;
                ?>
            </div>
            <?php
            if ($error) {
                echo '<div class="error">Dati errati, nessun utente riconosciuto</div>';
            } else if ($errore_connessione) {
                echo '<div class="error">Errore di connessione</div>';
            }
            ?>
            <input type="submit" value="Log in" id="log_in" name="login" class="btn btn-primary sign-in-button">
        </form>
        <div class="signin-container">
            <p class="subtitle">Crea un nuovo account</p>
            <a class="sign-in-button btn btn-primary" href="signIn.php">Sign in</a>
        </div>
    </div>
</body>
</html>
