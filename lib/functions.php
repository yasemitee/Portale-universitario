<?php
define("myhost", "localhost");
define("myuser", "yassine");
define("mypsw", "admin");
define("mydb", "portale_uni");
?>

<?php

/* 
    INTERAZIONE CON IL DB
*/

function open_pg_connection()
{

    $connection = "host=" . myhost . " dbname=" . mydb . " user=" . myuser . " password=" . mypsw;

    return pg_connect($connection);
}

function close_pg_connection($db)
{

    return pg_close($db);
}

/**
 * LOGIN E CAMBIO PASSWORD
 */

function login($email, $password)
{

    $params = array(
        $email,
        $password
    );

    $logged = null;
    $header = null;

    $db = open_pg_connection();

    $sql_studente = "SELECT id, email, nome, cognome FROM portale_uni.studente
     WHERE email = $1 AND password = $2 AND matricola IS NOT NULL;";
    $result_studente = pg_prepare($db, "check_studente", $sql_studente);
    $result_studente = pg_execute($db, "check_studente", $params);

    $sql_docente = "SELECT id, email, nome, cognome FROM portale_uni.docente
    WHERE email = $1 AND password = $2 AND specializzazione IS NOT NULL;";
    $result_docente = pg_prepare($db, "check_docente", $sql_docente);
    $result_docente = pg_execute($db, "check_docente", $params);

    $sql_segreteria = "SELECT id, email, nome, cognome FROM portale_uni.segreteria
                      WHERE email = $1 AND password = $2;";
    $result_segreteria = pg_prepare($db, "check_segreteria", $sql_segreteria);
    $result_segreteria = pg_execute($db, "check_segreteria", $params);

    if ($row = pg_fetch_assoc($result_studente)) {
        $id = $row['id'];
        $logged = $row['email'];
        $nome = $row['nome'];
        $cognome = $row['cognome'];
        $_SESSION['id'] = $id;
        $_SESSION['tipo_utente'] = 'studente';
        $_SESSION['nome'] = $nome;
        $_SESSION['cognome'] = $cognome;
        $header = 'Location: studente.php';
    } elseif ($row = pg_fetch_assoc($result_docente)) {
        $id = $row['id'];
        $logged = $row['email'];
        $nome = $row['nome'];
        $cognome = $row['cognome'];
        $_SESSION['id'] = $id;
        $_SESSION['tipo_utente'] = 'docente';
        $_SESSION['nome'] = $nome;
        $_SESSION['cognome'] = $cognome;
        $header = 'Location: docente.php';
    } elseif ($row = pg_fetch_assoc($result_segreteria)) {
        $id = $row['id'];
        $logged = $row['email'];
        $nome = $row['nome'];
        $cognome = $row['cognome'];
        $_SESSION['id'] = $id;
        $_SESSION['tipo_utente'] = 'segreteria';
        $_SESSION['nome'] = $nome;
        $_SESSION['cognome'] = $cognome;
        $header = 'Location: segreteria.php';
    }

    close_pg_connection($db);

    return array($logged, $header);
}

function cambiaPassword($email, $tipo_utente, $vecchia_password, $nuova_password)
{
    $db = open_pg_connection();

    switch ($tipo_utente) {
        case 'studente':
            $sql = "UPDATE portale_uni.studente
                    SET password = $3 WHERE email = $1 AND password = $2;";
            break;
        case 'docente':
            $sql = "UPDATE portale_uni.docente
                        SET password = $3 WHERE email = $1 AND password = $2;";
            break;
        case 'segretaria':
            $sql = "UPDATE portale_uni.segreteria
                            SET password = $3 WHERE email = $1 AND password = $2;";
            break;
    }


    $params = array(
        $email,
        $vecchia_password,
        $nuova_password
    );

    $result = pg_prepare($db, "change_password", $sql);
    $result = pg_execute($db, "change_password", $params);

    $err = '';

    if (pg_affected_rows($result) === 0) {
        $err = "Non è stato possibile cambiare la password, verificare i dati inseriti e riprovare.";
    }

    close_pg_connection($db);
    return $err;
}

