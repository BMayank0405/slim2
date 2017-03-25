<?php
/*function maybe_json_encode($row){
//  var_dump($row);
foreach($row as $key => $value)
{
  if($key==''){
    echo '"colour":'."$value".','."\r\n";
  }
  else{
    echo "\"$key:\""."$value".','."\r\n ";
  }

}
echo '}'."\r\n";
};
*/

function input_data($skus){
  $i=0;
  $skus_query='';
  foreach ($skus as $array){
    $product_name = $array['product_name'];
    $sku_id = $array["client_sku_id"];
    $skus_query = $skus_query."'$i',COLUMN_CREATE('product_name','$product_name','client_sku_id','$sku_id')".',';
    $i++;
    };

  //this makes query for updating the insertion query for skus attribute
  $skus_query = rtrim($skus_query,',');
  return $skus_query;

};


function json_object($array){
  $name =$array['name'] ;
  $city =$array['city'] ;
  $state =$array['state'] ;
  $contact_no =$array['contact_no'] ;
  $pincode =$array['pincode'] ;
  $address =$array['address'] ;

//query for rto_attribute and rts_attributes
  $json_query = "COLUMN_CREATE('name','$name','city','$city','state','$state','contact_no','$contact_no','pincode','$pincode','address','$address')";
  return $json_query;
};


function pickup($array){
  $address = $array['address'];
  $pincode = $array['pincode'];

  $pickup_query = "COLUMN_CREATE('address','$address','pincode','$pincode')";
  return $pickup_query;
};
 ?>
