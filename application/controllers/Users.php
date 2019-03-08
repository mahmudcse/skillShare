<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

	function __contruct(){
		parent::__contruct();
	}

	public function allOffers($cityId = null)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->MyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->MyModel->auth();
		        if($response['status'] == 200){
		        	$resp = $this->MyModel->allOffers($cityId);
	    			return json_output($response['status'],$resp);
		        }
			}
		}
	}

	public function offerDetails($offerId = null)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET' || $this->uri->segment(3) == '' || is_numeric($this->uri->segment(3)) == FALSE){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->MyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->MyModel->auth();
		        if($response['status'] == 200){
		        	$resp = $this->MyModel->offerDetails($offerId);
	    			return json_output($response['status'],$resp);
		        }
			}
		}
	}

	public function updateOffer($offerId = null)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'PUT' || $this->uri->segment(3) == '' || is_numeric($this->uri->segment(3)) == FALSE){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->MyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->MyModel->auth();
		        if($response['status'] == 200){

		        	$params = $_REQUEST;
					$_POST['title'] = $params['title'];
					$_POST['description'] = $params['description'];
					$_POST['validTill'] = date('Y-m-d H:i:s', strtotime($params['validTill']));

					if($this->MyModel->updateOffer($offerId)){
						return json_output(200,array('status' => 200,'message' => 'Successfully updated'));
					}else{
						return json_output(400,array('status' => 400,'message' => 'Failed to record. Try again.'));
					}
					
					// $this->form_validation->set_rules('title', 'Title', 'required|trim');
					// $this->form_validation->set_rules('description', 'Description', 'required|trim');

					// if($this->form_validation->run()){
					// 	if($this->MyModel->updateOffer($offerId)){
					// 		return json_output(200,array('status' => 200,'message' => 'Successfully updated'));
					// 	}else{
					// 		return json_output(400,array('status' => 400,'message' => 'Failed to record. Try again.'));
					// 	}
					// }else{
					// 	return json_output(400,array('status' => 400,'message' => 'Bad request. Inputs are not properly formatted'));
					// }
		        }
			}
		}
	}

	public function myOffers()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->MyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->MyModel->auth();
		        if($response['status'] == 200){
		        	$resp = $this->MyModel->myOffers();
	    			return json_output($response['status'],$resp);
		        }
			}
		}
	}

	public function createOffers(){
		$method = $_SERVER['REQUEST_METHOD'];

		if($method != 'POST'){
			json_output(400,array('status' => 400, 'message' => 'Bad request'));
		}else{
			$check_auth_client = $this->MyModel->check_auth_client();

			if($check_auth_client = true){

				$response = $this->MyModel->auth();
		        if($response['status'] == 200){
		        		$params = $_REQUEST;
						$_POST['title'] = $params['title'];
						$_POST['description'] = $params['description'];
						$_POST['validTill'] = date('Y-m-d H:i:s', strtotime($params['validTill']));
						
						$this->form_validation->set_rules('title', 'Title', 'required|trim');
						$this->form_validation->set_rules('description', 'Description', 'required|trim');

						if($this->form_validation->run()){
							if($this->MyModel->createOffers()){
								return json_output(200,array('status' => 200,'message' => 'Successfully created'));
							}else{
								return json_output(400,array('status' => 400,'message' => 'Failed to record. Try again.'));
							}
						}else{
							return json_output(400,array('status' => 400,'message' => 'Bad request. Inputs are not properly formatted'));
						}

				}
			}
		}
	}

	public function updateUserData(){
		$method = $_SERVER['REQUEST_METHOD'];

		if($method != 'PUT'){
			json_output(400,array('status' => 400, 'message' => 'Bad request'));
		}else{
			$check_auth_client = $this->MyModel->check_auth_client();

			if($check_auth_client = true){

				$response = $this->MyModel->auth();
		        if($response['status'] == 200){
		        		$params = $_REQUEST;




						$_POST['firstName'] = $params['firstName'];
						$_POST['lastName'] = $params['lastName'];
						$_POST['contact'] = $params['contact'];
						$_POST['city'] = $params['city'];
						$_POST['address'] = $params['address'];

						
						
						//$this->form_validation->set_rules('firstName', 'First Name', 'required|trim');
						//$this->form_validation->set_rules('lastName', 'Last Name', 'required|trim');
						//$this->form_validation->set_rules('contact', 'Contact', 'required|trim');
						//$this->form_validation->set_rules('city', 'City', 'required|trim');
						//$this->form_validation->set_rules('address', 'Adress', 'required|trim');

						if($this->MyModel->updateUserData()){
							return json_output(200,array('status' => 200,'message' => 'Successfully updated'));
						}else{
							return json_output(400,array('status' => 400,'message' => 'Failed to record. Try again.'));
						}

						// if($this->form_validation->run()){
						// 	if($this->MyModel->updateUserData()){
						// 		return json_output(200,array('status' => 200,'message' => 'Successfully updated'));
						// 	}else{
						// 		return json_output(400,array('status' => 400,'message' => 'Failed to record. Try again.'));
						// 	}
						// }else{
						// 	return json_output(400,array('status' => 400,'message' => 'Bad request. Inputs are not properly formatted'));
						// }

				}
			}
		}
	}

	public function countries(){

		$method = $_SERVER['REQUEST_METHOD'];

		if($method != 'GET'){
			json_output(400,array('status' => 400, 'message' => 'Bad request'));
		}else{
			$check_auth_client = $this->MyModel->check_auth_client();

			if($check_auth_client = true){

				$response = $this->MyModel->auth();
		        if($response['status'] == 200){
		        		return $this->MyModel->countries();
				}
			}
		}
	}

	public function states($countryId = null){

		$method = $_SERVER['REQUEST_METHOD'];

		if($method != 'GET'){
			json_output(400,array('status' => 400, 'message' => 'Bad request'));
		}else{
			$check_auth_client = $this->MyModel->check_auth_client();

			if($check_auth_client = true){

				$response = $this->MyModel->auth();
		        if($response['status'] == 200){
		        	return $this->MyModel->states($countryId);
				}
			}
		}
	}


	public function cities($stateId = null){

	$method = $_SERVER['REQUEST_METHOD'];

	if($method != 'GET'){
		json_output(400,array('status' => 400, 'message' => 'Bad request'));
	}else{
		$check_auth_client = $this->MyModel->check_auth_client();

		if($check_auth_client = true){

			$response = $this->MyModel->auth();
		        if($response['status'] == 200){
		        	return $this->MyModel->cities($stateId);
				}
			}
		}
	}
	
}