/**
 * REGISTRAZIONE UTENTI
 */

function registraStudente($nome, $cognome, $email, $password, $matricola, $corso_studi)
{
    if (empty($nome) || empty($cognome) || empty($password) || empty($email) || empty($matricola) || empty($corso_studi)) {
        return false;
    }

    $db = open_pg_connection();

    $sql = "SELECT COUNT(*) FROM portale_uni.studente WHERE email = $1;";

    $params = array(
        $email
    );

    $result = pg_prepare($db, "controllo_esistenza", $sql);
    $result = pg_execute($db, "controllo_esistenza", $params);

    $count = pg_fetch_result($result, 0, 0);
    if ($count > 0) {
        close_pg_connection($db);
        return false;
    }

    $sql = "INSERT INTO portale_uni.studente (matricola, corso_studi, anno_frequenza, anno_iscrizione, email, password, nome, cognome) 
            VALUES ($1, $2, 1,EXTRACT(year FROM CURRENT_DATE),$3, $4,$5,$6)";
    $params = array(
        $matricola,
        $corso_studi,
        $email,
        $password,
        $nome,
        $cognome,
    );

    $result = pg_prepare($db, "registrazione_studente", $sql);
    $result = pg_execute($db, "registrazione_studente", $params);

    close_pg_connection($db);
    return $result !== false;
}

function registraDocente($nome, $cognome, $email, $password, $specializzazione)
{
    if (empty($nome) || empty($cognome) || empty($password) || empty($email) || empty($specializzazione)) {
        return false;
    }

    $db = open_pg_connection();

    $sql = "SELECT COUNT(*) FROM portale_uni.docente WHERE email = $1;";

    $params = array(
        $email
    );

    $result = pg_prepare($db, "controllo_esistenza", $sql);
    $result = pg_execute($db, "controllo_esistenza", $params);

    $count = pg_fetch_result($result, 0, 0);
    if ($count > 0) {
        close_pg_connection($db);
        return false;
    }

    $sql = "INSERT INTO portale_uni.docente (specializzazione, email, password, nome, cognome) 
            VALUES ($1, $2,$3,$4,$5)";
    $params = array(
        $specializzazione,
        $email,
        $password,
        $nome,
        $cognome,
    );

    $result = pg_prepare($db, "registrazione_docente", $sql);
    $result = pg_execute($db, "registrazione_docente", $params);

    close_pg_connection($db);
    return $result !== false;
}

/**
 * INSERIMENTO CORSI/INSEGNAMENTI
 */

function inserisciCorso($codice, $nome, $descrizione, $facoltà, $durata)
{
    if (empty($codice) || empty($nome) || empty($descrizione) || empty($facoltà) || empty($durata)) {
        return false;
    }

    $db = open_pg_connection();

    $sql = "SELECT COUNT(*) FROM portale_uni.corso WHERE codice = $1;";

    $params = array(
        $codice
    );

    $result = pg_prepare($db, "controllo_esistenza_corso", $sql);
    $result = pg_execute($db, "controllo_esistenza_corso", $params);

    $count = pg_fetch_result($result, 0, 0);
    if ($count > 0) {
        close_pg_connection($db);
        return false;
    }

    $sql = "INSERT INTO portale_uni.corso (codice, nome, facoltà, durata, descrizione)
            VALUES ($1, $2, $3, $4, $5)";
    $params = array(
        $codice,
        $nome,
        $facoltà,
        $durata,
        $descrizione
    );

    $result = pg_prepare($db, "inserisci_corso", $sql);
    $result = pg_execute($db, "inserisci_corso", $params);

    close_pg_connection($db);
    return $result !== false;
}

