
$(document).ready(function () {
  $('body').on('click','.cards-wrapper', function () {
    //alert($(this).attr('tag'))
    if (selectedSlide === $(this).attr('tag')) {
        selectedSlide = null;
    }
    else {
        selectedSlide = $(this).attr('tag');
    }
    if (selectedSlide === null) {
        closeBottomBar();
    } else {
        openBottomBar();
    }
});
});
$(".btn-branch").click(function () {
  var tag = $(this).attr("tag");
  if ($(this).attr("aria-pressed") === 'false') {
      //show cards
      $("div[tag=" + tag + "]").show()
  } else {
      //hide cards
      $("div[tag=" + tag + "]").hide()
  }
});
$(".btn-type").click(function () {
  var tag = $(this).attr("title");
  if ($(this).attr("aria-pressed") === 'false') {
      //show cards
      $("div[order-type=" + tag + "]").show()
  } else {
      //hide cards
      $("div[order-type=" + tag + "]").hide()
  }
});
var selectedSlide = null;
$('#print-btn').on("click",function(e){
    PrintElem(selectedSlide,e);
});

function openBottomBar()
{
    $('.bottom-bar').css("bottom",$("footer").height()+$("header").height());
    $('.bottom-bar').collapse('show')

    //$('#main-swiper').addClass('h-80');
    //$('#main-swiper').removeClass('h-90');
}
function closeBottomBar()
{
    $('.bottom-bar').collapse('hide')
    //$('#main-swiper').addClass('h-90');
    //$('#main-swiper').removeClass('h-80');
}

var navStatus = false;

function toogleNav() {
  if (navStatus === false) {
    openNav();
  } else {
    closeNav();
  }
}

function openNav() {
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
  navStatus = true;

}

function closeNav() {
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft = "0";
  navStatus = false;

}
var swiperCar = new Swiper('.swiper', {
    pagination: {
        el: '.swiper-pagination',
    },
    slidesPerGroup: 1,
    spaceBetween: 10,
    slidesPerView: 1,
    slidesPerColumn: 1,
    cssMode: false,


    // Breakpoints
   breakpoints: {
    // when window width is >= 320px
    
    850: {
      slidesPerView: 2,
      spaceBetween: 10
    },
    // when window width is >= 480px
    1296: {
      slidesPerView: 3,
      spaceBetween: 10
    },
    // when window width is >= 640px
    1680: {
      slidesPerView: 4,
      spaceBetween: 10
    }
   },


    // To support iOS's swipe-to-go-back gesture (when being used in-app).
    edgeSwipeDetection: false,
    edgeSwipeThreshold: 20,

    effect: 'slide',
    // Unique Navigation Elements
    uniqueNavElements: true,

    // Resistance
    resistance: true,
    resistanceRatio: 0.85,

     // Round length
    roundLengths:false,

    // Options for touch events
    touchRatio: 1,
    touchAngle: 45,
    simulateTouch: true,
    shortSwipes: true,
    longSwipes: true,
    longSwipesRatio: 0.5,
    longSwipesMs: 300,
    followFinger: true,
    allowTouchMove: true,
    threshold: 0,
    touchMoveStopPropagation: false,
    touchStartPreventDefault: true,
    touchStartForcePreventDefault: false,
    touchReleaseOnEdges: false,

    // Use ResizeObserver (if supported by browser) on swiper container to detect container resize (instead of watching for window resize)
resizeObserver:true,

});

// var carouselWidth = $(".carousel-inner")[0].scrollWidth;
// var cardWidth = $(".carousel-item").width();
// var scrollPosition = 0;
// $("#carouselExampleInterval .btn-next").on("click", function () {
//     if (scrollPosition < carouselWidth - cardWidth * 4) {
//         scrollPosition += cardWidth;
//         $("#carouselExampleInterval .carousel-inner").animate({
//             scrollLeft: scrollPosition
//         },
//             600
//         );
//     }
// });
// $("#carouselExampleInterval .btn-prev").on("click", function () {
//     if (scrollPosition > 0) {
//         scrollPosition -= cardWidth;
//         $("#carouselExampleInterval .carousel-inner").animate({
//             scrollLeft: scrollPosition
//         },
//             600
//         );
//     }
// });