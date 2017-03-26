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
require_once __DIR__ . '/SearchForAWB.php';

//route for getting details of multiple awb numbers
require_once __DIR__ . '/SearchForMultipleAWB.php';



//route for cancelling the order
//$app->put('/cancel-order',function($request,$response){
//  $data =  $request->getParsedBody();
//});



// for inserting new order in the database
require_once __DIR__ . '/OrdersToBeInserted.php';



?>
