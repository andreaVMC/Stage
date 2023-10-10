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

function getAdminId($adminId)
{
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
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

function isAnAccount($username) {
    try {
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cerca l'username nella tabella admin
        $statement = $connessione->prepare("SELECT * FROM admin WHERE username = :username");
        $statement->execute([':username' => $username]);
        $admin = $statement->fetch(PDO::FETCH_ASSOC);

        // Cerca l'username nella tabella user
        $statement = $connessione->prepare("SELECT * FROM user WHERE username = :username");
        $statement->execute([':username' => $username]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        // Verifica se l'username esiste in almeno una delle tabelle
        if ($admin || $user) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}


try {
    // Stabilisco la connessione al database
    $connessione = new PDO(
        "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
        $GLOBALS['dbuser'],
        $GLOBALS['dbpassword']
    );
    $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recupera il valore id_admin corrispondente a $adminId
    $id_admin = getAdminId($adminId);

    if ($id_admin !== false) {
        // Recupera tutti i record dalla tabella `attivita` con id_admin uguale a $id_admin
        $stmt = $connessione->prepare("SELECT * FROM `attivita` WHERE id_admin = ?");
        $stmt->execute([$id_admin]);
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verifica se sono presenti attività
        if ($activities) {
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Andrea's Drive</title>

                <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.17/dist/tailwind.min.css" rel="stylesheet">
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
                    }

                    .nav {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 20px;
                        background-color: var(--primary-color);
                        color: white;
                    }

                    .nomeUtente {
                        margin: 0;
                        font-size: 20px;
                    }

                    .log_inButton {
                        cursor: pointer;
                        padding: 10px 20px;
                        background-color: var(--secondary-color);
                        color: white;
                        border: none;
                        border-radius: 4px;
                        transition: background-color 0.3s ease;
                    }

                    .log_inButton:hover {
                        background-color: var(--hover-color);
                    }

                    .content {
                        padding: 20px;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                        background-color: white;
                        color: var(--text-color);
                    }

                    th {
                        text-align: left;
                        padding: 10px;
                        background-color: var(--secondary-color);
                        color: white;
                    }

                    td {
                        padding: 10px;
                    }

                    .sorgente {
                        color: var(--secondary-color);
                        cursor: pointer;
                        transition: color 0.3s ease;
                    }

                    .sorgente:hover {
                        color: var(--hover-color);
                    }

                    @media (max-width: 768px) {
                        .nomeUtente {
                            font-size: 16px;
                        }

                        .log_inButton {
                            padding: 8px 16px;
                        }

                        table {
                            font-size: 14px;
                        }
                    }

                    @media (max-width: 480px) {
                        .nomeUtente {
                            font-size: 14px;
                        }

                        .log_inButton {
                            padding: 6px 12px;
                        }

                        table {
                            font-size: 12px;
                        }
                    }
                </style>
            </head>

            <body>
                <div class="nav">
                    <?php
                    if ($username === $adminId) {
                        echo '<p class="nomeUtente">admin: ' . $username . '</p>';
                    } else {
                        echo '<p class="nomeUtente">user: ' . $username . '<br>admin: ' . $adminId . '</p>';
                    }
                    ?>
                    <p class="log_inButton" onclick="location.href='dashboard.php?username=<?php echo $username ?>&admin=<?php echo $adminId ?>'">Dashboard</p>
                </div>
                <div class="content">
                    <?php
                    // Your PHP code for retrieving and displaying the activities
                    ?>
                    <table>
                        <tr>
                            <th>Data</th>
                            <th>Sorgente</th>
                            <th>Azione</th>
                            <th>Destinazione</th>
                        </tr>
                        <?php
                        foreach ($activities as $activity) {
                            ?>
                            <tr>
                                <td style="border-left: 1px solid transparent"><?php echo $activity['data'] ?></td>
                                <td>
                                    <div class="sorgente" onclick="location.href='utente.php?username=<?php echo $username ?>&admin=<?php echo $adminId ?>&subject=<?php echo $activity['sorgente'] ?>'">
                                        <?php echo $activity['sorgente'] ?>
                                    </div>
                                </td>
                                <td><?php echo $activity['azione'] ?></td>
                                <td>
                                    <?php
                                    if (isAnAccount($activity['destinazione'])) {
                                        ?>
                                        <div class="sorgente" onclick="location.href='utente.php?username=<?php echo $username ?>&admin=<?php echo $adminId ?>&subject=<?php echo $activity['destinazione'] ?>'">
                                            <?php echo $activity['destinazione'] ?>
                                        </div>
                                        <?php
                                    } else {
                                        echo $activity['destinazione'];
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </body>

            </html>
            <?php
        } else {
            echo 'Nessuna attività presente nella tabella con id_admin corrispondente.';
        }
    } else {
        echo 'Nessun id_admin corrispondente trovato.';
    }
} catch (PDOException $e) {
    echo $e->getMessage();
    return false;
}
?>
