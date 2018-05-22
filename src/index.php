<?php
 session_start();
 require_once('./functions.php');
 require_once('./settings.php');
 $sort = 'name';
 $order = false;
 if (isset($_SESSION['sort'])) $sort = $_SESSION['sort'];
 if (isset($_SESSION['order'])) $order = $_SESSION['order'];
 $subdir = '/';
 if (isset($_GET['dir'])) {
  $dir = trim($_GET['dir']);
  if (!empty($dir)) $subdir = $dir;
 }
 $currentdir = $basedir . $subdir;
 $templatedir = './templates/' . $template . '/';
 $header = file_get_contents($templatedir . 'header.html');
 $body = file_get_contents($templatedir . 'body.html'); 
 $disk = file_get_contents($templatedir . 'disk.html'); 
 $footer = file_get_contents($templatedir . 'footer.html');
 $error = file_get_contents($templatedir . 'error.html');
 $rowback = file_get_contents($templatedir . 'row-back.html');
 $rowdir = file_get_contents($templatedir . 'row-dir.html');
 $rowfile = file_get_contents($templatedir . 'row-file.html');
 $header = str_replace('[[subdir]]', $subdir, $header);
 $header = str_replace('[[css]]', $templatedir . 'style.css', $header);
 echo $header . "\r\n";
 if (file_exists($currentdir) && is_dir($currentdir)) {
  $total = disk_total_space($basedir);
  $free = disk_free_space($basedir);
  $used = $total - $free;
  $percent = round(($used / $total) * 100, 2);
  $no = $sort == 'name' && !$order ? true : false;
  $do = $sort == 'date' && !$order ? true : false;
  $so = $sort == 'size' && !$order ? true : false;
  $sortname = './sort.php?sort=name&amp;order=' . ($no ? 1 : 0) . '&amp;dir=' . $subdir;
  $sortdate = './sort.php?sort=date&amp;order=' . ($do ? 1 : 0) . '&amp;dir=' . $subdir;
  $sortsize = './sort.php?sort=size&amp;order=' . ($so ? 1 : 0) . '&amp;dir=' . $subdir;
  if ($diskinfo) {
   $disk = str_replace('[[percent]]', $percent, $disk);
   $disk = str_replace('[[used]]', human($used), $disk);
   $disk = str_replace('[[free]]', human($free), $disk);
   $disk = str_replace('[[total]]', human($total), $disk);
  } else $disk = '';
  $body = str_replace('[[disk]]', $disk, $body);
  $body = str_replace('[[subdir]]', $subdir, $body);
  $body = str_replace('[[sortname]]', $sortname, $body);
  $body = str_replace('[[sortdate]]', $sortdate, $body);
  $body = str_replace('[[sortsize]]', $sortsize, $body);
  $body = str_replace('[[ordername]]', $no ? '&#9650;' : '&#9660;', $body);
  $body = str_replace('[[orderdate]]', $do ? '&#9650;' : '&#9660;', $body);
  $body = str_replace('[[ordersize]]', $so ? '&#9650;' : '&#9660;', $body);
  $rows = '';
  $values = scandir($currentdir, 0);
  if ($subdir != '/') {
   $link = './?dir=' . substr($subdir, 0, strrpos(substr($subdir, 0, -1), '/') + 1);   
   $rows .= str_replace('[[link]]', $link, $rowback) . "\r\n";
  }
  $dirs = array();
  $files = array();
  foreach ($values as $v) {
   if ($v != '.' && $v != '..') {
    if (is_dir($currentdir . $v)) array_push($dirs, array('name' => $v, 'date' => filemtime($currentdir . $v)));
    else array_push($files, array('name' => $v, 'date' => filemtime($currentdir . $v), 'size' => filesize($currentdir . $v)));
   }
  }
  array_multisort(array_column($dirs, $sort == 'size' ? 'name' : $sort), $sort == 'size' ? SORT_ASC : ($order ? SORT_DESC : SORT_ASC), $dirs);
  array_multisort(array_column($files, $sort), $order ? SORT_DESC : SORT_ASC, $files);
  foreach ($dirs as $d) {
   $link = './?dir=' . $subdir . $d['name'] . '/';
   $r = str_replace('[[link]]', $link, $rowdir);
   $r = str_replace('[[name]]', $d['name'], $r);
   $r = str_replace('[[date]]', date("Y-m-d H:i:s", $d['date']), $r);
   $rows .= $r . "\r\n";
  }
  foreach ($files as $f) {
   $link = $currentdir . $f['name'];
   $r = str_replace('[[link]]', $link, $rowfile);
   $r = str_replace('[[name]]', $f['name'], $r);
   $r = str_replace('[[date]]', date("Y-m-d H:i:s", $f['date']), $r);
   $r = str_replace('[[size]]', human($f['size']), $r);
   $rows .= $r . "\r\n";
  }
  $rows = substr($rows, 0, -2);
  $body = str_replace('[[rows]]', $rows, $body);
  echo $body . "\r\n";
 } else {
  $error = str_replace('[[subdir]]', $subdir, $error);
  echo $error . "\r\n";
 }
 echo $footer;
?>