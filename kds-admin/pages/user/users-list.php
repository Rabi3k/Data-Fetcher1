<?php
$users = $userLogin->GetAllUsers();


?>
    <div class="row">
        <div class="col-4"></div>
        <div class="col-4"></div>
        <div class="col-4">
            <a class="btn btn-primary float-right" href="#print" role="button"><i data-feather='printer'></i><span> print</span></a>    
            <a class="btn btn-primary float-right" href="?new=" role="button"><i data-feather='plus-circle'></i><span> Ny</span></a>
        </div>
    </div>
    <script>
var hash = window.location.hash, //get the hash from url
    cleanhash = hash.replace("#", ""); //remove the #
    //alert(cleanhash);
</script>

<?php
$hash = "<script>document.writeln(cleanhash);</script>";
echo $hash;
?>
    <?php 
    
    if($_GET['fragment'])
    {
        echo $_GET['fragment']; //This variable contains the fragment
    }

    $url=parse_url($_SERVER["REQUEST_URI"]);
    if($url['fragment'])
    {
        echo $url["fragment"]; //This variable contains the fragment
    }else
    {
        //var_dump($url);
    }
 //This variable contains the fragment
    ?>
<hr/>
<div class="table-responsive ">
    <table class="table table-bordered table-hover" id="tblUsers">
        <caption>List of users</caption>
        <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">UserName</th>
                <th scope="col">Profile</th>
                <th scope="col">UserType</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) {
                $usertype = $user->usertype();
            ?>
                <tr class='clickable-row' data-href="?id=<?php echo $user->id ?>">
                    <th scope="row"><?php echo $user->id ?></th>
                    <td scope="row"><?php echo $user->full_name ?></td>
                    <td scope="row"><?php echo $user->email ?></td>
                    <td scope="row"><?php echo $user->user_name ?></td>
                    <td scope="row"><?php echo $user->profile->name ?></td>
                    <td scope="row"><?php echo $usertype ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function() {

        var table = $('#tblUsers').DataTable({
            responsive: true,
        });
    });
    $('#tblUsers tbody').on('click', 'tr', function() {
        //$(this).toggleClass('selected');
        window.location = $(this).data("href");
    });

    /*
    buttons: [
            {
                className: 'btn-export border-0 btn-outline-export',
                text: "<button class='bg-success text-white'><i class='las la-sync'></i></button>",
                action: function () {
                    alert("add new");
                },
                titleAttr: 'Add new '
            }
        ] */
</script>