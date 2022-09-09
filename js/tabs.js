//ad ogni li all'interno elemento con id nav setta evento onclick: quando ci clicco chiama  taggletab passando l'id dell'elemento su cui ho cliccato e 
//e tutti i data attribute di quell'elemento prendendo quello che ha come nome target
document.querySelectorAll("#nav li").forEach(function(navEl) {
  navEl.onclick = function() { toggleTab(this.id, this.dataset.target); }
});
//prende menu navigazione su cui ho cliccato e il contenuto che deve mostrare per quella tab
//setta var con elelemnti nav li 
function toggleTab(selectedNav, targetId) {
  var navEls = document.querySelectorAll("#nav li");
//cicla questi elementi e per ognuno controlla
  navEls.forEach(function(navEl) {
    if (navEl.id == selectedNav) {
      //se è quello selezionato mette class is active
      navEl.classList.add("is-active");
    } else {
      //se quello clicccato contiiene già is active glielo toglie
      if (navEl.classList.contains("is-active")) {
        navEl.classList.remove("is-active");
      }
    }
  });
//altra var con tutti elelemtni con classe tab pane
  var tabs = document.querySelectorAll(".tab-pane");
//li clicla e controlla che il target id che gli è sttao passato sia quello che sta ciclando: se è uello lo mostra, sennò no
  tabs.forEach(function(tab) {
    if (tab.id == targetId) {
      tab.style.display = "block";
    } else {
      tab.style.display = "none";
    }
  });
}