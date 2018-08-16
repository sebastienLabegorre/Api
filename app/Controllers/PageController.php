<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PageController {
	private $container;

	public function __construct($container) {
		$this->container = $container;
	}

	public function home(RequestInterface $req, ResponseInterface $res) {
		$res->getBody()->write('salut seb');
	}

	public function pole(RequestInterface $req, ResponseInterface $res) {
		$res->getBody()->write('salut pole');
	}

	public function getAllUsers($req, $res){
		$query = "SELECT * FROM `users__user`";
		$data = $this->container->db->query($query);

		$responseArray = array(
			'code' => '200',
			'message' => 'OK',
			'datas' => $data,
		);
		$json_data = json_encode($responseArray);
		$res->getBody()->write($json_data);
	}

	public function getCategoryRecherche($req, $res, $args) {
		$query = "SELECT * FROM `recipes__category` WHERE name = '".$args["arg1"]."'";
		$json_data = $this->serializeGet($query);
		$res->getBody()->write($json_data);
	}

	public function getUser($id){
		$query = "SELECT username, last_login, id FROM `users__user` WHERE id = ".$id;
		return $this->container->db->query($query);
	}
	public function addRecipes($req, $res){
		if(isset($_POST['-d'])){
			if (isset($_POST['-H'])) {
				$password = explode(' ', str_replace('"', '', $_POST['-H']));
				$password = $password[1];
				$query_pass = "SELECT username, last_login, id FROM `users__user` WHERE password = '".$password."'";

				$password = $this->container->db->query($query_pass);
				if($password == array()) {
					return $this->defaultResponce401($req, $res, array());
				} else {
					$user = $password[0];
				}
				$element = explode('&', str_replace('"', '', $_POST['-d']));

				foreach ($element as $key => $value) {
					$part = explode('=', str_replace('+', ' ', $value));
					$element[$part[0]] = $part[1];
				}
				if(!isset($element['slug'])){
					$element['slug'] = time();
				}
				$query = "INSERT INTO `recipes__recipe` (
					`user_id`,
					`name`,
					`slug`,
					`step`
					) VALUES (
						'".$user['id']."',
						'".$element['name']."',
						'".$element['slug']."',
						'".$element['step[]']."'
						)";
				$this->container->db->add($query);

				$id = $this->container->db->query("SELECT * FROM `recipes__recipe`WHERE slug = '".$element['slug']."'");
				$id = $id[0]['id'];
				$data = array(
					'code' => '201',
					'message' => 'Created',
					'datas' => array(
						'id' => $id,
						'name' => $element['name'],
						'user' => $user,
						'slug' => $element['slug'],
						'step' => explode(';', $element['step[]'])
					),
				);
				$json_data = json_encode($data);
				$res->getBody()->write($json_data);

			}else {
				return $this->defaultResponce403($req, $res, array());
			}
		} else {
			return $this->defaultResponce403($req, $res, array());
		}
	}

	public function getRecipesStep($req, $res, $args) {
		$arg = $args['arg1'];
		$query = "SELECT step FROM `recipes__recipe` where slug = '".$arg."'";
		$data = $this->container->db->query($query);
		$data = str_replace('"', '', $data[0]["step"]);
		$data = explode (';', $data);

		$responseArray = array(
			'code' => '200',
			'message' => 'OK',
			'datas' => $data,
		);
		$json_data = json_encode($responseArray);
		$res->getBody()->write($json_data);
	}

	public function getRecipes($req, $res, $args){
		$tmp = '';
		$end_query = " WHERE slug = ";
		foreach ($args as $key => $value) {
			$end_query = $end_query.$tmp."'".$value."'";
			$tmp = ' OR slug = ';
		}
		$query = "SELECT * FROM `recipes__recipe`".$end_query;
		$data = $this->container->db->query($query);

		if($data == array()) {
			return $this->defaultResponce404($req, $res, $args);
		}

		foreach($data as $key => $value){
			$datas = array();
			$user = $this->getUser($value["user_id"]);
			$user = $user[0];
			$datas[] = array(
				'id' => $value['id'],
				'name' => $value['name'],
				'user' => $user,
				'slug' => $value['slug'],
			);
		}
		$responseArray = array(
			'code' => '200',
			'message' => 'OK',
			'datas' => $datas,
		);
		$json_data = json_encode($responseArray);
		$res->getBody()->write($json_data);
	}

	public function getRecipesByCategory($req, $res, $args){
		$base_query = "SELECT RR.id, RR.user_id, RR.name, RR.slug FROM recipes__category RC LEFT JOIN recipes__recipe_category RRC ON RC.id = RRC.category_id LEFT JOIN recipes__recipe RR ON RRC.recipe_id = RR.id ";
		$end_query = "WHERE ";
		foreach ($args as $key => $value) {
			$end_query = $end_query."RC.name = '".$value."'";
		}
		$query = $base_query.$end_query;
		//var_dump($query);exit;
		$json_data = $this->serializeGet($query);
		$res->getBody()->write($json_data);
	}

	public function getCategory($req, $res){
		$query = 'SELECT * FROM `recipes__category`';
		$json_data = $this->serializeGet($query);
		$res->getBody()->write($json_data);
	}

	public function serializeGet($query){
		$data = $this->container->db->query($query);

		$responseArray = array(
			'code' => '200',
			'message' => 'OK',
			'datas' => $data,
		);
		$json_data = json_encode($responseArray);

		return $json_data;
	}

	public function getAllRecipesCategory($req, $res) {
		$query = "SELECT * FROM `recipes__recipe_category`";
		$data = $this->container->db->query($query);

		$responseArray = array(
			'code' => '200',
			'message' => 'OK',
			'datas' => $data,
		);
		$json_data = json_encode($responseArray);
		$res->getBody()->write($json_data);
	}

	public function getAllRecipes(RequestInterface $req, ResponseInterface $res) {
		$query = "SELECT id, user_id, name, slug FROM `recipes__recipe`";
		$data = $this->container->db->query($query);

		$responseArray = array(
			'code' => '200',
			'message' => 'OK',
			'datas' => $data,
		);
		$json_data = json_encode($responseArray);
		$res->getBody()->write($json_data);
	}

	public function defaultResponce($req, $res, $args) {
		$data = array(
			'code' => '200',
			'message' => 'OK',
			'args' => $args,
			'datas' => array(),
		);
		$json_data = json_encode($data);
		$res->getBody()->write($json_data);
	}

	public function defaultResponce200($req, $res, $args){
		$data = array(
			'code' => '200',
			'message' => 'OK',
			'datas' => array(),
		);
		$json_data = json_encode($data);
		$res->getBody()->write($json_data);
	}
	public function defaultResponce201($req, $res, $args){
		$data = array(
			'code' => '201',
			'message' => 'Created',
			'datas' => array(),
		);
		$json_data = json_encode($data);
		$res->getBody()->write($json_data);
	}
	public function defaultResponce400($req, $res, $args){
		$data = array(
			'code' => '400',
			'message' => 'Bad Request',
			'datas' => array(),
		);
		$json_data = json_encode($data);
		$res->getBody()->write($json_data);
	}
	public function defaultResponce401($req, $res, $args){
		$data = array(
			'code' => '401',
			'message' => 'Unauthorized',
		);
		$json_data = json_encode($data);
		$res->getBody()->write($json_data);
	}
	public function defaultResponce403($req, $res, $args){
		$data = array(
			'code' => '403',
			'message' => 'Forbidden'
		);
		$json_data = json_encode($data);
		$res->getBody()->write($json_data);
	}
	public function defaultResponce404($req, $res, $args){
		$data = array(
			'code' => '404',
			'message' => 'Not Found',
			'datas' => array(),
		);
		$json_data = json_encode($data);
		$res->getBody()->write($json_data);
	}
}