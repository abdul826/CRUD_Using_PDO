<?php
include 'Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Read and decode raw JSON data from PUT request
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['id']) && isset($data['name'])) {
        $id = $data['id'];
        $name = $data['name'];

        $db = new Database('localhost', 'abdulrahman', 'root', '');
        $db->updateMember($id, $name);

        echo json_encode(['Id' => $id, 'Name' => $name]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}
?>