function saveVideoPoster(id, project) {
    var selectedIndex = $('#image-picker-' + id).val();
    project = typeof project !== 'undefined' ? project : "";
    url = "/" + project + "video/set-index-thumb?id=" + id + "&index=" + selectedIndex;
    $.post(
        url
    ).success(function(data){
        if (data.result == true) {
            var d = new Date();
            var $t = $("#video_thumb_"+id);
            $t.attr("src", $t.attr("src")+'&t='+d.getTime());
            $('#show_video_thumbs_'+id).modal('hide');
            acc.alert.success('Картинка успешно изменена.')
        } else {
            acc.danger('Ошибка смены статуса. Попробуйте позднее');
        }
    }).fail(function(){
        acc.alert.danger('Ошибка выполнения запроса');
    });
}

function ...ApkAlert(message,type) {
    message = typeof message !== 'undefined' ? message : "";
    type = typeof type !== 'undefined' ? type : 'warning';

    if (message != "") {
        $('#alert-'+type+' .alert-text').text(' ' + message);
        $('.alert-holder').show();
        $('#alert-'+type).slideDown(300);
        setTimeout(function(){
            $('#alert-'+type).slideUp(300,function(){
                $('.alert-holder').hide();
                $('#alert-'+type+' .alert-text').text('');
            });
        },3000);
    }
}

function sortGrid(e){
    e.preventDefault();
    var field = $('#sort-grid-field').val();
    var dir = $('#sort-grid-dir').val();
    if (dir != '-') {
        dir = '';
    }
    insertParam('sort',dir+field);
}

function insertParam(key,value)
{
    key = encodeURI(key); value = encodeURI(value);
    var s = document.location.search;
    var kvp = key+"="+value;
    var r = new RegExp("(&|\\?)"+key+"=[^\&]*");
    s = s.replace(r,"$1"+kvp);
    if(!RegExp.$1) {s += (s.length>0 ? '&' : '?') + kvp;};
    //again, do what you will here
    s = removeURLParameter(s,'page');
    document.location.search = s;
}

function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts= url.split('?');
    if (urlparts.length>=2) {
        var prefix= encodeURIComponent(parameter)+'=';
        var pars= urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i= pars.length; i-- > 0;) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }
        url= urlparts[0]+'?'+pars.join('&');
        return url;
    } else {
        return url;
    }
}

function filterGrid(element) {
    var field = $(element).attr('data-searchField');
    var value = $(element).val();

    console.log(field);
    console.log(value);
    insertSearch(field,value);
}

function insertSearch(field,fieldValue)
{
    var s = document.location.search;

    key = encodeURI('filterField');
    value = encodeURI(field);
    var kvp = key+"="+value;
    console.log(kvp);
    var r = new RegExp("(&|\\?)"+key+"=[^\&]*");
    console.log(r);
    s = s.replace(r,"$1"+kvp);
    console.log(s);
    console.log(RegExp.$1);
    if(!RegExp.$1) {
        console.log(321);
        s += (s.length>0 ? '&' : '?') + kvp;
    };
    console.log(s);

    key = encodeURI('filterValue');
    value = encodeURI(fieldValue);
    kvp = key+"="+value;
    r = new RegExp("(&|\\?)"+key+"=[^\&]*");
    s = s.replace(r,"$1"+kvp);
    if(!RegExp.$1) {s += (s.length>0 ? '&' : '?') + kvp;};

    console.log(s);

    //again, do what you will here
//    s = removeURLParameter(s,'page');
//    document.location.search = s;
}
