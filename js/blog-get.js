var jsondata
var jsdata
var urljs = []
var dia = []
var x = 0
var y = 0

jQuery(document).ready(function () {
  fetch('https://api.rss2json.com/v1/api.json?rss_url=https%3A%2F%2Fmedium.com%2Ffeed%2Fyvyreciclagem')
    .then(x => x.text())

    .then(function (y) {
      jsondata = y;
      jsdata = JSON.parse(jsondata)
     putonscreen();
    });
})

function putonscreen() {
 
  for (x = 0; x < 3; x = x + 1) {
    jQuery("#titulo" + x).text(jsdata.items[x].title);
    jQuery("#link" + x).attr('href', jsdata.items[x].link);
    jQuery("#link" + x + x).attr('href', jsdata.items[x].link);
    const texto = jsdata.items[x].description.split("<p>");
    console.log(texto[1]);
    jQuery("#texto" + x).html(texto[1]);
    jQuery("#img" + x).attr('src', jsdata.items[x].thumbnail);
    
   
  }
  x = 0
  y = 0
 
}

  
  
    
        


    