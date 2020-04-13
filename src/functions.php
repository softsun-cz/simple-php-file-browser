<?php
 function human($bytes) {
  $type = array('', 'k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
  $i = 0;
  while ($bytes >= 1024) {
   $bytes /= 1024;
   $i++;
  }
  return round($bytes, 2) . ' ' . $type[$i] . 'B';
 }

 function getURLPath($path) {
  $fex = false;
  $urlsd = '';
  foreach (explode('/', $path) as $sd) {
   $urlsd .= ($fex ? '/' : '') . rawurlencode($sd);
   if (!$fex) $fex = true;
  }
  return $urlsd;
 }
 
 function isUsingDots($path) {
  foreach (explode('/', $path) as $dirname) {
   if (trim($dirname) == '..') return true;
  }
  return false;
 }
?>