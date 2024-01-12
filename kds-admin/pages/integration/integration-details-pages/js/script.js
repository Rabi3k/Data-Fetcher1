$(document).ready(function() {


    $("button.toogle-items, span.toogle-items").on("click", function() {
        var ulElm = $(this).attr("for-ul");

        var selct = $(this).hasClass("select-all");

        $("." + ulElm + ">.menu").each(function() {
            if (selct) {
                selectMenuItem(this);
            } else {
                unselectMenuItem(this);
            }
        });

        $(this).toggleClass("select-all unselect-all")
    });

    $("table").on("click", "li.menu, tr.menu", function() {
        if (!$(this).hasClass("is-invalid") && !$(this).hasClass("has-issue")) {
            selectMenuItem(this);
        } else {
            unselectMenuItem(this);
        }
    })

});

function selectMenuItem(element) {
    if ($(element).hasClass("is-valid")) {
        $(element).addClass("has-issue");
        $(element).removeClass("is-valid");
        $($(element).find("td.is-valid")).removeClass("is-valid").addClass("has-issue");
    } else if (!$(element).hasClass("is-invalid") && !$(element).hasClass("has-issue")) {
        $(element).addClass("is-invalid");
        $($(element).find("td.is-none")).removeClass("is-none").addClass("is-invalid");
        //$(this).removeClass("is-invalid");
    }

}

function unselectMenuItem(element) {
    if ($(element).hasClass("has-issue")) {
        $(element).removeClass("has-issue");
        $(element).addClass("is-valid");
        $($(element).find("td.has-issue")).removeClass("has-issue").addClass("is-valid");
    } else if ($(element).hasClass("is-invalid")) {
        //$(this).addClass("has-issue");
        $(element).removeClass("is-invalid");
        $($(element).find("td.is-invalid")).removeClass("is-invalid").addClass("is-none");
    }
}