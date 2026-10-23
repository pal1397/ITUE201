<?php
$message=''; $saved=false;
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $name=trim(strip_tags($_POST['name']??'')); $email=trim($_POST['email']??''); $body=trim(strip_tags($_POST['message']??''));
 if (!preg_match('/^[A-Za-z ]{2,100}$/',$name) || !filter_var($email,FILTER_VALIDATE_EMAIL) || $body==='') $message='Invalid input. Please check all fields.';
 else {
  $record=['name'=>$name,'email'=>$email,'message'=>$body,'submitted_at'=>date('c')];
  $file=__DIR__.'/data/submissions.csv'; $fp=fopen($file,'a');
  if ($fp) { if (flock($fp,LOCK_EX)) { fputcsv($fp,$record); flock($fp,LOCK_UN); $saved=true; } fclose($fp); }
  $message=$saved?'Form submitted and saved successfully.':'Unable to save the record.';
 }
} else { http_response_code(405); $message='Use the form with POST method.'; }
?><!doctype html><html><head><meta charset="utf-8"><title>Form Result</title><link rel="stylesheet" href="css/php-style.css"></head><body><main class="box"><h1>Form Result</h1><p><?=htmlspecialchars($message,ENT_QUOTES,'UTF-8')?></p><a href="php-form.html">Back to form</a></main></body></html>
