$("#datesearch").datepicker({
    minDate: -1,
    maxDate: "+2M",
});

//$('#datesearch').mouseover(function () {
//    $('#datesearch_show').datepicker().show();
//});
//
//$('#datesearch_show').mouseleave(function(){
//    $("#datesearch_show").datepicker().hide();
//
//})

$('#datesearch_show').click(function(){return false;});

$("#datesearch_show").datepicker({
    onSelect: function (dateText) {
        if ( ! datesearch_url)
                return;
        dateText=dateText.split("-");
        var newDate=dateText[1]+"/"+dateText[0]+"/"+dateText[2];
        var url = datesearch_url.replace('{{d}}', new Date(newDate).getTime()/1000);        
        document.location = url;
    } 
});
