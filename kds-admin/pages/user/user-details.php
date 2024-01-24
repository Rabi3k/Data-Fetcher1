<?php

use Src\Classes\Company;
use Src\Classes\GlobalFunctions;
use Src\Classes\User;
use Src\Classes\KMail;
use Src\TableGateways\CompanyGateway;
use Src\TableGateways\UserLoginGateway;
use Src\TableGateways\UserGateway;
use Src\TableGateways\RestaurantsGateway;
use Src\TableGateways\UserProfilesGateway;

$SaveType = "";
$idUrl = "";
if (isset($_GET['id'])) {
    $lUser = UserGateway::GetUserClass($_GET['id'], false);
    $SaveType = "update";
    $idUrl = "id=$lUser->id";
} else if (isset($_GET['new'])) {
    $lUser = new User();
    $SaveType = "add";
    $idUrl = "new";
}
$profiles = (new UserProfilesGateway($dbConnection))->GetAllProfiles();
$restaurants = (new RestaurantsGateway($dbConnection))->GetAll();
$companiesTree = Company::getAllCompaniesJsonTree();


$userSecret = $userGateway->GetEncryptedKey($lUser->email);
$secretKey =  bin2hex($userSecret);

$compTree = $lUser->GetUserComanyRelationTree();
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit-details') {

        if (isset($_POST['inputName']) && !empty($_POST['inputName'])) {
            $lUser->full_name = $_POST['inputName'];
        }
        if (isset($_POST['inputUserName']) && !empty($_POST['inputUserName'])) {
            $lUser->user_name = $_POST['inputUserName'];
        }
        if (isset($_POST['inputEmail']) && !empty($_POST['inputEmail'])) {
            $lUser->email = $_POST['inputEmail'];
        }
        // Activate user
        if (isset($_POST['userType']) && !empty($_POST['userType'])) {
            $lUser->SetUsertype(strval($_POST['userType']));
        }
        if (isset($_POST['screenType']) && !empty($_POST['screenType'])) {
            //$lUser->SetUsertype(strval($_POST['userType']));
            switch ($_POST['screenType']) {
                case "OrderDisplay":
                    $lUser->screen_type = 1;
                    break;

                case "ItemDisplay":
                    $lUser->screen_type = 2;
                    break;

                case "CustomerDisplay":
                    $lUser->screen_type = 3;
                    break;

                default:
                    $lUser->screen_type = 1;
                    break;
            }
        }
        if (isset($_POST['inputProfile']) && !empty($_POST['inputProfile'])) {
            $lUser->profile_id = intval($_POST['inputProfile']);
        }
        $lUser = $userGateway->InsertOrUpdate($lUser);
        $idUrl = "id=$lUser->id";
    }
}

if (isset($_POST['set-access'])) {
    $userRelations = array();
    if (isset($_POST['restaurtants'])) {
        foreach ($_POST['restaurtants'] as $key => $val) {
            if (gettype($val) === 'array') {
                foreach ($val as $bkey => $bval) {
                    $ur = new stdClass();
                    $ur->user_id = $lUser->id;
                    $ur->restaurant_id = $key;
                    $ur->company_id = $bkey;
                    array_push($userRelations, $ur);
                }
            } else {
                $ur = new stdClass();
                $ur->user_id = $lUser->id;
                $ur->restaurant_id = $key;
                $ur->company_id = null;
                array_push($userRelations, $ur);
            }
        }
    }

    if (count($userRelations) < 1) {
        $ur = new stdClass();
        $ur->user_id = $lUser->id;
        $ur->restaurant_id = null;
        $ur->company_id = null;
        array_push($userRelations, $ur);
    }
    $userGateway->updateUserRelations($userRelations);
    $lUser = UserGateway::GetUserClass($_GET['id'], false);
}
$companiesTreejs = array();
foreach ($companiesTree as $c) {
    $co = new stdClass();
    $co->parentNodeId = $c->id;
    $co->parentNodeTxt = $c->name;
    $co->childNodes = array();
    foreach ($c->restaurants as $r) {
        $cor = new stdClass();
        $cor->id = $r->id;
        $cor->name = $r->name;
        $co->childNodes[] = $cor;
    }
    $companiesTreejs[] = $co;
}

?>

