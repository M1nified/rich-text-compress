<?php namespace rich_text_compress;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

 function add_meta_boxes(){
     add_meta_box(
         'rich_text_compress-metabox-1',
         esc_html__('Rich Text Compress','rich_text_compress'),
         __NAMESPACE__.'\add_meta_box_1',
         ['post','page','attachment'],
        //  ['post','page'],
         'side',
         'default'
     );
 }

  function add_meta_box_1($post,$box){
     wp_nonce_field( basename( __FILE__ ), 'rich_text_compress_nonce_1' );
    //  echo '<p>'.esc_attr( get_post_meta( $post->ID, 'rich_text_compress-metabox-1', true ) ).'</p>';
    //  print_dialog_1(false,$post->ID);
    global $wpdb;
    global $db_table;
    $widget_rtc_options = get_option('widget_rich_text_compress_widget');
    // print_r($widget_rtc_options);
    $widgets = $wpdb->get_results(
        "SELECT *
        FROM
            wp_rtc_content
        WHERE 
	        DisplayOn like '%!{$post->ID}!%'
    ");
    print("<div><b>Remove:</b>");
    foreach($widgets as $widget){
        $widget_number = str_replace('rich_text_compress_widget-','',$widget->WidgetId);
        $title = isset($widget_rtc_options[$widget_number]) ? $widget_rtc_options[$widget_number]['title'] : NULL;
        print("<p><label><input type=\"checkbox\" name=\"rtc-row-to-remove-from[]\" value=\"{$widget->Id}\">{$title} ({$widget_number}) ({$widget->Id})</label></p>");
    }
    print("</div>");
    print("<div><p><b>Add wp_rtc_content id</b></p>");
    print("<p><input type=\"text\" name=\"rtc-row-to-add-to\" placeholder=\"Id\"></p>");
    print("</div>");
 }

 function metabox_save($post_id){
     // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'rich_text_compress_nonce_1' ] ) && wp_verify_nonce( $_POST[ 'rich_text_compress_nonce_1' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'rtc-row-to-remove-from' ] )) {
        global $wpdb;
        global $db_table;
        $ids = implode(',',$_POST[ 'rtc-row-to-remove-from' ]);
        $wpdb->query("UPDATE {$db_table}
            SET DisplayOn = replace(DisplayOn,'{$post_id}!','')
            WHERE Id in ($ids);
        ");
    }
    if( isset( $_POST[ 'rtc-row-to-add-to' ] ) && $_POST[ 'rtc-row-to-add-to' ] != ''){
        global $wpdb;
        global $db_table;
        $wpdb->query("UPDATE {$db_table}
            SET DisplayOn = CONCAT(DisplayOn,'{$post_id}!')
            WHERE Id = {$_POST[ 'rtc-row-to-add-to' ]};
        ");
    }

 }
