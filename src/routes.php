<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;

require_once __DIR__.'/../src/functions.php';
require_once __DIR__ . '/../config/config.php';

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
  $data = $request->getParam('client'); //parsedBody is an array of objects
if (empty($client)){
  echo 'success';
}
else{
  echo 'fail';
}
});


// for inserting new order in the database
require_once __DIR__ . '/OrdersToBeInserted.php';



?>
