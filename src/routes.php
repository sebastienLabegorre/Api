<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes


$app->get('/api/recipes', function (Request $request, Response $response, array $args) {
	// Sample log message
	$this->logger->info("Slim-Skeleton '/' route");

	$query = "SELECT id, user_id, name, slug FROM `recipes__recipe`";

	$data = mysqli_query($this->mysqli, $query);
	$data = mysqli_fetch_all($data, MYSQLI_ASSOC);
	$responseArray = array(
		'code' => '200',
		'message' => 'OK',
		'datas' => $data,
	);
	$json_data = json_encode($responseArray);
	$response->getBody()->write($json_data);
	// Render index view

	return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
	// Sample log message
	$this->logger->info("Slim-Skeleton '/' route");

	$responseArray = array(
		'code' => '404',
		'message' => 'Not Found',
		'datas' => array(),
	);
	$json_data = json_encode($responseArray);
	$response->getBody()->write($json_data);
	// Render index view

	return $this->renderer->render($response, 'index.phtml', $args);
});
