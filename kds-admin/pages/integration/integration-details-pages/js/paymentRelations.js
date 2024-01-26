function saveLPaymentRelations(gfPayment, loyverseId, spinerId) {
    let $spinner = $(`#${spinerId}`);
    $spinner.removeClass('visually-hidden');
    var settings = {
        "url": "/sessionservices/payments.php",
        "method": "POST",
        "data": JSON.stringify({
            'integrationId': parseInt('<?php echo $integration->Id ?>'),
            'gfPayment': gfPayment,
            'loyverseId': loyverseId
        }),
        success: function (response) {
            console.log(response);
            $spinner.addClass('visually-hidden');
            showAlert(`${response.GfPayment} Saved Successfully`);
            //loadLDisounts();
        },
        error: function (x, e) {
            console.log(e);
            console.log(x);
            showAlert(`${response.GfPayment} Not Saved!`,true);
            $spinner.addClass('visually-hidden');
        }
    };

    $.ajax(settings);

}
function loadLPaymentRelations() {
    let $select = $('#slPayments');
    $select.empty();
    var $option = $("<option></option>", {
        text: `(select one)`,
        value: `0`
    });
    $select.append($option);

    $.each(lpayments, function (k, v) {
        $option = $("<option></option>", {
            text: v.name,
            value: v.id
        });
        // if (row.LoyveresId === v.id) {
        //     $option.attr("selected", "selected");
        // }
        $select.append($option);
    });
    $select.val('<?php echo $payemntRelations->LoyveresId??0 ?>');
}
let lpaymentsLoaded = false;
let lpayments = [];
var tblpaymentRelations = $('#tblpaymentRelations').DataTable({
    dom: 't',
    ajax: {
        url: "/sessionservices/payments.php?iid=<?php echo $integration->Id ?>",
        dataType: 'json',
        type: 'GET',
    },
    columns: [{
        data: 'GfPayment'
    },
    {
        data: "LoyveresId",
        searchable: false,
        orderable:false,
        render: function (data, type, row, meta) {
            if (lpaymentsLoaded === false) {
                lpaymentsLoaded = true;
                lpayments = row.lpayments;
            }
            //console.log(`${row.GfPayment} => ${row.LoyveresId}`);
            var $select = $(`<select  id='sl-${row.GfPayment}'class="form-select form-select-sm mb-1 slPayments" aria-label=".form-select-lg slPayments"></select>`, {
                id: row.GfPayment + "start",
                //value: LDiscounts
            });
            var $option = $("<option></option>", {
                text: `(select one)`,
                value: `0`
            });
            $select.append($option);
            $.each(row.lpayments, function (k, v) {
                $option = $("<option></option>", {
                    text: v.name,
                    value: v.id
                });
                if (row.LoyveresId === v.id) {
                    $option.attr("selected", "selected");
                }
                $select.append($option);
            });
            return $select.prop("outerHTML");

        }
    },
    {
        visible: true,
        orderable: false,
        searchable: false,
        render: function (data, type, row, meta) {
            return `<span class="spinner spinner-border text-secondary spinner-border-sm float-end visually-hidden" id="prSpinner-${row.GfPayment}" role="status">
            </span>`;
        }
    }
    ],

    createdRow: function (row, data, dataIndex) {
        $(row).addClass("payments ");
    }
});
tblpaymentRelations.on('draw', function () {
    // your code here
    loadLPaymentRelations();
});
$(document).ready(function () {
    $(document).on("click", "#btnSavePayments", function () {
        $(tblpaymentRelations.rows().nodes()).each((k, v) => {
            let $spinnerId = $(v).find(".spinner").attr("id");
            let $select = $(v).find("select");
            let $gfPayment = tblpaymentRelations.row(v).data().GfPayment;
            let $loyverseId = $select.val();
            saveLPaymentRelations($gfPayment, $loyverseId, $spinnerId);
            //console.log(`${$gfPayment} =>${$loyverseId} =>${$spinnerId}`);
        });
    });
    $(document).on("click", "#btnSaveDefaultPayment", function () {
        let $spinnerId = $("#pr-spinner").attr("id");
        let $select = $('#slPayments');
        let $gfPayment = "default";
        let $loyverseId = $select.val();
        saveLPaymentRelations($gfPayment, $loyverseId, $spinnerId);
        //console.log(`${$gfPayment} =>${$loyverseId} =>${$spinnerId}`);
    });

});