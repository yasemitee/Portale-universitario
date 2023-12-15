# Manuale utente

## 1. **Installazione** 

Gli strumenti necessari per il corretto funzionamento dell’applicazione sono: 

- PostgreSQL (versione `15+`)
- Apache 
- PHP (versione `8+`) 

Nel mio ambiente di lavoro (MacOs) è stato utilizzato XAMPP. 

Una volta clonata la repo bisogna: 

- Importare `dump\_portale\_uni.sql` all’interno del proprio dbms. 
- Spostare la cartella *Progetto* all’interno della propria cartella di web root (`htdocs`), non bisogna spostare i file all’interno della cartella altrimenti l’applicativo non funzionerà correttamente (ad esempio le immagini, la sidebar ecc...). 
## 2. **Avvio dell’applicazione** 

Per avviare l’applicazione basta avviare il server apache e connettersi all’url `localhost/progetto/` in modo da essere indirizzati al login. Se il login non apparirà basterà connettersi a `localhost/progetto/index.php`  

## 3. **Uso dell’applicazione** 

Ovviamente si può utilizzare qualsiasi utente all’interno dell’applicazione ma gli utenti consigliati da usare (per avere una demo più completa) sono: 

- **Studente**: `email:[ yassine@gmail.com ](mailto:yassine@gmail.com)` `pass: pass1` Questo utente è iscritto al Corso di informatica “CS01” e per provare il funzionamento delle propedeuticità si può fare il test tra (Programmazione 1 e Programmazione 2) e (Base di dati 1, Base di dati 2). 
- **Docente**: `email:[ docente1@gmail.com ](mailto:docente1@gmail.com)` `pass: pass1` Questo docente è uno dei docenti di informatica (in realtà nel db per fari i test ne ho messi solo 2, ovvero lui e il docente con id 8) che governa gli insegnamenti di *Base di dati 1 e 2.* 
- **Segreteria**: `email:[ admin@gmail.com ](mailto:admin@gmail.com)` `pass: admin` Per ora l’unico segretario nel sistema. 
## 4. Possibili errori o malfunzionamenti 

Se si dovessero riscontrare malfunzionamenti front-end, provare a caricare dei cdn di bootstrap più recenti, inoltre nella pagina `inserimentoAppello.php` Sono stati caricari dei cdn per rendere funzionante il datePicker di bootstrap, se il datepicker non dovesse funzionare provare a inserire i cdn aggiornati del datepicker. 

# Documentazione tecnica

## 1. **Schema concettuale (ER) della base di dati** 


## 2. **Descrizione delle entità e delle scelte fatte** 

**utente:** è il padre coinvolto nella gerarchia (utente -> segreteria, docente, studente), questa entità rappresenta le informazioni essenziali che un utente deve avere (per essere identificato e per effettuare il login). 

**segreteria**: è una tipologia di utente che ha il compito di gestire e creare nuovi utenti di tipo docente e studente. 

**docente:** è una tipologia di utente che ha il compito di governare degli insegnamenti (fino a un massimo di 3) e per questi insegnamenti può stabilire un calendario con gli appelli degli esami (riferiti ai suoi insegnamenti), inoltre ha pure il compito di valutare gli studenti che hanno sostenuto un suo esame. 

**studente**: è una tipologia di utente che deve seguire esattamente un corso, in base al corso ha un insieme di esami svolgere per superare gli insegnamenti associati al corso. Ogni studente ha una carriera associata, in cui sono presenti i voti che lo studente ha ricevuto per ogni esame. 

**corso**: è un corso di laurea con una determinata durata, il corso è identificato univocamente nell’università tramite un codice. Inizialmente (in fase di creazione) può non avere nessun insegnamento associato, ma per essere valido deve poi essere associato a degli insegnamenti. 

**insegnamento**: è un insegnamento che è identificato tramite un codice e il codice del corso di laurea in cui l’insegnamento partecipa, questo perché ci possono essere insegnamenti con lo stesso codice ma che vengono proposti in corsi differenti (ad esempio ci può essere un insegnamento di programmazione per informatica che viene insegnato in un modo e un insegnamento di programmazione per ingegneri che viene insegnato in un altro modo). Ogni insegnamento deve avere un docente responsabile. 

**Esame**: è un esame associato ad un insegnamento, la scelta di avere un’entità esame è stata fatta perché all’interno un insegnamento ritenersi superato può essere composto da più esami (ad esempio una prova scritta e una prova di laboratorio). Quindi un esame è identificato univocamente tramite un codice ma due esami possono essere associati allo stesso insegnamento (identificato da codice e codice del corso di studi) 

**appello**: è un’entità che rappresenta un effettivo appello, un appello è una data associata ad un esame. È stata fatta la scelta di avere un’entità appello perché per ogni esame si possono stabilire più appelli fino a un massimo di tre (mi è sembrato un numero ragionevole per non creare problemi di accavallamento di date). Un appello non può essere nella stessa data di un altro appello se associato quest’ultimo è un esame dello stesso anno nello stesso corso di studi. 

**carriera**: è un’entità che rappresenta un voto di un esame appartenente ad uno studente; infatti, una carriera è identificata univocamente tramite un id e l’id dello studente che ha svolto l’esame. Nella carriera sono memorizzati tutti i voti, ma ai fini della carriera valida sono da considerare solo gli ultimi voti per ogni esame se questi sono sufficienti (voto >= 18). 

## 3. **Vincoli e requisiti della base di dati utente:**  
- L’email è unica all’interno del sistema 

**appello**:  

