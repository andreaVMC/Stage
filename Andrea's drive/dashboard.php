<?php
@require 'config.php';
session_start();

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

if (isset($_GET['path'])) {
    $path = $_GET['path'];
} else {
    $path = "";
}

$_SESSION['username'] = $username;
$_SESSION['adminId'] = $adminId;

function is_admin($username) {
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Controllo nella tabella `admin`
        $stmt = $connessione->prepare("SELECT COUNT(*) FROM `admin` WHERE `username` = ?");
        $stmt->execute([$username]);
        $count = $stmt->fetchColumn();

        return $count > 0; // Restituisce true se lo username viene trovato nella tabella `admin`, altrimenti restituisce false
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    header("Location: creaUser.php?username=$username");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Andrea's drive dashboard</title>
    <style>
        :root {
            --primary-color: #110E35;
            --secondary-color: #413A86;
            --background-color: #C9C7EE;
            --text-color: #374151;
            --hover-color: #404E78;
        }

        body {
            font-family: sans-serif;
            background-color: var(--background-color);
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100vh;
            overflow-x: hidden;
        }

        img{
            cursor: pointer;
        }

        .navbar {
            background-color: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
        }

        .navbar .nav-section {
            cursor: pointer;
            margin-right: 1rem;
        }

        .ricerca {
            padding: 1rem;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .ricerca>.nome_file {
            width: 80%;
            border: 1px solid var(--primary-color);
            padding: 0.5rem;
            border-radius: 0.375rem;
        }

        .content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            grid-gap: 20px;
            padding: 20px;
        }

        .content .item {
            text-align: center;
        }

        .content .item img {
            width: 100px;
            height: 100px;
        }

        .actions {
            width: 100%;
            display: flex;
            position: fixed;
            bottom: 15px;
            justify-content: center;
            margin-top: 1rem;
        }

        .actions button {
            border: none;
            background-color: transparent;
            cursor: pointer;
            margin-right: 1rem;
        }

        .actions img {
            width: 42px;
            height: 42px;
        }
    </style>
    <style>
        <?php
        // Add Tailwind CSS classes dynamically
        echo file_get_contents('https://cdn.tailwindcss.com/2.2.19');
        ?>
    </style>
    <script src="JS/dashboard.js"></script>
</head>
<body class="bg-background-color">
    <div class="navbar bg-primary-color text-white">
        <div class="nav_section" style="width:100%; margin-left:2%">
            <?php
            if (is_admin($username)) {
                echo 'admin: ' . $username;
                echo '
                    </div>
                    <div class="nav-section" onclick="redirectToCreaUser(\'' . $username . '\')">crea user</div>
                    <div class="nav-section" onclick="redirectToEliminaUser(\'' . $username . '\')">elimina user</div>
                ';
            } else {
                $adminId = $_SESSION['adminId'];
                echo 'user: ' . $username . '<br>admin_id: ' . $adminId . '</div>';
            }
            ?>
            <div class="nav-section" onclick="redirectToVisualizzaUser('<?php echo $adminId; ?>','<?php echo $username; ?>')">utenti</div>
            <div class="nav-section" onclick="redirectToVisualizzaAttivita('<?php echo $adminId; ?>','<?php echo $username; ?>')">attivita</div>
            <div class="nav-section" onclick="logout()">log out</div>
        </div>
    </div>
    <div class="ricerca">
        <input type="text" class="nome_file" placeholder="ricerca file con nome...">
    </div>
    <div class="content">
        <?php
        // Directory di base
        if ($path == "") {
            if (is_admin($username)) {
                // L'utente è un admin, utilizza solo il nome utente nella directory
                $directory = "datas/" . $username;
            } else {
                // L'utente non è un admin, utilizza l'ID dell'admin e il nome utente nella directory
                $directory = "datas/" . $adminId . "/" . $username;
            }
            $path = $directory;
        } else {
            $directory = $path;
        }

        // Controllo se la directory esiste
        if (is_dir($directory)) {
            // Recupero elenco delle directory e file nella cartella
            $items = scandir($directory);

            // Creazione del contenuto
            foreach ($items as $item) {
                // Ignora le directory speciali . e ..
                if ($item === "." || ($item === ".." && $directory === "datas/" . $adminId)) {
                    continue;
                }

                echo "<div class=\"item\">";
                if (is_dir($directory . '/' . $item)) {
                    if (!isParentDirectory($item)) {
                        $path = rtrim($directory, '/') . '/' . ltrim($item, '/');
                        echo "<img src='IMG/folder.png' alt='Directory Icon' onclick=\"location.href='" . htmlspecialchars($_SERVER['PHP_SELF']) . "?username={$username}&admin={$adminId}&path={$path}'\">";
                        echo "<br>";
                        echo abbreviateText($item, 15);
                    } else {
                        $path = removeLastDirectoryFromPath($path);
                        echo "<img src='IMG/folder.png' alt='Directory Icon' onclick=\"location.href='" . htmlspecialchars($_SERVER['PHP_SELF']) . "?username={$username}&admin={$adminId}&path={$path}'\">";
                        echo "<br>";
                        echo abbreviateText($item, 15);
                    }
                } elseif (is_file($directory . '/' . $item)) {
                    // Elemento è un file
                    echo "<img src='IMG/file.png' alt='File Icon' onclick='downloadFile(\"".$directory."/".$item."\")'>";
                    echo "<br>";
                    echo abbreviateText($item, 15);
                }

                echo "</div>";
            }
        } else {
            echo "La cartella non esiste";
        }

        // Aggiorna la variabile $path con l'indirizzo della directory corrente
        $path = $directory;

        function isParentDirectory($directory)
        {
            return ($directory === '..');
        }

        function removeLastDirectoryFromPath($path)
        {
            $lastSlashPos = strrpos($path, '/');
            if ($lastSlashPos !== false) {
                return substr($path, 0, $lastSlashPos);
            }
            return $path;
        }

        function abbreviateText($text, $maxLength)
        {
            if (strlen($text) > $maxLength) {
                return substr($text, 0, $maxLength) . "...";
            }
            return $text;
        }
        ?>
    </div>
    <div class="actions">
        <button class="carica_file" onclick="redirectToUpload('<?php echo $username; ?>', '<?php echo $adminId; ?>', '<?php echo $path ?>')">
            <img src="IMG/upload.png" alt="Upload Icon">
        </button>
        <button class="elimina_file" onclick="redirectToDelete('<?php echo $username; ?>', '<?php echo $adminId; ?>', '<?php echo $path ?>')">
            <img src="IMG/delete.png" alt="Delete Icon">
        </button>
    </div>
    <script>
            function redirectToCreaUser(username) {
                window.location.href = "creaUser.php?username=" + username;
            }

            function redirectToEliminaUser(username) {
                window.location.href = "eliminaUser.php?username=" + username;
            }

            function redirectToVisualizzaUser(admin_id, username) {
                window.location.href = "visualizzaUser.php?admin=" + admin_id + "&username=" + username;
            }

            function redirectToVisualizzaAttivita(admin_id, username) {
                window.location.href = "visualizzaAttivita.php?admin=" + admin_id + "&username=" + username;
            }

            function logout() {
                window.location.href = "index.php?logout=true";
            }

            function redirectToUpload(username, adminId, path) {
                window.location.href = "upload.php?username=" + username + "&admin=" + adminId + "&path=" + path;
            }

            function redirectToDelete(username, adminId, path) {
                window.location.href = "delete.php?username=" + username + "&admin=" + adminId + "&path=" + path;
            }

            // Add event listener to the search input
            var searchInput = document.querySelector('.nome_file');
            searchInput.addEventListener('input', search);


            function search() {
                var searchInput = document.querySelector('.nome_file');
                var filter = searchInput.value.toUpperCase();
                var content = document.querySelector('.content');
                var items = content.getElementsByClassName('item');

                for (var i = 0; i < items.length; i++) {
                    var item = items[i];
                    var itemName = (item.textContent || item.innerText).toUpperCase();

                    if (itemName.includes(filter)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                }
            }




            function downloadFile(filePath) {
                const link = document.createElement('a');
                link.href = filePath;
                link.download = getFileNameFromPath(filePath);
                link.target = "_blank";
                link.style.display = "none";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            function getFileNameFromPath(filePath) {
                const parts = filePath.split('/');
                return parts[parts.length - 1];
            }


        </script>
</body>
</html>
