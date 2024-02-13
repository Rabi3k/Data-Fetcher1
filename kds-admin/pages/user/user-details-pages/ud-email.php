<?php


?>

<form class="row g-3 needs-validation" id="email-setup-form">
    <div class="mb-3">
        <label for="txt-email" class="form-label">Email</label>
        <div class="input-group ps-2 pe-5">
            <input type="text" class="form-control ol-3" id="txt-email" placeholder="Username" value="<?php echo $lUser->funneat_user ?>" required />
            <div class="input-group-text fs-6">@</div>
            <div class="input-group-text fs-5 fw-bold col-8">funneat.dk</div>
        </div>
    </div>
    <div class="mb-3">
        <label for="" class="form-label">Password</label>
        <div class="input-group ps-2 pe-5">
            <input type="password" class="form-control" name="" id="email-password" placeholder="" value="<?php echo $lUser->funneat_pass ?>" required />
            <div class="input-group-text fs-6 bi bi-eye fs-5 togglePassword" for="email-password"></div>

        </div>
    </div>
    <div class="row">
        <div class="col  p-2">
            <button class="btn btn-info float-end" type="button" id="btn-save-femail">
                <i class="bi bi-save"></i>
                <span class="spinner spinner-border spinner-border-sm visually-hidden" role="status" aria-hidden="true"></span>
                Save
            </button>
        </div>
    </div>
</form>
<script>
    $("#btn-save-femail").on("click", function() {
        let form = $(this).parents("form");
        if (!$(form).valid()) {
            e.preventDefault();
            e.stopPropagation();
            return;
        } else {
            $(this).find(".spinner").removeClass("visually-hidden");
            $(this).attr("disabled", "true");
            let $data = JSON.stringify({
                "userId": $userId,
                "f_email": $("#txt-email").val(),
                "f_password": $("#email-password").val(),
            });
            var settings = {
                "url": "/sessionservices/users.php?q=change-femail",
                "method": "PUT",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": $data,
            };

            $.ajax(settings).done(function(response) {
                showAlert("email saved successfully");
                $("#btn-save-femail").find(".spinner").addClass("visually-hidden");
                $("#btn-save-femail").removeAttr("disabled");
                console.log(response);
            });
        }


    });
</script>