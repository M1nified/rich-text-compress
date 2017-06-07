<?php namespace rich_text_compress;
/**
 * Tutaj znajduje sie tresc wyswietlana w ustawieniach modulu
 **/
?>

<?php 

// include_once(realpath(__DIR__.'variables.php'));

global $wpdb;

global $db_table;

$widget_rtc_options = get_option('widget_rich_text_compress_widget');
// print_r($widget_rtc_options);

// print_r($_POST);
$mode = isset($_GET['mode']) ? $_GET['mode'] : null;
$mode = isset($_POST['mode']) ? $_POST['mode'] : $mode;
// var_dump($mode);
if(isset($mode)){
    if(isset($_POST['content'])){
        $content = \stripslashes($_POST['content']);
    }
    if(isset($_POST['DisplayOn'])){
        $display_on = $_POST['DisplayOn'];
        $display_on_list = explode(',',$display_on);
        $display_on = [];
        foreach($display_on_list as $id){
            $display_on[$id] = true;
        }
        $display_on = array_keys($display_on);
        $display_on = '!'.implode('!',$display_on).'!';
    }
    if($mode==='edit' && $_POST['rtc_id'] != ''){
        // $display_on = "!".str_replace(",","!",$_POST['DisplayOn'])."!";
        //Zapisywanie wprowadzonych zmian
        echo 1;
        $wpdb->update($db_table,[
            'Value' => $content,
            'DisplayOn' => $display_on
        ],[
            'Id' => $_POST['rtc_id']
        ]);
    }elseif($mode === 'add' && $_POST['WidgetId'] != ''){
        // $display_on = "!".preg_replace(",","!",$_POST['DisplayOn'])."!";
        echo 2;
        $wpdb->insert($db_table,[
            'Type' => 'content',
            'Value' => $content,
            'WidgetId' => $_POST['WidgetId'],
            'DisplayOn' => $display_on
        ]);
    }elseif($mode === 'delete' && $_GET['item_id']){
        global $db_table;
        echo 3;
        $wpdb->delete(
            $db_table,
            [
                'Id'    =>  $_GET['item_id']
            ]
        );
    }elseif($mode === 'apply_titles'){
        global $db_table;
        echo 4;
        foreach ($widget_rtc_options as $widget_id => $widget_options) {
            // print_r($widget_options);
            $widget_widget_id = "rich_text_compress_widget-{$widget_id}";
            $exists = $wpdb->get_var("SELECT count(`Value`) FROM `{$db_table}` WHERE Type = 'title' AND WidgetId = '$widget_widget_id';");
            echo $exists;
            if($exists != 0) continue;
            $wpdb->insert(
                $db_table,
                [
                    'Type' => 'title',
                    'Value' => $widget_options['title'],
                    'WidgetId' => $widget_widget_id,
                    'DisplayOn' => null
                ]
            );
        }
    }
}

