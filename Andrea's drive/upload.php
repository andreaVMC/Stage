<?php
@require 'config.php';
session_start();
$risultato='';
if (isset($_GET['username'])) {
    $username = $_GET['username'];
} else {
    echo "Username non specificato.";
    exit();
}

if (isset($_GET['admin'])) {
    $adminId = $_GET['admin'];
} else {
    echo "ID admin non specificato.";
    exit();
}

if (isset($_GET['path'])) {
    $path = $_GET['path'];
} else {
    $path = "";
}

$_SESSION['username'] = $username;
$_SESSION['adminId'] = $adminId;

if (isset($_POST['upload'])) {
    $files = $_FILES['uploadedFile'];
    if (upload($path, $files,$adminId, $username)) {
        $risultato="corretto";
        update_attivita($username,$adminId,$files);
    } else {
        $risultato="errore";
    }
}

function update_attivita($username, $adminId, $files)
{
    try {
        $connessione = new PDO(
            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Errore di connessione al database: " . $e->getMessage();
        return; // Esce dalla funzione in caso di errore di connessione
    }
    
    $id_admin = getAdminIdByUserName($adminId);
    $stmt = $connessione->prepare("INSERT INTO `attivita`(`sorgente`, `azione`, `destinazione`, `data`, `id_admin`) VALUES (?, ?, ?, NOW(), ?)");

    foreach ($files['name'] as $fileName) {
        try {
            $stmt->execute([$username, "ha caricato", $fileName, $id_admin]);
        } catch (PDOException $e) {
            echo "Errore durante il salvataggio dei dati: " . $e->getMessage();
        }
    }
}



function getAdminIdByUserName($username)
{
    $connessione = new PDO(
        "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
        $GLOBALS['dbuser'],
        $GLOBALS['dbpassword']
    );
    $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepara la query per recuperare l'id_admin
    $query = $connessione->prepare("SELECT id_admin FROM admin WHERE username = :username");
    $query->bindParam(':username', $username);

    // Esegue la query
    $query->execute();

    // Ottiene il risultato della query
    $result = $query->fetch(PDO::FETCH_ASSOC);

    // Verifica se è stato trovato un risultato
    if ($result) {
        return $result['id_admin'];
    } else {
        return null; // Oppure un valore di default, a seconda delle tue esigenze
    }
}



function upload($path, $files, $adminId, $username)
{
    try{
        $connessione = new PDO(
            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $uploaded = true;

        // Iterate over each file
        for ($i = 0; $i < count($files['name']); $i++) {
            $fileName = $files['name'][$i];
            $fileSize = $files['size'][$i];
            $fileTmp = $files['tmp_name'][$i];
            $fileType = $files['type'][$i];

            // Check if the file is a ZIP file
            if ($fileType === 'application/zip') {
                // Construct the destination path for the ZIP file
                $zipDestination = $path . '/' . $fileName;

                // Remove the existing file if it exists
                if (file_exists($zipDestination)) {
                    unlink($zipDestination);
                }

                // Move the uploaded ZIP file to the destination
                if (!move_uploaded_file($fileTmp, $zipDestination)) {
                    $uploaded = false;
                    break;
                } else {
                    // Extract the contents of the ZIP file
                    if (!extractZipContents($zipDestination, $path, $adminId, $username)) {
                        $uploaded = false;
                        break;
                    }
                }
            } else {
                // Construct the destination path for regular files
                $fileDestination = $path . '/' . $fileName;

                // Remove the existing file if it exists
                if (file_exists($fileDestination)) {
                    unlink($fileDestination);
                }

                // Move the uploaded file to the destination
                if (!move_uploaded_file($fileTmp, $fileDestination)) {
                    $uploaded = false;
                    break;
                } else {
                    chmod($path . '/' . $fileName, 0666);
                    if(!alreadyExist($fileName,$path,$username,$adminId)){
                        $query = $connessione->prepare("INSERT INTO `file`(`propietario`, `nome`, `path`, `id_admin`) VALUES (?,?,?,?)");
                        $query->execute([$username,$fileName,$path,getAdminIdByUserName($adminId)]);
                    }
                }
            }
        }
        return $uploaded;
    }catch (PDOException $e) {
        echo "Errore di connessione al database: " . $e->getMessage();
        return; // Esce dalla funzione in caso di errore di connessione
    }
}


function extractZipContents($zipPath, $extractPath,$adminId, $username){
    $zip = new ZipArchive();
    try{
        $connessione = new PDO(
            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($zip->open($zipPath) === true) {
            // Create the base extraction directory if it doesn't exist
            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0777, true);
            }
            chmod($extractPath, 0777);
    
            // Extract each file and directory individually
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                $entryPath = $extractPath . '/' . $entry;
    
                if (substr($entry, -1) === '/') {
                    // Entry is a directory, create it
                    if (!is_dir($entryPath)) {
                        mkdir($entryPath, 0777, true);
                    }
                    chmod($entryPath, 0777);
                } else {
                    // Entry is a file, extract it
                    if (!copy("zip://".$zipPath."#".$entry, $entryPath)) {
                        return false;
                    }
                    chmod($entryPath, 0777);
                }
                if(!alreadyExist(basename($entry),dirname($entryPath),$username,$adminId)){
                    $query = $connessione->prepare("INSERT INTO `file`(`propietario`, `nome`, `path`, `id_admin`) VALUES (?,?,?,?)");
                    $query->execute([$username,basename($entry),dirname($entryPath),getAdminIdByUserName($adminId)]);
                    
                }
            }
    
            $zip->close();
    
            // Remove the ZIP file
            unlink($zipPath);
    
            return true;
        } else {
            return false;
        }
    }catch (PDOException $e) {
        echo "Errore di connessione al database: " . $e->getMessage();
        return; // Esce dalla funzione in caso di errore di connessione
    }
    
}

