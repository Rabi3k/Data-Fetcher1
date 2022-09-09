var ActiveOrderIds =[];

function PrintElem(id)
{
        var mywindow = window.open(rootpath+'/kds/order.php?id='+id, 'PRINT', 'width=50px');
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10
        
        
        setTimeout(() => {
            mywindow.print();
            mywindow.close();
        }, "1000");
        return true;
}

function updateTime(){
    $('#time').html(Date(timestamp));
    timestamp++;
  }
  $(function(){
    setInterval(updateTime, 1000);
  });

  
function myFunction () {
    //console.log('Executed!');
    var now = new Date();
    var later = new Date(now.getMinutes()+10);

   
    $("input[name='OrderDate']").each(function(){
      if(now < new Date($(this).val())){
          $($(this).parent()).toggleClass('bg-danger text-white');
      }
      else if(later < new Date($(this).val())){
         $($(this).parent()).toggleClass('bg-warning');
     }

          })


 $.getJSON('../api/nyorders').then(r=>
 {
    let toRemove = ActiveOrderIds.filter(x => !r.includes(x));
 let toAdd = r.filter(x => !ActiveOrderIds.includes(x));
 if(toRemove && toRemove.length>0){
    toRemove.forEach(x=>{$('#accordion_'+x).remove(); });
 }
 
 if(toAdd && toAdd.length>0){
    toAdd.forEach(x=>{
      $.get('create-card.php?id='+x,function(resp){$('#orderCards').append(resp)});
      playSound();
   });
 }
 ActiveOrderIds = r;
 });
 
 
}