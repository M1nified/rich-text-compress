'use strict';
jQuery(function(){
    var editor = jQuery("#rich_text_compress_editor");
    var editorform = jQuery("#rich_text_compress_editor-form");
    var editorbox = jQuery("#rich_text_compress_editor-container").slideUp();
    var show = function(){editorbox.slideDown('fast');}
    var hide = function(){editorbox.slideUp();}
    var getContent = function(widget_id,plugin_path){
        var promise = new Promise(function(resolve,reject){
            jQuery.get(plugin_path+'ajax_content.php?func=get&widget_id='+widget_id).then(function(result){
                resolve(result);
            });
        });
        return promise;
    }
    var setContent = function(id,plugin_path,content){
        var promise = new Promise(function(resolve,reject){
            jQuery.post(plugin_path+'ajax_content.php?func=set&id='+id,{
                content : content
            }).then(function(result){
                console.log(result);
            })
        })
        return promise;
    }
    jQuery(document).on('click','.rich_text_compress-button-editor',function(){
        var form = jQuery(this).closest('form')[0];
        var widget_id = form.widget_id.value;
        var plugin_path = form.plugin_path.value;
        editorform[0].plugin_path.value = plugin_path;
        show();
        getContent(widget_id,plugin_path).then(function(result){
            console.log(result);
            editorform[0].store_id.value = result.Id;
            editor.val(result.Value);
            jQuery(".rich_text_compress-input-contentid",form).val(result.Id);
            console.log(form.content)//.value = result.Id;
            jQuery(form.savewidget).click();
        })
    })
    editorform.on('submit',function(evt){
        evt.preventDefault();
        var serialized = jQuery(this).serializeArray();
        var content = (function(serialized){
            for(var elem in serialized){
                if(serialized[elem].name === 'rich_text_compress_editor') return serialized[elem].value;
            }
        })(serialized);
        console.log(this,content);
        setContent(editorform[0].store_id.value,editorform[0].plugin_path.value,content).then(function(){

        })
    })
    jQuery("#rich_text_compress_editor-submit").on('click',function(){
        editorform.submit();
    })
})