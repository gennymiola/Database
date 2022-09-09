<nav class="navbar is-black" role="navigation" aria-label="main navigation">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item" href="index.php"> <img src="../img/home.png" alt="Logo" width="35" /> </a> <span class="navbar-burger burger is-right" data-target="navbarMenuHeroB">
                <span></span> <span></span> <span></span> </span>
        </div>
        <div id="navbarMenuHeroB" class="navbar-menu" style="background-color: black;">
            <div class="navbar-start"> <a class="navbar-item" href="index.php">
                    Home
                </a> </div>
            <div class="navbar-end">
                <?php
                //controllo che l'utente sia loggato (se vuoto no loggato), se invece è loggato scompaiono i bottoni accedi e registrati (non esegue parte del codice)
                if (empty($_SESSION['logged_user'])) { //controlla che l'indice dentro la variabile session sia o vuota o valore null

                ?>
                    <a class="navbar-item">
                        <a class="button is-light is-outlined is-normal is-fullheight" href="login.php"> <span class="icon is-medium">
                                <img src="../img/profilo.png" alt="Logo" href="index.html">
                        </a>
                        </span> </a>
                    <a class="navbar-item">
                        <a class="button is-light is-outlined is-normal is-fullheight" href="registrazione.php"> <span>Registrati</span> </a>
                    </a>
                <?php // se l'utente è loggato faccio comparire tasto area riservata    
                } else {
                ?> <span class="navbar-item">
                        <a class="button is-light is-outlined is-normal is-fullheight" href="areariservata.php">
                            <span>Area Riservata</span> </a> <a href="javascript:void(0)" onclick="logout()" name="logout" class="button is-light is-outlined is-normal is-fullheight"> logout </a>
                        <style>
                            .button {
                                margin-right: 3%;
                            }
                        </style>
                    </span>
                <?php
                }
                ?>
                </a>
            </div>
        </div>
    </div>
</nav>