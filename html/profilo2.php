<?php
require_once('../php/config.php');
//verifico che la variabile (id utente) sia settata: se torna true facciamo get, altrimenti false
$id_utente = (isset($_GET["id"]) ? $db->escape($_GET["id"]) : false);
$recuperautente = "SELECT * from utente_registrato WHERE id={$id_utente}";
//eseguo la query
$querydeseguo = $db->query($recuperautente);
#ottengo il risultato della query e sovrascrivo nella var  i dati del post che stampo in pagina
$datiutente = $db->fetchArray($querydeseguo);

$immagineProfilo = glob($percorsoimgutente . $datiutente['id'] . "_imgprofilo.*");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Il mio blog</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="../js/menu.js"></script>
    <script src="../js/jQuery.js">
    </script>
    <script src="../js/function.js">
    </script>
    <link id="blogfont" href="<?php //stampo il valore del link del font
                                echo $datipost['font'] ?>" rel="stylesheet">
</head>

<body style="background-color: #f7eac1;">
    <?php
    include("nav.php");
    ?>
    <section class="hero is-fullheight">
        <div class="container">
            <div class="has-text-centered">
                <h2 class="is-size-1 is-size-3-mobile has-text-weight-bold">Profilo utente</h2>
                <div class="media-center">
                    <?php if (count($immagineProfilo) > 0) {
                    ?> <img src="<?php echo $immagineProfilo[0];?>" class="circle2"  style="width:20%;" alt="Placeholder image" />
                    </figure>
                    <?php
                    } else { ?> <img src="../img/login.png" class="circle" style="width:10%;" alt="Placeholder image" />
                    <?php
                    }
                    ?>
                </div>
                <br>
                <div class="divider"></div>
                <div class="name">
                    <?php echo $datiutente['nome'] ?>
                    <?php echo $datiutente['cognome'] ?>
                </div>
                <div class="title">username:
                    <?php echo $datiutente['username'] ?>
                    <div class="title">email:
                        <?php echo $datiutente['email'] ?>
                    </div>
                </div>
            </div>
            <style>
                .divider {
                    background-color: #ca6060;
                    height: 1px;
                    width: 20%;
                    margin: auto;
                }

                .circle {
                    background-color: #fee7d3;
                    border-radius: 50%;
                    cursor: pointer;
                }

                .circle2 {
                    border-radius: 50%;
                    cursor: pointer;
                }

                .name {
                    color: #404245;
                    font-size: 36px;
                    font-weight: 600;
                    margin-top: 16px;
                    text-align: center;
                }

                .title {
                    color: #6e6e6e;
                    font-family: arial;
                    font-size: 14px;
                    font-style: italic;
                    margin-top: 4px;
                }
            </style>
            <br>
            <div class="column is-half is-offset-3">
                <div class="box">
                    <p class="subtitle is-5" style="text-align: center"> Blog creati dall'utente: </p>
                    <?php
                    //recupero i dati del blog e dell'utente che lo ha creato e l'username del coautore con una join con cui controllo che l'id del coautore sia uguale a quello dell'utente che lo ha creato (uso left join perché considera anche i record di blog anche se non c'è corrispondenza)
                    $recuperoblog = "SELECT b.*, u.username, a.tipo FROM blog as b LEFT JOIN utente_registrato as u ON b.coautore=u.id JOIN argomento as a ON b.argomento=a.codice WHERE b.id_utente='{$datiutente['id']}'";
                    //eseguo la query
                    $eseguiqueryblog = $db->query($recuperoblog);
                    #ottengo il risultato della query (tutti i blog dell'utente)
                    $blogutente = $db->fetchAll($eseguiqueryblog);
                    //per ogni elemento che stampa il foreach stampa ciò che c'è all'interno del corpo html
                    //ogni elemento di blogutente è assegnatoa riga blog, che sarà la singola riga della tabella blog con tutti i campi della tabella (select *) (la variabile finisce una volta finito il ciclo)
                    foreach ($blogutente as $rigablog) {
                    ?>
                        <div style="text-align: center;">
                            <?php
                            //tutto ciò che è dopo il ? è query string: una stringa che contiene delle variabili che si possono recuperare nella pagina di atterraggio (no dati sensibili)
                            echo "<a href='ricercablog.php?id_blog={$rigablog['codice']}'>{$rigablog['nome']} </a>";
                            ?>
                            <br>
                        <?php
                    }
                        ?>
                        </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>