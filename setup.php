<?php namespace rich_text_compress; 

 function add_menu_settings(){
    add_submenu_page(
        'themes.php',
        'Rich Text Compress',
        'Rich Text Compress',
        'edit_pages',
        'rich_text_compress_settings',
        function(){
            include realpath(__DIR__.'/settings_page.php');
        }
    );
 }

 add_action('admin_menu','rich_text_compress\add_menu_settings');

function setup_boxes(){
    add_action('add_meta_boxes',__NAMESPACE__.'\add_meta_boxes');
}
add_action('load-post.php',__NAMESPACE__.'\setup_boxes');
add_action('load-post-new.php',__NAMESPACE__.'\setup_boxes');
add_action('save_post',__NAMESPACE__.'\metabox_save');
