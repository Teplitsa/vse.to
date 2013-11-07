//var iframe = null

$(function(){
    
    $('#parsing-fill-btn').click(function(){
        
        var url = prompt("Введите адрес страницы события для автоматического заполенния полей");
        
        if(url != null)
        {
            var data = praseUrl(url);
        }
        return false;
    });
});


function praseUrl(url)
{
//    if(iframe == null)
//    {
//        iframe = $('<iframe/>').attr('id', 'parsing-frame').css('display','none').appendTo('body').ready(iframeLoaded);
//    }
//    iframe.attr('src',url);

    $.ajax({
        'dataType': "json",
        'method': 'post',
        'url': 'ajax_parsing',
        'data': {'parseurl': url},
        'success': function(response, textStatus, jqXHR ){
//            console.log(response.data.parseurl);

            if(response.data.status == 'notsupported')
            {
                alert('Данный сайт не поддерживается!');
            }
            else
            {
                var eventData = response.data.event;
                    
                var fullDesc = eventData.desc + "\n\nАнонс с "+url;

                $('#id-product1-caption').val(eventData.title);
                $('#id-product1-datetime').val(eventData.time);
                $('#id-product1-description').val(fullDesc);
                $("#id-product1-format option").filter(function() {
                    return $(this).text() == eventData.format;
                }).prop('selected', true);
            }
        },
        'error': function(jqXHR, textStatus, errorThrown ){
            alert('Произошла ошибка!');
        }
    });

}
//
//function iframeLoaded()
//{
//    var time = $('#sidebar>time', iframe);
//    var result = iframe.contents().find('#sidebar>time').html();
//
//    console.log(result);
//}