<?php namespace rich_text_compress;
class rich_text_compress_widget extends \WP_Widget{
    function __construct(){
        parent::__construct(
            'rich_text_compress_widget',
            __('Rich Text Compress','rich_text_compress_domain'),
            array(
                'description' => __('Rich text for massive content.','rich_text_compress_domain')
            )
        );
    }
    public function widget($args,$instance){
        // print_r($instance);
        // print_r($args);
        // print_r($GLOBALS['post']->ID);
        $hide = get_hide($instance);
        if($hide == 1) return;
        echo $args['before_widget'];
		$title = get_title($instance);
        $output_title = get_output_title($instance);
        $multiple_content = get_multiple_content($instance);
        $content_id = get_content_id($instance);
        global $wpdb;
        global $db_table;
        $select = "SELECT `value` FROM `{$db_table}`
            WHERE `Type`='content' AND `WidgetId` = '{$args['widget_id']}'";
        if($multiple_content == 0){
            // echo 1;
            $content = $wpdb->get_results($select);
        }else{
            // echo 2;
            // echo $select." AND `DisplayOn` LIKE '%!{$GLOBALS['post']->ID}!%'";
            $content = $wpdb->get_results($select." AND `DisplayOn` LIKE '%!{$GLOBALS['post']->ID}!%'");
        }
        // print_r($content);
        $content = isset($content[0])?$content[0]->value:'';
        if($output_title == 1 && $content != ''){
            echo $args['before_title'].$title.$args['after_title'];
        }
        echo $content;
		echo $args['after_widget'];
    }
    public function form($instance){
        $title = get_title($instance);
        $output_title = get_output_title($instance);
        $hide = get_hide($instance);
        $multiple_content = get_multiple_content($instance);
        $content_id = get_content_id($instance);
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
        </p>
        <p>
        <a class="button rich_text_compress-button-editor">Edit content</a>
        </p>
        <p>
        <input class="widefat" id="<?php echo $this->get_field_id( 'output_title' ); ?>" name="<?php echo $this->get_field_name( 'output_title' ); ?>" type="checkbox" value="1" <?php echo ($output_title==true)?'checked':''; ?> />
        <label for="<?php echo $this->get_field_id( 'output_title' ); ?>"><?php _e( 'Output title' ); ?></label>

        <input class="widefat" id="<?php echo $this->get_field_id( 'hide' ); ?>" name="<?php echo $this->get_field_name( 'hide' ); ?>" type="checkbox" value="1" <?php echo ($hide==true)?'checked':''; ?> />
        <label for="<?php echo $this->get_field_id( 'hide' ); ?>"><?php _e( 'Hide' ); ?></label>
        </p>

        <p>
        <input class="widefat" id="<?php echo $this->get_field_id( 'multiple_content' ); ?>" name="<?php echo $this->get_field_name( 'multiple_content' ); ?>" type="checkbox" value="1" <?php echo ($multiple_content==true)?'checked':''; ?> />
        <label for="<?php echo $this->get_field_id( 'multiple_content' ); ?>"><?php _e( 'Multiple content' ); ?></label>
        </p>
        <input type="hidden" name="plugin_path" value="<?php echo plugin_dir_url( __FILE__ ); ?>">
        <?php
    }
    public function update($new_instance,$old_instance){
        return $new_instance;
    }
}
function statsoft_kursy_load_widget(){
    register_widget('rich_text_compress\rich_text_compress_widget');
}
add_action('widgets_init','rich_text_compress\statsoft_kursy_load_widget');

function in_widget_form($instance){
    if($instance->number!="__i__"){
        echo "<input type=\"hidden\" name=\"widget_id\" value=\"{$instance->id}\">"; 
    }
}
add_action('in_widget_form','rich_text_compress\in_widget_form');

function load_js($hook) {
    if ( 'widgets.php' != $hook ) {
        return;
    }
    wp_enqueue_script( 'my_custom_script', plugin_dir_url( __FILE__ ) . 'WPRichTextCompress.js' );
}
add_action( 'admin_enqueue_scripts', 'rich_text_compress\load_js' );

function load_editor($hook){
    // var_dump($hook);
    // if ( 'widgets.php' != $hook ) {
    //     return;
    // }
    echo '<div class="wrap" id="rich_text_compress_editor-container" style="display:none;"><form id="rich_text_compress_editor-form" action="#">';
    wp_editor('','rich_text_compress_editor');
    ?>
    <input type="hidden" name="plugin_path" value="">
    <input type="hidden" name="store_id" value="">
    <p>
    <input type="submit" class="button button-primary widget-control-save" id="rich_text_compress_editor-submit">
    </p>
    <?php
    echo '</form></div>';
}
add_action( 'in_admin_header', 'rich_text_compress\load_editor' );