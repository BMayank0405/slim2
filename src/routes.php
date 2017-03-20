<?php
// Routes

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->post('/order/{id}', function ($request, $response, $args) {
	$parsedBody = $request->getParsedBody();
	return $response->withJson($parsedBody);
});
