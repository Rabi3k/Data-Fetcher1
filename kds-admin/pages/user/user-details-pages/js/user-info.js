$("#btn-SaveUserInfo").click(
    function (e) {
        let form = $(this).parents("form");
        if (!$(form).valid()) {
            e.preventDefault();
            e.stopPropagation();
            return;
        }

        $(form).addClass('was-validated');
        let $userinfo =
        {
            userId: parseInt(`<?php echo $lUser->id ?>`),
            full_name: $("#inputName").val(),
            user_name: $("#inputUserName").val(),
            email: $("#inputEmail").val(),
            userType: $('input[name="userType"]:checked').val(),
            screenType: $('input[name="screenType"]:checked').val(),
            profileId: $("#inputProfile").val()
        };
        var settings = {
            "url": "/sessionservices/users.php?q=edit-details",
            "method": "PUT",
            "timeout": 0,
            "headers": {
                "Content-Type": "application/json"
            },
            "data": JSON.stringify($userinfo),
            "success": function (data) {
                console.log(data);
                if ($userinfo.userId === 0) { 
                    window.location.href = `/admin/users/${data.id}` 
                }
                else { showAlert("User-Info saved successfully"); }
            }
        };

        $.ajax(settings).done(function (response) {
            console.log(response);

        });
    }
);
// const validateEmail = (email) => {
//     return String(email)
//         .toLowerCase()
//         .match(
//             /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
//         );
// };
// $("#passwordForm").validate({
//     rules: {
//         password1: {
//             required: true,
//             pwcheck: true,
//             minlength: 8
//         },
//         password2: {
//             required: true,
//             equalTo: "#password1"
//         },
//     },
//     messages: {
//         password1: {
//             required: "Please provide a password",
//             minlength: "Your password must be at least 8 characters long"
//         },
//         email: "Please enter a valid email address"
//     },

// });
$(document).ready(function () {
    $("#userDetails").validate({
        rules: {
            inputUserName: {
                alphanumeric: true
            },
            inputEmail: {
                emailcheck: true,
            }
        }
    });
    $.validator.addMethod("emailcheck", function (value, element) {
        let email = value;
        if (!(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(email))) {
            return false;
        }
        return true;
    });
});