function inserisciInsegnamento($codice, $nome, $corso_studi, $descrizione, $anno, $docente_responsabile, $propedeuticità)
{
    if (empty($codice) || empty($nome) || empty($descrizione) || empty($corso_studi) || empty($anno) || empty($docente_responsabile)) {
        return false;
    }


    $db = open_pg_connection();

    $sql = "SELECT COUNT(*) FROM portale_uni.insegnamento WHERE codice = $1;";

    $params = array(
        $codice
    );

    $result = pg_prepare($db, "controllo_esistenza_insegnamento", $sql);
    $result = pg_execute($db, "controllo_esistenza_insegnamento", $params);

    $count = pg_fetch_result($result, 0, 0);
    if ($count > 0) {
        close_pg_connection($db);
        return false;
    }


    $sql = "SELECT COUNT(*) FROM portale_uni.corso WHERE codice = $1;";

    $params = array(
        $corso_studi
    );

    $result = pg_prepare($db, "controllo_esistenza_corso", $sql);
    $result = pg_execute($db, "controllo_esistenza_corso", $params);

    $count = pg_fetch_result($result, 0, 0);
    if ($count == 0) {
        close_pg_connection($db);
        return false;
    }


    $sql = "INSERT INTO portale_uni.insegnamento(codice, nome, descrizione, anno, docente_responsabile, corso_studi)
            VALUES ($1, $2, $3, $4, $5, $6)";
    $params = array(
        $codice,
        $nome,
        $descrizione,
        $anno,
        $docente_responsabile,
        $corso_studi
    );

    $result = pg_prepare($db, "inserisci_insegnamento", $sql);
    $result = pg_execute($db, "inserisci_insegnamento", $params);

    if ($result == false) {
        close_pg_connection($db);
        return false;
    }

    if (!empty($propedeuticità)) {
        $sql = "INSERT INTO portale_uni.propedeuticità(insegnamento, corso_studi, propedeuticità)
    VALUES ($1, $2, $3)";
        $params = array(
            $codice,
            $corso_studi,
            $propedeuticità
        );

        $result = pg_prepare($db, "inserisci_propedeuticità", $sql);
        $result = pg_execute($db, "inserisci_propedeuticità", $params);
    }

    if ($result == false) {
        removeInsegnamento($codice);
        close_pg_connection($db);
        return false;
    }


    close_pg_connection($db);
    return $result !== false;
}

/**
 * RIMOZIONE UTENTI/CORSI/INSEGNAMENTI
 */
function removeStudente($id_studente)
{
    $err = '';
    $db = open_pg_connection();
    $sql = "DELETE FROM portale_uni.studente WHERE id = $1;";
    $params = array(
        $id_studente
    );

    $result = pg_prepare($db, "remove_studente", $sql);
    $result = pg_execute($db, "remove_studente", $params);

    if (pg_affected_rows($result) > 0) {
        $err = "";
    } else {
        $err = "Si è verificato un errore durante la rimozione dello studente.";
    }

    close_pg_connection($db);
    return $err;
}


function removeDocente($id_docente)
{
    $err = '';
    $insegnamenti_docente = getInsegnamentiDocete($id_docente);
    if (count($insegnamenti_docente) > 0) {
        $err = "Il docente è ancora responsabile degli insegnamenti: ";
        foreach ($insegnamenti_docente as $insegnamento) {
            $err .= $insegnamento['codice'] . ' ';
        }
        return $err;
    }
    $db = open_pg_connection();
    $sql = "DELETE FROM portale_uni.docente WHERE id = $1;";
    $params = array($id_docente);

    $result = pg_prepare($db, "remove_docente", $sql);
    $result = pg_execute($db, "remove_docente", $params);

    if (pg_affected_rows($result) > 0) {
        $err = "";
    } else {
        $err = "Si è verificato un errore durante la rimozione del docente.";
    }

    close_pg_connection($db);
    return $err;
}


