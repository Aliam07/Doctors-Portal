<?php
header("Content-Type: application/json");

$speciality = isset($_GET['speciality']) ? $_GET['speciality'] : '';

if ($speciality) {
    // Replace with your logic to fetch doctors based on the speciality from the database
    $doctors = [
        ["name" => "Dr. Smith", "speciality" => "Cardiology"],
        ["name" => "Dr. Jones", "speciality" => "Neurology"],
    ];

    // Filter doctors based on the selected speciality
    $filteredDoctors = array_filter($doctors, function($doctor) use ($speciality) {
        return $doctor['speciality'] === $speciality;
    });

    echo json_encode(["doctors" => array_values($filteredDoctors)]);
} else {
    echo json_encode(["doctors" => []]);
}
?>
