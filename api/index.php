<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

include 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$dbConnect = new DbConnect();
$conn = $dbConnect->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        // Fetch tasks from the database
        $sql = "SELECT * FROM tasks";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE id = :id"; // Add a space before WHERE
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            $stmt->execute();
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Return the tasks as a JSON response
        echo json_encode($tasks, JSON_FORCE_OBJECT);
        break;

    case "POST":
        $task = json_decode(file_get_contents('php://input'));

        $sql = "INSERT INTO tasks (Title, EndDate, Description, CreateDate) VALUES (:Title, :EndDate, :Description, :CreateDate)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':Title', $task->Title);
        $stmt->bindParam(':EndDate', $task->EndDate);
        $stmt->bindParam(':Description', $task->Description);
        $createDate = date('Y-m-d');
        $stmt->bindParam(':CreateDate', $createDate);

        if ($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record created successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to create record.'];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $task = json_decode(file_get_contents('php://input'));

        // Debugging: Output the received data
        file_put_contents('debug.txt', print_r($task, true));

        $sql = "UPDATE tasks SET Title = :Title, EndDate = :EndDate, Description = :Description WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $task->id);
        $stmt->bindParam(':Title', $task->Title);
        $stmt->bindParam(':EndDate', $task->EndDate);
        $stmt->bindParam(':Description', $task->Description);

        if ($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to update record.'];
        }

        // Log the response
        error_log(json_encode($response));

        echo json_encode($response);
        break;

    default:
        echo "Unsupported method";
        break;
}
