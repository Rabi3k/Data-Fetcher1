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