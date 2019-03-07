<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __contruct(){
		parent::__contruct();
	}

	public function registration(){
		$method = $_SERVER['REQUEST_METHOD'];

		if($method != 'POST'){
			json_output(400,array('status' => 400, 'message' => 'Bad request'));
		}else{
			$check_auth_client = $this->MyModel->check_auth_client();

			if($check_auth_client = true){
				$params = $_REQUEST;
				$_POST['email'] = $params['email'];
				$_POST['password'] = $params['password'];


				$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]');
				$this->form_validation->set_rules('password', 'Password', 'required|trim');

				if($this->form_validation->run()){
					if($this->MyModel->registration()){
						return json_output(200,array('status' => 200,'message' => 'Successfully registered'));
					}else{
						return json_output(400,array('status' => 400,'message' => 'Failed to record. Try again.'));
					}
				}else{
					return json_output(400,array('status' => 400,'message' => 'Bad request.email exists or not properly formatted'));
				}
			}
		}
	}

	public function login()
	{

		$method = $_SERVER['REQUEST_METHOD'];

		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {

			$check_auth_client = $this->MyModel->check_auth_client();
			
			if($check_auth_client == true){

				$params = $_REQUEST;

		        $email = $params['email'];
		        $password = $params['password'];

		        $response = $this->MyModel->login($email,$password);
				json_output($response['status'],$response);
			}
		}
	}

	public function logout()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->MyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->MyModel->logout();
				json_output($response['status'],$response);
			}
		}
	}
	
}
