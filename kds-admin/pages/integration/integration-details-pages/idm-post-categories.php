<div class="row">
    <div class="col d-grid gap-2 mx-auto">
        <span class="btn btn-success fs-4" name="postCategories" id="btnPostCategoriesT">Post Categories</span>
    </div>
</div>
<div class="row">
    <div class="table-responsive-sm">
        <table id="tblCategories" class="table table-striped w-100">
            <thead class="table-secondary">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">loyverse Id</th>
                    <th scope="col">Validaty</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody class="categories">
                <?php foreach ($aCats as $key => $value) {
                    $validationClass = isset($value->hasIssue) && $value->hasIssue != false ? "has-issue" : (isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid'  : "is-invalid");
                    /*
                     <tr class='menu order <?php echo $validationClass ?>' id="o-<?php echo $order->id ?>" lid="<?php echo $order->loyverse_id  ?>" o-type="<?php echo $order->type ?>">
                                        <td class="fs-5"><?php echo "$order->id" ?></td>
                     */
                ?>
                    <tr class='menu category <?php echo $validationClass ?>' id="c-<?php echo $value->gf_id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>">
                        <td class="fs-5 fw-bolder"><?php echo $value->gf_id ?></td>
                        <td class="fs-5 fw-bolder"><?php echo $value->name ?></td>
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
<script type="text/javascript">
    <?php include "js/categories.min.js" ?>
</script>