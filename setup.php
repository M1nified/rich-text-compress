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