function removeCorso($codice_corso)
{
    $err = '';
    $db = open_pg_connection();

    $sql = "SELECT count(*) FROM portale_uni.insegnamento WHERE corso_studi = $1;";
    $params = array(
        $codice_corso
    );

    $result = pg_prepare($db, "verifica_corso_rimuovi", $sql);
    $result = pg_execute($db, "verifica_corso_rimuovi", $params);

    $count = 0;
    if ($row = pg_fetch_assoc($result)) {
        $count = $row['count'];
    }

    if ($count == 0) {
        $err = "";
    } else {
        $err = "Il corso è ancora associato a degli insegnamenti, prima di rimuove il corso procedere a rimuove gli insegnamenti del corso.";
        close_pg_connection($db);

        return $err;
    }

    $sql = "DELETE FROM portale_uni.corso WHERE codice = $1;";
    $params = array(
        $codice_corso
    );

    $result = pg_prepare($db, "remove_corso", $sql);
    $result = pg_execute($db, "remove_corso", $params);

    if (pg_affected_rows($result) > 0) {
        $err = "";
    } else {
        $err = "Si è verificato un errore durante la rimozione del corso.";
    }
    close_pg_connection($db);

    return $err;
}
function removeInsegnamento($codice_insegnamento)
{
    $err = '';
    $db = open_pg_connection();

    $err = "";
    $sql = "DELETE FROM portale_uni.propedeuticità WHERE insegnamento = $1;";
    $params = array(
        $codice_insegnamento
    );

    $result = pg_prepare($db, "remove_insegnamento_propedeuticità", $sql);
    $result = pg_execute($db, "remove_insegnamento_propedeuticità", $params);


    $sql = "DELETE FROM portale_uni.insegnamento WHERE codice = $1;";
    $params = array(
        $codice_insegnamento
    );

    $result = pg_prepare($db, "remove_insegnamento", $sql);
    $result = pg_execute($db, "remove_insegnamento", $params);

    if (pg_affected_rows($result) > 0) {
        $err = '';
    } else {
        $err = "Si è verificato un errore durante la rimozione dell'insegnamento.";
    }

    return $err;
}


/**
 * GETTERS DELLE INFORMAZIONI ASSOCIATE AGLI UTENTI
 */

function getInfoStudente($email)
{
    $db = open_pg_connection();
    $sql = "SELECT s.id,s.nome, s.cognome, s.matricola, s.email, c.codice as codice_corso, c.nome as nome_corso, c.durata, s.anno_frequenza, s.anno_iscrizione  
    FROM portale_uni.studente s inner join portale_uni.corso c on c.codice = s.corso_studi 
    WHERE s.email  = $1;";
    $params = array(
        $email
    );

    $result = pg_prepare($db, "get_info_studente", $sql);
    $result = pg_execute($db, "get_info_studente", $params);

    if ($row = pg_fetch_assoc($result)) {
        close_pg_connection($db);
        return $row;
    } else {
        $sql = "SELECT s.id,s.nome, s.cognome, s.matricola, s.email, c.codice as codice_corso, c.nome as nome_corso, c.durata, s.anno_iscrizione  
        FROM portale_uni.storico_studenti s inner join portale_uni.corso c on c.codice = s.corso_studi 
        WHERE s.email  = $1;";
        $params = array(
            $email
        );

        $result = pg_prepare($db, "get_info_studente_storico", $sql);
        $result = pg_execute($db, "get_info_studente_storico", $params);
        if ($row = pg_fetch_assoc($result)) {
            close_pg_connection($db);
            return $row;
        }
    }
}

function getInfoDocente($email)
{
    $db = open_pg_connection();
    $sql = "SELECT d.id,d.nome, d.cognome, d.email, d.specializzazione
    FROM portale_uni.docente d
    WHERE d.email  = $1;";
    $params = array(
        $email
    );

    $result = pg_prepare($db, "get_info_docente", $sql);
    $result = pg_execute($db, "get_info_docente", $params);

    if ($row = pg_fetch_assoc($result)) {
        close_pg_connection($db);
        return $row;
    }
    close_pg_connection($db);
}

