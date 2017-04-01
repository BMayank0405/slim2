<?php

function token_validation($request){
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
    return 'Success';
  }
  else
  {
    return 'Fail';
  }
};

 ?>