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

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--primary-color);
            color: #fff;
            padding: 10px 20px;
            margin-bottom: 20px;
        }

        .header .logo {
            font-size: 24px;
        }

        .header .nav {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
        }

        .header .nav button {
            color: #fff;
            background-color: var(--secondary-color);
            text-decoration: none;
            padding: 5px 10px;
            height: fit-content;
            margin-left: 5px;
            border-radius: 3px;
            transition: background-color 0.3s;
            border-color: transparent;
        }

        .header .nav button:hover {
            background-color: var(--hover-color);
        }

        .content {
            background-color: #fff;
            padding: 10px;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
        }

        .content table th,
        .content table td {
            padding: 10px;
            border-bottom: 1px solid var(--text-color);
            text-align: center;
        }

        .content table th {
            background-color: var(--secondary-color);
            color: #fff;
            text-align: center;
        }

        .content table tr:hover {
            background-color: var(--background-color);
            cursor: pointer;
        }

        .password {
            background-color: var(--text-color);
            transition-duration: 0.2s;
            border-radius: 2px;
        }

        .password:hover {
            background-color: transparent;
        }

        @media (max-width: 768px) {
            .container {
                padding: 5px;
            }

            .content table {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .content table {
                font-size: 10px;
            }
        }

        /* Responsiveness for the table */
        @media (max-width: 768px) {
            .responsive-table {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .responsive-table {
                font-size: 10px;
            }

            .content table th,
            .content table td {
               padding: 8px 2px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">Andrea's Drive</div>
            <div class="nav">
                <?php
                    if ($username === $adminId) {
                        echo '<p class="nomeUtente">admin: ' . $username . '</p>';
                    } else {
                        echo '<p class="nomeUtente">user: ' . $username . '<br>admin: ' . $adminId . '</p>';
                    }
                ?>
                <button class="log_inButton"
                    onclick="location.href='dashboard.php?username=<?php echo $username ?>&admin=<?php echo $adminId ?>'">Dashboard</button>
            </div>
        </div>
        <div class="content">
            <?php
            try {
                // Stabilisco la connessione al database
                $connessione = new PDO(
                    "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
                    $GLOBALS['dbuser'],
                    $GLOBALS['dbpassword']
                );
                $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Recupera il valore id_admin corrispondente a $adminId
                $id_admin = getAdminId($adminId);

                if ($id_admin !== false) {
                    // Recupera tutti i record dalla tabella `user` con id_admin uguale a $id_admin
                    $stmt = $connessione->prepare("SELECT * FROM `user` WHERE id_admin = ?");
                    $stmt->execute([$id_admin]);
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Verifica se sono presenti utenti
                    if ($users) {
                        echo '<table class="w-full responsive-table">';
                        echo '<tr class="bg-secondary-color text-white">';
                        echo '<th class="py-2 px-4">ID</th>';
                        echo '<th class="py-2 px-4">Nome</th>';
                        echo '<th class="py-2 px-4">Cognome</th>';
                        echo '<th class="py-2 px-4">Username</th>';
                        echo '<th class="py-2 px-4">Email</th>';
                        if ($username === $adminId) {
                            echo '<th class="py-2 px-4">Password</th>';
                        }
                        echo '</tr>';

                        foreach ($users as $user) {
                            echo '<tr class="hover:bg-hover-color cursor-pointer" onclick="location.href=\'utente.php?username='.$username.'&admin='.$adminId.'&subject='.$user['username'].'\'">';
                            echo '<td class="border-l-4 border-primary-color py-2 px-4">' . $user['id'] . '</td>';
                            echo '<td class="py-2 px-4">' . (strlen($user['nome']) > 10 ? substr($user['nome'], 0, 7) . '...' : $user['nome']) . '</td>';
                            echo '<td class="py-2 px-4">' . (strlen($user['cognome']) > 10 ? substr($user['cognome'], 0, 7) . '...' : $user['cognome']) . '</td>';
                            echo '<td class="py-2 px-4">' . (strlen($user['username']) > 10 ? substr($user['username'], 0, 7) . '...' : $user['username']) . '</td>';
                            echo '<td class="py-2 px-4">' . (strlen($user['email']) > 10 ? substr($user['email'], 0, 7) . '...' : $user['email']) . '</td>';
                            if ($username === $adminId) {
                                echo '<td class="py-2 px-4"><div class="password">' . (strlen($user['password']) > 10 ? substr($user['password'], 0, 7) . '...' : $user['password']) . '</div></td>';
                            }
                            echo '</tr>';
                        }

                        echo '</table>';
                    } else {
                        echo '<p class="text-center">Nessun utente,<br>puoi crearli andando nella sezione<br>"crea user" della tua dashboard</p>';
                    }
                } else {
                    echo '<p class="text-center">Nessun id_admin corrispondente trovato.</p>';
                }

            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
            ?>
        </div>
    </div>
</body>

</html>
