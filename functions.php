<?php namespace rich_text_compress;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function extract_widget_id($widget_id_full)
{
  $is_match = preg_match('/\d+/i', $widget_id_full, $matches);
  if($is_match === 1)
  {
    return $matches[0];
  }
  return false;
}