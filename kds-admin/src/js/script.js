$("#passwordForm").on("submit", function (e) {

        var d = checkPassword();
        if (d < 5) {
                e.preventDefault();
                alert("submit form" + d);
        }
});
function showAlert(message, error = false) {
        if (error) {
                //show error message
                $("#div-alert-error").removeClass("visually-hidden");
                $("#div-alert-error").find(".alert-msg").text(message);
                setTimeout(function () {
                        $("#div-alert-error").addClass("visually-hidden");
                }, 5000);
        }
        else {
                $("#div-alert-success").removeClass("visually-hidden");
                $("#div-alert-success").find(".alert-msg").text(message);
                setTimeout(function () {
                        $("#div-alert-success").addClass("visually-hidden");
                }, 5000);
        }
}
$("input[type=password]").keyup(function () { checkPassword(); });
function checkPassword() {
        var score = 0;
        var ucase = new RegExp("[A-Z]+");
        var lcase = new RegExp("[a-z]+");
        var num = new RegExp("[0-9]+");

        if ($("#password1").val().length >= 8 && $("#password1").val().length <= 20) {
                $("#8char")
                        .replaceWith(feather.icons['check']
                                .toSvg({ 'id': '8char', "stroke": "green" }));
                score++;
        } else {
                $("#8char")
                        .replaceWith(feather.icons['x']
                                .toSvg({ 'id': '8char', "stroke": "red" }));
        }

        if (ucase.test($("#password1").val())) {
                $("#ucase")
                        .replaceWith(feather.icons['check']
                                .toSvg({ 'id': 'ucase', "stroke": "green" }));
                score++;
        } else {
                $("#ucase")
                        .replaceWith(feather.icons['x']
                                .toSvg({ 'id': 'ucase', "stroke": "red" }));
        }

        if (lcase.test($("#password1").val())) {
                $("#lcase")
                        .replaceWith(feather.icons['check']
                                .toSvg({ 'id': 'lcase', "stroke": "green" }));
                score++;
        } else {
                $("#lcase")
                        .replaceWith(feather.icons['x']
                                .toSvg({ 'id': 'lcase', "stroke": "red" }));
        }

        if (num.test($("#password1").val())) {
                $("#num")
                        .replaceWith(feather.icons['check']
                                .toSvg({ 'id': 'num', "stroke": "green" }));
                score++;
        } else {
                $("#num")
                        .replaceWith(feather.icons['x']
                                .toSvg({ 'id': 'num', "stroke": "red" }));
        }

        if ($("#password1").val() == $("#password2").val()) {
                $("#pwmatch")
                        .replaceWith(feather.icons['check']
                                .toSvg({ 'id': 'pwmatch', "stroke": "green" }));
                score++;
        } else {
                $("#pwmatch")
                        .replaceWith(feather.icons['x']
                                .toSvg({ 'id': 'pwmatch', "stroke": "red" }));
        }
        return score;
}
