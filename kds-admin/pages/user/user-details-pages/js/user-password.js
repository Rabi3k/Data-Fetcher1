$(document).ready(function () {
    $("#passwordForm").validate({
        rules: {
            password1: {
                required: true,
                pwcheck: true,
                minlength: 8
            },
            password2: {
                required: true,
                equalTo: "#password1"
            },
        },
        messages: {
            password1: {
                required: "Please provide a password",
                minlength: "Your password must be at least 8 characters long"
            },
            email: "Please enter a valid email address"
        },

    });
    $.validator.addMethod("pwcheck", function (value, element) {
        let password = value;
        if (!(/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W)(.{8,20}$)/.test(password))) {
            return false;
        }
        return true;
    }, function (value, element) {
        let password = $(element).val();
        if (!(/^(.{8,20}$)/.test(password))) {
            return 'Password must be between 8 to 20 characters long.';
        } else if (!(/^(?=.*[A-Z])/.test(password))) {
            return 'Password must contain at least one uppercase.';
        } else if (!(/^(?=.*[a-z])/.test(password))) {
            return 'Password must contain at least one lowercase.';
        } else if (!(/^(?=.*[0-9])/.test(password))) {
            return 'Password must contain at least one digit.';
        } else if (!(/^(?=.*\W)/.test(password))) {
            return "Password must contain special characters.";
        }
        return false;
    });


    $("#btn-resetPass").click(function () {
        var settings = {
            "url": "/sessionservices/users.php?q=send-rest-pasword",
            "method": "PUT",
            "timeout": 0,
            "headers": {
                "Content-Type": "application/json"
            },
            "data": JSON.stringify({
                "userId": $userId
            }),
            "success": function (data) {
                showAlert("Email sent successfully");
            }
        };

        $.ajax(settings).done(function (response) {
            console.log(response);

        });
    });
    $("#btn-save-passkey").click(function()
    {
        let $data = JSON.stringify({
            "userId": $userId,
            "password": $("#passkey").val()
        });
        showAlert($data);
    });
    $("#btn-change-1").click(function (e) {
        if ($("#passwordForm").valid()) {
            $("#btn-change-1").find(".spinner").removeClass("visually-hidden");
            $(this).attr("disabled", "true");
            var settings = {
                "url": "/sessionservices/users.php?q=change-password",
                "method": "PUT",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify({
                    "userId": $userId,
                    "password": $("#password1").val()
                }),
            };

            $.ajax(settings).done(function (response) {
                $("#btn-change-1").removeAttr("disabled")
                $("#btn-change-1").find(".spinner").addClass("visually-hidden");
                $("#password1").val('');
                $("#password2").val('');
                showAlert("Password changed successfully");
                console.log(response);
            });
        }
    });
});