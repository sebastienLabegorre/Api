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

$app->get('/api/recipes/{arg1}/steps.json', function (Request $request, Response $response, $arg1) {
	$this->logger->info("Slim-Skeleton '/' route");

	var_dump($arg1);
	exit;

	$query = "SELECT slug FROM `recipes__recipe`";
	$slugs = mysqli_query($this->mysqli, $query);
	$slugs = mysqli_fetch_all($slugs, MYSQLI_ASSOC);
	foreach ($slugs as $key => $value) {
		if ($value["slug"] == $arg1) {
			$query = "SELECT step FROM `recipes__recipe` where slug = '".$arg1."'";
			$data = mysqli_query($this->mysqli, $query);
			$data = mysqli_fetch_all($data, MYSQLI_ASSOC);
			$data = str_replace('"', '', $data[0]["step"]);
			$data = explode (';', $data);
			$responseArray = array(
				'code' => '200',
				'message' => 'OK',
				'datas' => $data,
			);
			$json_data = json_encode($responseArray);
			$response->getBody()->write($json_data);
			return $this->renderer->render($response, 'index.phtml', array());
		}
	}
	$responseArray = array(
		'code' => 404,
		'message' => 'Not Found',
	);
	$json_data = json_encode($responseArray);
	$response = $response->withStatus(404, 'Not Found');
	$response->getBody()->write($json_data);
	return $this->renderer->render($response, 'index.phtml', array());
});

$app->get('/api/recipes/[{arg1}]', function (Request $request, Response $response, array $args) {
	$this->logger->info("Slim-Skeleton '/' route");

	$recherche = str_replace('.json', '',$args[arg1]);
	$query = "SELECT slug FROM `recipes__recipe`";
	$slugs = mysqli_query($this->mysqli, $query);
	$slugs = mysqli_fetch_all($slugs, MYSQLI_ASSOC);
	foreach ($slugs as $key => $value) {
		if ($value["slug"] == $recherche) {
			$query = "SELECT * FROM `recipes__recipe` WHERE slug = '".$recherche."'";
			$data = mysqli_query($this->mysqli, $query);
			$data = mysqli_fetch_all($data, MYSQLI_ASSOC);
			$data = $data[0];
			$query = "SELECT username, last_login, id FROM `users__user` WHERE id = ".$data["user_id"];
			$user = mysqli_query($this->mysqli, $query);
			$user = mysqli_fetch_all($user, MYSQLI_ASSOC);
			$user = $user[0];
			$responseArray = array(
				'code' => 200,
				'message' => 'OK',
				'datas' => array(
					'id' => $data['id'],
					'name' => $data['name'],
					'user' => $user,
					'slug' => $data['slug'],
				),
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

	$query = "SELECT id, name, slug FROM `recipes__recipe`";

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