- si possono stabilire un massimo di tre appelli (che “esistono” contemporaneamente) per ogni esame. 
- Non si possono inserire date nel passato (ma possono esserci, quindi sarà compito del docente rimuovere gli appelli passati dopo aver valutato ogni studente che si è iscritto all’appello. Se questo non viene fatto il docente non può stabilire nuovi appelli.) 
- Un appello non può sovrapporsi con un altro appello di un esame dello stesso anno dello stesso corso di studi. 

**carriera:** 

- Il voto di ogni carriera deve essere un numero compreso tra 0 e 30 
- La data di verbalizzazione non può essere antecedente alla data dell’appello a cui uno studente si è iscritto (questo vincolo è garantito lato web) 
- Quando viene eliminato un utente, tutti i voti associati allo studente vengono trasferiti in uno storico. 

**corso:** 

- La durata del corso è un numero compreso tra 1 e 5 (non ho vincolato il numero a 3 o 2 per lasciare più libertà nella scelta della durata, ad esempio possiamo avere corsi triennali, magistrali, a ciclo unico (5 anni) ecc. 

**insegnamento:** 

- Non possono essere stabilite propedeuticità inverse (ovvero non si può avere una propedeuticità ad un insegnamento che è a sua volta propedeutico all’insegnamento per cui si vuole avere la propedeuticità) 
- L’anno in cui è previsto l’insegnamento deve essere compatibile con la durata del corso in cui partecipa 
- Le propedeuticità possono essere definite solo tra insegnamenti dello stesso corso di laurea 
- Non può essere definito un insegnamento propedeutico uguale all’insegnamento per cui si vuole definire la propedeuticità. 

**docente:** 

- Può governare al massimo tre insegnamenti 

**studente:** 

- Uno studente può iscriversi ad un esame solo se ha superato tutti gli insegnamenti propedeutici dell’insegnamento associato all’esame. 
- Uno studente può iscriversi solo agli esami associati ad un insegnamento previsto nel suo corso di studi. 
- L’anno di frequenza di uno studente deve essere aggiornato (tramite un’apposita funzione) all’inizio di ogni anno accademico, lo studente inizia al primo anno ma non ha un limite sugli anni di frequenza. 
- L’anno di iscrizione deve essere un anno <= all’anno attuale. In altre parole, non si può iscrivere uno studente nel futuro. 
- Quando viene eliminato un utente, le sue informazioni vengono trasferite in uno storico. 
2. **Schema logico (relazionale) della base di dati**  

## 4. Scelte di progettazione logica

- La gerarchia che aveva come padre utente e figli docente, studente, segreteria è stata risolta facendo “scendere” gli attributi del padre ai figli, quindi anche l’id, che sarà la chiave primaria di ogni figlio. Osservazione: nel database ho fatto in modo che un email sia univoca tra i vari utenti, questo per non avere problemi di autenticazione in pagine sbagliate e per identificare in alcune parti (come la gestione utenti in segreteria) un utente in modo univoco. 
- La relazione *frequenta* tra studente e corso viene risolta aggiungendo a studente una chiave esterna che identifica il corso di studi che lo studente frequenta. 
- Insegnamento è identificato univocamente all’interno di un corso di studi tramite la chiave primaria composta (codice, corso\_studi). La relazione *appartiene* viene risolta con la chiave esterna corso\_studi in insegnamento 
- La relazione ricorsiva *è\_propedeutico* molti a molti è stata risolta con una nuova tabella “propedeuticità” che contiene l’insegnamento per cui si vuole stabilire una propedeuticità, il corso di laurea (che sarà lo stesso per i due insegnamenti coinvolti nella propedeuticità) e propedeuticità che rappresenta l’insegnamento propedeutico. una propedeuticità viene identificata univocamente con una chiave composta che comprende tutti e tre gli attributi, (insegnamento,corso\_studi) sono una chiave esterna che identifica un insegnamento, (propedeuticità, corso\_studi) è una chiave esterna che identifica un altro insegnamento. 
- corso ha come chiave primaria il suo codice (che deve essere univoco). 
- esame ha come chiave primaria il suo codice univoco e la relazione *è\_composto* viene risolta aggiungendo a esame la chiave esterna (insegnamento, corso\_studi) che identifica un insegnamento. 
- La relazione ternaria *si\_iscrive* rappresentava il fatto che uno studente si può iscrivere ad un massimo di 3 appelli per ogni esame(questo perché per ogni esame si ha un massimo di 3 appelli), quindi questa relazione viene risolta creando una nuova tabella “iscrizione” che ha come chiave primaria un id e come chiavi esterne: id\_studente che identifica uno studente (che si iscrive ad un appello), esame che identifica un esame, appello che identifica un appello (associato ad un esame). 
- Appello ha come chiave primaria un id e dato che deve essere associato ad un esame ha come chiave esterna “esame” che identifica l’esame associato all’appello 
- Carriera ha come chiave primaria composta (id, studente), studente è anche chiave esterna e identifica uno studente, la scelta di avere una chiave composta è dettata dal fatto che uno studente può avere diversi voti, e una carriera ha senso di esistere solo se è associata ad uno studente. 
- Per supportare la funzionalità che prevede di salvare le informazioni associate ad uno studente e la sua carriera uno volta che quest’ultimo viene rimosso, è stato scelto di avere due nuove tabelle storico\_studenti e storico\_carriere 
- storico\_studenti ha come chiave primaria un id (che non è lo stesso id che aveva lo studente prima della rimozione) 
- storico\_docente ha come chiave primaria composta (id, studente) dove studente è una chiave 
