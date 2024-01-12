<div class="row">
    <div class="col d-grid gap-2 mx-auto">
        <span class="btn btn-success fs-4" name="postModifiers" id="btnPostModifiersT">Post Modifiers</span>
    </div>
</div>
<div class="row">
    <div class="table-responsive-sm">
        <table id="tblModifiers" class="table table-striped w-100 display">
            <thead class="table-secondary">
                <tr>
                    <th></th>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Options</th>
                    <th scope="col">loyverse Id</th>
                    <th scope="col">Validaty</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody class="modifiers">
                <?php foreach ($modifiers as $key => $value) {
                    $validationClass = isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid' : "is-invalid";
                   // echo json_encode($value);
                ?>
                    <tr class='menu modifier  <?php echo $validationClass  ?>' id="m-<?php echo $value->id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>">
                        <td></td>
                        <td class="fs-5 fw-bolder"><?php echo $value->id ?></td>
                        <td class="fs-5 fw-bolder"><?php echo $value->name ?></td>
                        <td class="fs-5 fw-bolder"><?php echo json_encode($value->options) ?></td>
                        <td class="fs-5 fw-bolder"><?php echo $value->loyverse_id ?></td>
                        <td><?php echo $validationClass ?></td>
                        <td class="spiner-col <?php echo $validationClass ?>">
                            <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                        </td>
                    </tr>
                <?php } ?>

            </tbody>
        </table>
    </div>
</div>

<ul class="modifiers card list-group  overflow-auto max-list-5">

</ul>
<script type="text/javascript">
    <?php include "js/modifiers.js" ?>

   
</script>