function getInfoSegretario($email)
{
    $db = open_pg_connection();
    $sql = "SELECT s.nome, s.cognome, email
    FROM portale_uni.segreteria s
    WHERE s.email  = $1;";
    $params = array(
        $email
    );

    $result = pg_prepare($db, "get_info_segretaria", $sql);
    $result = pg_execute($db, "get_info_segretaria", $params);

    if ($row = pg_fetch_assoc($result)) {
        close_pg_connection($db);
        return $row;
    }

    close_pg_connection($db);
}

function getInfoCorso($codice_corso)
{
    $db = open_pg_connection();

    $sql = "SELECT *
    from portale_uni.corso
    where codice = $1";
    $params = array(
        $codice_corso
    );

    $result = pg_prepare($db, "get_info_corso", $sql);
    $result = pg_execute($db, "get_info_corso", $params);

    if ($row = pg_fetch_assoc($result)) {
        close_pg_connection($db);
        return $row;
    }
    close_pg_connection($db);
}

function getInfoInsegnamento($codice_insegnamento)
{
    $db = open_pg_connection();

    $sql = "SELECT i.*, d.nome as nome_docente, d.cognome as cognome_docente
    from portale_uni.insegnamento i inner join portale_uni.docente d
    on i.docente_responsabile = d.id
    where i.codice = $1";
    $params = array(
        $codice_insegnamento
    );

    $result = pg_prepare($db, "get_info_insegnamento", $sql);
    $result = pg_execute($db, "get_info_insegnamento", $params);

    if ($row = pg_fetch_assoc($result)) {
        close_pg_connection($db);
        return $row;
    }
    close_pg_connection($db);
}

function getInsegnamentiCorso($corso)
{
    $db = open_pg_connection();

    $sql = "SELECT i.codice, i.nome, i.anno, i.descrizione, d.nome as nome_docente, d.cognome as cognome_docente
    FROM portale_uni.insegnamento i INNER JOIN portale_uni.docente d 
    ON i.docente_responsabile = d.id 
    WHERE i.corso_studi  = $1 
    ORDER BY i.anno;";
    $params = array(
        $corso
    );

    $result = pg_prepare($db, "get_insegnamenti_corso", $sql);
    $result = pg_execute($db, "get_insegnamenti_corso", $params);

    $insegnamenti = array();
    while ($row = pg_fetch_assoc($result)) {
        $insegnamenti[$row['codice']] = $row;
    }
    close_pg_connection($db);
    return $insegnamenti;
}


function getInsegnamentiDocete($id_docente)
{
    $db = open_pg_connection();
    $sql = "SELECT i.codice, i.nome, i.anno, i.descrizione, i.corso_studi
    FROM portale_uni.insegnamento i INNER JOIN portale_uni.docente d 
    ON i.docente_responsabile = d.id
    WHERE d.id = $1
    ORDER BY i.anno;";
    $params = array(
        $id_docente
    );

    $result = pg_prepare($db, "get_insegnamenti_docente", $sql);
    $result = pg_execute($db, "get_insegnamenti_docente", $params);

    $insegnamenti = array();
    while ($row = pg_fetch_assoc($result)) {
        $insegnamenti[$row['codice']] = $row;
    }
    close_pg_connection($db);
    return $insegnamenti;
}

function numeroStudentiInsegnamento($codice_insegnamento, $corso_studi)
{
    $db = open_pg_connection();

    $sql = "SELECT COUNT(*)
    FROM portale_uni.studente_insegnamento 
    WHERE codice_insegnamento = $1 AND corso_studi = $2";

    $params = array(
        $codice_insegnamento,
        $corso_studi
    );

    $result = pg_prepare($db, "get_numero_studenti", $sql);
    $result = pg_execute($db, "get_numero_studenti", $params);

    $numero = pg_fetch_assoc($result)['count'];
    close_pg_connection($db);
    return $numero;
}

