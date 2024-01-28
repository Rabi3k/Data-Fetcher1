
const tblComanies = new DataTable('#tblComanies', {
    columns: [{
        className: 'dt-control',
        orderable: false,
        data: null,
        defaultContent: ''
    },
    {
        data: 'id'
    },
    {
        data: 'name'
    },
    {
        data: 'restaurants',
        visible: false
    },
    {
        orderable: false,
        data: null,
        render: function (data, type, row, meta) {
            return `<input class="form-check-input cb-cSelect" type="checkbox" value="${data.id}"/>`;
        }
    },
    ],
    "createdRow": function (row, data, dataIndex) {
        const exists = $UserRestaurantsIds.some(e => e.companyId === parseInt(data.id)); //$UserComanyIds.includes(parseInt(data.id))
        if (exists) {
            $(row).addClass('selected');
            $(row).find(".cb-cSelect").prop("checked", true);
        }
    }
});

tblComanies.on('change', '.cb-cSelect', function (e) {
    $(this).parents('tr').toggleClass('selected');
    let companyId = parseInt($(this).val());
    $UserRestaurantsIds = addOrRemoveObject($UserRestaurantsIds, {
        "companyId": companyId,
        "restaurantId": null,
    });
    let tr = $(this).parents('tr');
    if (!$(tr).hasClass('selected')) {
        let row = tblComanies.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
        }
    }
});
tblComanies.on('change', '.cb-rSelect', function (e) {
    $(this).parents('li').toggleClass('active');
    let companyId = parseInt($(this).parents('ul').attr("id"));
    let restaurantId = parseInt($(this).val());
    $UserRestaurantsIds = addOrRemoveObject($UserRestaurantsIds, {
        "companyId": companyId,
        "restaurantId": restaurantId,
    });
});
restaurantsSeleted = [];
const addOrRemove = (array, item) => {
    const exists = array.includes(item)
    if (exists) {
        return array.filter((c) => {
            return c !== item
        })
    } else {
        const result = array
        result.push(item)
        return result
    }
}
const addOrRemoveObject = (array, item) => {
    if (array.some(e => e.companyId === item.companyId &&
        (
            (e.restaurantId === null && item.restaurantId === null) ||
            (e.restaurantId !== null && e.restaurantId.includes(item.restaurantId))
        ))) {
        if (item.restaurantId === null) {
            return array.filter((e) => {
                return e.companyId !== item.companyId || !e.restaurantId === item.restaurantId
            });
        }
        const result = array;
        const cresult = array.find(e => e.companyId === item.companyId);
        cresult.restaurantId = cresult.restaurantId.filter((e) => {
            return e != item.restaurantId
        });
        if (cresult.restaurantId.length === 0) {
            cresult.restaurantId = null;
        }
        return result;
    } else if (array.some(e => e.companyId === item.companyId)) {
        const result = array;
        const cresult = array.find(e => e.companyId === item.companyId);
        if (cresult.restaurantId === null) {
            cresult.restaurantId = [];
        }
        if (item.restaurantId !== null) {
            cresult.restaurantId.push(item.restaurantId)
        } else {
            return array.filter((e) => {
                return e.companyId !== item.companyId
            });
        }

        return result;
    } else {
        const result = array;
        const cresult = {
            "companyId": item.companyId,
            "restaurantId": null,
        };
        if (item.restaurantId !== null) {
            cresult.restaurantId = [];
            cresult.restaurantId.push(item.restaurantId)
        }
        result.push(cresult);
        return result;
    }
}
const addOrIgnore = (array, item) => {
    const exists = array.includes(item)
    if (!exists) {
        const result = array
        result.push(item)
        return result
    }
}
tblComanies.on('click', 'td.dt-control', function (e) {
    e.preventDefault();
    let tr = e.target.closest('tr');
    let row = tblComanies.row(tr);

    if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
    } else if ($(tr).hasClass('selected')) {
        // Open this row
        row.child(format(row.data().restaurants, row.data().id)).show();
    }
});

function format(r, c) {
    rests = JSON.parse(r);
    let liRests = [];

    rests.forEach((v) => {
        //.includes(parseInt(data.id)
        const exists = $UserRestaurantsIds.some(e => e.companyId === parseInt(c) && e.restaurantId !== null && e.restaurantId.includes(parseInt(v.id)))

        let lirest =
            `<li class="list-group-item ${exists ? "active" : ""} ">
                    <div class="row">
                        <div class="col"></div>
                        <div class="col">
                            ${v.alias}
                        </div>
                        <div class="col">
                            ${v.city}
                        </div>
                        <div class="col">
                        <input class="form-check-input cb-rSelect" type="checkbox" value="${v.id}" ${exists ? "checked" : ""} />
                        </div>
                    </div>
                </li>`;
        liRests.push(lirest);
    });
    return (`<ul class="list-group list-group-info bg-light text-dark" id="${c}">${liRests.join("")}</ul>`);
}

$("#btn-save-userRelations").click(function (e) {
    let $userRelations = {
        'userId': parseInt("<?php echo $lUser->id; ?>"),
        'relations': $UserRestaurantsIds
    };
    var settings = {
        "url": "/sessionservices/users.php?q=set-user-relations",
        "method": "PUT",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json"
        },
        "data": JSON.stringify($userRelations),
        "success": function (data) {
            showAlert("User-relations saved successfully");
        }
    };

    $.ajax(settings).done(function (response) {
        console.log(response);

    });

    //showAlert(JSON.stringify($userRelations), true);
});
//  document.querySelector('#button').addEventListener('click', function () {
//      alert(tblComanies.rows('.selected').data().length + ' row(s) selected');
//  });