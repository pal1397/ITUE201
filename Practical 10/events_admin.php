<?php
require __DIR__.'/config/functions.php'; require __DIR__.'/config/db.php'; require_admin();
$uploadDir=__DIR__.'/uploads'; if(!is_dir($uploadDir)) mkdir($uploadDir,0755,true);
$message=''; $editEvent=null; if(isset($_GET['edit'])){ $es=$pdo->prepare('SELECT * FROM events WHERE id=?'); $es->execute([(int)$_GET['edit']]); $editEvent=$es->fetch(); }
if($_SERVER['REQUEST_METHOD']==='POST') {
 $action=$_POST['action']??'';
 if($action==='delete') { $pdo->prepare('DELETE FROM events WHERE id=?')->execute([(int)$_POST['id']]); audit_log($pdo,'Delete event','ID '.$_POST['id']); }
 else {
  $title=trim($_POST['title']??''); $date=$_POST['event_date']??''; $venue=trim($_POST['venue']??''); $description=trim($_POST['description']??''); $poster=null;
  if($title===''||$venue===''||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)) $message='Enter valid event details.';
  else {
   if(isset($_FILES['poster']) && $_FILES['poster']['error']===UPLOAD_ERR_OK) {
    $f=$_FILES['poster']; $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if($f['size']>2*1024*1024 || !isset($allowed[$mime])) $message='Poster must be JPG, PNG or WEBP and maximum 2 MB.';
    else { $poster=bin2hex(random_bytes(12)).'.'.$allowed[$mime]; move_uploaded_file($f['tmp_name'],$uploadDir.'/'.$poster); }
   }
   if($message==='') {
    if($action==='update') { $pdo->prepare('UPDATE events SET title=?,description=?,event_date=?,venue=?,category=?,poster=COALESCE(?,poster),status=? WHERE id=?')->execute([$title,$description,$date,$venue,$_POST['category']??'General',$poster,$_POST['status']??'Open',(int)$_POST['id']]); audit_log($pdo,'Update event',$title); }
    else { $pdo->prepare('INSERT INTO events(title,description,event_date,venue,category,poster) VALUES(?,?,?,?,?,?)')->execute([$title,$description,$date,$venue,$_POST['category']??'General',$poster]); audit_log($pdo,'Add event',$title); }
   }
  }
 }
}
$events=$pdo->query('SELECT * FROM events ORDER BY event_date')->fetchAll();
?><!doctype html><html><head><meta charset="utf-8"><title>Event Management</title><link rel="stylesheet" href="css/php-style.css"></head><body><main class="box wide"><h1>Event Management</h1><p><?=e($message)?></p>
<h2><?= $editEvent ? 'Update Event' : 'Add Event' ?></h2><form method="post" enctype="multipart/form-data"><input type="hidden" name="action" value="<?= $editEvent ? 'update' : 'add' ?>"><?php if($editEvent): ?><input type="hidden" name="id" value="<?=e($editEvent['id'])?>"><?php endif; ?><label>Event title</label><input name="title" value="<?=e($editEvent['title']??'')?>" required><label>Description</label><textarea name="description"><?=e($editEvent['description']??'')?></textarea><label>Date</label><input type="date" name="event_date" value="<?=e($editEvent['event_date']??'')?>" required><label>Venue</label><input name="venue" value="<?=e($editEvent['venue']??'')?>" required><label>Category</label><input name="category" value="<?=e($editEvent['category']??'General')?>"><label>Status</label><select name="status"><option value="Open" <?= (($editEvent['status']??'Open')==='Open')?'selected':'' ?>>Open</option><option value="Closed" <?= (($editEvent['status']??'Open')==='Closed')?'selected':'' ?>>Closed</option></select><label>Poster (JPG/PNG/WEBP, max 2 MB)</label><input type="file" name="poster" accept="image/jpeg,image/png,image/webp"><button><?= $editEvent ? 'Update Event' : 'Add Event' ?></button><?php if($editEvent): ?><a href="events_admin.php">Cancel</a><?php endif; ?></form>
<h2>Existing Events</h2><table><tr><th>Title</th><th>Date</th><th>Venue</th><th>Poster</th><th>Action</th></tr><?php foreach($events as $event): ?><tr><td><?=e($event['title'])?></td><td><?=e($event['event_date'])?></td><td><?=e($event['venue'])?></td><td><?php if($event['poster']): ?><img src="uploads/<?=e($event['poster'])?>" width="70" alt="Event poster"><?php endif; ?></td><td><a href="?edit=<?=e($event['id'])?>">Edit</a><form method="post" onsubmit="return confirm('Delete event?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=e($event['id'])?>"><button>Delete</button></form></td></tr><?php endforeach; ?></table><p><a href="admin_dashboard.php">Admin dashboard</a></p></main></body></html>
