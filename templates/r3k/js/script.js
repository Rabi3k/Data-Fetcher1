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

function myFunction() {
  //console.log('Executed!');
  var now = new Date();
  var later = new Date()
  later.setMinutes(now.getMinutes() + 10);


  $("input[name='OrderDate']").each(function () {
    var oDate = new Date($(this).val());
    if (now > oDate) {
      $($(this).parent()).toggleClass('bg-danger text-white');
    }
    else if (later > oDate) {
      $($(this).parent()).toggleClass('bg-warning');
    }

  })
  var s = getdatestr(now, '');
  //console.log(userSecrets);
  var secrets = JSON.stringify(userSecrets);

  $.getJSON('../api/orders/' + s + '-' + s + '?secrets=' + secrets).then(r => {
    let toRemove = ActiveOrderIds.filter(x => !r.includes(x));
    let toAdd = r.filter(x => !ActiveOrderIds.includes(x));
    if (toRemove && toRemove.length > 0) {
      toRemove.forEach(x => { $('#accordion_' + x).remove(); });
    }

    if (toAdd && toAdd.length > 0) {
      toAdd.forEach(x => {
        const index = r.findIndex(object => {
          return object === x;
        });

        $.get('/card/' + x, function (resp) {
          switch (index) {
            case 0:
              $('#orderCards').prepend(resp);
              break;
            case r.length - 1:
              $('#orderCards').append(resp);
              break;
            default:
              var idx = r[index + 1];
              $('#accordion_' + idx).before(resp);
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
    if(carouselWidth)
    {
      carouselWidth = $(".carousel-inner")[0].scrollWidth;
      cardWidth = $(".carousel-item").width();
    }
  });

}