function alreadyExist($filename, $path, $username, $adminId)
{
    try {
        $connessione = new PDO(
            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = $connessione->prepare("SELECT COUNT(*) FROM `file` WHERE `nome` = ? AND `path` = ? AND `id_admin` = ?");
        $query->execute([$filename, $path, getAdminIdByUserName($adminId)]);
        $count = $query->fetchColumn();
        
        if ($count > 0) {
            $query = $connessione->prepare("SELECT `propietario` FROM `file` WHERE `nome` = ? AND `path` = ? AND `id_admin` = ?");
            $query->execute([$filename, $path, getAdminIdByUserName($adminId)]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($result['propietario'] == $username) {
                return true;
            } else {
                $query = $connessione->prepare("UPDATE `file` SET `propietario` = ? WHERE `nome` = ? AND `path` = ? AND `id_admin` = ?");
                $query->execute([$username, $filename, $path, getAdminIdByUserName($adminId)]);
                return true;
            }
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo "Errore di connessione al database: " . $e->getMessage();
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
    <title>Andrea's drive</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap');


        :root{
            --primary-color: #110E35;
            --secondary-color: #413A86;
            --background-color: #C9C7EE;
            --text-color: #374151;
            --hover-color: #404E78;
            --font: 'Roboto', sans-serif;
        }

        body{
            width: 100%;
            height: 100vh;
            border: 0;
            margin: 0;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: var(--background-color);
            font-family:var(--font);
            color: var(--text-color);
        }

        .titolo{
            font-size: 6vw;
            font-weight: 900;
            margin-top: 1%;
            color: var(--primary-color);
        }

        .form{
            width: 60%;
            display:flex;
            flex-direction: column;
            align-items: center;
            /*border: 1px solid red;*/
            margin-top: 5%;
            border-radius: 5px;
        }

        .dropzone {
            width: 100%;
            height: 20vh;
            border: 2px dashed var(--secondary-color);
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 20px;
            cursor: pointer;
        }

        .submit{
            margin-top: 2%;
            padding: 1% 5% 1% 5%;
            font-size: 1.1vw;
            background-color: var(--secondary-color);
            color: white;
            transition-duration: 0.2s;
            border: 0px solid transparent;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit:hover{
            background-color: var(--hover-color);
        }

        button{
            margin-top: auto;
            margin-bottom: 2%;
            padding: 1% 5% 1% 5%;
            font-size: 1.1vw;
            background-color: var(--secondary-color);
            transition-duration: 0.2s;
            border: 0px solid transparent;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        button:hover{
            background-color: var(--hover-color);
        }

        @media (max-width: 925px) {
            .submit{
                margin-top: 2%;
                padding: 1% 5% 1% 5%;
                font-size: 3vw;
                background-color: var(--secondary-color);
                color: white;
                transition-duration: 0.2s;
                border: 0px solid transparent;
                border-radius: 5px;
                cursor: pointer;
            }

            .submit:hover{
                background-color: var(--hover-color);
            }

            button{
                margin-top: auto;
                margin-bottom: 2%;
                padding: 1% 5% 1% 5%;
                font-size: 3vw;
                background-color: var(--secondary-color);
                transition-duration: 0.2s;
                border: 0px solid transparent;
                border-radius: 5px;
                width: fit-content;
                cursor: pointer;
                color: white;
            }

            button:hover{
                background-color: var(--hover-color);
            }
        }
        @media (max-width: 425px){
        .submit{
                margin-top: 2%;
                padding: 1% 5% 1% 5%;
                font-size: 5vw;
                background-color: var(--secondary-color);
                color: white;
                transition-duration: 0.2s;
                border: 0px solid transparent;
                border-radius: 5px;
                cursor: pointer;
            }

            .submit:hover{
                background-color: var(--hover-color);
            }

            button{
                margin-top: auto;
                margin-bottom: 2%;
                padding: 1% 5% 1% 5%;
                font-size: 5vw;
                background-color: var(--secondary-color);
                transition-duration: 0.2s;
                border: 0px solid transparent;
                border-radius: 5px;
                width: fit-content;
                cursor: pointer;
                color: white;
            }

            button:hover{
                background-color: var(--hover-color);
            }
        }
    </style>
</head>
<body>
    <div class="titolo">Upload</div>
    <form id="uploadForm" class="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?username=".$username."&admin=".$adminId."&path=".$path;?>" method="post" enctype="multipart/form-data">
        <?php if($risultato=="corretto"): ?>
            <div class="success-message" style="color: green;">Upload avvenuto con successo</div>
        <?php elseif($risultato=="errore"): ?>
            <div class="error-message" style="color:red;">Errore durante il caricamento dei file</div>
        <?php endif; ?>
        <div class="dropzone" id="dropzone">Trascina e rilascia i file qui, se vuoi caricare intere cartelle rilascia il file .ZIP</div>
        <input type="file" id="fileInput" name="uploadedFile[]" multiple style="display: none;">
        <input type="submit" value="Carica" name="upload" class="submit">
    </form>

    <button onclick="location.href='dashboard.php?username=<?php echo $username ?>&admin=<?php echo $adminId ?>&path=<?php echo $path ?>'">dashboard</button>
    <script>
        var dropzone = document.getElementById('dropzone');
        var fileInput = document.getElementById('fileInput');

        dropzone.addEventListener('dragover', handleDragOver, false);
        dropzone.addEventListener('drop', handleDrop, false);

        function handleDragOver(e) {
            e.preventDefault();
            dropzone.style.background = '#f5efff';
        }

        function handleDrop(e) {
            e.preventDefault();
            dropzone.style.background = '#f5efff';
            var files = e.dataTransfer.files;

            // Crea una nuova DataTransfer e aggiungi i file
            var dataTransfer = new DataTransfer();

            for (var i = 0; i < files.length; i++) {
                dataTransfer.items.add(files[i]);
            }

            // Aggiungi i file già presenti nell'input file
            var existingFiles = fileInput.files;

            for (var i = 0; i < existingFiles.length; i++) {
                dataTransfer.items.add(existingFiles[i]);
            }

            // Assegna la nuova FileList all'input file
            fileInput.files = dataTransfer.files;

            // Nascondi il testo "Trascina e rilascia i file qui"
            dropzone.innerHTML = "";

            // Mostra i nomi dei file caricati
            for (var i = 0; i < fileInput.files.length; i++) {
                var fileName = document.createElement('div');
                fileName.textContent = fileInput.files[i].name;
                dropzone.appendChild(fileName);
            }
        }
    </script>
</body>
</html>
