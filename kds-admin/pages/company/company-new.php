


<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
  Launch wizard modal
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="js-title-step"></h4>
            </div>
            <div class="modal-body">
                <div class="row hide" data-step="1" data-title="This is the first step!">
                    <div class="jumbotron">This is the first step!</div>
                </div>
                <div class="row hide" data-step="2" data-title="This is the second step!">
                    <div class="jumbotron">This is the second step!</div>
                </div>
                <div class="row hide" data-step="3" data-title="This is the last step!">
                    <div class="jumbotron">This is the last step!</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default js-btn-step pull-left" data-orientation="cancel" data-dismiss="modal"></button>
                <button type="button" class="btn btn-warning js-btn-step" data-orientation="previous"></button>
                <button type="button" class="btn btn-success js-btn-step" data-orientation="next"></button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="/kds-admin/src/js/modal-steps.min.js"></script>
<script>
    $('#myModal').modalSteps({
  btnCancelHtml: "Cancel",
  btnPreviousHtml: "Previous",
  btnNextHtml: "Next",
  btnLastStepHtml: "Complete",
  disableNextButton: false,
  completeCallback: function() {},
  callbacks: {},
  getTitleAndStep: function() {}
});
</script>