<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $line = $lines[$id];
    $val = json_decode($line);
    $val->lineNum = $id;
    
    $val->errorType = isset($val->context->exception->level)? FriendlyErrorType($val->context->exception->level):"E_ERROR";
} else {
    $val = new stdClass();
}

function PrintoutArray($array)
{ ?>
    <fieldset disabled>
        <ul class='list-group'>
            <?php foreach ($array as $key => $val) { ?>
                <li class='list-group-item'>
                    <?php if (gettype($val) == 'array') { ?>
                        <div class='card'>
                        <label class='card-header theader' data-target='#<?php echo $key ?>' data-toggle='collapse' aria-expanded='false' aria-controls='<?php echo $key ?>'>
                            <i class='fas fa-plus'></i><b> <?php echo $key ?></b></label>
                        <ul id='<?php echo $key ?>' class='collapse'>
                            <?php PrintoutArray($val); ?>
                        </ul></div>
                    <?php } else { ?>
                        <div class='input-group input-group-sm'>
                            <div class='input-group-prepend'>
                                <label class='input-group-text'><?php echo $key ?></label>
                            </div>
                            <input type='text' id='<?php echo $key ?>' class='form-control' aria-describedby='<?php echo $key ?>' value='<?php echo $val ?>'>
                        </div>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
    </fieldset>
<?php }
$arrayVal = json_decode(json_encode($val), true); ?>
<div class="row">
    <div class="col-4">
        <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
            <a class="btn btn-primary" role="button" href="/admin/loggy"><i class="fa-solid fa-circle-chevron-left"></i> Back</a>

        </div>
    </div>
    <div class="col-4"></div>
    <div class="col-4"></div>
</div>
<hr />
<div class="">
    <?php PrintoutArray($arrayVal); ?>
    <script>
        $('.theader').click(function() {
            $(this).find('i').toggleClass('fas fa-plus fas fa-minus');
        });
    </script>
</div>