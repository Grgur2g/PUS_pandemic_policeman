var step = 4
var page = 0

var pictures;
function loadImages() {
      $.ajax({
        url:"GetImagesFromFolder.php", //the page containing php script
        type: "POST", //request type
        success:function(result){
         pictures = JSON.parse(result);
         load();
       }
     });
 }


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

function loadMore(){
  if(page >= Math.ceil(pictures.length / 4) - 1){
    return;
  }
  page += 1;
  this.load();
}

function loadLast(){
  page = Math.ceil(pictures.length / 4) - 1;
  this.load();
}

function loadFirst(){
  page = 0;
  this.load();
}

function loadLess(){
  if(page == 0){
    return;
  }
  page -= 1;
  this.load();
}


function load(){
  if(pictures.length > 0){
    document.getElementById("page").innerHTML = page + 1;
    //Reload slikice div
    document.getElementById("slikice").innerHTML = "";
    for (var i = 0; i < step; i++){
        if(page*step+i >= pictures.length){
          break;
        }
      var elem = document.createElement("img");
      elem.setAttribute("src", "slike/" + pictures[page*step+i] + "?x=" + Date.now());
      elem.setAttribute("height", "240");
      elem.setAttribute("width", "320");
      elem.setAttribute("id", pictures[page*step+i]);
      elem.setAttribute("alt", "Kriminalac");
      elem.setAttribute("style", "margin:10px; border: solid black");
      document.getElementById("slikice").appendChild(elem);
    }
  }
}

document.getElementById("defaultOpen").click();
