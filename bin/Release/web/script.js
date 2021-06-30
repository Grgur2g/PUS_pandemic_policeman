var step = 4
var trenutne_slike = 5
var nema_slike = 0
var brojac_neslika = 0 

function openCam(evt, section) {
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(section).style.display = "block";
  evt.currentTarget.className += " active";

}

function nisam(){
  document.getElementById("defaultOpen").click();
}

function loadMore(){
if(nema_slike == 1){
  return;
}
document.getElementById("slikice").innerHTML = "";
trenutne_slike += step;
this.load();

}

function loadLess(){
  if(trenutne_slike <= step+1){
    return;
  }
  document.getElementById("slikice").innerHTML = "";
  trenutne_slike -= step;
  this.load();
  nema_slike = 0;
}

function load(){
  var i = trenutne_slike - step;
  for (; i < trenutne_slike; i++){
      var elem = document.createElement("img");
      elem.setAttribute("src", "slike/" + i +".jpg");
      elem.setAttribute("onError", "removeMe(this)");
      elem.setAttribute("height", "240");
      elem.setAttribute("width", "320");
      elem.setAttribute("id", i + "slika");
      elem.setAttribute("alt", "Nema dalje");
      elem.setAttribute("style", "margin:10px; border: solid #f1f1f1");
      document.getElementById("slikice").appendChild(elem);
      }  
      brojac_neslika = 0; 
    }

function removeMe(element){
  element.remove();
  nema_slike = 1;
  brojac_neslika += 1;
  
  //console.log(brojac_neslika);
  if (brojac_neslika == 4 && trenutne_slike >= 9) { // 9 is what next iteration would be && trenutne_slike >= 9
    brojac_neslika = 0;
    document.getElementById("slikice").innerHTML = '<p>There are no more criminals, click "<" to load back </p>';
  }

  else if (brojac_neslika == 4 && trenutne_slike == 5) { // 9 is what next iteration would be && trenutne_slike >= 9
    brojac_neslika = 0;
    document.getElementById("slikice").innerHTML = '<p>There are no more criminals, wait for new people to not wear a mask</p>';
  }
}
document.getElementById("defaultOpen").click();

