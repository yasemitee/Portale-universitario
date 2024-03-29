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

$info = getInfoStudente($_SESSION['user']);
$voti = getCarrieraValida($_SESSION['id']);

if (isset($_POST['codice_esame']) && isset($_POST['id_appello'])) {
    $codice_esame = $_POST['codice_esame'];
    $id_appello = $_POST['id_appello'];
    $err = iscriviEsame($_SESSION['id'], $codice_esame, $id_appello);
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
                <div class="row mx-5 my-4 p-3 shadow rounded table-responsive">
                    <h5 class="mb-4 text-uppercase">Iscrizione a un nuovo esame</h5>
                    <?php
                    $esami = getEsamiCorso($info['codice_corso']);
                    if (!empty($esami)) {
                    ?>
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Codice</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Data Appello</th>
                                    <th scope="col">Stato</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($esami as $esame) { ?>
                                    <tr>
                                        <th scope="row"><?php echo $esame['codice']; ?></th>
                                        <td><?php echo $esame['nome']; ?></td>
                                        <td><?php echo $esame['data']; ?></td>
                                        <?php
                                        if (array_key_exists($esame['codice'], $voti)) {
                                        ?>
                                            <td class="text-success">
                                                Superato
                                            </td>
                                        <?php
                                        } else {
                                        ?>
                                            <td class="text-danger">
                                                Da svolgere
                                            <?php
                                        }
                                            ?>
                                            <td>
                                                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '#alert'; ?>">
                                                    <input type="hidden" name="codice_esame" value="<?php echo $esame['codice']; ?>">
                                                    <input type="hidden" name="id_appello" value="<?php echo $esame['id_appello']; ?>">
                                                    <button type="submit" class="btn btn-light ">Iscriviti</button>
                                                </form>
                                            </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <div class="alert alert-primary mt-3">Nessun esame disponibile.</div>
                    <?php }
                    if (isset($err) && $err == '') {
                    ?>
                        <div class="alert alert-success alert-dismissible fade show mx-auto my-4"><?php echo "Iscrizione andata a buon fine!"; ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                    <?php } elseif (isset($err) && $err != '') {
                    ?>

                        <div class="alert alert-danger alert-dismissible fade show mx-auto my-4"><i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo $err; ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                    <?php
                    }
                    ?>

                </div>
                <!-- questo div mi serve solo per fare il redirect agli alert -->
                <div class="" id="alert">
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>