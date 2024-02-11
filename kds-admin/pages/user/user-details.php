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
    $compTree = $lUser->GetUserComanyRelationTree();
} else if (isset($_GET['new'])) {
    $lUser = new User();
    $SaveType = "add";
    $idUrl = "new";
    $compTree = null;
}
$profiles = (new UserProfilesGateway($dbConnection))->GetAllProfiles();
$restaurants = (new RestaurantsGateway($dbConnection))->GetAll();
$companiesTree = Company::getAllCompaniesJsonTree();

$userSecret = $userGateway->GetEncryptedKey($lUser->email);
$secretKey =  bin2hex($userSecret);

function digitalizeStr($str, int $len)
{
    // Ensure the last part has exactly 4 digits
    if (strlen($str) > $len) {
        return substr($str, 0, $len);
    } elseif (strlen($str) < $len) {
        return str_pad($str, $len, '0', STR_PAD_LEFT);
    }
    return $str;
}

function GeneratePassKey()
{
    global $lUser;
    // Generate random hexadecimal values for the first and second parts
    $hex1 = digitalizeStr(dechex(rand(0, 256)), 2); // Random hex between 00 and FF
    $hex2 = digitalizeStr(dechex(rand(0, 256)), 2); // Random hex between 00 and FF

    // Generate random hexadecimal values for the Company ID and User ID parts
    //$companyID = str_pad(dechex(rand(0, 4095)), 3, '0', STR_PAD_LEFT); // Random hex between 000 and FFF
    $userID = digitalizeStr(dechex($lUser->id),4);

    // Generate random hexadecimal values for the last part
    $randomPart = digitalizeStr(dechex(rand(0, 65535)), 4); // Random hex between 0000 and FFFF

    // Concatenate the values to form the passkey
    $passkey = $hex1 . $userID . $randomPart;

    // Output the passkey
    return  $passkey;
}
?>
<style>
    .list-group-info .list-group-item {
        background-color: var(--bs-light);
        border: 0px solid var(--bs-info);

    }

    .list-group-info .list-group-item:hover {
        background-color: var(--bs-info);
    }

    .list-group-info .list-group-item.active {
        z-index: 2;
        color: var(--bs-list-group-active-color);
        background-color: var(--bs-info);
        border-color: var(--bs-info);
    }
</style>
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
                <a class="btn btn-info pull-right" role="button" id="btnCopyUserLogin" onclick="CopyToClipboard(this);"><i class="bi bi-clipboard-check-fill"></i> Copy url</a>
            </div>
            <div class="col btn-group-vertical" role="group" aria-label="Vertical button group">
                <a class="btn btn-warning" role="button" target="_blank" href="/login.php?secret=0x<?php echo $secretKey ?>" id="btnUserLogin"><i class="fa fa-solid fa-sign-in"></i> Login with user</a>
            </div>

        </div>

    </div>
</div>
<script>
    function CopyToClipboard(e) {
        // Get the text field
        var copyText = document.getElementById("btnUserLogin");

        let $userUrlSecret = $(copyText).attr("href");

        // Copy the text inside the text field
        try {
            copyToClipboard(`${window.location.protocol}//${window.location.host}${$userUrlSecret}`)
                .then(() => {
                    console.log('Text copied to the clipboard!')
                    $(e).addClass("bg-dark text-light disabled");
                    let btTxt = $(e).html();
                    $(e).text("URL Copied !");
                    setTimeout(function() {
                        $(e).removeClass("bg-dark text-light disabled");
                        $(e).html(btTxt);
                    }, 2000);

                });


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
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#home">User Details</a>
    </li>
    <li class="nav-item <?php echo  $SaveType === "update" ? "" : "disabled"  ?> ">
        <a class="nav-link" data-toggle="tab" href="#access">Profile</a>
    </li>
    <li class="nav-item <?php echo  $SaveType === "update" ? "" : "disabled"  ?> ">
        <a class="nav-link" data-toggle="tab" href="#password">Change Password</a>
    </li>
    <li class="nav-item <?php echo  $SaveType === "update" ? "" : "disabled"  ?> ">
        <a class="nav-link" data-toggle="tab" href="#email">Email setup</a>
    </li>
</ul>

<div class="tab-content p-2 border border-top-0" id="myTabContent">
    <!-- set User Details Tab -->
    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
        <?php include("user-details-pages/ud-info.php") ?>
    </div>
    <!-- End of User Details Tab -->
    <!-- set Profifle Tab -->
    <!-- Make sure not to inlude files on new (usless if not used) -->

    <div class="tab-pane fade" id="access" role="tabpanel" aria-labelledby="profile-tab">
        <?php if ($SaveType === "update") {
            include("user-details-pages/ud-restaurants.php");
        } ?>
    </div>
    <!-- End of Profifle Tab -->
    <!-- Change Password Tab -->
    <!-- Make sure not to inlude files on new (usless if not used) -->
    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="contact-tab">
        <?php if ($SaveType === "update") { ?>
            <?php include "user-details-pages/user-password.php" ?>
        <?php } ?>
    </div>
    <!-- End of Change Password Tab -->
    <!-- Setup Email Tab -->
    <!-- Make sure not to inlude files on new (usless if not used) -->
    <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="contact-tab">
        <?php if ($SaveType === "update") { ?>
            <?php include "user-details-pages/ud-email.php" ?>
        <?php } ?>
    </div>
    <!-- End of Change Password Tab -->
</div>

<script type="text/javascript">
    let $userId = parseInt("<?php echo $lUser->id ?>");

    var val = '<?php echo isset($_GET['tab']) ? $_GET['tab'] : "home" ?>';
    if (val != '') {
        //alert(val);   
        $(function() {
            $('a[href="#' + val + '"]').addClass('active');
            $('#' + val).addClass('show active');
        });
    }
    
</script>

<script>
    const togglePassword = $(".togglePassword");
    

    togglePassword.on("click", function() {
        passwordId = $(this).attr("for");
        let password = $("#"+passwordId)
        // toggle the type attribute
        const type = password.attr("type") === "password" ? "text" : "password";
        password.attr("type", type);

        // toggle the eye icon
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
</script>