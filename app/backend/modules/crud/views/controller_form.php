<?
    include dirname(__FILE__) . "/_menu.php";
?>

<div class="error"></div>
<div style="margin:10px;">
    <button type="button" id="saveForm" class="btn btn-large btn-primary" data-loading-text="Обработка...">
        Сохранить</button>
</div>


<script>
   $(function(){

        var inc =1;
       $('.addAction').on('click',function(e){
           $('.list_actions').append('<li><div style="width: 30px;display: inline-block;"><a href="javascript:;""><i class="icon-remove"></i></a></div> '+
                   ' <input type="text" name="action['+inc+']" class="span11"> '+
                   '</li>');
           inc++;
       })

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
                   url:'/admin/crud/controller/save',
                   data: dataJson, type:'post', dataType:'json',
                   success: function(m){
                       if (m.success == false) {
                           $('.error').html('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Error!</h4><ul></ul></div>');
                           $.each(m.errors, function (key, item) {
                               $('.error ul').append('<li>' + item + '</li>');
                           });
                       } else if (m.success == true) {
                           $('.error').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Success!</h4>Контроллер сохранен<ul></ul></div>')
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
   });
</script>
<blockquote>
<div class="form row-fluid">


        <div class="span6">

            <div class="row-fluid">

                <div class="span6">

                    <div class="b-head"><label>Название контроллера [a-z]</label></div>
                    <input type="text" name="name" class="span12" value="">

                    <div class="b-head"><label><i class="icon-leaf"></i> Layout по умолчанию</label></div>
                    <input type="text" name="layout" class="span12" value="//main">


                </div>

                <div class="span6">
                    <div class="b-head"><label><i class="icon-cog"></i> Опции</label></div>
                    <label class="label"><input type="checkbox" name="overwrite" value="1"> перезапись файла</label>
                </div>

            </div>

            <div>
                <div class="b-head"><label><i class="icon-folder-close"></i> Путь куда сохранить контроллер</label></div>
                <input type="text" name="path" class="span12" value="/app/common/models/">
            </div>

            <div class="row-fluid">
                <div class="b-head"><label><i class="icon-th-list"></i> Создать методы и представления к ним</label></div>
                <button class="btn circle addAction"><i class="icon-plus"></i></button>
                <br><!--Отметьте флешки где нужно создать view для этого метода--><br>
                <ul class="unstyled list_actions">
                    <li><div style="width: 32px;display: inline-block;"></div><input type="text" disabled="disabled" class="span11" value="index"></li>

                </ul>
            </div>



        </div>



</div></blockquote>