var tblCategories = $('#tblCategories').DataTable({
    dom: '<"container-fluid pt-2"<"row"<"col"l><"col d-flex justify-content-center"f><"col d-flex justify-content-end"B>>>rtip',//'rfBltip',
    buttons: [
        {
            className: 'toogle-items select-all btn btn-sm btn-info',
            text: '',
            attr: {
                "for-ul": "categories"
            },
            // action: function ( e, dt, node, config ) {
            //     alert( 'Button activated' );
            //     console.log(dt.data());
            // }
        }
    ],
    columnDefs: [{
        target: 0,
        visible: true
    },
    {
        target: 1,
        visible: true
    },
    {
        target: 2,
        visible: false
    },
    {
        target: 3,
        visible: false
    },
    {
        target: 4,
        visible: true
    }

    ]
});

$("#btnPostCategoriesT").on("click", function () {
    let integrationId = $(hdfIntegrationId).val();
    let gfMenuId = $(txtGfMenuId).text();
    $("tr.category .has-issue,tr.category .is-invalid").each(function (key, value) {
        let rowParent = $(this).parent("tr");
        rowParent.find("td>.spinner").toggleClass("visually-hidden");

        rowParent.removeClass("is-invalid").removeClass("has-issue").addClass("is-none");
        $(this).removeClass("is-invalid").removeClass("has-issue").addClass("is-none");
        var rowId = rowParent.attr("id");
        // var rowData = tblCategories.row(rowParent);
        // console.log(rowData.data());
        let gfid = rowParent.attr("id").substring(2);
        let lid = $(rowParent).attr("lid");
        let name = $(rowParent).attr("name");
        let data = JSON.stringify({
            "integration_id": integrationId,
            "gf_menu_id": gfMenuId,
            "gf_id": gfid,
            "l_id": lid,
            "name": name,
        });

        //console.log(data);
        PostCategory(data, rowParent);
    });

});

function PostCategory(data, elem) {

    var settings = {
        "url": "/sessionservices/integration.php?q=postcategory",
        "method": "POST",
        "timeout": 0,
        //"async": true,
        "headers": {
            "Content-Type": "application/json",
        },
        "data": data,
        "success": function (response) {
            console.log(response);
            $(elem).find(".spinner").toggleClass("visually-hidden");
            $($(elem).find(".spinner")).parent("td").addClass("is-valid");
            $(elem).addClass("is-valid");
        }
    };
    $.ajax(settings).fail(function (response) {
        console.log(response);
        $(elem).find(".spinner").toggleClass("visually-hidden");
        $(elem).addClass("is-invalid");
        $($(elem).find(".spinner")).parent("td").addClass("is-invalid");
    });
}