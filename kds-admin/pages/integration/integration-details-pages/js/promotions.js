var tblPromotions = $('#tblPromotions').DataTable({

    ajax: {
        url: "/sessionservices/promotions.php?uid=<?php echo $integration->gfUid ?>&iid=<?php echo $integration->Id ?>",
        dataType: 'json',
        type: 'GET',
    },
    //data: jsonfile,
    columns: [{
        data: 'id'
    },
    {
        data: 'name'
    },
    {
        data: 'description'
    },
    {
        data: 'outcomes[0]'
    },
    {
        data: 'outcome_config[0].function'
    },
    {
        data: 'outcome_config[0].discounts[0]'
    },
    {
        data: 'updatedAt'

    },
    {
        data: 'loyverseId',
        visible: false

    },
    {
        data: "discount",
        render: function (data, type, row, meta) {

            var $select = $("<select id='" + row.id + "'></select>", {
                id: row[0] + "start",
                value: LDiscounts
            });
            $.each(LDiscounts, function (k, v) {
                var $option = $("<option></option>", {
                    text: v.name,
                    value: v.id
                });
                //   if(d === v){
                //       $option.attr("selected", "selected");
                //   }
                $select.append($option);
            });
            return $select.prop("outerHTML");

        }
    },
    
    {
        
        visible: true,
        data: function (row, type, val, meta) {
            //console.log(type);

            let retval = ' <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>';
            if (row.loyverseId === null) {
                retval = retval + "<span class='is-invalid'></span>";
            } else {
                retval = retval + "<span class='is-valid'></span>";
            }
            return retval;

        }
    },
    ],
    // columnDefs: [{
    //     targets: 8,
    //     visible: true,
        
    // }],
    createdRow: function (row, data, dataIndex) {
        $(row).addClass("menu promotion");
        if (data.loyverseId === null) {
            $(row).addClass('is-invalid');
        } else {
            $(row).addClass('is-valid');
        }
    }
});