function getCarrieraCompleta($id)
{
    $db = open_pg_connection();
    $sql = "SELECT c.esame as codice, e.nome, c.voto, TO_CHAR(c.data_verbalizzazione, 'dd/mm/yyyy') as data_verbalizzazione 
    from portale_uni.carriera c inner join portale_uni.esame e 
    on c.esame = e.codice 
    where c.studente = $1
    order by data_verbalizzazione;";
    $params = array(
        $id
    );

    $result = pg_prepare($db, "get_info_carriera_completa", $sql);
    $result = pg_execute($db, "get_info_carriera_completa", $params);

    //è diverso da getCarrieraValid perché sennò mi elimina delle chiavi (non possono esserci duplicati)
    $voti = array();
    $i = 0;
    while ($row = pg_fetch_assoc($result)) {
        $voti[$i] = $row;
        $i++;
    }
    close_pg_connection($db);
    return $voti;
}

function getCarrieraCompletaStorico($id)
{
    $db = open_pg_connection();
    $sql = "SELECT c.esame as codice, e.nome, c.voto, TO_CHAR(c.data_verbalizzazione, 'dd/mm/yyyy') as data_verbalizzazione 
    from portale_uni.storico_carriere c inner join portale_uni.esame e 
    on c.esame = e.codice 
    where c.studente = $1
    order by data_verbalizzazione;";
    $params = array(
        $id
    );

    $result = pg_prepare($db, "get_carriera_completa_storico", $sql);
    $result = pg_execute($db, "get_carriera_completa_storico", $params);

    //è diverso da getCarrieraValid perché sennò mi elimina delle chiavi (non possono esserci duplicati)
    $voti = array();
    $i = 0;
    while ($row = pg_fetch_assoc($result)) {
        $voti[$i] = $row;
        $i++;
    }
    close_pg_connection($db);
    return $voti;
}

function getCarrieraValida($id)
{
    $db = open_pg_connection();
    $sql = "SELECT c.esame as codice, c.nome, c.voto, TO_CHAR(c.data_verbalizzazione, 'dd/mm/yyyy') as data_verbalizzazione 
    from portale_uni.ultimi_esami c
    where c.studente = $1 AND c.voto >= 18
    order by data_verbalizzazione;";
    $params = array(
        $id
    );

    $result = pg_prepare($db, "get_info_carriera_valida", $sql);
    $result = pg_execute($db, "get_info_carriera_valida", $params);

    $voti = array();
    while ($row = pg_fetch_assoc($result)) {
        $voti[$row['codice']] = $row;
    }
    close_pg_connection($db);
    return $voti;
}

function getCarrieraValidaStorico($id)
{
    $db = open_pg_connection();
    $sql = "SELECT c.esame as codice, c.nome, c.voto, TO_CHAR(c.data_verbalizzazione, 'dd/mm/yyyy') as data_verbalizzazione 
    from portale_uni.ultimi_esami_storico c
    where c.studente = $1 AND c.voto >= 18
    order by data_verbalizzazione;";
    $params = array(
        $id
    );

    $result = pg_prepare($db, "get_carriera_valida_storico", $sql);
    $result = pg_execute($db, "get_carriera_valida_storico", $params);

    $voti = array();
    while ($row = pg_fetch_assoc($result)) {
        $voti[$row['codice']] = $row;
    }
    close_pg_connection($db);
    return $voti;
}



function getCorsi()
{
    $db = open_pg_connection();
    $sql = "SELECT * FROM portale_uni.corso
    order by facoltà;";

    $params = array();

    $result = pg_prepare($db, "get_info_corso", $sql);
    $result = pg_execute($db, "get_info_corso", $params);



    $corsi = array();
    while ($row = pg_fetch_assoc($result)) {
        $corsi[$row['codice']] = $row;
    }
    close_pg_connection($db);
    return $corsi;
}

