<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__.'/../src/functions.php';

//route for getting detail of a single awb number
$app->get('/awb/{awb}',function(Request $request,Response $response,$args){
  global $db;
  $awb = $args['awb'];
  $query = "SELECT * FROM cutomers";
  $result = $db->query($query);
  if($result){
    while($row = $result->fetch_assoc()){
      echo json_encode($row,JSON_PRETTY_PRINT);
      echo "\r\n";
    }
  }
  else {
    echo '{"message": "Invalid AWB Number"}';
  }
});

//route for getting details of multiple awb numbers
$app->post('/order', function ($request, $response, $args) {
  global $db;
  $data = $request->getParsedBody();//parsedBody is an array of objects
  $query = "SELECT * FROM cutomers";
  $result = $db->query($query);
  if($result){
    while($row = $result->fetch_assoc()){
      $response->withAddedHeader('Content-Type', 'application/json');
       $response->write(json_encode($row,JSON_PRETTY_PRINT));
       $response->write("\r\n");
       }
       return $response;
  }
  /*$length = count($data);
  echo $length;
  for ($i=0;$i<$length;$i++){
    echo "1 \r\n";
  }*/
   /*$query = "SELECT * FROM cutomers WHERE `id`=1";
    $result = $db->query($query);
    if($result){
      while($row = $result->fetch_row()){
        echo json_encode($row);
        echo '<br />';
      }
    }
    else {
      echo '{"message": "Invalid AWB Number"}';
    }
  });
*/
});


//route for cancelling the order
//$app->put('/cancel-order',function($request,$response){
//  $data =  $request->getParsedBody();
//});

$app->post('/place-order', function ($request, $response, $args) {
  $data = $request->getParsedBody(); //parsedBody is an array of objects
  print_r($data);
  echo json_encode($data);
});



$app->post('/test', function($request,$response,$args){
  global $db;
  $client = $request->getParam("client_order_id");
  $awb_no = $request->getParam('awb_number');
  $pincode= $request->getParam('pincode');
  $customer_name = $request->getParam('customer_name');
  $customer_phone = $request->getParam('customer_phone');
  $customer_address = $request->getParam('customer_address');
  $c_city = $request->getParam('c_city');
  $c_state = $request->getParam('c_state');
  $declared_value = $request->getParam('declared_value');
  $cod_amount = $request->getParam('cod_amount');
  $deliver_type = $request->getParam('deliver_type');

  //here onwards json object start so we use some functions to make them store in mariadb
  $pickup_address = pickup($request->getParam('pickup_address_attributes'));
  $rto_address =json_object($request->getParam('rto_attributes'));
  $rts_address =json_object($request->getParam('rts_attributes'));
  $not_delivered = $request->getParam('return_type_if_not_delivered');
  $skus = input_data($request->getParam('skus_attributes'));


  $query ="INSERT INTO items
    VALUES (NULL,
    '$client','$awb_no',$pincode,'$customer_name','$customer_phone','$customer_address','$c_city','$c_state',
    $declared_value,$cod_amount,'$deliver_type',$pickup_address,$rto_address,$rts_address,'$not_delivered',
    COLUMN_CREATE($skus));";

  $result = $db->query($query);
  var_dump ($result);

  });

$app->post('/new-test', function($request,$response,$args){
  $client = $request->getParam("client_order_id");
  echo $client."\r\n";
  $awb_no = $request->getParam('awb_number');
  echo $awb_no;
});





?>
