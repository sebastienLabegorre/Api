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

$app->get('/api/recipes/[{arg1}]', function (Request $request, Response $response, array $args) {
	$this->logger->info("Slim-Skeleton '/' route");

	$recherche = str_replace('.json', '',$args[arg1]);
	$query = "SELECT slug FROM `recipes__recipe`";
	$slugs = mysqli_query($this->mysqli, $query);
	$slugs = mysqli_fetch_all($slugs, MYSQLI_ASSOC);
	foreach ($slugs as $key => $value) {
		if ($value["slug"] == $recherche) {
			$query = "SELECT id, name, slug, step FROM `recipes__recipe` WHERE slug = '".$recherche."'";
			$data = mysqli_query($this->mysqli, $query);
			$data = mysqli_fetch_all($data, MYSQLI_ASSOC);
			$data = $data[0];
			$responseArray = array(
				'code' => 200,
				'message' => 'OK',
				'datas' => $data,
			);
			$json_data = json_encode($responseArray);
			$response->getBody()->write($json_data);
			return $this->renderer->render($response, 'index.phtml', $args);
		}
	}
	$responseArray = array(
		'code' => 404,
		'message' => 'Not Found',
	);
	$json_data = json_encode($responseArray);
	$response = $response->withStatus(404, 'Not Found');
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
	$response = $response->withStatus(404, 'Not Found');
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
	$response = $response->withStatus(404, 'Not Found');
	$response->getBody()->write($json_data);
	return $this->renderer->render($response, 'index.phtml', $args);
});
