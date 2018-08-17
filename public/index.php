<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
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



require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
