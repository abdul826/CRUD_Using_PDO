<?php
include 'Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $parentId = isset($_POST['parentId']) && $_POST['parentId'] !== '' ? $_POST['parentId'] : null;

    $db = new Database('localhost', 'abdulrahman', 'root', '');
    $id = $db->insertMember($name, $parentId);

    echo json_encode(['Id' => $id, 'Name' => $name]);
}