$widget_id = isset($_GET['widget_id']) ? $_GET['widget_id'] : null;
$widgets = $wpdb->get_results("SELECT DISTINCT `WidgetId` FROM {$db_table} ORDER BY `WidgetId`");
$content_filter = isset($_GET['content_filter']) ? $_GET['content_filter'] : '';
echo "<h1>Rich Text Compress</h1>";
echo "<form method=\"GET\" action=\"#\">";
echo "<input type=\"hidden\" name=\"page\" value=\"{$_GET['page']}\">";
echo "<p><b>Content filter: </b><input type=\"text\" name=\"content_filter\" value=\"{$content_filter}\" placeholder=\"Optional content filter\"></p>";
echo "<p><b>Instance to edit: </b><select id=\"rtc-select-module\" name=\"widget_id\">";
if($widget_id == '') echo '<option disabled selected></option>';
foreach ($widgets as $key => $widget) {
    $selected = $widget->WidgetId === $widget_id ? 'selected' : '';
    $widget_number = str_replace('rich_text_compress_widget-','',$widget->WidgetId);
    $title = isset($widget_rtc_options[$widget_number]) ? $widget_rtc_options[$widget_number]['title'] : NULL;
    echo "<option value=\"{$widget->WidgetId}\" {$selected}>{$widget->WidgetId} {$title}</option>";
}
echo '</select></p>';
echo '<p><input type="submit" class="button button-primary" value="Go to"></p>';
echo '</form><hr>';
if($widget_id != ''){
    echo "<h2>Edit: {$widget_id}</h2>";
    echo '<a href="#rtc_add" class="button">Add new content</a>';
    $widgets = $wpdb->get_results("SELECT `Id`,`Type`,`Value`,`WidgetId`,`DisplayOn`
    FROM {$db_table}
    WHERE WidgetId = '{$widget_id}' AND `Type` = 'content' AND `Value` LIKE '%{$content_filter}%';");
    // print_r($widgets);
    foreach ($widgets as $key => $widget) {
        $widget_number = str_replace('rich_text_compress_widget-','',$widget->WidgetId);
        $uniqid = uniqid();
        echo "<form method=\"POST\" action=\"#\">";
        echo "<h3>({$widget_number}) ({$widget->Id})</h3>";
        echo "<p>wp_rtc_content-&gt;id = {$widget->Id}<p>";
        echo '<p>';
        wp_editor($widget->Value,'content-'.$widget->Id,[
            "textarea_name" => 'content'
        ]);
        echo '</p>';
        $display_on = $widget->DisplayOn;
        $display_on = preg_replace('/(^,|,$)/','',str_replace("!",",",$display_on));
        echo "<p><label style=\"display:flex;align-items:center;\"><b>Display on: </b><input class=\"rtc-displayon\" type=\"text\" name=\"DisplayOn\" value=\"{$display_on}\" style=\"flex-grow:2;\" data-group-id=\"{$uniqid}\"></label></p>";
        echo "<p>";
        echo "<button type=\"button\" class=\"button button-primary rtc-change-list\" data-group-id=\"{$uniqid}\">Select pages</button>";
        $url_delete = add_query_arg([
            'mode'      =>  'delete',
            'item_id'   =>  $widget->Id
        ]);
        echo " <a href=\"{$url_delete}\" class=\"button\">Remove</a>";
        echo "</p>";
        echo "<p class=\"rtc-post-list-spot\" data-group-id=\"{$uniqid}\"></p>";
        echo "<p><input type=\"submit\" class=\"button button-primary widget-control-save right\"></p><div class=\"clear\"></div>";
        echo "<input type=\"hidden\" name=\"rtc_id\" value=\"{$widget->Id}\">";
        echo "<input type=\"hidden\" name=\"mode\" value=\"edit\">";
        echo "</form>";
        echo "<hr>";
    }
    echo "<a name=\"rtc_add\"></a><form method=\"POST\" action=\"#\">";
    echo "<h3>Add new: {$widget->WidgetId}</h3>";
    $uniqid = uniqid();
    echo '<p>';
    wp_editor('','content');
    echo '</p>';
    echo "<p><label style=\"display:flex;align-items:center;\"><b>Display on: </b><input class=\"rtc-displayon\" type=\"text\" name=\"DisplayOn\" value=\"\" style=\"flex-grow:2;\" data-group-id=\"{$uniqid}\"></label></p>";
    echo "<input type=\"hidden\" name=\"WidgetId\" value=\"{$widget->WidgetId}\">";
    echo "<input type=\"hidden\" name=\"mode\" value=\"add\">";
    echo "<p><button type=\"button\" class=\"button button-primary rtc-change-list\" data-group-id=\"{$uniqid}\">Select pages</button></p>";
    echo "<p class=\"rtc-post-list-spot\" data-group-id=\"{$uniqid}\"></p>";
    echo "<p><input type=\"submit\" class=\"button button-primary widget-control-save right\"></p><div class=\"clear\"></div>";
    echo "</form>";
}
?>
<div id="rtc-post-list-container" style="display:none;">
    <div id="rtc-post-list-handler">
    <select multiple size="10" id="rtc-post-list" data-group-id="">
    <?php
    $pages_to_display_on = $wpdb->get_results("SELECT
        ID, post_title
        FROM {$wpdb->posts}
        WHERE post_type = 'page'
        ORDER BY ID");
    foreach($pages_to_display_on as $post){
        $id = str_replace("~","&nbsp;",str_pad($post->ID,7,"~"));
        echo "<option value=\"{$post->ID}\">{$id}{$post->post_title}</option>";
    }
    ?>
    </select>
    <p>
        <button type="button" id="rtc-post-list-btn-clear" class="button">Uncheck all</button>
    </p>
    </div>
</div>
<script type="text/javascript">
    jQuery("#rtc-select-module").on('change',function(){
        // console.log(this)
        // let search = location.search.replace(/[&?]widget_id=[^&#]*/im,'');
        // location = location.origin + location.pathname + search + (search == '' ? '?' : '&') + 'widget_id=' + this.value;
    });
</script>
<script>
// Display on
    if(typeof $ === 'undefined'){
        var $ = jQuery;
    }
    jQuery(function(){
        $("button.rtc-change-list").on('click',function(evt){
            var group_id = $(this).data('group-id');
            var list = $("#rtc-post-list");
            $(list).data('group-id',group_id);
            $('.rtc-post-list-spot[data-group-id="'+group_id+'"]').append($("#rtc-post-list-handler"));
            var listposts = $("#rtc-post-list>option");
            $(listposts).removeAttr('selected');
            var input_display = $('input.rtc-displayon[data-group-id="'+group_id+'"]');
            var page_ids = $(input_display).first().val().split(",");
            // console.log(input_display);
            // console.log(page_ids);
            page_ids.forEach(function(pageid) {
                if(pageid){
                    $(listposts).filter('[value='+pageid+']').attr('selected',true);
                }
            }, this);

        });
        $("select#rtc-post-list").on('change',function(){
            var list = $(this).val();
            var group_id = $(this).data('group-id');
            var input_display = $('input.rtc-displayon[data-group-id="'+group_id+'"]');
            $(input_display).first().val(list ? list.join(',') : '');
        });
        $("select#rtc-post-list option").on('mousedown',function(evt){
            evt.preventDefault();
            $(this).prop('selected', $(this).prop('selected') ? false : true);
            $("select#rtc-post-list").trigger('change');
            return false;
        });
        $("#rtc-post-list-btn-clear").on('click',function(){
            $("select#rtc-post-list option").removeAttr('selected');
            $("select#rtc-post-list").trigger('change');
        });
    });
</script>