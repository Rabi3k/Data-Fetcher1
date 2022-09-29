         <div class="row">
            <div class="col-4">
               <div class="form-group">
                  <label for="min">Minimum date:</label>
                  <input type="text" id="min" name="min">
               </div>
               <div class="form-group">
                  <label for="max">Maximum date:</label>
                  <input type="text" id="max" name="min">
               </div>
            </div>
            <div class="col-4"></div>
            <div class="col-4">
               <a class="btn btn-primary float-right" href="?new=" role="button"><i data-feather='plus-circle'></i><span> Ny</span></a>
            </div>
         </div>
         <hr />
         <div class="row">
            <?php include_once "loggy/logs-list.php" ?>
         </div>
      </div>