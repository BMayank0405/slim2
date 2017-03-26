<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__.'/../src/functions.php';

$app->post('/getauthtoken', function ($request, $response, $args) {
  $parsedBody = $request->getParsedBody();
  $token = (new Builder())->setIssuer('http://example.com') // Configures the issuer (iss claim)
                          ->setAudience('http://example.org') // Configures the audience (aud claim)
                          ->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
                          ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                          ->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
                          ->setExpiration(time() + 3600) // Configures the expiration time of the token (nbf claim)
                          ->set('uid', 1) // Configures a new claim, called "uid"
                          ->getToken();

  $response->withAddedHeader('Content-Type', 'application/json');
  return $response->write("{ \"token\" : \"" . $token . "\" }");
});


$app->get('/testtoken', function ($request, $response, $args) {

  $token = $request->getHeaderLine('Authorization');
  $token = (new Parser())->parse((string) $token);
  $data = new ValidationData();
  $data->setIssuer('http://example.com');
  $data->setAudience('http://example.org');
  $data->setId('4f1g23a12aa');
  $data->setCurrentTime(time()+60);
  $response->withAddedHeader('Content-Type', 'application/json');

  if(!$token->validate($data))
  {
    return $response->withStatus(401);
  }
  else
  {
    return $response->write("{ \"status\" : \"Ok\" }");
  }
});



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

$app->post('/pl', function ($request, $response, $args) {
  $data = $request->getParam('rts_attributes'); //parsedBody is an array of objects
  print_r($data);
  echo json_encode($data);
});



$app->post('/placeOrder', function($request,$response,$args){
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
  var_dump($result);
  if($result){
          $r_query = "SELECT id,client_order_id,  awb_number,	pincode,	customer_name,
                    customer_phone,customer_address,c_city,c_state,declared_value,
                    cod_amount,deliver_type,
                    column_json(pickup_address_attributes) as pickup,
                    column_json(rto_attributes) as rto,
                    column_json(rts_attributes) as rts,
                    return_type_if_not_delivered,
                    column_json(skus_attributes) as skus
                     FROM `items` ";
        $r_result = $db->query($r_query);
        if($r_result){
        while($r_row = $r_result->fetch_assoc()){
          echo '{'."\r\n";
          echo '"message":"Success",'."\r\n";
          echo '"Client reqiest:"{'."\r\n";
          maybe_json_encode($r_row);
          echo '}';
          echo "\r\n";

        }
      }
        else {
        echo '{"error": "some error may have occured"}';
      }
  }
  else {

  }

});







?>
