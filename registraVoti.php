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


if (isset($_GET['codice_esame']) && isset($_GET['id_appello'])) {
    $_SESSION['codice_esame'] = $_GET['codice_esame'];
    $_SESSION['id_appello'] = $_GET['id_appello'];
}


if (isset($_POST) && isset($_POST['matricola']) && isset($_POST['valutazione'])) {
    $matricola = $_POST['matricola'];
    $valutazione = $_POST['valutazione'];
    $err = registraVoto($matricola, $_SESSION['codice_esame'], $valutazione, $_SESSION['id_appello']);
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
            include_once('lib/sidebar-docente.php');
            ?>
            <!-- Contenuto di destra -->
            <div id="content" class="col py-3  offset-1 offset-md-3 offset-lg-2 offset-sm-3">
                <div class="row mx-5 my-4 p-3 shadow rounded">
                    <h3 class="mb-4">Inserisci le valutazioni per l'esame: <strong><?php echo $_SESSION['codice_esame'] ?></strong></h3>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <label class="col-form-label">Matricola Studente</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="matricola" placeholder="Inserisci la matricola">
                        </div>
                        <label class="col-form-label">Valutazione <label class="text-secondary">0 a 30</label></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="valutazione" placeholder="Inserisci la valutazione">
                        </div>
                        <button type="submit" class="btn custom-btn w-md-25 mt-4 p-2">Conferma</button>
                    </form>
                    <?php
                    if (isset($_POST['matricola']) && isset($_POST['valutazione']) && empty($err)) {
                    ?><div class="alert alert-success alert-dismissible fade show mt-3">Esame registrato con successo!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                    <?php
                    } elseif (isset($_POST['matricola']) && isset($_POST['valutazione']) && !empty($err)) {
                    ?><div class="alert alert-danger alert-dismissible fade show mt-3"><i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo $err; ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>