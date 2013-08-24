<?
include dirname(__FILE__) . "/_menu.php";
?>


<script>
$(function(){

    $('.list_actions').on('click','.icon-remove',function(e){
        $(this).closest('li').remove();
    })

    $('#saveForm').on('click',function(e){
        var _this = $(this) , jsonData = {};
        if(!_this.hasClass('disabled')){
            var bnt_text =  _this.text();
            _this.addClass('disabled').attr('disabled','disabled').text(_this.attr('data-loading-text'));
            var dataJson = jsonForm('.form');

            $.ajax({
                url:'/admin/crud/module/save',
                data: dataJson, type:'post', dataType:'json',
                success: function(m){
                    if (m.success == false) {
                        $('.error').html('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Error!</h4><ul></ul></div>');
                        $.each(m.errors, function (key, item) {
                            $('.error ul').append('<li>' + item + '</li>');
                        });
                    } else if (m.success == true) {
                        $('.error').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Success!</h4>Модуль сохранен<ul></ul></div>')
                        $.each(m.list, function (key, item) {
                            $('.error ul').append('<li>' + item + '</li>');
                        });
                    }
                    _this.removeClass('disabled').removeAttr('disabled').text(bnt_text);
                },
                error: function(){
                    _this.removeClass('disabled').removeAttr('disabled').text(bnt_text);
                }
            });
        }
    });

    $('.list_path a').on('click',function(e){
        $('input[name="path"]').val( $(this).text() );
    });


});
</script>

<div class="error"></div>

<div style="margin:10px;">
    <button type="button" id="saveForm" class="btn btn-large btn-primary" data-loading-text="Обработка...">Сохранить</button>
</div>

<blockquote>
<div class="form row-fluid">


<div class="span6">

    <div class="b-head"><label>Название модуля [a-z]</label></div>
    <input type="text" name="name" class="span12" value="">

    <div class="b-head"><label><i class="icon-folder-close"></i> Путь куда сохранить модуль</label></div>

    <div class="btn-group fr" style="margin: -1px 2px 0 0;">
        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
            <span class="caret"></span>
        </a>

        <ul class="dropdown-menu list_path">
            <li><a>/app/backend/modules</a></li>
            <li class="divider"></li>
            <li><a>/app/frontend/modules</a></li>
        </ul>
    </div>

    <input type="text" name="path" class="span11 fl" value="/app/frontend/modules">

</div>




</div>
</blockquote>