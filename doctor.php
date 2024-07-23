<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "Ali@12021";  // Correct password
$dbname = "doctor_portal";
$port = 3306;  // Specify the port number

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    echo json_encode(["message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'POST':
        createDoctor($conn);
        break;
    case 'GET':
        if (isset($_GET['speciality'])) {
            getDoctorsBySpeciality($conn, $_GET['speciality']);
        } else {
            getAllDoctors($conn);
        }
        break;
    case 'DELETE':
        if(isset($_GET['id'])) {
            deleteDoctor($conn, $_GET['id']);
        }
        break;
    case 'PUT':
        if(isset($_GET['id'])) {
            updateDoctor($conn, $_GET['id']);
        }
        break;
    default:
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

$conn->close();

function createDoctor($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error: " . json_last_error_msg());
        echo json_encode(["success" => false, "message" => "Invalid JSON"]);
        return;
    }

    if (empty($data['name']) || empty($data['speciality'])) {
        echo json_encode(["success" => false, "message" => "Name and Speciality are required"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO doctors (name, speciality) VALUES (?, ?)");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        return;
    }

    $stmt->bind_param("ss", $data['name'], $data['speciality']);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Doctor created successfully"]);
    } else {
        error_log("Execute failed: " . $stmt->error);
        echo json_encode(["success" => false, "message" => "Execute failed: " . $stmt->error]);
    }

    $stmt->close();
}

function getAllDoctors($conn) {
    $sql = "SELECT * FROM doctors";
    $result = $conn->query($sql);

    if (!$result) {
        error_log("Query failed: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Query failed: " . $conn->error]);
        return;
    }

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    echo json_encode(["doctors" => $doctors]);
}

function getDoctorsBySpeciality($conn, $speciality) {
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE speciality = ?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        return;
    }

    $stmt->bind_param("s", $speciality);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        error_log("Execute failed: " . $stmt->error);
        echo json_encode(["success" => false, "message" => "Execute failed: " . $stmt->error]);
        return;
    }

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    echo json_encode(["doctors" => $doctors]);
    $stmt->close();
}

function deleteDoctor($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        return;
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Doctor deleted successfully"]);
    } else {
        error_log("Execute failed: " . $stmt->error);
        echo json_encode(["success" => false, "message" => "Error deleting doctor"]);
    }

    $stmt->close();
}

function updateDoctor($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error: " . json_last_error_msg());
        echo json_encode(["success" => false, "message" => "Invalid JSON"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE doctors SET name = ?, speciality = ? WHERE id = ?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        return;
    }

    $stmt->bind_param("ssi", $data['name'], $data['speciality'], $id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Doctor updated successfully"]);
    } else {
        error_log("Execute failed: " . $stmt->error);
        echo json_encode(["success" => false, "message" => "Error updating doctor"]);
    }

    $stmt->close();
}
?>
