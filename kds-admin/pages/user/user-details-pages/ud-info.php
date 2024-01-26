<div class="container-fluid">


    <form method="post" id="userDetails" action="?<?php echo $idUrl ?>&action=edit-details&tab=home" class="needs-validation">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="form-row">
                    <div class="form-group col mb-3">
                        <label for="inputEmail" class="form-label">Name</label>
                        <input type="text" class="form-control" name="inputName" id="inputName" value="<?php echo $lUser->full_name ?>" required>
                    </div>
                    <div class="form-group col mb-3" >
                        <label for="inputUserName" class="form-label">User Name</label>
                        <input type="text" class="form-control" name="inputUserName" id="inputUserName" value="<?php echo $lUser->user_name ?>" required>
                    </div>
                </div>
                <div class="form-group col mb-3">
                    <label for="inputAddress" class="form-label">Email</label>
                    <input type="email" class="form-control" name="inputEmail" id="inputEmail" value="<?php echo $lUser->email ?>" required>
                </div>
                <div class="form-group col mb-3">
                    <label for="inputProfile" class="form-label">Profile</label>
                    <select id="inputProfile" name="inputProfile" class="form-select form-select-lg">
                        <?php foreach ($profiles as $profile) {
                            echo  "<option name='" . $profile->GetProfileType() . "' value='$profile->id'>$profile->name</option>";
                        } ?>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="row mb-3">
                    <div class="col-12">
                        <fieldset class="form-group col">
                            <label>User Type</label>
                            <div class="card p-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="userType" id="rb_SuperAdmin" value="SuperAdmin">
                                    <label class="form-check-label" for="SuperAdmin">
                                        Super Admin
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="userType" id="rb_Admin" value="Admin">
                                    <label class="form-check-label" for="Admin">
                                        Admin
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="userType" id="rb_User" value="User">
                                    <label class="form-check-label" for="User">
                                        User
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <fieldset class="form-group">
                            <label>Screen Type</label>
                            <div class="card p-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="screenType" id="rb_ods" value="1">
                                    <label class="form-check-label" for="rb_ods">
                                        Order Display
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="screenType" id="rb_ids" value="2">
                                    <label class="form-check-label" for="rb_ids">
                                        Item Display
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="screenType" id="rb_cds" value="3">
                                    <label class="form-check-label" for="rb_cds">
                                        Customer Display
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-1 float-end">
                <button type="submit" class="btn btn-primary float-end">Save</button>
            </div>
            <div class="col float-end">
                <button type="button" class="btn btn-info float-end" id="btn-SaveUserInfo">
                    <i class="bi bi-save"></i>
                    save
                </button>
            </div>
        </div>
    </form>
</div>
<script>
    <?php include("js/user-info.min.js") ?>
</script>