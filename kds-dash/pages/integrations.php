<?php

?>
<div class="container-fluid">
    <center>
        <div class="row">
            <h3>Integration with POS Systems</h3>
        </div>
    </center>
    <hr />
    <div class="row">
        <div class="col-6">
            <div class="input-group mb-3">
                <span class="input-group-text">restaurant UID</span>
                <input type="text" name="uid" id="txt-Uid" class="form-control" placeholder="placeholder">
            </div>
        </div>
        <div class="col-3">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalId">
                Fetch Categories to Pos
            </button>

            <!-- Modal -->
            <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitleId">Modal title</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <span id="txt-categories">aaaa</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="btnSave" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                var modalId = document.getElementById('modalId');

                modalId.addEventListener('show.bs.modal', function(event) {
                    // Button that triggered the modal
                    let button = event.relatedTarget;
                    // Extract info from data-bs-* attributes
                    let recipient = button.getAttribute('data-bs-whatever');

                    // Use above variables to manipulate the DOM
                    fetchMenu($("#txt-Uid").val());

                });

                $("#btnSave").click(function(){
                    PostCategories(categoryNames);
                });
                function fetchMenu(Urid) {

                    var settings = {
                        "url": "/api/restaurant/" + Urid,
                        "method": "GET",
                        "timeout": 0,
                    };

                    $.ajax(settings).done(function(response) {
                        console.log(response);

                        resp = response.data;
                        var cats = $.map(resp.menu.categories, function(v) {
                            return v.name;
                        });
                        categoryNames = cats;
                        console.log(categoryNames);
                        let text = cats.join(", ");
                        $("#txt-categories").text(text);
                        

                    });
                };
                let categoryNames ={};

                function PostCategories(cats) {
                    $.each(cats, function(key, value) {
                        alert(key + ": " + value);
                        var settings = {
                            "url": "https://api.loyverse.com/v1.0/categories",
                            "method": "POST",
                            "timeout": 0,
                            "headers": {
                                "Content-Type": "application/json",
                                "Authorization": "Bearer 685a16950383408ca5dd9f883f291d5e"
                            },
                            "data": JSON.stringify({
                                "name": value,
                                "color": "RED"
                            }),
                        };

                        $.ajax(settings).done(function(response) {
                            console.log(response);
                        });
                    });

                }
            </script>

        </div>
        <div class="col-3"></div>
    </div>
</div>
<script>

</script>