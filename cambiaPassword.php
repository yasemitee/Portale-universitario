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

if (isset($_POST) && isset($_POST['vecchia_password']) && isset($_POST['nuova_password'])) {
    $err = cambiaPassword($logged, $_POST['vecchia_password'], $_POST['nuova_password']);
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
    <link rel="stylesheet" href="style/style-pagine.css" />
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <!-- Sidebar -->
            <?php
            include_once('lib/sidebar-esterna.php');
            ?>
            <!-- Contenuto di destra -->
            <div id="content" class="col py-3 overflow-y-scroll offset-1 offset-md-2 offset-sm-3">
                <div class="row mx-5 my-4 p-3 shadow rounded">
                    <h3 class="mb-4">Cambio password</h3>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <label class="form-label">Password attuale</label>
                        <input type="password" class="form-control mb-3" name="vecchia_password" placeholder="inserisci la password attuale" />
                        <label class="form-label">Nuova password</label>
                        <input type="password" class="form-control mb-3" name="nuova_password" placeholder="inserisci la nuova password" />
                        <button type="submit" class="btn btn-dark w-100 mt-3">Conferma</button>
                    </form>
                    <?php if (!empty($err)) { ?>
                        <div class="alert alert-danger  mx-auto my-4"><?php echo $err; ?></div>
                    <?php } elseif (isset($_POST['nuova_password'])) { ?>
                        <div class="alert alert-success mx-auto my-4"><?php echo 'La password è stata cambiata correttamente'; ?></div>
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