<div class="row">
    <div class="col">
        <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
            <a class="btn btn-danger" role="button" href="/admin/users"><i class="fa-solid fa-circle-chevron-left"></i> Back</a>
        </div>
    </div>
    <div class="col"></div>

    <div class="col-5 pull-right">
        <div class="row">
            <div class="col btn-group-vertical pull-right" role="group" aria-label="Vertical button group">
                <a class="btn btn-info pull-right" role="button" id="btnCopyUserLogin" onclick="CopyToClipboard();"><i class="fa fa-solid fa-sign-in"></i> Copy url</a>
            </div>
            <div class="col btn-group-vertical" role="group" aria-label="Vertical button group">
                <a class="btn btn-warning" role="button" target="_blank" href="/login.php?secret=0x<?php echo $secretKey ?>" id="btnUserLogin"><i class="fa fa-solid fa-sign-in"></i> Login with user</a>
            </div>

        </div>

    </div>
</div>
<script>
    function CopyToClipboard() {
        // Get the text field
        var copyText = document.getElementById("btnUserLogin");

        let $userUrlSecret = $(copyText).attr("href");

        // Copy the text inside the text field
        try {
            copyToClipboard(`${window.location.protocol}//${window.location.host}${$userUrlSecret}`).then(() =>
                console.log('Text copied to the clipboard!'));
        } catch (error) {
            console.error(error);
        }
    }
    async function copyToClipboard(textToCopy) {
        // Navigator clipboard api needs a secure context (https)
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(textToCopy);
        } else {
            // Use the 'out of viewport hidden text area' trick
            const textArea = document.createElement("textarea");
            textArea.value = textToCopy;

            // Move textarea out of the viewport so it's not visible
            textArea.style.position = "absolute";
            textArea.style.left = "-999999px";

            document.body.prepend(textArea);
            textArea.select();

            try {
                document.execCommand('copy');
            } catch (error) {
                console.error(error);
            } finally {
                textArea.remove();
            }
        }
    }
</script>
<hr />
<div class="alert alert-success visually-hidden" role="alert" id="div-alert-success">
    <center>
        <h5 class="alert-heading fw-bold">Well done!</h5>
        <hr />
        <span id="alert-msg" class="fs-6">Some Word</span>
    </center>
</div>
<div class="alert alert-danger visually-hidden" role="alert" id="div-alert-error">
    <center>
        <h5 class="alert-heading fw-bold">Error!</h5>
        <hr />
        <span id="alert-msg" class="fs-6">Some Word</span>
    </center>
</div>
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#home">User Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#access">Profile</a>
    </li>
    <li class="nav-item <?php echo  $SaveType === "update" ? "" : "disabled"  ?> ">
        <a class="nav-link" data-toggle="tab" href="#password">Change Password</a>
    </li>
</ul>

