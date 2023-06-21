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

if (isset($_POST['rimuovi_appello'])) {
  $id_appello = $_POST['rimuovi_appello'];
  $rimozione = removeAppello($id_appello);
  header("Location: " . $_SERVER['PHP_SELF'] . "#gestione-esami");
}

if ($_SESSION['tipo_utente'] != 'docente') {
  header('Location: ' . $_SESSION['tipo_utente'] . '.php');
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
      <div id="content" class="col py-3 offset-1 offset-md-3 offset-lg-2 offset-sm-3">
        <!-- Informazioni generali -->
        <div class="row mx-3 mx-md-5 my-4 p-3 shadow rounded" id="informazioni_personali">
          <h6 class="mb-4 text-uppercase">Informazioni personali</h6>
          <?php
          $info = getInfoDocente($_SESSION['user']);
          ?>
          <div class="mb-1">
            <label class="fs-2"><strong><?php echo ($info['nome'] . ' ' . $info['cognome']); ?></strong></label>
          </div>
          <div class="d-flex mb-1">
            <label class="fs-6 "><strong>Specializzazione: </strong><?php echo ($info['specializzazione']); ?></label>
          </div>
        </div>
        <!-- Insegnamenti del docente -->
        <div class="row mx-3 mx-md-5 my-4 p-3 shadow rounded" id="insegnamenti">
          <h4 class="mb-4 text-uppercase">Insegnamenti di cui sei repsonsabile</h4>
          <?php
          $insegnamenti = getInsegnamentiDocete($_SESSION['id']);

          if (!empty($insegnamenti)) {
            foreach ($insegnamenti as $insegnamento) {
              $numero_studenti = numeroStudentiInsegnamento($insegnamento['codice'], $insegnamento['corso_studi'])
          ?>
              <div class="card my-4 border" style="max-width: 540px; border-style: none;">
                <div class="row g-0">
                  <div class="col-md-5 py-3 py-md-5 px-3">
                    <h5 class="card-title mb-3"><?php echo $insegnamento['nome'] ?></h5>
                    <h6 class="card-text"><strong>Corso di studi: </strong><?php echo $insegnamento['corso_studi'] ?></h6>
                    <h6 class="card-text"><strong>Anno: </strong><?php echo $insegnamento['anno'] ?></h6>
                    <h6 class="card-text"><strong>N.Studenti: </strong><?php echo $numero_studenti ?></h6>
                  </div>
                  <div class="col-md-7 mt-md-0 mt-3 py-md-5 pb-3 px-3">
                    <h5 class="card-title">Descrizione:</h5>
                    <h6 class="card-text"><?php echo $insegnamento['descrizione'] ?> </h6>
                  </div>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <div class="alert alert-secondary mt-3">Nessun insegnamento disponibile</div>
          <?php } ?>
        </div>
        <!-- Calendario esami -->
        <div class="row mx-3 mx-md-5 my-4 p-3 shadow rounded" id="gestione-esami">
          <h4 class="mb-4 text-uppercase">Calendario appelli</h4>
          <?php
          foreach ($insegnamenti as $insegnamento) {
            $codice_esame = getEsameByInsegnamento($insegnamento['codice']);
            $appelli = getAppelliEsame($codice_esame);
          ?>

            <div class=" my-4 p-3" id="esami">

              <h6 class="mb-4 text-uppercase">Appelli di <strong><?php echo $insegnamento['nome'] ?></strong>
              </h6>

              <?php
              if (!empty($appelli)) {
              ?>
                <table class="table mx-2">
                  <thead>
                    <tr>
                      <th scope="col">Nome esame</th>
                      <th scope="col">Data appello</th>
                      <th scope="col"></th>
                      <th scope="col"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($appelli as $appello) { ?>
                      <tr>
                        <td><?php echo $appello['nome']; ?></td>
                        <td><?php echo $appello['data']; ?></td>
                        <td>
                          <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="rimuovi_appello" value="<?php echo $appello['id_appello']; ?>">
                            <button type="submit" class="btn btn-light ">Rimuovi appello</button>
                          </form>
                        </td>
                        <td>
                          <form method="GET" action="./registraVoti.php">
                            <input type="hidden" name="codice_esame" value="<?php echo $codice_esame; ?>">
                            <input type="hidden" name="id_appello" value="<?php echo $appello['id_appello']; ?>">
                            <button type="submit" class="btn btn-light">Registra voti</button>
                          </form>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              <?php } else { ?>
                <div class="alert alert-secondary mt-3">Non è stata programmato nessun appello</div>
              <?php }

              if (isset($_POST['rimuovi_appello']) && $rimozione == "") {
                echo '<div class="alert alert-success mt-3">Appello rimosso con successo!</div>';
              } elseif (isset($_POST['rimuovi_esame'])) {
                echo '<div class="alert alert-danger mt-3">Qualcosa è andato storto nella rimozione</div>';
              }
              ?>
              <form method="GET" action="./inserimentoAppello.php">
                <input type="hidden" name="codice_esame" value="<?php echo $codice_esame; ?>">
                <input type="hidden" name="corso_studi" value="<?php echo $insegnamento['corso_studi']; ?>">
                <button type="submit" class="btn custom-btn my-2 my-sm-0">Inserisci un nuovo appello</button>
              </form>
            </div>
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