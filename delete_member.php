<?php
include 'Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    $db = new Database('localhost', 'abdulrahman', 'root', '');
    $db->deleteMember($id);

    echo json_encode(['Id' => $id]);
}
?>
