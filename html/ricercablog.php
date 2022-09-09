<?php
require_once('../php/config.php');
//recupero la variabile usata nella query string in area riservata
$id_blog = $db->escape($_GET["id_blog"]);
//se l'utente è loggato, recupero i suoi dati che ho in sessione, altrimenti inizializzo un array vuoto
$datiutente = (isset($_SESSION['logged_user'])) ? $_SESSION['logged_user'] : array();
// controllo se è settato elimina post (ho cliccato su una x), se l'ho fatto elimino dalla tabella post il post con quell'id
if (isset($_POST['elimina_post'])) {
    $cancella_post = "DELETE FROM post_esempio WHERE codicepost={$_POST['elimina_post']} ";
    //eseguo la query
    $db->query($cancella_post);
}
//con una query recupero i dati del blog e faccio una join tra blog e argomento e tra blog e utente registrato (come risultato avrò tutti i campi della tab blog e a seguire tutti i campi della tab argomento e tutti i campi della tabella utente registrato) e tra blog e grafica per ripredndere i dati della grafica selezionata 
$datiblogsql = "SELECT b.*,a.tipo,u.username, g.*, b.codice as codice_blog, b.nome as nome_blog FROM blog as b JOIN argomento as a ON b.argomento=a.codice JOIN utente_registrato as u ON b.id_utente=u.id LEFT JOIN grafica as g ON g.codice=b.grafica WHERE b.codice={$id_blog}";
#eseguo la query
$querydatiblog = $db->query($datiblogsql);
#ottengo il risultato della query e sovrascrivo nella var dati blog i dati del blog che stampo in pagina
$datiblog = $db->fetchArray($querydatiblog);
//inizializzo una var che sarà true o false e che mi permette di controllare che l'utente sia loggato, che l'utente sia o l'autore del post o il coautore del blog a cui appartiene il post 
$controllo_utente = (!empty($datiutente) && ($datiutente['id'] == $datiblog['id_utente'] || $datiutente['id'] == $datiblog['coautore']));
//recupero i post del blog e mostro gli ultimi 3 pubblicati
$postdelblog = "SELECT * FROM post_esempio WHERE codice_blog={$id_blog} ORDER BY data desc LIMIT 3 ";
#eseguo la query
$querypostblog = $db->query($postdelblog);
#ottengo il risultato della query e sovrascrivo nella var dati post i dati del post relativo al blog che stampo in pagina (fetchall perché ho più post per il blog (datipost è un array 3 elementi, di cui ognuno a sua volta è un array))
$datipost = $db->fetchAll($querypostblog);
//uso la funzione php glob per recuperare le immagini relative al post che stampo e le cerco nella cartella images e con il nome id post seguito immaginecopertina (perché voglio stampare solo l'immagine di copertina) seguita da qualsiasi carattere(* per l'estensione)
$immaginecopertinablog = glob($percorsoimmagineblog . $datiblog['codice_blog'] . "_immagine_copertina.*");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Il mio blog</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="../js/jQuery.js">
    </script>
    <script src="../js/function.js">
    </script>
    <script src="../js/menu.js"></script>
    <?php if (!empty($datiblog['font'])) { ?>
        <link id="blogfont" href="<?php //stampo il valore del link del font
                                    echo $datiblog['font'] ?>" rel="stylesheet">
    <?php } else {
    ?>
        <style>
            .hero.is-dark .subtitle {
                color: #000000;
            }

            .hero.is-dark .title {
                color: #000000;
            }
        </style>
    <?php
    }
    ?>
</head>

