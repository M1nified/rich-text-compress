<?php namespace rich_text_compress;
    
global $db_table;

 function create_tables(){
     global $wpdb;
     $table = "CREATE TABLE `{$db_table}` (
    `Id` int(11) NOT NULL AUTO_INCREMENT,
    `Type` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
    `Value` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
    `WidgetId` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
    PRIMARY KEY (`Id`),
    UNIQUE KEY `Id_UNIQUE` (`Id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;";
    $wpdb->query($table);
 }
 function install(){
    create_tables();
 }
register_activation_hook(__DIR__.'/rich-text-compress.php','rich_text_compress\install');