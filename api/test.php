<?php

$headers =  getallheaders();
var_dump($headers);
foreach($headers as $key=>$val){
  echo $key . ': ' . $val . '<br>';
}
if (in_array('pdo', get_loaded_extensions())) {
   print 'pdo';
}

