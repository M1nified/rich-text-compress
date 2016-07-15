<?php namespace rich_text_compress; 

 function ssk_add_menu_settings(){
    add_submenu_page(
        'options-general.php',
        'StatSoft Kursy',
        'StatSoft Kursy',
        'edit_pages',
        'ssk_settings',
        function(){
            include realpath(__DIR__.'/settings_page.php');
        }
    );
 }

//  add_action('admin_menu','rich_text_compress\ssk_add_menu_settings');

