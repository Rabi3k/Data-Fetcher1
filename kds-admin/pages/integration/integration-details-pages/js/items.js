
var tblItems = new DataTable('#tblItems', {
    dom: '<"container-fluid pt-2"<"row"<"col"l><"col d-flex justify-content-center"f><"col d-flex justify-content-end"B>>>rtip',//'rfBltip',
    buttons: [
        {
            className: 'toogle-items select-all btn btn-sm btn-info',
            text: '',
            attr: {
                "for-ul": "items"
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
        visible: true,
        data:'category'
    },
    {
        target: 4,
        visible: true,
        data: 'price',
        render: function (data, type, row) {
            let currencyLocal = new Intl.NumberFormat('da-DK', {
                style: 'currency',
                currency: 'DKK',
            });
            return currencyLocal.format(data);
        }

    },
    {
        target: 5,
        visible: false,
        data: 'options',

    },
    {
        target: 6,
        visible: false
    },
    {
        target: 7,
        visible: false
    },
    {
        target: 8,
        visible: true
    }

    ],
    initComplete: function () {
        this.api()
            .columns(3)
            .every(function () {
                let column = this;

                // Create select element
                let select = document.createElement('select');
                select.add(new Option(''));
                column.footer().replaceChildren(select);

                // Apply listener for user change in value
                select.addEventListener('change', function () {
                    var val = DataTable.util.escapeRegex(select.value);

                    column
                        .search(val ? '^' + val + '$' : '', true, false)
                        .draw();
                });

                // Add list of options
                column
                    .data()
                    .unique()
                    .sort()
                    .each(function (d, j) {
                        select.add(new Option(d));
                    });
            })
    }


});


// Array to track the ids of the details displayed rows
const detailItemsRows = [];

tblItems.on('click', 'tbody td.dt-control', function () {
    let tr = event.target.closest('tr');
    let row = tblItems.row(tr);
    let idx = detailItemsRows.indexOf(tr.id);

    if (row.child.isShown()) {
        tr.classList.remove('details');
        row.child.hide();

        // Remove from the 'open' array
        detailItemsRows.splice(idx, 1);
    }
    else {
        tr.classList.add('details');
        row.child(format(row.data())).show();

        // Add to the 'open' array
        if (idx === -1) {
            detailItemsRows.push(tr.id);
        }
    }
});

// On each draw, loop over the `detailRows` array and show any child rows
tblItems.on('draw', () => {
    detailItemsRows.forEach((id, i) => {
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
        let currencyLocal = new Intl.NumberFormat('da-DK', {
            style: 'currency',
            currency: 'DKK',
        });
        let str = `<li class="menu option list-group-item form-control" lid="${item.loyverse_id}" name="${item.name}" price="${item.price}">
            <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
            <span class="fs-6 fw-semibold">${item.name}</span>
            <span class="fs-6 float-end"> ${currencyLocal.format(item.price)}</span>
            </li>`
        optionsLines.push(str);
    });
    return '<ul class="options card list-group  overflow-auto max-list-5">' + optionsLines.join('') + '</ul>';
}
$("#btnPostItemsT").on("click", function() {
    let integrationId = $(hdfIntegrationId).val();
    let gfMenuId = $(txtGfMenuId).text();
    $("tr.item.has-issue,tr.item.is-invalid").each(function(key, value) {
        $(this).find(".spinner").toggleClass("visually-hidden");
        $(this).removeClass("has-issue");
        $(this).removeClass("is-invalid");

        let gfid = $(this).attr("id").substring(2);
        // $($(this).find("li.variant")).each(function(oKey, oVal) {
        //     $(this).removeClass("has-issue");
        //     $(this).removeClass("is-invalid");
        //     $(this).removeClass("is-valid");

        // })
        items[gfid].integration_id = integrationId;
        items[gfid].gf_menu_id = gfMenuId;
        let data = JSON.stringify(items[gfid]);

        //console.log(data);
        PostItem(data, this);
    })
});
function PostItem(data, elem) {
    /*
    integration_id =>hdfIntegrationId
    gf_id
    l_id
    name
    gf_menu_id =>txtGfMenuId
     */
    var settings = {
        "url": "/sessionservices/integration.php?q=postitem",
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
            $(elem).find("li.variant").addClass("is-valid");
            $($(elem).find(".spinner")).parent("td").addClass("is-valid");
            $(elem).addClass("is-valid");
        }
    };
    $.ajax(settings).fail(function(response) {
        console.log(response);
        $(elem).find(".spinner").toggleClass("visually-hidden");
        $(elem).addClass("is-invalid");
        $(elem).find("li.variant").addClass("is-invalid");
    });
}