
var tblModifiers = new DataTable('#tblModifiers', {
    dom: '<"container-fluid pt-2"<"row"<"col"l><"col d-flex justify-content-center"f><"col d-flex justify-content-end"B>>>rtip',//'rfBltip',
    buttons: [
        {
            className: 'toogle-items select-all btn btn-sm btn-info',
            text: '',
            attr: {
                "for-ul": "modifiers"
            },
            // action: function ( e, dt, node, config ) {
            //     alert( 'Button activated' );
            //     console.log(dt.data());
            // }
        }
    ],
    columns: [{
        class: 'dt-control',
        orderable: false,
        data: null,
        defaultContent: ''
    },
    {
        target: 1,
        visible: true
    },
    {
        target: 2,
        visible: true
    },
    {
        target: 3,
        visible: false,
        data: 'options',
    },
    {
        target: 4,
        visible: false
    },
    {
        target: 5,
        visible: false
    },
    {
        target: 6,
        visible: true
    }

    ],
});

// Array to track the ids of the details displayed rows
const detailRows = [];

tblModifiers.on('click', 'tbody td.dt-control', function () {
    let tr = event.target.closest('tr');
    let row = tblModifiers.row(tr);
    let idx = detailRows.indexOf(tr.id);

    if (row.child.isShown()) {
        tr.classList.remove('details');
        row.child.hide();

        // Remove from the 'open' array
        detailRows.splice(idx, 1);
    }
    else {
        tr.classList.add('details');
        row.child(format(row.data())).show();

        // Add to the 'open' array
        if (idx === -1) {
            detailRows.push(tr.id);
        }
    }
});

// On each draw, loop over the `detailRows` array and show any child rows
tblModifiers.on('draw', () => {
    detailRows.forEach((id, i) => {
        let el = document.querySelector('#' + id + ' td.dt-control');

        if (el) {
            el.dispatchEvent(new Event('click', { bubbles: true }));
        }
    });
});

function format(d) {
    // `d` is the original data object for the row
    let options = JSON.parse(d.options);
    optionsLines = [];
    options.forEach((item) => {
        // <li class='menu option list-group-item form-control <?php echo isset($pOption[$o->id]) && $pOption[$o->id]->loyverse_id != null ? 'is-valid' : "is-invalid"   ?>' id="m-<?php echo $o->id ?>" lid="<?php echo  $pOption[$o->id]->loyverse_id  ?>" name="<?php echo $o->name ?>" price="<?php echo $o->price ?>">
        //     <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
        //     <span class="fs-6 fw-semibold"><?php echo $o->name ?> </span>
        //     <span class="fs-6 float-end"><?php echo $o->price ?> DKK</span>
        // </li>
        let currencyLocal = new Intl.NumberFormat('da-DK', {
            style: 'currency',
            currency: 'DKK',
        });
        let str = `<li class="menu option list-group-item form-control" lid="${item.loyverse_id }" name="${item.name}" price="${item.price }">
            <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
            <span class="fs-6 fw-semibold">${item.name}</span>
            <span class="fs-6 float-end"> ${currencyLocal.format(item.price)}</span>
            </li>`
        optionsLines.push(str);
    });
    return '<ul class="options card list-group  overflow-auto max-list-5">' + optionsLines.join('') + '</ul>';
}

$("#btnPostModifiersT").on("click", function() {
    let integrationId = $(hdfIntegrationId).val();
    let gfMenuId = $(txtGfMenuId).text();
    $("tr.modifier.has-issue,tr.modifier.is-invalid").each(function(key, value) {
        $(this).find(".spinner").toggleClass("visually-hidden");
        $(this).removeClass("has-issue");
        $(this).removeClass("is-invalid");


        let options = [];
        let optionsObj =JSON.parse( tblModifiers.row(this).data().options);

        optionsObj.forEach(function(oVal) {
           let id =oVal.id;
            options.push({
                "gf_id": oVal.id,
                "l_id": oVal.loyverse_id,
                "name": oVal.name,
                "price": parseInt(oVal.price)
            })
        })
        let gfid = $(value).attr("id").substring(2);
        let lid = $(value).attr("lid");
        let name = $(value).attr("name");
        let data = JSON.stringify({
            "integration_id": integrationId,
            "gf_menu_id": gfMenuId,
            "gf_id": gfid,
            "l_id": lid,
            "name": name,
            "options": options

        });

        //console.log(data);
        PostModifier(data, this);
    })
});

function PostModifier(data, elem) {
    /*
    integration_id =>hdfIntegrationId
    gf_id
    l_id
    name
    gf_menu_id =>txtGfMenuId
     */
    var settings = {
        "url": "/sessionservices/integration.php?q=postmodifier",
        "method": "POST",
        "timeout": 0,
        //"async": true,
        "headers": {
            "Content-Type": "application/json",
        },
        "data": data,
        "success": function(response) {
            console.log(response);
            $(elem).find(".spinner").toggleClass("visually-hidden");
            $(elem).addClass("is-valid");
            $(elem).find("li.option").addClass("is-valid");
            $($(elem).find(".spinner")).parent("td").addClass("is-valid");
            $(elem).addClass("is-valid");
        }
    };
    $.ajax(settings).fail(function(response) {
        console.log(response);
        $(elem).find(".spinner").toggleClass("visually-hidden");
        $(elem).addClass("is-invalid");
        $(elem).find("li.option").addClass("is-invalid");
        $($(elem).find(".spinner")).parent("td").addClass("is-valid");
    });
}