<?php

function setJsonOutPut(){
  header('Content-Type:application/json');
}

function setTextOutPut(){
  header('Content-Type:text/html');
}

function output($data){
  setJsonOutPut();
  echo json_encode($data);
  exit();
}
