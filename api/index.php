<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");


include 'DBbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$path = explode('/', $_SERVER['REQUEST_URI']);
if(isset($path[4])){
    $secretpath = '/' . $path[1] . '/'. $path[2] . '/'. $path[3] . '/' . $path[4];
}


$method = $_SERVER['REQUEST_METHOD'];
switch($method) {

    case "GET":
        if($_SERVER['REQUEST_URI'] == "/api/endpoints/orders"){
            $sql = "SELECT * FROM Orders ORDER BY webhook_date DESC;";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($users);
            break;
        } 

    case "POST":
        if($_SERVER['REQUEST_URI'] == "/api/endpoints/webhooks"){
            $newson = json_decode(file_get_contents('php://input'));
            $payid = $newson->payment->id;
            $status = $newson->type;
            $amount = $newson->payment->paymentOutput->amountOfMoney->amount
            $dateweb = date('Y-m-d H:i:s');
            

            
            $sql = "INSERT INTO Orders(orderid, amount, payid, orderstatus, webhook_date) VALUES(null, :amount, :payid, :orderstatus, :webhook_date )";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payid', $payid);
            $stmt->bindParam(':orderstatus', $status);
            $stmt->bindParam(':webhook_date', $dateweb);
            $stmt->execute();

            echo json_encode(array()); 
        }
       
}