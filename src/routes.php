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

$app->get('/api/delete/[{arg1}]', function (Request $request, Response $response, $args){
	$arg1 = $args['arg1'];
	$query = "DELETE FROM `recipes__recipe` WHERE `slug` = '".$arg1."'";
	mysqli_query($this->mysqli, $query);
	$responseArray = array(
		'code' => 200,
		'message' => 'Delete',
	);
	$json_data = json_encode($responseArray);
	$response = $response->withStatus(200, 'OK');
	$response->getBody()->write($json_data);
	return $this->renderer->render($response, 'index.phtml', $args);
});

$app->put('/api/recipes/[{arg1}]', function ($request, $response, $args) {
	$arg1 = $args['arg1'];
	$arg1 = str_replace('.json', '', $arg1);
	$query = "SELECT * FROM `recipes__recipe` where slug = '".$arg1."'";
	$data = mysqli_query($this->mysqli, $query);
	$data = mysqli_fetch_all($data, MYSQLI_ASSOC);
	$data = $data[0];
	$id = $data["id"];
	$user_id = $data ["user_id"];
	$name = $data ["name"];
	$slug = $data ["slug"];
	$step = $data ["step"];
	$allPostPutVars = $request->getParsedBody();
	var_dump($allPostPutVars);
	exit;
});

$app->post('/api/recipes.json', function (Request $request, Response $response, array $args) {
	if(isset($_POST['step']) && isset($_POST['name']) ){
		if(isset($_POST['slug'])) {
			$query = "SELECT id FROM `recipes__recipe` WHERE `slug` = '".$_POST['slug']."'";
			$slug = mysqli_query($this->mysqli, $query);
			$slug = mysqli_fetch_all($slug, MYSQLI_ASSOC);
			if($slug[0] != array()){
				$responseArray = array(
					'code' => 400,
					'message' => 'Bad Request',
					'datas' => array(),
				);
				$json_data = json_encode($responseArray);
				$response = $response->withStatus(400, 'Bad Request');
				$response->getBody()->write($json_data);
				return $this->renderer->render($response, 'index.phtml', $args);
			}
		} else {
			$_POST['slug'] = time();
		}
		$headerValueArray = $request->getHeader('authorization');
		$password = '';
		if (isset($headerValueArray[0])) {
			$password = $headerValueArray[0];
			$query_pass = "SELECT username, last_login, id FROM `users__user` WHERE password = '".$password."'";
			$password = mysqli_query($this->mysqli, $query_pass);
			$password = mysqli_fetch_all($password, MYSQLI_ASSOC);
			if($password == array()) {
				$responseArray = array(
					'code' => 401,
					'message' => 'Unauthorized',
				);
				$json_data = json_encode($responseArray);
				$response = $response->withStatus(401, 'Unauthorized');
				$response->getBody()->write($json_data);
				return $this->renderer->render($response, 'index.phtml', $args);
			} else {
				$user = $password[0];
			}
			//$element = explode('&', str_replace('"', '', $_POST['-d']));
			$step_str = '';
			$separateur = '';
			foreach ($_POST['step'] as $key => $value) {
				$step_str = $separateur.$step_str.str_replace('+', ' ', $value);
				$separateur = ';';
			}

			$query = "INSERT INTO `recipes__recipe` (
				`user_id`,
				`name`,
				`slug`,
				`step`
				) VALUES (
					'".$user['id']."',
					'".$_POST['name']."',
					'".$_POST['slug']."',
					'".$step_str."'
					)";
			mysqli_query($this->mysqli, $query);

			$mystep = array();
			foreach ($_POST['step'] as $key => $value) {
				$mystep[] = str_replace('+', ' ', $value);
			}

			$id = mysqli_query($this->mysqli, "SELECT * FROM `recipes__recipe`WHERE slug = '".$_POST['slug']."'");
			$id = mysqli_fetch_all($id, MYSQLI_ASSOC);
			$id = $id[0]['id'];
			$data = array(
				'code' => 201,
				'message' => 'Created',
				'datas' => array(
					'id' => $id,
					'name' => $_POST['name'],
					'user' => $user,
					'slug' => $_POST['slug'],
					'step' => $mystep,
				),
			);
			$json_data = json_encode($data);
			$response = $response->withStatus(201, 'Created');
			$response->getBody()->write($json_data);
			return $this->renderer->render($response, 'index.phtml', $args);
		}else {
			$responseArray = array(
				'code' => 401,
				'message' => 'Unauthorized',
			);
			$json_data = json_encode($responseArray);
			$response = $response->withStatus(401, 'Unauthorized');
			$response->getBody()->write($json_data);
			return $this->renderer->render($response, 'index.phtml', $args);
		}
	} else {
		$headerValueArray = $request->getHeader('authorization');
		$password = '';
		if (isset($headerValueArray[0])) {
			$password = $headerValueArray[0];
			$query_pass = "SELECT username, last_login, id FROM `users__user` WHERE password = '".$password."'";
			$password = mysqli_query($this->mysqli, $query_pass);
			$password = mysqli_fetch_all($password, MYSQLI_ASSOC);
			if($password == array()) {
				$responseArray = array(
					'code' => 401,
					'message' => 'Unauthorized',
				);
				$response = $response->withStatus(401, 'Unauthorized');
			}else {
				$responseArray = array(
							'code' => 400,
							'message' => 'Bad Request',
							'datas' => array(),
						);
						$response = $response->withStatus(400, 'Bad Request');
			}
			$json_data = json_encode($responseArray);

				$response->getBody()->write($json_data);
				return $this->renderer->render($response, 'index.phtml', $args);
		}
		$responseArray = array(
			'code' => 401,
			'message' => 'Unauthorized',
		);
		$json_data = json_encode($responseArray);
		$response = $response->withStatus(401, 'Unauthorized');
		$response->getBody()->write($json_data);
		return $this->renderer->render($response, 'index.phtml', $args);
	}
});

$app->get('/api/recipes/{arg1}/steps.json', function (Request $request, Response $response, $args) {
	$this->logger->info("Slim-Skeleton '/' route");

	$arg1 = $args['arg1'];
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
			foreach ($data as $key => $value) {
				$data[$key] = utf8_encode($value);
			}

			$responseArray = array(
				'code' => 200,
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
