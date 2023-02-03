var ActiveOrderIds = [];

function PrintElem(id) {
  var mywindow = window.open('/order/' + id, 'PRINT');
  mywindow.document.close(); // necessary for IE >= 10
  mywindow.focus(); // necessary for IE >= 10


  setTimeout(() => {
    mywindow.print();
    //mywindow.close();
  }, "1000");
  return true;
}

function getDateByTimezone(date, timezone, culture) {
  return date.toLocaleString(culture, { timeZone: timezone, dateStyle: "full" });
}
function getTimeByTimezone(date, timezone, culture) {
  return date.toLocaleString(culture, { timeZone: timezone, timeStyle: "long" });
}
function getDateTimeByTimezone(date, timezone, culture) {
  return date.toLocaleString(culture, { timeZone: timezone, dateStyle: "full", timeStyle: "long" });
}
function firstLetterCapitalize(_string) {
  return _string.charAt(0).toUpperCase() + _string.slice(1);
}
function updateTime() {
  var now = new Date();
  //weekday:"long", year:"numeric", month:"short", day:"numeric",
  const CopenhagenDate = getDateByTimezone(now, 'Europe/Copenhagen', 'da-DK');
  //(new Date()).toLocaleString('da-DK',{timeZone:'Europe/Copenhagen',dateStyle:"full"});
  const CopenhagenTime = getTimeByTimezone(now, 'Europe/Copenhagen', 'da-DK');
  //(new Date()).toLocaleString('da-DK',{timeZone:'Europe/Copenhagen',timeStyle:"long"});

  $('.time-text').html(firstLetterCapitalize(CopenhagenDate) + "<br/>" + CopenhagenTime);
  var nowD = new Date(getDateTimeByTimezone(now, 'Europe/Copenhagen', 'da-DK'));
  var later = new Date()
  later.setMinutes(now.getMinutes() + 10);

  $("input[name='OrderDate']").each(function () {
    var oDate = new Date($(this).val());
    var parent = $(this).parents('.card');
    var parentHeader = $(this).parents('.card-header');
    var timeRTxt = $(parent).find('.time-remaining');
    var timeR = (oDate.getTime() - now.getTime()) / 1000;
    if (now > oDate) { timeR = (now.getTime() - oDate.getTime()) / 1000; }

    const hours = Math.floor(timeR / 3600);
    timeR = timeR - hours * 3600;
    const minutes = Math.floor(timeR / 60);
    const seconds = Math.floor(timeR - minutes * 60);

    var t = "";
    if (hours > 0) {
      t += hours + "h";
    }
    if (minutes > 0) {
      t += minutes + "m";
    }
    if (seconds > 0) {
      t += seconds + "s";
    }

    if (now > oDate) {
      $(parentHeader).toggleClass('bg-danger text-white');
      $(timeRTxt).text("+" + t);
    }
    else if (later > oDate) {
      $(parentHeader).toggleClass('bg-warning');
      $(timeRTxt).text(t);
    }
    else {
      $(timeRTxt).text(t);
    }

  })
}
$(function () {
  setInterval(updateTime, 1000);
});
function getdatestr(date, seperator) {
  const year = date.getFullYear();

  const month = String(date.getMonth() + 1).padStart(2, '0');

  const day = String(date.getDate()).padStart(2, '0');

  const joined = [day, month, year].join(seperator);
  return joined;
}
function createElementFromHTML(htmlString) {
  var div = document.createElement('div');
  div.innerHTML = htmlString.trim();

  // Change this to div.childNodes to support multiple top-level nodes.
  return div.firstChild;
}
function myFunction() {
  //console.log('Executed!');
  var now = new Date();




  var s = getdatestr(now, '');
  //console.log(userSecrets);
  var secrets = JSON.stringify(userSecrets);

  $.getJSON('../api/orders/' + s + '-' + s + '?secrets=' + secrets).then(r => {
    let toRemove = ActiveOrderIds.filter(x => !r.includes(x));
    let toAdd = r.filter(x => !ActiveOrderIds.includes(x));
    if (toRemove && toRemove.length > 0) {
      
      toRemove.forEach(x => { 
        let index = ActiveOrderIds.findIndex(object => {
          return object === x;
        });
        swiperCar.removeSlide(index);
        //$('#accordion_' + x).remove(); 
      });
    }

    if (toAdd && toAdd.length > 0) {
      toAdd.forEach(x => {
        let index = r.findIndex(object => {
          return object === x;
        });

        $.get('/card/' + x, function (resp) {
          let htmlObject = createElementFromHTML(resp);
          switch (index) {
            case 0:
              swiperCar.prependSlide(htmlObject);
              //$('#orderCards').prepend(resp);
              break;
            case r.length - 1:
              swiperCar.appendSlide( htmlObject);
              //$('#orderCards').append(resp);
              break;
            default:
              swiperCar.addSlide(index, htmlObject);
              break;
          }
        });
        // if (index === 0) { $.get('/card/' + x, function (resp) {  $('#orderCards').prepend(resp); });}
        // else if (index === r.length - 1) {
        //   $.get('/card/' + x, function (resp) { $('#orderCards').append(resp) });
        // }
        // else {
        //   var idx = r[index + 1];
        //   $.get('/card/' + x, function (resp) { $('#accordion_' + idx).before(resp) });
        // }

        playSound();
      });
    }
    ActiveOrderIds = r;
    // if (carouselWidth) {
    //   carouselWidth = $(".carousel-inner")[0].scrollWidth;
    //   cardWidth = $(".carousel-item").width();
    // }
  });

}