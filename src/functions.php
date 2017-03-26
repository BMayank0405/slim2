<?php




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

  $new_query = json_encode($array);

//query for rto_attribute and rts_attributes
  $json_query = "COLUMN_CREATE('name','$name','city','$city','state','$state','contact_no','$contact_no','address','$address','pincode',$pincode)";
  return $json_query;
};


function pickup($array){
  $address = $array['address'];
  $pincode = $array['pincode'];

//query for pickup json object
  $pickup_query = "COLUMN_CREATE('address','$address','pincode',$pincode)";
  return $pickup_query;
};




function maybe_json_encode($row){

  $find = array("{" , "," , "}");
  $replace = array("{\r\n    " , ",\r\n    " , "\r\n  }");//this array appends new line after {
  $row['pickup'] = str_replace($find,$replace,$row['pickup']);
  $row['rto'] = str_replace($find,$replace,$row['rto']);//making json pretty
  $row['rts'] = str_replace($find,$replace,$row['rts']);

  $pattern = '/{"\d{1}":/'; //this finds all the keys inside which are dont needed to be shown in skus
  $replacement = '';
  $row['skus']  = preg_replace($pattern,$replacement,$row['skus'],-1,$count);//count here is used to find no of replacement
  $rm_bracket = '';
  //looping for making an string of no of brackets for removing them
  for ($i=0;$i<=$count;$i++){
    $rm_bracket = $rm_bracket.'}';
  };
  $rm_bracket = '/'.$rm_bracket.'/';
  $replacement = '}';
  $row['skus'] = preg_replace($rm_bracket,$replacement,$row['skus']);//replacing last brackets
  $row['skus'] = str_replace($find,$replace,$row['skus']);//making json pretty


  foreach($row as $key => $value)
  { //here in these values we don't need to put quotes on values
    if($key =='id' || $key=='pincode' || $key=='declared_value' || $key=='cod_amount'|| $key=='pickup'|| $key=='rto'|| $key=='rts'){
      echo '  '."\"$key\":"."$value".','."\r\n";
    }
    elseif($key=='skus'){ //skus is returned as an array of json object
      echo '  '."\"$key\":".'['."$value".']'."\r\n";
    }
    else{ //here we need to put quotes on values
      echo '  '."\"$key\":"."\"$value\"".','."\r\n";
    }
  }
  echo ' }'."\r\n";

};

 ?>