function getIscrizioni($id_studente)
{
    $db = open_pg_connection();
    $sql = "SELECT i.id ,e.codice, e.nome, TO_CHAR(a.data, 'dd/mm/yyyy') as data , a.id as id_appello
    from portale_uni.iscrizione i inner join portale_uni.esame e
    on i.esame = e.codice
    inner join portale_uni.appello a
    on i.appello = a.id
    where i.id_studente = $1
    order by a.data;";

    $params = array(
        $id_studente
    );

    $result = pg_prepare($db, "get_iscrizioni", $sql);
    $result = pg_execute($db, "get_iscrizioni", $params);



    $iscrizioni = array();
    while ($row = pg_fetch_assoc($result)) {
        $iscrizioni[$row['id']] = $row;
    }
    close_pg_connection($db);
    return $iscrizioni;
}

function getEsamiCorso($corso)
{
    $db = open_pg_connection();
    $sql = "SELECT e.codice, e.nome, a.id as id_appello, a.data
    FROM portale_uni.insegnamento i inner join portale_uni.esame e
    on e.insegnamento = i.codice AND e.corso_studi = i.corso_studi
    inner join portale_uni.appello a
    on a.esame = e.codice
    LEFT JOIN portale_uni.carriera c ON c.esame = e.codice
    WHERE i.corso_studi  = $1
    order by CASE WHEN c.id IS NULL THEN 0 ELSE 1 END, e.codice,a.data;";

    $params = array(
        $corso
    );

    $result = pg_prepare($db, "get_esami_corso", $sql);
    $result = pg_execute($db, "get_esami_corso", $params);



    $esami = array();
    $i = 0;
    while ($row = pg_fetch_assoc($result)) {
        $esami[$i++] = $row;
    }
    close_pg_connection($db);
    return $esami;
}

function getEsameByInsegnamento($codice_insegnamento)
{
    $db = open_pg_connection();
    $sql = "SELECT e.codice
    FROM portale_uni.esame e
    where e.insegnamento = $1";
    $params = array(
        $codice_insegnamento
    );

    $result = pg_prepare($db, "get_esame_by_insegnamento", $sql);
    $result = pg_execute($db, "get_esame_by_insegnamento", $params);

    $i = 0;
    $esami = array();
    while ($row = pg_fetch_assoc($result)) {
        $esami[$i++] = $row['codice'];
    }
    close_pg_connection($db);
    return $esami;
}

function getAppelliEsame($codice_esame)
{
    $db = open_pg_connection();
    $sql = "SELECT a.id as id_appello, e.codice, e.nome, TO_CHAR(a.data, 'dd/mm/yyyy') as data
    FROM portale_uni.esame e INNER JOIN portale_uni.appello a
    ON e.codice = a.esame
    where a.esame = $1";
    $params = array(
        $codice_esame
    );

    $result = pg_prepare($db, "get_appelli", $sql);
    $result = pg_execute($db, "get_appelli", $params);

    $appelli = array();
    while ($row = pg_fetch_assoc($result)) {
        $appelli[$row['id_appello']] = $row;
    }
    close_pg_connection($db);
    return $appelli;
}

/**
 * GESTIONE APPELLI (INSERIMENTO VOTI, APPELLI E GESTIONE)
 */

function inserimentoAppello($codice_esame, $data_appello)
{
    $db = open_pg_connection();
    $err = "";

    $sql = "INSERT INTO portale_uni.appello (data, esame)
                VALUES ($1,$2)";
    $params = array($data_appello, $codice_esame);
    $result = pg_prepare($db, "inserisci_appello", $sql);
    $result = pg_execute($db, "inserisci_appello", $params);

    $err = pg_last_error($db);

    if (preg_match('/ERROR:\s*(.*)\s*CONTEXT:/s', $err, $matches)) {
        $err = $matches[1];
    }
    close_pg_connection($db);
    return $err;
}

