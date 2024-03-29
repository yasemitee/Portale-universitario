<?php
ini_set("display_errors", "Off");
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

if (isset($_POST) && isset($_POST['corso'])) {
    $corso = json_decode($_POST['corso'], true);
    $insegnamenti = getInsegnamentiCorso($corso['codice']);
}

if (isset($_POST) && isset($_POST['cerca_corso'])) {
    $corsi = getCorsi();
    $c = strtoupper($_POST['cerca_corso']);
    $corso = $corsi[$c];
    if (!isset($corso)) {
        $_SESSION['error'] = "Il corso $c non esiste";
        header("Location: corsi.php");
    } else {
        unset($_SESSION['error']);
        $insegnamenti = getInsegnamentiCorso($corso['codice']);
    }
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
            <div id="content" class="col py-3 offset-1 offset-md-3 offset-lg-2 offset-sm-3">
                <h3 class="mb-4 text-uppercase mx-5"><?php echo ('Corso di ' . $corso['nome']); ?></h3>
                <div class="row mx-5 my-5 p-3 shadow rounded">
                    <h5 class="mb-4 text-uppercase">Informazioni generali sul corso</h5>
                    <label class="fs-6 my-2"><strong>Codice del corso: </strong><?php echo ($corso['codice']); ?></label>
                    <label class="fs-6 my-2"><strong>Durata: </strong><?php echo ($corso['durata']); ?></label>
                    <label class="fs-6 my-2"><strong>Descrizione: </strong><?php echo ($corso['descrizione']); ?></label>
                </div>
                <div class="row mx-5 my-5 shadow rounded">
                    <h5 class="mb-4 text-uppercase my-3">Gli insegnamenti del corso</h5>
                    <div class="row row-cols-xl-3 g-4 pb-5">
                        <?php
                        if (!empty($insegnamenti)) {
                        ?>
                            <?php
                            foreach ($insegnamenti as $insegnamento) {
                            ?>
                                <div class="col">
                                    <div class="card mx-auto rounded" style="width: 19rem; height:20rem;;">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $insegnamento['nome']; ?></h5>
                                            <h6 class="card-subtitle mb-2 text-body-secondary"> Anno:
                                                <?php echo $insegnamento['anno']; ?>
                                                <p class="card-text">Docente: <?php echo $insegnamento['nome_docente'] . " " . $insegnamento['cognome_docente']; ?></p>
                                            </h6>
                                            <h6 class="card-subtitle mt-3">Descrizione:</h6>
                                            <p class="card-text h-50" style="display: -webkit-box;-webkit-line-clamp: 6; -webkit-box-orient: vertical; overflow: hidden;"><?php echo $insegnamento['descrizione']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                        } else { ?>
                            <div class="alert alert-primary mt-3 mx-3 w-50">Nessun insegnamento disponibile per il corso</div>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>