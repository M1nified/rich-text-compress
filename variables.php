<?php namespace rich_text_compress;

global $db_table;
$db_table = "{$wpdb->prefix}rtc_content";

function get_title($instance){
    return isset($instance['title'])?$instance['title']:'';;
}
function get_output_title($instance){
    return isset($instance['output_title'])?$instance['output_title']:0;
}
function get_multiple_content($instance){
    return isset($instance['multiple_content'])?$instance['multiple_content']:0;
}
function get_content_id($instance){
    return isset($instance['content'])?$instance['content']:'';;
}