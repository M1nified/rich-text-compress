<?php namespace rich_text_compress;
/**
 * Plugin Name: Rich Text Compress
 * Description: Rich Text Pluinn
 * Version: 0.1.0
 * Author: M1nified
 */
 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

 include_once(realpath(__DIR__.'/variables.php'));

 include_once(realpath(__DIR__.'/install.php'));

 include_once(realpath(__DIR__.'/meta-boxes.php'));

 include_once(realpath(__DIR__.'/setup.php'));

 include_once(realpath(__DIR__.'/widget.php'));
