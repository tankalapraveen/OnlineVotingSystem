<?php
include('db_connect.php');
session_start();

// Ensure only students can vote
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access!"]);
    exit();
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['events']) || empty($data['events'])) {
        echo json_encode(["status" => "error", "message" => "No events selected."]);
        exit();
    }

    $selectedEvents = $data['events'];

    // Ensure the number of selected events is between 1 and 10
    if (count($selectedEvents) < 1 || count($selectedEvents) > 10) {
        echo json_encode(["status" => "error", "message" => "Invalid voting process! You must select between 1 and 10 events."]);
        exit();
    }

    // Update votes for each selected event
    foreach ($selectedEvents as $event_name) {
        $query = "UPDATE eventtable SET votes = votes + 1 WHERE event_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $event_name);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(["status" => "success", "message" => "Votes recorded successfully!"]);
}

$conn->close();
?>
