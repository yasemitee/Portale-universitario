<?php
ini_set("display_errors", "On");
ini_set("error_reporting", E_ALL);
include_once('lib/functions.php');

// inizio della sessione: PRIMA di qualunque output html!
session_start();

// imposta la variabile $logged se esiste una sessione aperta
if (isset($_SESSION['user'])) {
    $logged = $_SESSION['user'];
}

// aggiorna la variabile di sessione
if (isset($logged)) {
    $_SESSION['user'] = $logged;
}

// se l'utente fa logout, inizializza $logged
if (isset($_GET) && isset($_GET['log']) && $_GET['log'] == 'del') {
    unset($_SESSION['user']);
    $logged = null;
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style/style-pagine.css?<?php echo time(); ?>" />
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <!-- Sidebar -->
            <?php
            include_once('lib/sidebar-studente.php');
            ?>
            <!-- Contenuto di destra -->
            <div id="content" class="col py-3 offset-1 offset-md-2 offset-sm-3">
                <div class="row mx-5 my-4 p-3 shadow rounded">
                    <h3 class="mb-4">Informazioni personali</h3>
                    <div class="d-flex mb-1">
                        <?php
                        $info = getInfoStudente($_SESSION['id']);
                        ?>
                        <label class="fs-6"><strong>Nome: </strong><?php echo ($info['nome_studente']); ?></label>
                    </div>
                    <div class="d-flex mb-1">
                        <label class="fs-6 "><strong>Cognome: </strong><?php echo ($info['cognome_studente']); ?></label>
                    </div>
                    <div class="d-flex mb-1">
                        <label class="fs-6 "><strong>Matricola: </strong><?php echo ($info['matricola']); ?></label>
                    </div>
                    <div class="d-flex my-1">
                        <label class="fs-6 "><strong>Durata corso: </strong><?php echo ($info['durata']); ?></label>
                    </div>
                    <div class="d-flex">
                        <label class="fs-6  my-1"><strong>Corso: </strong><?php echo ($info['nome']); ?></label>
                    </div>
                    <div class="d-flex">
                        <label class="fs-6  my-1"><strong>Anno di frequenza: </strong><?php echo ($info['anno_frequenza']); ?></label>
                    </div>
                    <a href="./cambiaPassword.php" class="btn btn-dark mt-3">Cambia password</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>