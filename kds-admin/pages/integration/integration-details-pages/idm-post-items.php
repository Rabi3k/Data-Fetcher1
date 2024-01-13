<div class="row">
    <div class="col d-grid gap-2 mx-auto">
        <span class="btn btn-success fs-4" name="postItems" id="btnPostItemsT">Post Items</span>
    </div>
</div>
<div class="row">
    <div class="table-responsive-sm">
        <table id="tblItems" class="table table-striped w-100 display">
            <thead class="table-secondary">
                <tr>
                    <th></th>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Category</th>
                    <th scope="col">Price</th>
                    <th scope="col">Options</th>
                    <th scope="col">loyverse Id</th>
                    <th scope="col">Validaty</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody class="items">
                <?php foreach ($fItems as $key => $value) {
                    $validationClass = isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid' : "is-invalid";
                    //echo json_encode($value).",";
                ?>
                    <tr class='menu item  <?php echo $validationClass  ?>' id="i-<?php echo $value->id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>">
                        <td></td>
                        <td class="fs-5 fw-bolder"><?php echo $value->id ?></td>
                        <td class="fs-5 fw-bolder"><?php echo $value->name ?></td>
                        <td class="fs-5 fw-bolder"><?php echo $value->gf_category_name ?></td>
                        <td class="fs-5 fw-bolder"><?php echo $value->price ?></td>
                        <td class="fs-5 fw-bolder"><?php echo json_encode($value->sizes) ?></td>
                        <td class="fs-5 fw-bolder"><?php echo $value->loyverse_id ?></td>
                        <td><?php echo $validationClass ?></td>
                        <td class="spiner-col <?php echo $validationClass ?>">
                            <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                        </td>
                    </tr>
                <?php } ?>

            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Category</th>
                    <th scope="col">Price</th>
                    <th scope="col">Options</th>
                    <th scope="col">loyverse Id</th>
                    <th scope="col">Validaty</th>
                    <th scope="col"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<ul class="modifiers card list-group  overflow-auto max-list-5">

</ul>
<script type="text/javascript">
    <?php include "js/items.min.js" ?>
</script>