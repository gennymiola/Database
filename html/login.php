<?php
require_once('../php/config.php');
//inizializzo var errori (var array)
$errori = [];
#quando clicco su bottone login
if (count($_POST) > 0) {
  $username = $db->escape($_POST['username']);
  $password = $db->escape($_POST['password']);
  $sql = "SELECT * FROM utente_registrato WHERE username='{$username}' AND password=MD5('{$password}')";
  #ritorna id numerico e lo salva nella var $queryutente
  $queryUtente = $db->query($sql);
  #ritorna tutti i campi della select come risultato
  $datiUtente = $db->fetchArray($queryUtente);
  #verifico che ho trovato l'utente con id e pssw inseriti
  if (!empty($datiUtente)) {
    #salvo i dati dell'utente
    $_SESSION['logged_user'] = $datiUtente;
    //chiamo la funzione nel file config che calcola il livello dell'utente e gli passo l'id dell'utente loggato
    calcolalivelloutente($datiUtente['id']);
    #redirect alla pagina index.php
    header("location: index.php");
  } else {
    // ERRORE UTENTE NON TROVATO
    $errori[] = "utente non trovato. Controlla username e password";
  }
  // svuota la variabile post
  unset($_POST);
}
?>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
  <!-- Bulma Version 0.6.0 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.0/css/bulma.min.css" integrity="sha256-HEtF7HLJZSC3Le1HcsWbz1hDYFPZCqDhZa9QsCgVUdw=" crossorigin="anonymous" />
  <script src="../js/jQuery.js">
  </script>
  <script src="../js/function.js">
  </script>
  <link rel="stylesheet" type="text/css" href="../css/login.css">
</head>

<body>
  <section class="hero is-fullheight">
    <style>
      .hero {
        background-color: black;
      }
    </style>
    <div class="hero-body">
      <div class="container has-text-centered">
        <div class="column is-4 is-offset-4">
          <h3 class="title" style="color:#f7eac1;">Accedi</h3>
          <p class="subtitle has-text-white">Effettua l'accesso inserendo le tue credenziali</p>
          <div class="box">
            <figure class="avatar"> <i class="material-icons md-48 " style="font-size: 5.5rem;">person</i>
              <br>
            </figure>
            <!-- non passo parametri ad action, i dati della login vengono controllati dalla pagina login stessa -->
            <form action="" method="POST">
              <div class="field">
                <div class="control">
                  <input class="input is-warning is-medium has-text-centered" maxlength="20" name="username" type="text" placeholder="Username" required autofocus="" />
                </div>
              </div>
              <div class="field">
                <div class="control">
                  <input class="input is-warning is-medium has-text-centered" name="password" maxlength="20" type="password" required placeholder=" Password" />
                </div>
              </div>
              <button class="button is-dark is-outlined is-medium is-fullheight">Login</button>
              <br>
              <br>
              <p>Non hai ancora un account? <a href="registrazione.php" style="color:black;"><strong>REGISTRATI</strong></a></p>
            </form>
          </div>
          <p class="has-text-grey"> <a href="index.php" style="color:#f7eac1;">Home</a> </p>
        </div>
      </div>
    </div>
    <?php //gestione errore
    //inizializzo var per visualizzare il pop  di errore quando c'è un errore
    $popupvisibile = "";
    // se l'array errori non è vuoto (c'è almeno un errore) faccio comparire pop up
    if (!empty($errori)) {
      $popupvisibile = " is-active";
    }
    ?>
    <div class="modal<?php echo $popupvisibile; ?>" id="e">
      <div class="modal-background"></div>
      <div class="modal-content">
        <article class="message is-dark">
          <div class="message-header">
            <p>Errore</p>
            <button class="delete" aria-label="delete" id="errore" onclick="$('#e').removeClass('is-active');"></button>
          </div>
          <div class="message-body">
            <p class="content has-text-justified">
              <?php echo implode("<br/>", $errori); //stampo, con il metodo implode che trasforma gli elementi dell'array errori in un'unica stringa separati dal separatore <Br>, l'array

              ?>
            </p>
          </div>
        </article>
      </div>
    </div>
    <script async type="text/javascript" src="../js/bulma.js"></script>
  </section>
</body>

</html>