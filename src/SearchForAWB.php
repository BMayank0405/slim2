<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/awb/{awb}',function(Request $request,Response $response,$args){
  global $db;
  $awb = htmlentities(strip_tags(trim($args['awb'])),ENT_QUOTES);
  $awb = mysqli_real_escape_string($db, $awb);
  $query = "SELECT id,client_order_id,  awb_number,	pincode,	customer_name,
            customer_phone,customer_address,c_city,c_state,declared_value,
            cod_amount,deliver_type,
            column_json(pickup_address_attributes) as pickup,
            column_json(rto_attributes) as rto,
            column_json(rts_attributes) as rts,
            return_type_if_not_delivered,
            column_json(skus_attributes) as skus
             FROM `items` WHERE `awb_number`=\"$awb\";";

  $result = $db->query($query);
  try{
    if($result){
      while($row = $result->fetch_assoc()){
      echo '{'."\r\n";
      echo '"message":"Success",'."\r\n";
      echo '"Client request:"{'."\r\n";
      maybe_json_encode($row);
      echo '}';
      echo "\r\n";
      }
    }
    else {
      throw new InvalidArgumentException('awb number is not correct');
    }
  }
  catch(Exception $ex){
    $response->write($ex->getMessage());
    return $response;
  }
});

?>
