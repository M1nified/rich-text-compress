<?php
require_once('../../../wp-load.php');
require_once('variables.php');

global $wpdb;
global $db_table;
header('Content-type:text/json');

switch ($_GET['func']) {
    case 'get':
        $content = $wpdb->get_results("SELECT * FROM {$db_table} WHERE `WidgetId` = '{$_GET['widget_id']}'");
        if(sizeof($content) == 0){
            $content = [
                'Type' => 'content',
                'Value' => '',
                'WidgetId' => $_GET['widget_id']
            ];
            $wpdb->insert(
                $db_table,
                $content
            );
            $content['Id'] = $wpdb->insert_id;
        }else{
            $content = $content[0];
        }
        print_r(json_encode($content));
        break;
    case 'set':
        $wpdb->update(
            $db_table,
            [
                'value' => $_POST['content']
            ],
            [
                'Id' => $_GET['id']
            ]
        );
        break;
}