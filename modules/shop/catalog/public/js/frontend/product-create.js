$(function(){
    
    $('#parsing-fill-btn').click(function(){
        
        var url = prompt('Введите адрес страницы события для автоматического заполенния полей.\n\nВсе поля будут заполнены автоматически, кроме полей "лектор"и "площадка"');
        
        if(url != null)
        {
            var data = praseUrl(url);
        }
        return false;
    });
    
    $('#id-product1').xtautosave();
});


function praseUrl(url)
{
    $.ajax({
        'dataType': "json",
        'method': 'post',
        'url': 'ajax_parsing',
        'data': {'parseurl': url},
        'success': function(response, textStatus, jqXHR ){

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
                $('#image_url').val(eventData.image_url);
                $('#id-product1-description').val(fullDesc);
                $("#id-product1-format option").filter(function() {
                    return $(this).text() == eventData.format;
                }).prop('selected', true);
                
                
                // Set invalid fields
                $('#lecturer_name').addClass('invalid');
                $('#place_name').addClass('invalid');
            }
        },
        'error': function(jqXHR, textStatus, errorThrown ){
            alert('Произошла ошибка!');
        }
    });

}