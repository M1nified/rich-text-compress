<?php namespace rich_text_compress;
/**
 * Tutaj znajduje sie tresc wyswietlana w ustawieniach modulu
 **/
?>

<?php 

// include_once(realpath(__DIR__.'variables.php'));

global $wpdb;

global $db_table;

// print_r($_POST);
if($_POST['mode']==='edit' && $_POST['rtc_id'] != ''){
    $display_on = "!".str_replace(",","!",$_POST['DisplayOn'])."!";
    //Zapisywanie wprowadzonych zmian
    echo 1;
    $wpdb->update($db_table,[
        'Value' => $_POST['content'],
        'DisplayOn' => $display_on
    ],[
        'Id' => $_POST['rtc_id']
    ]);
}elseif($_POST['mode'] === 'add' && $_POST['WidgetId'] != ''){
    $display_on = "!".preg_replace(",","!",$_POST['DisplayOn'])."!";
    echo 2;
    $wpdb->insert($db_table,[
        'Type' => 'content',
        'Value' => $_POST['content'],
        'WidgetId' => $_POST['WidgetId'],
        'DisplayOn' => $display_on
    ]);
}

$widget_id = $_GET['widget_id'];
$widgets = $wpdb->get_results("SELECT DISTINCT `WidgetId` FROM {$db_table} ORDER BY `WidgetId`");
echo "<h1>Rich Text Compress</h1>";
echo "<form method=\"GET\" action=\"#\">";
echo "<p><b>Instance to edit: </b><select id=\"rtc-select-module\" name=\"module_id\">";
if($widget_id == '') echo '<option disabled selected></option>';
foreach ($widgets as $key => $widget) {
    $selected = $widget->WidgetId === $widget_id ? 'selected' : '';
    echo "<option value\"{$widget->WidgetId}\" {$selected}>{$widget->WidgetId}</option>";
}
echo '</select></p>';
echo '</form>';
if($widget_id != ''){
    echo "<h2>Edit: {$widget_id}</h2>";
    echo '<a href="#rtc_add" class="button">Add new content</a>';
    $widgets = $wpdb->get_results("SELECT `Id`,`Type`,`Value`,`WidgetId`,`DisplayOn`
    FROM {$db_table}
    WHERE WidgetId = '{$widget_id}' AND `Type` = 'content'");
    // print_r($widgets);
    foreach ($widgets as $key => $widget) {
        echo "<form method=\"POST\" action=\"#\">";
        // echo "<h3>{$widget->WidgetId}</h3>";
        echo '<p>';
        wp_editor($widget->Value,'content-'.$widget->Id,[
            "textarea_name" => 'content'
        ]);
        echo '</p>';
        $display_on = $widget->DisplayOn;
        $display_on = preg_replace('/(^,|,$)/','',str_replace("!",",",$display_on));
        echo "<p><label style=\"display:flex;align-items:center;\"><b>Display on: </b><input type=\"text\" name=\"DisplayOn\" value=\"{$display_on}\" style=\"flex-grow:2;\"></label></p>";
        echo "<p><input type=\"submit\" class=\"button button-primary widget-control-save right\"></p><div class=\"clear\"></div>";
        echo "<input type=\"hidden\" name=\"rtc_id\" value=\"{$widget->Id}\">";
        echo "<input type=\"hidden\" name=\"mode\" value=\"edit\">";
        echo "</form>";
        echo "<hr>";
    }
    echo "<a name=\"rtc_add\"></a><form method=\"POST\" action=\"#\">";
    echo "<h3>Add new: {$widget->WidgetId}</h3>";
    echo '<p>';
    wp_editor('','content');
    echo '</p>';
    echo "<p><label style=\"display:flex;align-items:center;\"><b>Display on: </b><input type=\"text\" name=\"DisplayOn\" value=\"\" style=\"flex-grow:2;\"></label></p>";
    echo "<input type=\"hidden\" name=\"WidgetId\" value=\"{$widget->WidgetId}\">";
    echo "<input type=\"hidden\" name=\"mode\" value=\"add\">";
    echo "<p><input type=\"submit\" class=\"button button-primary widget-control-save right\"></p><div class=\"clear\"></div>";
    echo "</form>";
}

?>
<script type="text/javascript">
    jQuery("#rtc-select-module").on('change',function(){
        console.log(this)
        let search = location.search.replace(/[&?]widget_id=[^&#]*/im,'');
        location = location.origin + location.pathname + search + (search == '' ? '?' : '&') + 'widget_id=' + this.value;
    });
</script>