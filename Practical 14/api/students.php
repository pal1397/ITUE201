<?php
require __DIR__.'/../config/functions.php'; require __DIR__.'/../config/db.php';
header('Content-Type: application/json; charset=utf-8');
function respond($data,$code=200){http_response_code($code);echo json_encode($data);exit;}
require_login();
$method=$_SERVER['REQUEST_METHOD'];
if($method==='GET'){ $rows=$pdo->query('SELECT id,name,email,course,year FROM students ORDER BY id DESC')->fetchAll(); respond($rows); }
if(($_SESSION['role']??'')!=='admin') respond(['error'=>'Admin access required'],403);
$input=json_decode(file_get_contents('php://input'),true) ?? [];
if($method==='POST'){
 $name=trim($input['name']??'');$email=trim($input['email']??'');$course=trim($input['course']??'');$year=trim($input['year']??'');
 if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||$course===''||$year==='') respond(['error'=>'All fields are required and email must be valid'],422);
 $s=$pdo->prepare('INSERT INTO students(name,email,course,year) VALUES(?,?,?,?)');$s->execute([$name,$email,$course,$year]);audit_log($pdo,'API add student',$email);respond(['message'=>'Student added','id'=>$pdo->lastInsertId()],201);
}
if($method==='PUT'){
 $id=(int)($input['id']??0);$name=trim($input['name']??'');$email=trim($input['email']??'');$course=trim($input['course']??'');$year=trim($input['year']??'');
 if(!$id||$name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||$course===''||$year==='') respond(['error'=>'Valid ID and all fields are required'],422);
 $s=$pdo->prepare('UPDATE students SET name=?,email=?,course=?,year=? WHERE id=?');$s->execute([$name,$email,$course,$year,$id]);audit_log($pdo,'API update student','ID '.$id);respond(['message'=>'Student updated']);
}
if($method==='DELETE'){
 $id=(int)($input['id']??0);if(!$id) respond(['error'=>'Student ID is required'],422);
 $s=$pdo->prepare('DELETE FROM students WHERE id=?');$s->execute([$id]);audit_log($pdo,'API delete student','ID '.$id);respond(['message'=>'Student deleted']);
}
header('Allow: GET, POST, PUT, DELETE');respond(['error'=>'Method not allowed'],405);
