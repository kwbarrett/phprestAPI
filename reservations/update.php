<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


 $method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
    die();
}


if ($_SERVER['REQUEST_METHOD'] !== 'PUT') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Bad Request detected! Only PUT method is allowed',
    ]);
    exit;
endif;

require '../db_connect.php';
$database = new Operations();
$conn = $database->dbConnection();

$data = json_decode(file_get_contents("php://input"));

//print_r($data);

//die();

// $hobbies = $data->hobbyField;
//print_r($hobbies);
// $hobbies_list = '';
// foreach ($hobbies as $hobby) {
//     $hobbies_list .= $hobby.',';
//  } 

if (!isset($data->id)) {
    echo json_encode(['success' => 0, 'message' => 'Please enter correct reservation id.']);
    exit;
}

try {

    $fetch_post = "SELECT * FROM `reservations` WHERE id=:id";
    $fetch_stmt = $conn->prepare($fetch_post);
    $fetch_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);
    $fetch_stmt->execute();

    if ($fetch_stmt->rowCount() > 0) :
     //echo 'AAA';
        $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);
        $guestName = isset($data->guestName) ? $data->guestName : $row['guestName'];
        $guestEmail = isset($data->guestEmail) ? $data->guestEmail : $row['guestEmail'];
        $checkInDate = isset($data->checkInDate) ? $data->checkInDate : $row['checkInDate'];
        $checkOutDate = isset($data->checkOutDate) ? $data->checkOutDate : $row['checkOutDate'];
        $roomNumber = isset($data->roomNumber) ? $data->roomNumber : $row['roomNumber'];

        // $hobbies = $hobbies_list;

        // $country = isset($data->country) ? $data->country : $row['country'];

       $update_query = "UPDATE `reservations` SET guestName = :guestName, guestEmail = :guestEmail, checkInDate = :checkInDate, checkOutDate = :checkOutDate, roomNumber = :roomNumber
        WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        $update_stmt->bindValue(':guestName', htmlspecialchars(strip_tags($guestName)), PDO::PARAM_STR);
        $update_stmt->bindValue(':guestEmail', htmlspecialchars(strip_tags($guestEmail)), PDO::PARAM_STR);
        $update_stmt->bindValue(':checkInDate', htmlspecialchars(strip_tags($checkInDate)), PDO::PARAM_STR);
        $update_stmt->bindValue(':checkOutDate', htmlspecialchars(strip_tags($checkOutDate)), PDO::PARAM_STR);
        $update_stmt->bindValue(':roomNumber', htmlspecialchars(strip_tags($roomNumber)), PDO::PARAM_INT);
        $update_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);


        if ($update_stmt->execute()) {

            echo json_encode([
                'success' => 1,
                'message' => 'Record udated successfully'
            ]);
            exit;
        }

        echo json_encode([
            'success' => 0,
            'message' => 'Did not udpate. Something went  wrong.'
        ]);
        exit;

    else :
        echo json_encode(['success' => 0, 'message' => 'Invalid ID. No record found by the ID.']);
        exit;
    endif;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}