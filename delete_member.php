<?php
include 'Database.php';

// Ensure the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Get the raw POST data
    $input = json_decode(file_get_contents('php://input'), true);

    // Check if the id is provided
    if (isset($input['id'])) {
        $id = $input['id'];

        // Create Database object
        $db = new Database('localhost', 'abdulrahman', 'root', '');
        $db->deleteMember($id); // Delete member from the database

        // Send a response back to the client
        echo json_encode(['status' => 'success', 'message' => 'Member deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No member ID provided']);
    }
}
?>
