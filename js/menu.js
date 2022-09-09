//quando la pagina è caricata
document.addEventListener('DOMContentLoaded', () => {

  // Get all "navbar-burger" elements
  const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

  // Check if there are any navbar burgers
  if ($navbarBurgers.length > 0) {
//se è maggiore di 0 per ognuno aggiunge l'evento click
    // Add a click event on each of them
    $navbarBurgers.forEach( el => {
      el.addEventListener('click', () => {
//quando è cliccato navburger recupera il suo dataattribute con nome target e il suo valore lo cerca come id in tutta la pagina
        // Get the target from the "data-target" attribute
        const target = el.dataset.target;
        const $target = document.getElementById(target);
//recupera lista classi dell'elemento e gli mette o toglie la classe is active e lo stesso fa per gli elementi nel menù
        // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
        el.classList.toggle('is-active');
        $target.classList.toggle('is-active');

      });
    });
  }

});