<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signin extends CI_Controller {

	function __construct(){
        parent::__construct();
        // is_signed_in($this);
        $this->load->library('Twitter_lib');
    }

	public function index()
	{
		echo $this->twitter_lib->app_auth_url();
	}

    public function process()
    {
        if($this->twitter_lib->auth_response()) {
            redirect('welcome');
        }
    }
}