<!-- set User Details Tab -->
<div class="tab-content p-2 border border-top-0" id="myTabContent">
    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
        <form method="post" id="userDetails" action="?<?php echo $idUrl ?>&action=edit-details&tab=home">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail">Name</label>
                    <input type="text" class="form-control" name="inputName" id="inputName" value="<?php echo $lUser->full_name ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputUserName">User Name</label>
                    <input type="text" class="form-control" name="inputUserName" id="inputUserName" value="<?php echo $lUser->user_name ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputAddress">Email</label>
                <input type="email" class="form-control" name="inputEmail" id="inputEmail" value="<?php echo $lUser->email ?>" required>
            </div>
            <div class="form-row">
                <fieldset class="form-group col-md-3">
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
                <fieldset class="form-group col-md-5">
                    <label>Screen Type</label>
                    <div class="card p-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="screenType" id="rb_ods" value="OrderDisplay">
                            <label class="form-check-label" for="rb_ods">
                                Order Display
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="screenType" id="rb_ids" value="ItemDisplay">
                            <label class="form-check-label" for="rb_ids">
                                Item Display
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="screenType" id="rb_cds" value="CustomerDisplay">
                            <label class="form-check-label" for="rb_cds">
                                Customer Display
                            </label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group col-md-4">
                    <label for="inputProfile">Profile</label>
                    <select id="inputProfile" name="inputProfile" class="form-control">

                        <?php foreach ($profiles as $profile) {
                            echo  "<option name='" . $profile->GetProfileType() . "' value='$profile->id'>$profile->name</option>";
                        } ?>
                        <!-- <option name="SuperAdmin" value="1">SA Profile1</option>
                        <option name="SuperAdmin" value="2">SA Profile2</option>
                        <option name="SuperAdmin" value="3">SA Profile3</option>
                        <option name="Admin" value="4">A Profile1</option>
                        <option name="Admin" value="5">A Profile2</option>
                        <option name="Admin" value="6">A Profile3</option>
                        <option name="User"value="7">Super Admin3</option>
                        <option name="User" value="8">Admin3</option>
                        <option name="User"value="9">User3</option> -->
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
    <!-- End of User Details Tab -->
    <!-- set Profifle Tab -->
    <div class="tab-pane fade" id="access" role="tabpanel" aria-labelledby="profile-tab">
        <form method="post" name="setAccess" action="?<?php echo $idUrl ?>&action=set-access&tab=access">
            <?php
            $allComapnies = (new CompanyGateway($dbConnection))->GetAllAdvanced();
            $UserComanyRelation = $lUser->GetUserComanyRelationTree();
            $UserComaniesRelationObj = array();
            $UserRestaurantsIds = array();
            foreach ($UserComanyRelation as $key => $value) {
                # code...
                $UserComanyRelationObj = $value->getJson();
                $UserComanyRelationObj->restaurants = json_decode(GlobalFunctions::ClassObjArrToJsonStr($value->restaurants));
                $UserComaniesRelationObj[] = $UserComanyRelationObj;
                $UserRestaurantsIds =
                    array_unique(array_merge($UserRestaurantsIds, array_column($value->restaurants, "id")), SORT_REGULAR);
            }
            // echo(GlobalFunctions::ClassObjArrToJsonStr($UserComanyRelation->restaurants));
            //echo json_encode($UserComanyRelationObj);
            ?>

            <div class="table-responsive">
                <table class="table table-info w-100" id="tblComanies">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Restaurants</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allComapnies as $key => $c) {

                        ?>
                            <tr>
                                <td></td>
                                <td><?php echo $c->id ?></td>
                                <td><?php echo $c->name ?></td>
                                <td><?php echo $c->restaurants ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <button type="submit" name="set-access" class="btn btn-primary">Save</button>
        </form>



        <script>
            let $UserComanyRelationObj = JSON.parse('<?php echo json_encode($UserComaniesRelationObj) ?>');
            let $UserComanyIds = JSON.parse('<?php echo json_encode(array_column($UserComaniesRelationObj, "id")) ?>');
            let $UserRestaurantsIds = JSON.parse('<?php echo json_encode($UserRestaurantsIds) ?>');
            const tblComanies = new DataTable('#tblComanies', {
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'restaurants',
                        visible: false
                    },
                    {
                        orderable: false,
                        data: null,
                        render: function(data, type, row, meta) {
                            return `<input class="form-check-input cb-cSelect" type="checkbox" value="${data.id}"/>`;
                        }
                    },
                ],
                "createdRow": function(row, data, dataIndex) {
                    const exists = $UserComanyIds.includes(parseInt(data.id))
                    if (exists) {
                        $(row).addClass('selected');
                        $(row).find(".cb-cSelect").prop("checked", true);
                    }
                }
            });

            tblComanies.on('change', '.cb-cSelect', function(e) {
                $(this).parents('tr').toggleClass('selected');
                $UserComanyIds = addOrRemove($UserComanyIds, parseInt($(this).val()));
            });
            restaurantsSeleted = [];
            const addOrRemove = (array, item) => {
                const exists = array.includes(item)
                if (exists) {
                    return array.filter((c) => {
                        return c !== item
                    })
                } else {
                    const result = array
                    result.push(item)
                    return result
                }
            }
            tblComanies.on('change', '.cb-rSelect', function(e) {
                $(this).parents('li').toggleClass('active');
                $UserRestaurantsIds = addOrRemove($UserRestaurantsIds, parseInt($(this).val()));
            });
            tblComanies.on('click', 'td.dt-control', function(e) {
                e.preventDefault();
                let tr = e.target.closest('tr');
                let row = tblComanies.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                } else {
                    // Open this row
                    row.child(format(row.data().restaurants)).show();
                }
            });

            function format(d) {
                // `d` is the original data object for the row
                /*`<dl>
                        <dt>Full name:</dt>
                        <dd> ${d.name} </dd>
                        <dt>Extension number:</dt>
                        <dd>${d.extn} </dd>
                        <dt>Extra info:</dt>
                        <dd>And any further details here (images etc)...</dd>
                    </dl>` 
                    <ul class="list-group list-group-numbered">
                        <li class="list-group-item active">Active item</li>
                        <li class="list-group-item">Item</li>
                        <li class="list-group-item disabled">Disabled item</li>
                    </ul>
                    
                    */
                rests = JSON.parse(d);
                let liRests = [];

                rests.forEach((v) => {
                    const exists = $UserRestaurantsIds.includes(parseInt(v.id))

                    let lirest =
                        `<li class="list-group-item ${exists?"active":""} ">
                        <div class="row">
                            <div class="col"></div>
                            <div class="col">
                                ${v.alias}
                            </div>
                            <div class="col">
                                ${v.city}
                            </div>
                            <div class="col">
                            <input class="form-check-input cb-rSelect" type="checkbox" value="${v.id}" ${exists?"checked":""} />
                            </div>
                        </div>
                    </li>`;
                    liRests.push(lirest);
                });
                return (`<ul class="list-group bg-light text-dark">${liRests.join("")}</ul>`);
            }
            //  document.querySelector('#button').addEventListener('click', function () {
            //      alert(tblComanies.rows('.selected').data().length + ' row(s) selected');
            //  });
        </script>

    </div>
    <!-- End of Profifle Tab -->
    <!-- Change Password Tab -->
    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="contact-tab">
        <div class="container">
            <div class="row text-center">
                <div class="col-12 p-2">
                    <p class="text-center h5">Use the form below to change your password.</p>
                </div>
            </div>
            <div class="row text-center">
                <input type="submit" class="btn btn-info btn-load btn-lg" id="btn-resetPass" name="SendRestPaswordEmail" data-loading-text="Sending Email..." value="Send Reset Password Email">
            </div>

            <hr />
            <div class="row text-center">
                <div class="col-12 p-2">
                    <p class="text-center h5">Or</p>
                </div>
            </div>
            <hr />
            <form method="post" id="passwordForm" action="?<?php echo $idUrl ?>&action=change-password&tab=password" class="row g-3 needs-validation" novalidate>
                <?php include "user-password.php" ?>
            </form>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
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
        $.validator.addMethod("pwcheck", function(value, element) {
            let password = value;
            if (!(/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W)(.{8,20}$)/.test(password))) {
                return false;
            }
            return true;
        }, function(value, element) {
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

        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        });
    });
    $("#btn-resetPass").click(function() {
        var settings = {
            "url": "/sessionservices/users.php?q=send-rest-pasword",
            "method": "PUT",
            "timeout": 0,
            "headers": {
                "Content-Type": "application/json"
            },
            "data": JSON.stringify({
                "userId": <?php echo $lUser->id ?>
            }),
            "success": function(data) {
                $("#div-alert-success").removeClass("visually-hidden");
                $("#alert-msg").text("Email sent successfully");
                setTimeout(function() {
                    $("#div-alert-success").addClass("visually-hidden");
                }, 5000);
            }
        };

        $.ajax(settings).done(function(response) {
            console.log(response);

        });
    });
    $("#btn-change-1").click(function(e) {
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
                    "userId": <?php echo $lUser->id ?>,
                    "password": $("#password1").val()
                }),
            };

            $.ajax(settings).done(function(response) {
                $("#btn-change-1").removeAttr("disabled")
                $("#btn-change-1").find(".spinner").addClass("visually-hidden");
                $("#div-alert-success").removeClass("visually-hidden");
                $("#alert-msg").text("Password changed successfully");
                $("#password1").val('');
                $("#password2").val('');
                setTimeout(function() {
                    $("#div-alert-success").addClass("visually-hidden");
                }, 5000);
                console.log(response);
            });
        }
    });
</script>


<?php

?>
<script type="text/javascript">
    var val = '<?php echo isset($_GET['tab']) ? $_GET['tab'] : "home" ?>';
    if (val != '') {
        //alert(val);   
        $(function() {
            $('a[href="#' + val + '"]').addClass('active');
            $('#' + val).addClass('show active');
        });
    }
    $('input:radio[name="userType"]').change(
        function() {
            $('#inputProfile option').hide();
            if ($(this).is(':checked')) {
                $('#inputProfile option[name=' + $(this).val() + ']').show();
                $('#inputProfile').val($('#inputProfile option[name=' + $(this).val() + ']').first().val());
                // append goes here
            }
        });
    $('#inputProfile option').hide();
    $('#inputProfile option[name=<?php echo $lUser->UserType() ?>]').show();
    $("#rb_<?php echo $lUser->UserType()   ?>").prop("checked", true);
    $("#rb_<?php echo $lUser->GetScreenType()   ?>").prop("checked", true);
    $("#inputProfile").val("<?php echo $lUser->Profile->id   ?>");
    $("#userDetails").validate({
        rules: {
            inputUserName: {
                alphanumeric: true
            }
        }
    });
</script>