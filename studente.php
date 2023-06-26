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

if (isset($logged)) {
  $_SESSION['carriera_valida'] = getCarrieraValida($_SESSION['id']);
  $_SESSION['carriera_completa'] = getCarrieraCompleta($_SESSION['id']);
}


if (isset($_POST['rimuovi_esame'])) {
  $id_iscrizione = $_POST['rimuovi_esame'];
  $rimozione = removeIscrizione($id_iscrizione);
}

if ($_SESSION['tipo_utente'] != 'studente') {
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
      include_once('lib/sidebar-studente.php');
      ?>
      <!-- Contenuto di destra -->
      <div id="content" class="col py-3 offset-1 offset-md-3 offset-lg-2 offset-sm-3">
        <!-- Informazioni generali -->
        <div class="row mx-5 my-4 p-3 shadow rounded" id="informazioni_personali">
          <!-- <img src="img/wave-haikei.svg" alt="wave"> -->
          <h6 class="mb-4 text-uppercase">Informazioni personali</h6>
          <?php
          $info = getInfoStudente($_SESSION['user']);
          ?>
          <div class="d-flex flex-column">
            <div class="mb-1">
              <label class="fs-2"><strong><?php echo ($info['nome'] . ' ' . $info['cognome']); ?></strong></label>
            </div>
            <div class="mb-1">
              <label class="fs-6 matricola"><strong>#<?php echo ($info['matricola']); ?></strong></label>
            </div>
          </div>
          <div class="d-flex">
            <label class="fs-6  mt-4 my-1"><strong>Corso: </strong><?php echo ($info['nome_corso']); ?></label>
          </div>
          <div class="d-flex">
            <label class="fs-6  my-1"><strong>Anno di frequenza: </strong><?php echo ($info['anno_frequenza']); ?></label>
          </div>
          <div class="d-flex my-1">
            <label class="fs-6 "><strong>Durata corso: </strong><?php echo ($info['durata']); ?></label>
          </div>
          <div class="d-flex my-1">
            <label class="fs-6 "><strong>Anno di iscrizione: </strong><?php echo ($info['anno_iscrizione']); ?></label>
          </div>
        </div>
        <!-- Insegnamenti del corso -->
        <div class="row mx-5 my-4 p-3 shadow rounded" id="insegnamenti">
          <h6 class="mb-4 text-uppercase">Insegnamenti del corso di studi</h6>
          <?php
          $insegnamenti = getInsegnamentiCorso($info['codice_corso']);
          if (!empty($insegnamenti)) {
          ?>
            <table class="table align-middle">
              <thead>
                <tr>
                  <th scope="col">Codice</th>
                  <th scope="col">Nome</th>
                  <th scope="col">Anno</th>
                  <th scope="col">Docente</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($insegnamenti as $insegnamento) { ?>
                  <tr>
                    <th scope="row"><?php echo $insegnamento['codice']; ?></th>
                    <td><?php echo $insegnamento['nome']; ?></td>
                    <td>&nbsp&nbsp&nbsp<?php echo $insegnamento['anno']; ?></td>
                    <td><?php echo $insegnamento['nome_docente'] . " " . $insegnamento['cognome_docente']; ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          <?php } else { ?>
            <div class="alert alert-primary mt-3">
              Nessun insegnamento disponibile.
            </div>
          <?php } ?>
        </div>
        <!-- Carriera -->
        <div class="row mx-5 my-4 p-3 shadow rounded table-responsive" id="carriera">
          <h6 class="mb-4 text-uppercase">Carriera dello studente</h6>
          <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <button type="button" class="nav-link active" id="nav-valida-tab" data-bs-toggle="tab" data-bs-target="#carriera_valida" role="tab" aria-controls="nav-valida" aria-selected="true">Carriera valida</button>
              <button type="button" class="nav-link" id="nav-completa-tab" data-bs-toggle="tab" data-bs-target="#carriera_completa" role="tab" aria-controls="nav-completa" aria-selected="false">Carriera completa</button>
            </div>
          </nav>
          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="carriera_valida" role="tabpanel" aria-labelledby="valida" tabindex="0">
              <?php
              $carriera_valida = array();
              if (isset($_SESSION['carriera_valida'])) {
                $carriera_valida = $_SESSION['carriera_valida'];
              }
              if (!empty($carriera_valida)) {
              ?>
                <table class="table align-middle">
                  <thead>
                    <tr>
                      <th scope="col">C.Esame</th>
                      <th scope="col">Esame</th>
                      <th scope="col">Valutazione</th>
                      <th class="d-none d-sm-table-cell" scope="col">Data verbalizzazione</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($carriera_valida as $voto) { ?>
                      <tr>
                        <th scope="row"><?php echo $voto['codice']; ?></th>
                        <td><?php echo $voto['nome']; ?></td>
                        <td>&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $voto['voto']; ?></td>
                        <td><?php echo $voto['data_verbalizzazione']; ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              <?php
              } else { ?>
                <div class="alert alert-primary my-3">Nessun esame disponibile.</div>
              <?php } ?>
            </div>
            <div class="tab-pane fade" id="carriera_completa" role="tabpanel" aria-labelledby="completa" tabindex="0">
              <?php
              $carriera_completa = array();
              if (isset($_SESSION['carriera_completa'])) {
                $carriera_completa = $_SESSION['carriera_completa'];
              }
              if (!empty($carriera_completa)) {
              ?>
                <table class="table align-middle">
                  <thead>
                    <tr>
                      <th scope="col">C.Esame</th>
                      <th scope="col">Esame</th>
                      <th scope="col">Valutazione</th>
                      <th scope="col">Data verbalizzazione</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($carriera_completa as $voto) { ?>
                      <tr>
                        <th scope="row"><?php echo $voto['codice']; ?></th>
                        <td><?php echo $voto['nome']; ?></td>
                        <td><?php echo $voto['voto']; ?></td>
                        <td class="d-none d-sm-table-cell"><?php echo $voto['data_verbalizzazione']; ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              <?php
              } else { ?>
                <div class="alert alert-primary my-3">Nessun esame disponibile.</div>
              <?php } ?>
            </div>
          </div>
        </div>

        <!-- Iscrizione agli esami  -->
        <div class="row mx-5 my-4 p-3 shadow rounded table-responsive" id="esami">
          <h6 class="mb-4 text-uppercase">Esami in programma</h6>
          <?php
          $iscrizioni = getIscrizioni($_SESSION['id']);
          if (!empty($iscrizioni)) {
          ?>
            <table class="table table-hover align-middle table-borderless">
              <thead>
                <tr>
                  <th scope="col">C.Esame</th>
                  <th scope="col">Nome esame</th>
                  <th scope="col">Data esame</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($iscrizioni as $iscrizione) {
                ?>
                  <tr>
                    <th scope="row"><?php echo $iscrizione['codice']; ?></th>
                    <td><?php echo $iscrizione['nome']; ?></td>
                    <td><?php echo $iscrizione['data']; ?></td>
                    <td>
                      <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#esami"; ?>">
                        <input type="hidden" name="rimuovi_esame" value="<?php echo $iscrizione['id']; ?>">
                        <button type="submit" class="btn btn-light ">Rimuovi</button>
                      </form>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          <?php } else { ?>
            <div class="alert alert-primary mt-3">Non è stata programmata nessuna iscrizione.</div>
          <?php }

          if (isset($_POST['rimuovi_esame']) && $rimozione == "") {
          ?><div class="alert alert-success alert-dismissible fade show mt-3">Esame rimosso con successo!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
          <?php
          } elseif (isset($_POST['rimuovi_esame'])) {
          ?><div class="alert alert-danger alert-dismissible fade show mt-3"><i class="fa-solid fa-triangle-exclamation me-2"></i>Qualcosa è andato storto nella rimozione<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
          <?php
          }
          ?>
          <a href="./iscrizioneEsame.php" class="btn custom-btn my-4 p-md-2 mx-auto mx-md-0 w-50">Iscrizione a un nuovo esame</a>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>