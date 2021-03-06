<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MyModel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key       = "simplerestapi";

    public function check_auth_client(){

        return true;

        // $client_service = $this->input->get_request_header('Client-Service', TRUE);
        // $auth_key  = $this->input->get_request_header('Auth-Key', TRUE);
        
        // if($client_service == $this->client_service && $auth_key == $this->auth_key){
        //     return true;
        // } else {
        //     return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        // }
    }

    public function login($username,$password)
    {
        $q  = $this->db->select('password,id')->from('users')->where('email',$username)->get()->row();
       
        if($q == ""){
            return array('status' => 204,'message' => 'User not found.');
        } else {
            $hashed_password = $q->password;
            $id              = $q->id;
            // echo $hashed_password ." ".$password;
        //exit;
            // if (hash_equals($hashed_password, crypt($password, $hashed_password))) {

            if(password_verify($password, $hashed_password)){

               $last_login = date('Y-m-d H:i:s');
               $token = md5(rand());
               //$token = crypt(substr( md5(rand()), 0, 7));
               $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
               $this->db->trans_start();
               $this->db->where('id',$id)->update('users',array('lastLogedIn' => $last_login));
               $this->db->insert('users_authentication',array('users_id' => $id,'token' => $token,'expired_at' => $expired_at));
               if ($this->db->trans_status() === FALSE){
                  $this->db->trans_rollback();
                  return array('status' => 500,'message' => 'Internal server error.');
               } else {
                  $this->db->trans_commit();
                  return array('status' => 200,'message' => 'Successfully login.','id' => $id, 'token' => $token);
               }
            } else {
                echo "Wrong password";
                exit();
               return array('status' => 204,'message' => 'Wrong password.');
            }
        }
    }

    public function logout()
    {
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token     = $this->input->get_request_header('Authorization', TRUE);
        $this->db->where('users_id',$users_id)->where('token',$token)->delete('users_authentication');
        return array('status' => 200,'message' => 'Successfully logout.');
    }

    public function auth()
    {
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token     = $this->input->get_request_header('Authorization', TRUE);
        $q  = $this->db->select('expired_at')->from('users_authentication')->where('users_id',$users_id)->where('token',$token)->get()->row();
        if($q == ""){
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        } else {
            if($q->expired_at < date('Y-m-d H:i:s')){
                return json_output(401,array('status' => 401,'message' => 'Your session has been expired.'));
            } else {
                //$updated_at = date('Y-m-d H:i:s');
                $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
                $this->db->where('users_id',$users_id)->where('token',$token)->update('users_authentication',array('expired_at' => $expired_at));
                return array('status' => 200,'message' => 'Authorized.');
            }
        }
    }

    public function registration(){

        $data = array(
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT)
        );

        $insert = $this->db->insert('users', $data);

        if($insert){
            return true;
        }else{
            return false;
        }

    }

    public function updateOffer($offerId = null){
        $data = array(
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'validTill' => $this->input->post('validTill')
        );

        $userId = $this->input->get_request_header('User-ID', TRUE);
        $this->db->where('id', $offerId);
        $this->db->where('userId', $userId);
        $update = $this->db->update('offers', $data);

        if($update){
            return true;
        }else{
            return false;
        }
    }

    public function updateUserData(){

        $data = array(
                'firstName' => $this->input->post('firstName'),
                'lastName' => $this->input->post('lastName'),
                'contact' => $this->input->post('contact'),
                'city' => $this->input->post('city'),
                'address' => $this->input->post('address'),
                'createdAt' => date('Y-m-d H:i:s')
        );

        $userId = $this->input->get_request_header('User-ID', TRUE);
        $this->db->where('id', $userId);
        $update = $this->db->update('users', $data);

        if($update){
            return true;
        }else{
            return false;
        }

    }

    public function myOffers(){

            $this->db->select('o.id, o.title, o.description, o.createdAt, o.validTill, o.userId');
            $this->db->from('offers o');
            $this->db->where('o.userId', $this->input->get_request_header('User-ID', TRUE));
            $myOffers = $this->db->get()->result_array();
            return $myOffers;
    }

    public function allOffers($cityId = null){

            $this->db->select('o.id,
                                o.title,
                                o.description,
                                o.validTill,
                                u.contact,
                                u.address');
            $this->db->from('offers o');
            $this->db->join('users u', "u.id = o.userId AND u.city = $cityId", 'INNER');
            $this->db->where('o.validTill >', date('Y-m-d H:i:s'));
            $allOffers = $this->db->get()->result_array();
            return $allOffers;
    }    

    public function offerDetails($offerId = null){
        $this->db->select('*');
        $this->db->from('offers o');
        $this->db->where('o.id', $offerId);
        $offerDetails = $this->db->get()->result_array();
        return $offerDetails;
    }

    public function createOffers(){

        $data = array(
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'createdAt' =>  date('Y-m-d H:i:s'),
                'userId' => $this->input->get_request_header('User-ID', TRUE),
                'validTill' => $this->input->post('validTill')
        );
        $insert = $this->db->insert('offers', $data);

        if($insert){
            return true;
        }else{
            return false;
        }

    }

    public function countries(){
        $this->db->select('c.id,c.name');
        $this->db->from('countries c');
        $countries = $this->db->get()->result_array();
        return json_output(400, $countries);
    }

    public function states($countryId = null){

        $this->db->select('s.id, s.name');
        $this->db->from('states s');
        $this->db->where('s.countryId', $countryId);
        $states = $this->db->get()->result_array();
        return json_output(400, $states);
    }

    public function cities($stateId = null){

        $this->db->select('c.id, c.name');
        $this->db->from('cities c');
        $this->db->where('c.stateId', $stateId);
        $cities = $this->db->get()->result_array();
        return json_output(400, $cities);
    }

    public function book_all_data()
    {
        return $this->db->select('id,title,author')->from('books')->order_by('id','desc')->get()->result();
    }

    public function book_detail_data($id)
    {
        return $this->db->select('id,title,author')->from('books')->where('id',$id)->order_by('id','desc')->get()->row();
    }

    public function book_create_data($data)
    {
        $this->db->insert('books',$data);
        return array('status' => 201,'message' => 'Data has been created.');
    }

    public function book_update_data($id,$data)
    {
        $this->db->where('id',$id)->update('books',$data);
        return array('status' => 200,'message' => 'Data has been updated.');
    }

    public function book_delete_data($id)
    {
        $this->db->where('id',$id)->delete('books');
        return array('status' => 200,'message' => 'Data has been deleted.');
    }

}
