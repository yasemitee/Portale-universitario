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
