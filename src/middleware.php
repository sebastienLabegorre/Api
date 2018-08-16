<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

class middlewere {
	public function __invoke($req, $res, $next) {
		$res->write('<h1>Middle</h1>');
		$res = $next($req, $res);
		$res->write('<h1>Middle END</h1>');
		return $res;
	}
}
$app->add(new Middlewere());