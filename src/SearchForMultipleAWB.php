<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/order', function ($request, $response, $args) {
  global $db;
  $data = $request->getParam("awb_numbers");//parsedBody is an array of objects
  $len = count($data);
  for($i=0;$i<$len;$i++){
    //echo $data[$i];
    $data[$i] = htmlentities(strip_tags(trim($data[$i])),ENT_QUOTES);
    $data[$i] = mysqli_real_escape_string($db, $data[$i]);
  }
  $query_str = '(';
  for($i=0;$i<$len;$i++){
    $query_str = $query_str."\"$data[$i]\"".',';
  }
  $query_str = rtrim($query_str,',');
  $query_str = $query_str.')';
  //echo $query_str;
  $query = "SELECT id,client_order_id,awb_number,pincode,customer_name,
            customer_phone,customer_address,c_city,c_state,declared_value,
            cod_amount,deliver_type,
            column_json(pickup_address_attributes) as pickup,
            column_json(rto_attributes) as rto,
            column_json(rts_attributes) as rts,
            return_type_if_not_delivered,
            column_json(skus_attributes) as skus
             FROM `items` WHERE `awb_number`in $query_str;";

  $result = $db->query($query);
  try{
    if($result){
      while($row = $result->fetch_assoc()){
      echo '{'."\r\n";
      echo '"message":"Success",'."\r\n";
      echo '"Client request:"{'."\r\n";
      maybe_json_encode($row);
      echo '}';
      echo "\r\n\r\n";
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
