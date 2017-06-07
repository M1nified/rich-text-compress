<?php namespace rich_text_compress;
  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class RichTextCompressMasterWidget extends \WP_Widget{
    function __construct(){
        parent::__construct(
            'RichTextCompressMasterWidget',
            __('Rich Text Compress Master','rich_text_compress_domain'),
            array(
                'description' => __('Rich text for massive content. All in one widget instance.','rich_text_compress_domain')
            )
        );
    }
    public function widget($args,$instance){
        global $wpdb;
        global $db_table;
        // print_r($instance);
        // print_r($args);
        // print_r($GLOBALS['post']->ID);
        $hide = get_hide($instance);
        if($hide == 1) return;
		    $widget_ids = get_master_widgets($instance);
        $widget_ids_str = implode(",", array_map(function($widget_id){return "'{$widget_id}'";},$widget_ids));
        $query = "SELECT DISTINCT t1.`Value` as content, t1.`WidgetId` as widget_id, t2.`Value` as title
        FROM `{$db_table}` as t1 
        LEFT JOIN `{$db_table}` as t2 ON t1.`WidgetId` = t2.`WidgetId` AND t2.Type = 'title'
        WHERE t1.`Type`='content' 
          AND t1.Id in (
			      SELECT min(Id)
            FROM wp_rtc_content
            WHERE `DisplayOn` LIKE '%!{$GLOBALS['post']->ID}!%'
            GROUP BY WidgetId
          )
          AND t1.WidgetId in ({$widget_ids_str})
        ;";
        $possible_widgets = $wpdb->get_results($query);
        // echo sizeof($possible_widgets);
        // echo "<pre>"; print_r($possible_widgets); echo "</pre>";
        foreach($widget_ids as $full_id)
        {
          // echo $full_id;
          $matching_widgets = array_filter($possible_widgets, function($possible_widget) use ($full_id){
            return $possible_widget->widget_id == $full_id;
          });
          // print_r($matching_widgets);
          // echo sizeof($matching_widgets);
          if(sizeof($matching_widgets) > 0)
          {
            $widget = $matching_widgets[array_keys($matching_widgets)[0]];
            // echo "<pre>"; print_r($widget); echo "</pre>";
            $content = $widget->content;
            $title = $widget->title;
            $output_title = 1;
            if(isset($content) && $content != null && $content != ''){
                echo $args['before_widget'];
                // echo '<section>';
                if($output_title == 1){
                    echo $args['before_title'].$title.$args['after_title'];
                }
                echo $content;
                // echo '</section>';
                echo $args['after_widget'];
            }
            
          }
        }
    }
    public function form($instance){
      // print_r($instance);
      global $wpdb, $db_table;
      $widgets_all = $wpdb->get_results("SELECT `value` AS `title`, `WidgetId` as widget_id FROM `{$db_table}` WHERE `Type` = 'title' ORDER BY `value`;");
      $widgets_listed = array_key_exists('widgets', $instance) && is_array($instance['widgets']) ? $instance['widgets'] : [];
      $active_count = sizeof($widgets_listed);
      foreach($widgets_listed as $order => $widget_active)
      {
        ?>
        <p>
        <select name="<?php echo $this->get_field_name( "widgets[{$order}]" ); ?>">
          <option></option>
          <?php foreach($widgets_all as $widget)
          {
            $selected = $widget->widget_id == $widget_active ? " selected" : "";
            $widget_id = extract_widget_id($widget->widget_id);
            if($widget_id === false) continue;
            echo "<option value=\"{$widget->widget_id}\"{$selected}>{$widget->title} ({$widget_id})</option>";
          }?>
        </select>
        </p>
        <?php
      } 
      ?>
      <select name="<?php echo $this->get_field_name( "widgets[{$active_count}]" ); ?>">
        <option></option>
        <?php 
        foreach($widgets_all as $widget)
        {
          $widget_id = extract_widget_id($widget->widget_id);
          if($widget_id === false) continue;
          echo "<option value=\"{$widget->widget_id}\">{$widget->title} ({$widget_id})</option>";
        }
        ?>
      </select>
      <?php
    }
    public function update($new_instance,$old_instance){
        // if(!$old_instance){
        //     global $db_table;
        //     global $wpdb;
        //     $wpdb->insert(
        //         $db_table,
        //         [
        //             'Type' => 'content',
        //             'WidgetId' => $this->id,
        //             'DisplayOn' => ''
        //         ]
        //     );
        // }
        $new_instance['widgets'] = array_filter($new_instance['widgets']);
        return $new_instance;
    }
}
function statsoft_kursy_load_widget_master(){
    register_widget('rich_text_compress\RichTextCompressMasterWidget');
}
add_action('widgets_init','rich_text_compress\statsoft_kursy_load_widget_master');