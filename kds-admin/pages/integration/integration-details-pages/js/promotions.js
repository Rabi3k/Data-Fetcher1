function loadLDisounts()
{           
    $("#dp-spinner").removeClass('visually-hidden');
    $($('#slDiscounts').find(".variable-amount")).empty();

    $.ajax({            
        url: '/sessionservices/promotions.php?q=lpromotions&iid=<?php echo $integration->Id ?>',
        dataType: 'json',
        type: 'GET',
        success: function(response) 
        {      
            //console.log(response);
            let discounts =response.data.VARIABLE_AMOUNT;        
            
            //console.log(discounts);
            if(discounts && discounts.length>0)
            {
                discounts.forEach(d => {
                    $($('#slDiscounts').find(".variable-amount")).append(`<option value="${d.id}">${d.name}</option>`)
                    if(d.name ==="Online Rabat")
                    {
                        $("#osc-new").attr("disabled",'true');
                    }
                });
                if(discountLoyveresId!==""){
                    $('#slDiscounts').val(discountLoyveresId);
                }
            }
            $("#dp-spinner").addClass('visually-hidden');
        },
        error: function(x, e) {
            $("#dp-spinner").addClass('visually-hidden');
        }
    });
}
function saveLDisounts()
{           
    $("#dp-spinner").removeClass('visually-hidden');
    let seletedVal =  $('#slDiscounts').val();
    let seletedTxt =  $('#slDiscounts option:selected').text();
    if(seletedVal === '0')
    {
        seletedVal=null;
    }
    var settings = {
        "url": "/sessionservices/promotions.php?q=lpromotions",
        "method": "POST",
        "data": JSON.stringify({
                'integrationId':parseInt('<?php echo $integration->Id ?>'),
                'gfMenuId':parseInt('<?php echo $gfMenu->menu_id ?>'),
                'discountId':seletedVal,
                'discountName':seletedTxt,
        }),
        success: function(response) 
        { 
            console.log(response);
            $("#dp-spinner").addClass('visually-hidden');
            discountLoyveresId = response.loyverse_id;
            loadLDisounts();
        },
        error: function(x, e) {
            console.log(e);
            console.log(x);
            $("#dp-spinner").addClass('visually-hidden');
        }
      };
      
      $.ajax(settings);
}
$(document).on('change','#slDiscounts',function(){
    //console.log($(this).val());
});
$(document).ready(function () {
    
    loadLDisounts();
    
                    
});
// var tblPromotions = $('#tblPromotions').DataTable({

//     ajax: {
//         url: "/sessionservices/promotions.php?q=gfpromotions&uid=<?php echo $integration->gfUid ?>&iid=<?php echo $integration->Id ?>",
//         dataType: 'json',
//         type: 'GET',
//     },
//     //data: jsonfile,
//     columns: [{
//         data: 'id'
//     },
//     {
//         data: 'name'
//     },
//     {
//         data: 'description'
//     },
//     {
//         data: 'outcomes[0]'
//     },
//     {
//         data: 'outcome_config[0].function'
//     },
//     {
//         data: 'outcome_config[0].discounts[0]'
//     },
//     {
//         data: 'updatedAt'

//     },
//     {
//         data: 'loyverseId',
//         visible: false

//     },
//     {
//         data: "discount",
//         render: function (data, type, row, meta) {

//             var $select = $("<select  id='" + row.id + "' class='ldiscounts' ></select>", {
//                 id: row[0] + "start",
//                 //value: LDiscounts
//             });
//             $.each(LDiscounts, function (k, v) {
//                 var $option = $("<option></option>", {
//                     text: v.name,
//                     value: v.id
//                 });
//                 //   if(d === v){
//                 //       $option.attr("selected", "selected");
//                 //   }
//                 $select.append($option);
//             });
//             return $select.prop("outerHTML");

//         }
//     },
    
//     {
        
//         visible: true,
//         data: function (row, type, val, meta) {
//             //console.log(type);

//             let retval = ' <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>';
//             if (row.loyverseId === null) {
//                 retval = retval + "<span class='is-invalid'></span>";
//             } else {
//                 retval = retval + "<span class='is-valid'></span>";
//             }
//             return retval;

//         }
//     },
//     ],
//     // columnDefs: [{
//     //     targets: 8,
//     //     visible: true,
        
//     // }],
//     createdRow: function (row, data, dataIndex) {
//         $(row).addClass("menu promotion");
//         if (data.loyverseId === null) {
//             $(row).addClass('is-invalid');
//         } else {
//             $(row).addClass('is-valid');
//         }
//     }
// });
// $("table").on("change","select.ldiscounts",function(){
//     console.log($(this).attr("id"));
//     console.log($(this).val());
//     promotionsArrays[$(this).attr("id")] = $(this).val();
// })

// let promotionsArrays =[];