function registraVoto($matricola, $codice_esame, $valutazione, $id_appello)
{
    $db = open_pg_connection();
    $err = "";

    // Recupero dell'id dello studente
    $sql = "SELECT id
    from portale_uni.studente
    where matricola = $1;";
    $params = array($matricola);
    $result = pg_prepare($db, "get_id_studente", $sql);
    $result = pg_execute($db, "get_id_studente", $params);


    if ($row = pg_fetch_assoc($result)) {
        $id_studente = $row['id'];

        //Controllo per vedere se lo studente si è iscritto all'esame
        $sql = "SELECT i.id
        from portale_uni.iscrizione i 
        where i.id_studente = $1 AND i.appello = $2";
        $params = array($id_studente, $id_appello);
        $result = pg_prepare($db, "check_iscrizione", $sql);
        $result = pg_execute($db, "check_iscrizione", $params);

        if ($row = pg_fetch_row($result)) {
            $id_iscrizione = $row[0];

            if (isset($id_iscrizione)) {
                $sql = "INSERT INTO portale_uni.carriera (studente, esame, voto, data_verbalizzazione)
                VALUES ($1,$2,$3, current_timestamp);";
                $params = array($id_studente, $codice_esame, $valutazione);
                $result = pg_prepare($db, "inserisci_appello", $sql);
                $result = pg_execute($db, "inserisci_appello", $params);
                if ($result) {
                    $err = removeIscrizione($id_iscrizione);
                } else {
                    $err = "Registrazione del voto non riuscita, controllare i campi e riprovare";
                }
            } else {
                $err = "Lo studente non si è iscritto all'esame o la valutazione è già stata registrata";
            }
        } else {
            $err = "Studente non si è iscritto all'esame o la valutazione è già stata registrata";
        }
    } else {
        $err = "Studente non esistente";
    }
    close_pg_connection($db);
    return $err;
}


function iscriviEsame($id_studente, $codice_esame, $codice_corso, $id_appello, $carriera)
{
    $err = '';
    $success = '';
    $db = open_pg_connection();


    $sql = "INSERT INTO portale_uni.iscrizione (id_studente, esame, appello) VALUES ($1, $2, $3)";
    $params = array(
        $id_studente,
        $codice_esame,
        $id_appello
    );
    $result = pg_prepare($db, "iscrivi_esame", $sql);
    $result = pg_execute($db, "iscrivi_esame", $params);

    $err = pg_last_error($db);

    if (preg_match('/ERROR:\s*(.*)\s*CONTEXT:/s', $err, $matches)) {
        $err = $matches[1];
    }

    close_pg_connection($db);
    return $err;
}

function removeIscrizione($id_iscrizione)
{
    $err = '';
    $db = open_pg_connection();
    $sql = "DELETE FROM portale_uni.iscrizione WHERE id = $1;";
    $params = array(
        $id_iscrizione
    );

    $result = pg_prepare($db, "remove_iscrizione", $sql);
    $result = pg_execute($db, "remove_iscrizione", $params);

    if (pg_affected_rows($result) > 0) {
        $err = "";
    } else {
        $err = "Si è verificato un errore durante la rimozione dell'iscrizione.";
    }

    close_pg_connection($db);
    return $err;
}

function removeAppello($id_appello)
{
    $err = '';
    $db = open_pg_connection();
    $sql = "DELETE FROM portale_uni.appello WHERE id = $1;";
    $params = array(
        $id_appello
    );

    $result = pg_prepare($db, "remove_appello", $sql);
    $result = pg_execute($db, "remove_appello", $params);

    if (pg_affected_rows($result) > 0) {
        $err = "";
    } else {
        $err = "Si è verificato un errore durante la rimozione dell'iscrizione.";
    }

    close_pg_connection($db);
    return $err;
}

?>