<body style="background-color: #f7eac1">
    <?php
    include("nav.php");
    ?>
    <!-- END NAV -->
    <div id="grafica">
        <section class="hero is-medium is-dark">
            <div class="hero-body" style="<?php
                                            //controllo se c'è elemento immagine e stampo l'immagine di background dell'elemento
                                            if (count($immaginecopertinablog) > 0) {
                                            ?> background-image:url('<?php echo $immaginecopertinablog[0] ?>');background-repeat: no-repeat;background-size:cover;background-position:center; <?php } ?> ">
                <div class="container has-text-centered">
                    <!-- stampo il campo nome di datiblog per avere il titolo -->
                    <h3 class="title" style="font-family:'<?php echo $datiblog['nome_font'] ?>';font-size:<?php echo $datiblog['dimensione_font'] ?>px !important;color:<?php echo $datiblog['colore'] ?> !important;"></h3>
                    <div class="column is-3 is-offset-4" style="margin:auto;">
                        <div class="box" style="position: relative;margin:auto;box-shadow: 16px 16px 50px 15px grey;">
                            <h2 class="subtitle is-5" style="font-family:<?php echo $datiblog['nome_font'] ?>;font-size:<?php echo $datiblog['dimensione_font'] ?>px !important;color:<?php echo $datiblog['colore'] ?> !important;"><?php echo $datiblog['nome_blog']; ?><br>Il Blog di <?php echo $datiblog['username']; ?>, <br><?php echo $datiblog['descrizione']; ?></h2>
                            <?php
                            //se l'utente è loggato ed è o il proprietario del blog o coautore mostro il bottone di modifica
                            if ($controllo_utente) {
                            ?> <a class="button is-small is-dark is-outlined" href="blog.php?id_blog=<?php echo $id_blog ?>">Modifica Blog</a>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="container has-text-centered">
            <form action="" method="post" enctype="multipart/form-data">
                <p class="title is-4" style="padding:10px; ">POST DEL BLOG</p>
            </form>
            <div class="container">
                <!-- START ARTICLE FEED -->
                <?php foreach ($datipost as $post) {  //con un ciclo stampo il titolo degli ultimi 3 post inseriti
                    //uso la funzione php glob per recuperare le immagini relative al post che stampo e le cerco nella cartella images e con il nome id post seguito immaginecopertina (perché voglio stampare solo l'immagine di copertina) seguita da qualsiasi carattere(* per l'estensione)
                    $immaginipost = glob($percorsoimmagine . $post['codicepost'] . "_immagine_copertina.*");

                ?>
                    <section class="articles">
                        <div class="column is-5 is-offset-3" style="margin:auto;">
                            <!-- START ARTICLE -->
                            <div id="f" class="card article" style="padding-bottom:10px;<?php //controllo se c'è elemento immagine e stampo l'immagine di background dell'elemento
                                                                                        if (count($immaginipost) > 0) { ?> background-image:url('<?php echo $immaginipost[0] ?>');background-repeat: no-repeat;background-size:cover;background-position:center; ;<?php } ?>">
                                <div class="card-content">
                                    <style>
                                        #f:hover {
                                            background-blend-mode: luminosity;
                                        }
                                    </style>
                                    <div class="media"> </div>
                                    <div class="media-content has-text-centered">
                                        <p class="title article-title">
                                            <!-- rendo titolo cliccabile che rimanda al dettaglio del post con quell'id-->
                                            <a id="title" style="color:black; { font-size: 20px; display: block;}" href="pag.php?id_post=<?php echo $post['codicepost']; ?>">
                                                <?php echo $post['titolo']; ?>
                                            </a>
                                            <style>
                                                #title:hover {
                                                    font-size: 40px;
                                                }
                                            </style>
                                        </p>
                                        <div id="b" class="tags has-addons level-item"> <span class="tag is-rounded is-warning">@<?php echo $datiblog['username']; ?></span> <span class="tag is-rounded"><?php echo date('d/m/Y H:i', strtotime($datiblog['data_creazione'])); ?></span> </div>
                                    </div>
                                </div>
                                <br>
                                <div class="button is-light"> <a href="pag.php?id_post=<?php echo $post['codicepost'] ?>">Leggi il post</a>
                                </div>
                                  <div id="b">
                                    <form action="" method="POST">
                                        <?php if (!empty($datiutente) && $post['codice_autore'] == $datiutente['id']) { ?>
                                            <button style="border-color:red;"class="button is-light" name="elimina_post" value="<?php echo $post['codicepost'] ?>">Elimina post</button>

                                        <?php }
                                        ?>
                                        <style>
                                            a {
                                                color: black;
                                            }
                                            #b{
                                                display: inline-block;
                                            }
                                        </style>
                               
                                <style>
                                    .button {
                                        margin-left: auto;
                                    }
                                </style>
                                </form>
                            </div>
                            </div>
                        </div>
                    <?php }
                if (empty($datipost)) {
                    ?>
                         <div class="column is-half is-offset-3">
                    <div class="box">
                        <p style="text-align:center;">Non ci sono ancora post per il tuo blog. <br> Aggiunge subito uno!</p>
                    </div>
                </div>
                       
                        <div style="text-align:center; margin-top:5%; padding-bottom: 50%;"><a class='button is-light is-normal'style="border-style: solid 1px; border-color: black;margin:auto;" href='crea.php?id_blog=<?php echo $id_blog ?>'>Crea nuovo post </a></div>
                    <?php
                }
                    ?>
                    </section>
            </div>
        </div>
    </div>
    <!-- END ARTICLE -->
</body>

</html>