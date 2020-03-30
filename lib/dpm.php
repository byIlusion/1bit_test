<?php

// Dump
function dpm($data, $return = FALSE) {
  $deb = debug_backtrace();
  
  $output = '<pre>';
    $output .= print_r($data, TRUE);
    $output .= '<div>';
   	  $output .= '<span class="path">' . $deb[0]['file'] . '</span>';
   	  $output .= isset($deb[1]) ? '<span class="func">:' . $deb[1]['function'] . '</span>' : '';
   	  $output .= '<span class="line">:' . $deb[0]['line'] . '</span>';
    $output .= '</div>';
  $output .= '</pre>';
  
  if ($return)
    return $output;
	echo $output;
}

?>