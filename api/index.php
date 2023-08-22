<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

require __DIR__ . '/vendor/autoload.php';

use OnlinePayments\Sdk\DefaultConnection;
use OnlinePayments\Sdk\CommunicatorConfiguration;
use OnlinePayments\Sdk\Communicator;
use OnlinePayments\Sdk\Client;
use OnlinePayments\Sdk\Domain\CreateHostedCheckoutRequest;
use OnlinePayments\Sdk\Domain\AmountOfMoney;
use OnlinePayments\Sdk\Domain\Order;
use OnlinePayments\Sdk\Domain\HostedCheckoutSpecificInput;

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
            $payid = substr($newson->payment->id, 0, -2);
            $status = $newson->type;
            $amount = intval($newson->payment->paymentOutput->amountOfMoney->amount)/100;
            $statusCode = $newson->payment->statusOutput->statusCode;
            $now = DateTime::createFromFormat('U.u', microtime(true));
            $dateweb = $now->format("Y-m-d H:i:s.u");
            $ipaddr = $_SERVER['REMOTE_ADDR'];
            

            $sql = "INSERT INTO Orders(orderid, amount, payid, orderstatus, webhook_date, statusCode, ipaddr) VALUES(null, :amount, :payid, :orderstatus, :webhook_date, :statusCode, :ipaddr )";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payid', $payid);
            $stmt->bindParam(':orderstatus', $status);
            $stmt->bindParam(':webhook_date', $dateweb);
            $stmt->bindParam(':statusCode', $statusCode);
            $stmt->bindParam(':ipaddr', $ipaddr);
            $stmt->execute();

            echo json_encode(array()); 
        }
        elseif($_SERVER['REQUEST_URI'] == "/api/endpoints/hostedcheckout"){
            $user = json_decode( file_get_contents('php://input') );
            $yourApiKey = "D3E746548D2BB8648DBE";    
            $yourApiSecret = "syVXH2CYRMMUWa1ARTteHUbscow6CcmbmXeCgviD6GAU+VXvUUMDYWgp2mWazs5L+63Kt0ujEUadNMUWsjze3g==";
            $yourPspId = "ilovedamianos";
    
            # Create a URI for our TEST/LIVE environment
            $apiEndpoint = "https://payment.preprod.direct.worldline-solutions.com";
    
            # Initialise the client with the apikey, apisecret and URI
            $connection = new DefaultConnection();
    
            $communicatorConfiguration = new CommunicatorConfiguration(
                $yourApiKey,
                $yourApiSecret,
                $apiEndpoint,
                'OnlinePayments'
            );
    
            $communicator = new Communicator(
                $connection,
                $communicatorConfiguration
            );
    
            $client = new Client($communicator);
            

            $createHostedCheckoutRequest = new CreateHostedCheckoutRequest();
            $amountOfMoney = new AmountOfMoney();
            $amountOfMoney->setAmount($user->amount*100);
            $amountOfMoney->setCurrencyCode("EUR");
    
            $order = new Order();
            $order->setAmountOfMoney($amountOfMoney);
    
            $hostedCheckoutSpecificInput = new HostedCheckoutSpecificInput();
            $hostedCheckoutSpecificInput->setReturnUrl("https://duckcave.com/success");
    
            $createHostedCheckoutRequest->setOrder($order);
            $createHostedCheckoutRequest->setHostedCheckoutSpecificInput($hostedCheckoutSpecificInput);
    
            # ...
    
            # Send the request to your PSPID on our platform and receive it via an instance of CreateHostedCheckoutResponse
            $createHostedCheckoutResponse = $client->merchant($yourPspId)->hostedCheckout()->createHostedCheckout($createHostedCheckoutRequest);
            $hostedCheckoutUrl = $createHostedCheckoutResponse->getRedirectUrl();

            echo($hostedCheckoutUrl);
            break;
        }
       
}