<?php

require '../vendor/autoload.php';

class middlewere {
	public function __invoke($req, $res, $next) {
		//$res->write('<h1>Middle</h1>');
		$res = $next($req, $res);
	//	$res->write('<h1>Middle END</h1>');
		return $res;
	}
}

class database {
	private $mysqli;
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	public function query($query) {
		$result = mysqli_query($this->mysqli, $query);
		$result = mysqli_fetch_all($result, MYSQLI_ASSOC);
		return $result;
	}
	public function add($query){
		mysqli_query($this->mysqli, $query);
		return 1;
	}
}

$app = new \Slim\App ([
	'settings' => [
		'displayErrorDetails' => true,
	]
]);

$container = $app->getContainer();
$container['mysqli'] = function() {
/*	$pdo	=	new PDO('mysql:dbname=etna-rest;host=localhost', 'root', '');
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);*/
	$mysqli = new mysqli("localhost","root","","etna-rest");
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	return $mysqli;
//	return $pdo;
};
$container['db'] = function($container) {
	return new Database($container->mysqli);
};
$app->add(new Middlewere());


//$app->get('/', \App\Controllers\PageController::class . ':home');
$app->get('/recipes', 							\App\Controllers\PageController::class . ':getAllRecipes');
$app->get('/recipes/{arg1}', 				\App\Controllers\PageController::class . ':getRecipes');
$app->get('/recipes/{arg1}/steps', 	\App\Controllers\PageController::class . ':getRecipesStep');
$app->get('/recipes/{arg1}/{arg2}', \App\Controllers\PageController::class . ':defaultResponce404');
$app->get('/recipes/{arg1}/{arg2}/', \App\Controllers\PageController::class . ':defaultResponce404');
$app->get('/recipes/{arg1}/{arg2}/{arg3}', \App\Controllers\PageController::class . ':defaultResponce404');
//$app->post('/api/recipes/{arg1}', \App\Controllers\PageController::class . ':defaultResponce404');
$app->post('/api/recipes', \App\Controllers\PageController::class . ':addRecipes');

$app->get('/allUsers', \App\Controllers\PageController::class . ':getAllUsers');
$app->get('/category', \App\Controllers\PageController::class . ':getCategory');
$app->get('/category/{arg1}', \App\Controllers\PageController::class . ':getCategoryRecherche');
$app->get('/recipesbycategory/{arg1}', \App\Controllers\PageController::class . ':getRecipesByCategory');

//$app->get("/users[/{.json}]", \App\Controllers\PageController::class . ':getAllRecipes');
//$app->get('/pole', \App\Controllers\PageController::class . ':pole');

$app->get('/{arg1}', \App\Controllers\PageController::class . ':defaultResponce404');
$app->get('/{arg1}/', \App\Controllers\PageController::class . ':defaultResponce404');
$app->get('/{arg1}/{arg2}/', \App\Controllers\PageController::class . ':defaultResponce404');
$app->get('/{arg1}/{arg2}/{arg3}/', \App\Controllers\PageController::class . ':defaultResponce404');

$app->run();