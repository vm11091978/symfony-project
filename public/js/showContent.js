$(function() {
    console.log('Привет, это старый js ))');

    init_get_new();
    init_post_new();
});

function init_get()
{
    $('a.ajaxArticleBodyByGet').one('click', function(){
        var contentId = $(this).attr('data-contentId');
        console.log('ID статьи = ', contentId);
        showLoaderIdentity();
        $.ajax({
            url:'index.php?route=ajax/show&articleId=' + contentId,
            dataType: 'text'
        })
        .done (function(obj){
            hideLoaderIdentity();
            console.log('Ответ получен');
            $('li.' + contentId).append(obj);
        })
        .fail(function(xhr, status, error){
            hideLoaderIdentity();

            console.log('ajaxError xhr:', xhr); // выводим значения переменных
            console.log('ajaxError status:', status);
            console.log('ajaxError error:', error);

            console.log('Ошибка соединения при получении данных (GET)');
        });

        return false;
    });  
}

function init_post()
{
    $('a.ajaxArticleBodyByPost').one('click', function(){
        var content = $(this).attr('data-contentId');
        showLoaderIdentity();
        $.ajax({
            url:'index.php?route=ajax/show',
            dataType: 'json',
            data: { articleId: content },
            method: 'POST'
        })
        .done (function(obj){
            hideLoaderIdentity();
            console.log('Ответ получен', obj);
            $('li.' + content).append(obj);
        })
        .fail(function(xhr, status, error){
            hideLoaderIdentity();

            console.log('Ошибка соединения с сервером (POST)');
            console.log('ajaxError xhr:', xhr); // выводим значения переменных
            console.log('ajaxError status:', status);
            console.log('ajaxError error:', error);
        });

        return false;
    });  
}

function init_get_new()
{
    $('a.ajaxByGet').one('click', function() {
        var articleId = $(this).attr('data-articleId');
        showLoaderIdentity(articleId);

        $.ajax({
            type: "GET",
            url: '/index.php/ajax/get/' + articleId,
            dataType: 'json',
            // обработчик успешного ответа
            success: function(response, textStatus, jqXHR) {
                console.log('Получили ответ:', response);

                // Сделаем небольшую задержку скрипта, чтобы увидеть работу лоадера
                setTimeout(() => {
                    hideLoaderIdentity();
                    $('li.' + articleId + ' .ajax-load').hide();
                    $('li.' + articleId).append(response);
                }, 1000 );
            },
            // обработчик ошибки
            error: function(xhr, status, error) {
                hideLoaderIdentity();

                console.log('ajaxError xhr:', xhr);
                console.log('ajaxError status:', status);
                console.log('ajaxError error:', error);
                // соберем самое интересное в переменную
                var errorInfo = 'Ошибка выполнения запроса: '
                        + '\n[' + xhr.status + ' ' + status   + ']'
                        +  ' ' + error + ' \n '
                        + xhr.responseText
                        + '<br>'
                        + xhr.responseJSON;

                console.log('ajaxError:', errorInfo);
                // alert(errorInfo); // если требуется и то на экран
            }
        });

        return false;
    });
}

function init_post_new()
{
    $('a.ajaxByPost').one('click', function() {
        var articleId = $(this).attr('data-articleId');
        showLoaderIdentity(articleId);

        $.ajax({
            type: "POST",
            url: '/index.php/ajax/post', // адрес отправки (url)
            dataType: 'json',
            data: { articleId: articleId },
            // обработчик успешного ответа
            success: function(response, textStatus, jqXHR) {
                console.log('Получили ответ:', response);

                // Сделаем небольшую задержку скрипта, чтобы увидеть работу лоадера
                setTimeout(() => {
                    hideLoaderIdentity();
                    $('li.' + articleId + ' .ajax-load').hide();
                    $('li.' + articleId).append(response);
                }, 1000 );
            },
            // обработчик ошибки
            error: function(xhr, status, error) {
                hideLoaderIdentity();

                console.log('ajaxError xhr:', xhr);
                console.log('ajaxError status:', status);
                console.log('ajaxError error:', error);
                // соберем самое интересное в переменную
                var errorInfo = 'Ошибка выполнения запроса: '
                        + '\n[' + xhr.status + ' ' + status   + ']'
                        +  ' ' + error + ' \n '
                        + xhr.responseText
                        + '<br>'
                        + xhr.responseJSON;

                console.log('ajaxError:', errorInfo);
                // alert(errorInfo); // если требуется и то на экран
            }
        });

        return false;
    });
}
