<?php

  $app->post('/placeOrder', function($request,$response,$args){
  global $db;
  $client = $request->getParam("client_order_id");
  $awb_no = $request->getParam('awb_number');
  $pincode = $request->getParam('pincode');
  $customer_name = $request->getParam('customer_name');
  $customer_phone = $request->getParam('customer_phone');
  $customer_address = $request->getParam('customer_address');
  $c_city = $request->getParam('c_city');
  $c_state = $request->getParam('c_state');
  $declared_value = $request->getParam('declared_value');
  $cod_amount = $request->getParam('cod_amount');
  $deliver_type = $request->getParam('deliver_type');

  //here onwards json object start so we use some functions to make them store in mariadb
  $pickup_address =$request->getParam('pickup_address_attributes');
  $rto_attributes =$request->getParam('rto_attributes');
  $rts_attributes =$request->getParam('rts_attributes');
  $not_delivered = $request->getParam('return_type_if_not_delivered');
  $skus = input_data($request->getParam('skus_attributes'));


//validation of the input data
  if(empty($client)){
    $errors[]='client_order needed to be filled';
  }
  if(empty($awb_no)){
    $errors[]='awb_number needed to be filled';
  }
  if(!(preg_match("/^[0-9]{6}$/",$pincode))){
    $errors[]='pincode of 6 digit needed to be filled';
  }
  if(!(preg_match("/^([0-9]){10,}$/",$customer_phone))){
    $errors[]='customer phone should be 10 digit long';
  }
  if(empty($customer_address)){
    $errors[]='customer_address needed to be filled';
  }
  if(empty($c_city)){
    $errors[]='c_city needed to be filled';
  }
  if(empty($c_state)){
    $errors[]='c_state needed to be filled';
  }
  if(empty($declared_value)){
    $errors[]='declared_value needed to be filled';
  }
  if(empty($cod_amount)){
    $errors[]='cod_amount needed to be filled';
  }
  if(empty($deliver_type)){
    $errors[]='deliver_type needed to be filled';
  }
  if(!(preg_match("/^[0-9]{6}$/",$pickup_address['pincode']))){
    $errors[]='pincode of 6 digit needed to be filled in pickup address';
  }
  if(array_search('',$rto_attributes)===false){
    if(!(preg_match("/^[0-9]{6}$/",$rto_attributes['pincode']))){
      $errors[]='pincode of 6 digit needed to be filled in rto address';
    }
    if(!(preg_match("/^([0-9]){10,}$/",$rto_attributes['contact_no']))){
      $errors[]='contact number in rto address should be 10 digit long';
    }
  }else {
    $errors[] = 'all the fields of rto_attributes are mandatory';
  }

  if(array_search('',$rts_attributes)===false){
    if(!(preg_match("/^[0-9]{6}$/",$rts_attributes['pincode']))){
      $errors[]='pincode of 6 digit needed to be filled in rts address';
    }
    if(!(preg_match("/^([0-9]){10,}$/",$rts_attributes['contact_no']))){
      $errors[]='contact number in rts address should be 10 digit long';
    }
  }else {
    $errors[] = 'all the fields of rts_attributes are mandatory';
  }
//end of validation of input data


try {

  if(empty($errors)){
      $pickup_address = pickup($pickup_address);
      $rto_attributes = json_object($rto_attributes);
      $rts_attributes = json_object($rts_attributes);

      $query ="INSERT INTO items
        VALUES (NULL,
        '$client','$awb_no',$pincode,'$customer_name','$customer_phone','$customer_address','$c_city','$c_state',
        $declared_value,$cod_amount,'$deliver_type',$pickup_address,$rto_attributes,$rts_attributes,'$not_delivered',
        COLUMN_CREATE($skus));";

      $result = $db->query($query);
      try{
        if($result){
              $r_query = "SELECT id,client_order_id,  awb_number,	pincode,	customer_name,
                        customer_phone,customer_address,c_city,c_state,declared_value,
                        cod_amount,deliver_type,
                        column_json(pickup_address_attributes) as pickup,
                        column_json(rto_attributes) as rto,
                        column_json(rts_attributes) as rts,
                        return_type_if_not_delivered,
                        column_json(skus_attributes) as skus
                         FROM `items` WHERE `awb_number`=\"$awb_no\"";
            $r_result = $db->query($r_query);
            if($r_result){
            while($r_row = $r_result->fetch_assoc()){
              echo '{'."\r\n";
              echo '"message":"Success",'."\r\n";
              echo '"Client request:"{'."\r\n";
              maybe_json_encode($r_row);
              echo '}';
              echo "\r\n";
            }
          }
          else {
          echo '{"error": "some error may have occured"}';
          }
        }
      else{
        throw new InvalidArgumentException('there might some problem with your entries');
      }
    }
    catch (Exception $ex){
      $response->write($ex->getMessage());
      return $response;
    }
  }
  else {
    throw new Exception(json_encode($errors,JSON_PRETTY_PRINT));
  }
}
catch(Exception $ex) {
  $response->write($ex->getMessage());
  return $response;
}
});

?>
