<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

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
  $first = $request->getParam('1');



  $skus = $request->getParam('skus');
  $second = $request->getParam('2');

  $i=0;
  $make_query ='';
  foreach ($skus as $array){
    $sku_id = $array['sku_id'];
    $price= $array["price"];
    $id = $array["id"];
    $product = $array["product"];
    $make_query =$make_query."'$i',COLUMN_CREATE('sku_id','$sku_id','price','$price','id','$id','product','$product')".',';
    $i++;
    };

  $make_query = rtrim($make_query,',');
  echo "\r\n";

  $query ="INSERT INTO test
    VALUES (NULL,
    '$first',
    COLUMN_CREATE($make_query),
    '$second')";
  echo $query;
  $result = $db->query($query);
  var_dump ($result);

  });
?>
