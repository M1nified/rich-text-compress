<?php namespace rich_text_compress;
    

 function create_tables(){
     global $wpdb;
     global $db_table;
     $tabquery = "CREATE TABLE `{$db_table}` (
    `Id` int(11) NOT NULL AUTO_INCREMENT,
    `Type` varchar(256) DEFAULT NULL,
    `Value` longtext,
    `WidgetId` varchar(256) DEFAULT NULL,
    `DisplayOn` mediumtext,
    PRIMARY KEY (`Id`),
    UNIQUE KEY `Id_UNIQUE` (`Id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
    ";
    $wpdb->query($tabquery);
 }
 function install(){
    create_tables();
 }
register_activation_hook(__DIR__.'/rich-text-compress.php','rich_text_compress\install');