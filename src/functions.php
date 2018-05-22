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
?>