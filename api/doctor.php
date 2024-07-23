<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$servername = "localhost";  // Replace with your server name
$username = "root";         // Replace with your database username
$password = "";             // Replace with your database password
$dbname = "doctor_portal";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
    if (empty($data['name']) || empty($data['speciality'])) {
        echo json_encode(["success" => false, "message" => "Name and Speciality are required"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO doctors (name, speciality) VALUES (?, ?)");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        return;
    }
    
    $stmt->bind_param("ss", $data['name'], $data['speciality']);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Doctor created successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Execute failed: " . $stmt->error]);
    }

    $stmt->close();
}

function getAllDoctors($conn) {
    $sql = "SELECT * FROM doctors";
    $result = $conn->query($sql);

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    echo json_encode(["doctors" => $doctors]);
}

function getDoctorsBySpeciality($conn, $speciality) {
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE speciality = ?");
    $stmt->bind_param("s", $speciality);
    $stmt->execute();
    $result = $stmt->get_result();

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    echo json_encode(["doctors" => $doctors]);
    $stmt->close();
}

function deleteDoctor($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Doctor deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting doctor"]);
    }

    $stmt->close();
}

function updateDoctor($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("UPDATE doctors SET name = ?, speciality = ? WHERE id = ?");
    $stmt->bind_param("ssi", $data['name'], $data['speciality'], $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Doctor updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating doctor"]);
    }

    $stmt->close();
}
?>
