<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
    die();
}

 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Bad Request!.Only POST method is allowed',
    ]);
    exit;
endif;
 
require '../db_connect.php';
$database = new Operations();
$conn = $database->dbConnection();
 
$data = json_decode(file_get_contents("php://input"));


// $hobbies = $data->hobbyField;
// //print_r($hobbies);
// $hobbies_list = '';
// foreach ($hobbies as $hobby) {
//     $hobbies_list .= $hobby.',';
//  } 

if (!isset($data->guestName) || !isset($data->guestEmail) || !isset($data->checkInDate) || !isset($data->checkOutDate) || !isset($data->roomNumber)) :
 
    echo json_encode([
        'success' => 0,
        'message' => 'Please enter compulsory fileds |  First Name, Last Name and Email',
    ]);
    exit;
 
elseif (!isset($data->guestName) || !isset($data->guestEmail) || !isset($data->checkInDate) || !isset($data->checkOutDate) || !isset($data->roomNumber)) :
 
    echo json_encode([
        'success' => 0,
        'message' => 'Field cannot be empty. Please fill all the fields.',
    ]);
    exit;
 
endif;
 
try {
 
    $guestName = htmlspecialchars(trim($data->guestName));
    $guestEmail = htmlspecialchars(trim($data->guestEmail));
    $checkInDate = htmlspecialchars(trim($data->checkInDate));
    $checkOutDate = htmlspecialchars(trim($data->checkOutDate));
    $roomNumber = $data->roomNumber;
    $hobbies = $hobbies_list;
    $country = $data->country;
 
    $query = "INSERT INTO `reservations`(
    guestName,
    guestEmail,
    checkInDate,
    checkOutDate,
    roomNumber
    ) 
    VALUES(
    :guestName,
    :guestEmail,
    :checkInDate,
    :checkOutDate,
    :roomNumber
    )";
 
    $stmt = $conn->prepare($query);
 
    $stmt->bindValue(':guestName', $guestName, PDO::PARAM_STR);
    $stmt->bindValue(':guestEmail', $guestEmail, PDO::PARAM_STR);
    $stmt->bindValue(':checkInDate', $checkInDate, PDO::PARAM_STR);
    $stmt->bindValue(':checkOutDate', $checkOutDate, PDO::PARAM_STR);
    $stmt->bindValue(':roomNumber', $roomNumber, PDO::PARAM_INT);
    

    if ($stmt->execute()) {
 
        http_response_code(201);
        echo json_encode([
            'success' => 1,
            'message' => 'Data Inserted Successfully.'
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => 0,
        'message' => 'There is some problem in data inserting'
    ]);
    exit;
 
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}
