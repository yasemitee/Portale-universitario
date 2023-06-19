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

if (isset($_POST) && isset($_POST['tipo_carriera'])) {
  $carriera = $_POST['tipo_carriera'];
  if ($carriera == 'carriera_valida') {
    $_SESSION['voti'] = getCarrieraValida($_SESSION['id']);
  } elseif ($carriera == 'carriera_completa') {
    $_SESSION['voti'] = getCarrieraCompleta($_SESSION['id']);
  }
  header("Location: " . $_SERVER['PHP_SELF'] . "#carriera");
}

if (isset($_POST['rimuovi_esame'])) {
  $id_iscrizione = $_POST['rimuovi_esame'];
  $rimozione = removeIscrizione($id_iscrizione);
  header("Location: " . $_SERVER['PHP_SELF'] . "#esami");
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
      <div id="content" class="col py-3  offset-1 offset-md-2 offset-sm-3">
        <!-- Informazioni generali -->
        <div class="row mx-5 my-4 p-3 shadow rounded" id="informazioni">
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
        </div>
        <!-- Insegnamenti del corso -->
        <div class="row mx-5 my-4 p-3 shadow rounded" id="insegnamenti">
          <h3 class="mb-4">Insegnamenti del corso di studi</h3>
          <?php
          $insegnamenti = getInsegnamentiCorso($logged);
          if (!empty($insegnamenti)) {
          ?>
            <table class="table">
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
                    <td><?php echo $insegnamento['anno']; ?></td>
                    <td><?php echo $insegnamento['nome_docente'] . " " . $insegnamento['cognome_docente']; ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          <?php } else { ?>
            <div class="alert alert-danger mt-3">Nessun insegnamento disponibile.</div>
          <?php } ?>
        </div>
        <!-- Carriera -->
        <div class="row mx-5 my-4 p-3 shadow rounded" id="carriera">
          <h3 class="mb-4">Carriera dello studente</h3>
          <div class="d-flex mt-2 mb-4">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              <input type="hidden" name="tipo_carriera" value="carriera_valida">
              <button type="submit" class="btn custom-btn m-0">Carriera valida</button>
            </form>
            <form class="mx-3" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              <input type="hidden" name="tipo_carriera" value="carriera_completa">
              <button type="submit" class="btn custom-btn">Carriera completa</button>
            </form>
          </div>
          <?php
          if (isset($_SESSION['voti'])) {
            $voti = $_SESSION['voti'];

          ?>
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">C.Esame</th>
                  <th scope="col">Esame</th>
                  <th scope="col">Valutazione</th>
                  <th class="d-none d-sm-table-cell" scope="col">Data verbalizzazione</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($voti as $voto) { ?>
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
          } elseif (isset($_POST['tipo_carriera'])) { ?>
            <div class="alert alert-danger my-3">Nessun esame disponibile.</div>
          <?php } ?>
        </div>
        <!-- Iscrizione agli esami  -->
        <div class="row mx-5 my-4 p-3 shadow rounded" id="esami">
          <h3 class="mb-4">Esami in programma</h3>
          <a href="./iscrizioneEsame.php" class="btn custom-btn mt-2 mb-4 w-50">Iscrizione a un nuovo esame</a>
          <?php
          $iscrizioni = getIscrizioni($_SESSION['id']);
          if (!empty($iscrizioni)) {
          ?>
            <table class="table">
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
                      <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="rimuovi_esame" value="<?php echo $iscrizione['id']; ?>">
                        <button type="submit" class="btn btn-light ">Rimuovi</button>
                      </form>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          <?php } else { ?>
            <div class="alert alert-secondary mt-3">Non è stata programmata nessuna iscrizione.</div>
          <?php }

          if (isset($_POST['rimuovi_esame']) && $rimozione == "") {
            echo '<div class="alert alert-success mt-3">Esame rimosso con successo!</div>';
          } elseif (isset($_POST['rimuovi_esame'])) {
            echo '<div class="alert alert-danger mt-3">Qualcosa è andato storto nella rimozione</div>';
          }
          ?>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>