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
		'code' => 200,
		'message' => 'OK',
		'datas' => $data,
	);
	$json_data = json_encode($responseArray);
	$response->getBody()->write($json_data);
	// Render index view

	return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/api/recipes/[{arg1}].json', function (Request $request, Response $response, array $args) {
	$this->logger->info("Slim-Skeleton '/' route");

	$query = "SELECT * FROM `recipes__recipe` WHERE slug = '".$args[arg1]."'";

	$data = mysqli_query($this->mysqli, $query);
	$data = mysqli_fetch_all($data, MYSQLI_ASSOC);
	$responseArray = array(
		'code' => 200,
		'message' => 'OK',
		'datas' => $data,
	);
	$json_data = json_encode($responseArray);
	$response->getBody()->write($json_data);

	return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/api/recipes.json', function (Request $request, Response $response, array $args) {
	// Sample log message
	$this->logger->info("Slim-Skeleton '/' route");

	$query = "SELECT id, user_id, name, slug FROM `recipes__recipe`";

	$data = mysqli_query($this->mysqli, $query);
	$data = mysqli_fetch_all($data, MYSQLI_ASSOC);
	$responseArray = array(
		'code' => 200,
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
		'code' => 404,
		'message' => 'Not Found'
	);
	$json_data = json_encode($responseArray);
	$response->getBody()->write($json_data);
	// Render index view

	return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/api/[{name}]', function (Request $request, Response $response, array $args) {
	$this->logger->info("Slim-Skeleton '/' route");
	$responseArray = array(
		'code' => 404,
		'message' => 'Not Found',
	);
	$json_data = json_encode($responseArray);
	$response->getBody()->write($json_data);
	return $this->renderer->render($response, 'index.phtml', $args);
});
