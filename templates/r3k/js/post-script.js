
$(document).ready(function () {
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
});
var selectedSlide = null;
$('.cards-wrapper').on('click', function () {
    //alert($(this).attr('tag'))
    if (selectedSlide === $(this).attr('tag')) {
        selectedSlide = null;
    }
    else {
        selectedSlide = $(this).attr('tag');
    }
    if (selectedSlide === null) {
        alert('bottombar is not showed => card unselected');
    } else {
        alert('bottombar is showed => card #'+selectedSlide);
    }
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