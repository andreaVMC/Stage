<?php
require 'config.php';
session_start();
$connessione = instauraConnessione();

if (isset($_POST['submit'])) {
    $messaggio = $_POST['messaggio'];
    $data = date("Y-m-d H:i:s");
    inserisciMessaggio($messaggio, $data, $connessione);

    // Esegui il redirect alla stessa pagina senza i dati POST
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

function instauraConnessione()
{
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connessione;
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

function inserisciMessaggio($messaggio, $data, $connessione)
{
    $stmt = $connessione->prepare("INSERT INTO `chat`(`data`, `messaggio`) VALUES (?, ?)");
    $stmt->execute([$data, $messaggio]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Andrea Chat</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Ballet&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap');

        :root{
            --sfondo:#E2E8CE;
            --secondario:#ACBFA4;
            --primario:#FF7F11;
            --contrasto:#262626;
            --testo: black;
        }

        body{
            width:100%;
            height: 100vh;
            overflow-x: hidden;
            background-color: var(--sfondo);
            display: flex;
            margin: 0;
            border: 0;
            flex-direction: column;
            align-items: center;
            scroll-behavior: smooth;
            font-family: 'Lato', sans-serif;
            color: var(--testo);
        }

        .nav{
            background-color: var(--contrasto);
            width: 100%;
            height: 8vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1% 0%;
            position:fixed;
            top: 0;
        }

        .shadow-nav{
            width: 100%;
            height:12vh;
            position: relative;
        }

        .titolo{
            font-family: 'Ballet', cursive;
            color: var(--primario);
            font-size: 2vw;
            font-weight: 400;
        }

        .fot{
            background-color: #262626;
            padding: 1% 5%;
            border-radius: 100px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            width: 15%;
            position: fixed;
            bottom: 5%;
        }

        input{
            font-weight: 600;
            background-color: transparent;
            border: 2px solid var(--primario);
            border-radius: 15px;
            color: var(--primario);
            padding: 1% 2%;
        }

        ::-webkit-input-placeholder { /* WebKit browsers (Safari, Google Chrome, Opera 15+) */
            color: var(--primario);
        }
        :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
            color: var(--primario);
            opacity: 0.5;
        }
        ::-moz-placeholder { /* Mozilla Firefox 19+ */
            color: var(--primario);
            opacity: 0.5;
        }
        :-ms-input-placeholder { /* Internet Explorer 10+ */
            color: var(--primario);
        }
        
        button{
            background-color: transparent;
            border: 2px solid var(--primario);
            border-radius: 5px;
            color: var(--primario);
            padding: 1% 2%;
            cursor: pointer;
        }

        .chat{
            width: 60%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-top: 15%;
        }

        .blocco-messaggio{
            margin: 2% 0%;
            background-color: var(--secondario);
            padding: 2% 2%;
            border-radius: 15px;
            max-width: 100%;
        }

        .messaggio{
            max-width: 100%;
            word-break: break-all;
            margin-bottom: 1%;
        }

        .data{
            color: var(--primario);
            background-color: var(--contrasto);
            width: fit-content;
            padding: 0% 2%;
            border-radius: 5px;
            font-size: 100%;
            white-space: nowrap;
        }

        @media (max-width: 1650px) 
        {

            .fot{
                flex-direction: column;
                width: 15%;
                padding: 2% 2%;
            }

            .fot>button{
                margin-top: 2%;
                width: 25%;
            }

            .data{
                font-size: 80%;
            }
        }

        @media (max-width: 1500px) 
        {
            .fot{
                width: 20%;
            }
            .blocco-messaggio{
                padding: 2% 3%;
            }
            .data{
                font-size: 70%;
            }
        }

        @media (max-width: 1200px) 
        {
            .fot{
                width: 30%;
            }
            .titolo{
                font-weight: 600;
            }
            .blocco-messaggio{
                padding: 2% 4%;
            }
            .data{
                font-size: 60%;
            }
        }

        @media (max-width: 900px) 
        {
            .fot{
                width: 40%;
            }

            .titolo{
                font-size: 4vw;
            }

            .blocco-messaggio{
                padding: 2% 5%;
            }
            .data{
                font-size: 50%;
            }
        }

        @media (max-width: 600px) 
        {
            .fot{
                width: 60%;
            }
            .titolo{
                font-size: 6vw;
            }

            .blocco-messaggio{
                padding: 2% 6%;
            }
            .data{
                font-size: 4    0%;
            }
        }
    </style>
</head>
<body>
    <div class="nav">
        <div class="titolo">Andrea's Chat</div>
    </div>
    <div class="shadow-nav"></div>
    <div class="chat">
        <?php
            $stmt = $connessione->prepare("SELECT * FROM `chat` ORDER BY `data` DESC");
            $stmt->execute();
            $messaggi = $stmt->fetchAll();

            foreach ($messaggi as $messaggio) {
                ?>
                    <div class="blocco-messaggio">
                        <div class="messaggio">
                            <?php echo $messaggio['messaggio']?>
                        </div>
                        <div class="data">data: 
                            <?php echo $messaggio['data']?>
                        </div>
                    </div>
                <?php
            }
        ?>
    </div>
    <form class="fot" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <input type="text" name="messaggio" placeholder="messaggio">
        <input type="submit" id="submit" name="submit" style="display:none;">
        <button onclick="document.getElementById('submit').click()">send</button>
    </form>
</body>
</html>
