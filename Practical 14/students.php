<?php
require __DIR__ . '/config/functions.php';
require_login();
require __DIR__ . '/config/mysqli.php';
$isAdmin = (($_SESSION['role'] ?? '') === 'admin');
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $mysqli->prepare('DELETE FROM students WHERE id = ?');
        $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close();
        $message = 'Student deleted.';
    } elseif (in_array($action, ['add', 'update'], true)) {
        $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? '');
        $course = trim($_POST['course'] ?? ''); $year = trim($_POST['year'] ?? '');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $course === '' || $year === '') {
            $message = 'Enter valid details in every field.';
        } elseif ($action === 'add') {
            $stmt = $mysqli->prepare('INSERT INTO students(name,email,course,year) VALUES(?,?,?,?)');
            $stmt->bind_param('ssss', $name, $email, $course, $year);
            $message = $stmt->execute() ? 'Student added.' : 'Could not add student. Check for a duplicate email.'; $stmt->close();
        } else {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $mysqli->prepare('UPDATE students SET name=?,email=?,course=?,year=? WHERE id=?');
            $stmt->bind_param('ssssi', $name, $email, $course, $year, $id);
            $message = $stmt->execute() ? 'Student updated.' : 'Could not update student.'; $stmt->close();
        }
    }
}
$editStudent = null;
if ($isAdmin && isset($_GET['edit'])) {
    $id = (int)$_GET['edit']; $stmt = $mysqli->prepare('SELECT * FROM students WHERE id=?');
    $stmt->bind_param('i', $id); $stmt->execute(); $editStudent = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
$q = trim($_GET['q'] ?? '');
$courseFilter = trim($_GET['course'] ?? '');
$yearFilter = trim($_GET['year'] ?? '');
$where = []; $types = ''; $params = [];
if ($q !== '') { $where[] = '(name LIKE ? OR email LIKE ? OR course LIKE ?)'; $like = '%' . $q . '%'; array_push($params, $like, $like, $like); $types .= 'sss'; }
if ($courseFilter !== '') { $where[] = 'course = ?'; $params[] = $courseFilter; $types .= 's'; }
if ($yearFilter !== '') { $where[] = '`year` = ?'; $params[] = $yearFilter; $types .= 's'; }
$whereSql = $where ? (' WHERE ' . implode(' AND ', $where)) : '';
$countStmt = $mysqli->prepare('SELECT COUNT(*) AS total FROM students' . $whereSql);
if ($types !== '') $countStmt->bind_param($types, ...$params);
$countStmt->execute(); $totalRows = (int)$countStmt->get_result()->fetch_assoc()['total']; $countStmt->close();
$perPage = 10; $page = max(1, (int)($_GET['page'] ?? 1)); $totalPages = max(1, (int)ceil($totalRows / $perPage)); $page = min($page, $totalPages); $offset = ($page - 1) * $perPage;
$listStmt = $mysqli->prepare('SELECT * FROM students' . $whereSql . ' ORDER BY id DESC LIMIT ? OFFSET ?');
$listTypes = $types . 'ii'; $listParams = $params; $listParams[] = $perPage; $listParams[] = $offset;
$listStmt->bind_param($listTypes, ...$listParams); $listStmt->execute(); $rows = $listStmt->get_result()->fetch_all(MYSQLI_ASSOC); $listStmt->close();
$courseOptions = $mysqli->query('SELECT DISTINCT course FROM students ORDER BY course')->fetch_all(MYSQLI_ASSOC);
$yearOptions = $mysqli->query('SELECT DISTINCT `year` FROM students ORDER BY `year`')->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Student CRUD</title><link rel="stylesheet" href="css/php-style.css"></head><body><main class="box wide"><h1>Student Management (MySQLi)</h1><p><?= e($message) ?></p>
<?php if ($isAdmin): ?><h2><?= $editStudent ? 'Edit Student' : 'Add Student' ?></h2><form method="post"><input type="hidden" name="action" value="<?= $editStudent ? 'update' : 'add' ?>"><?php if ($editStudent): ?><input type="hidden" name="id" value="<?= e($editStudent['id']) ?>"><?php endif; ?><label>Name</label><input name="name" value="<?= e($editStudent['name'] ?? '') ?>" required><label>Email</label><input name="email" type="email" value="<?= e($editStudent['email'] ?? '') ?>" required><label>Course</label><input name="course" value="<?= e($editStudent['course'] ?? '') ?>" required><label>Year</label><input name="year" value="<?= e($editStudent['year'] ?? '') ?>" required><button><?= $editStudent ? 'Update' : 'Add' ?></button></form><?php endif; ?>
<form method="get"><input name="q" value="<?= e($q) ?>" placeholder="Search name, email or course"><select name="course"><option value="">All courses</option><?php foreach ($courseOptions as $option): ?><option value="<?=e($option['course'])?>" <?= $courseFilter===$option['course']?'selected':'' ?>><?=e($option['course'])?></option><?php endforeach; ?></select><select name="year"><option value="">All years</option><?php foreach ($yearOptions as $option): ?><option value="<?=e($option['year'])?>" <?= $yearFilter===$option['year']?'selected':'' ?>><?=e($option['year'])?></option><?php endforeach; ?></select><button>Apply</button><a href="students.php">Reset</a></form><table><tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><?php if ($isAdmin): ?><th>Action</th><?php endif; ?></tr>
<?php foreach ($rows as $s): ?><tr><td><?= e($s['id']) ?></td><td><?= e($s['name']) ?></td><td><?= e($s['email']) ?></td><td><?= e($s['course']) ?></td><td><?= e($s['year']) ?></td><?php if ($isAdmin): ?><td><a href="?edit=<?= e($s['id']) ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this student?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($s['id']) ?>"><button>Delete</button></form></td><?php endif; ?></tr><?php endforeach; ?></table><p>Showing <?=count($rows)?> of <?=$totalRows?> student(s). Page <?=$page?> of <?=$totalPages?>.</p><nav aria-label="Pagination"><?php if($page>1): ?><a href="?<?=http_build_query(array_merge($_GET,['page'=>$page-1]))?>">Previous</a><?php endif; ?> <?php if($page<$totalPages): ?><a href="?<?=http_build_query(array_merge($_GET,['page'=>$page+1]))?>">Next</a><?php endif; ?></nav><p><a href="student_dashboard.php">Dashboard</a></p></main></body></html>
