<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    function __construct(){
        parent::__construct();
        // is_signed_in($this);
        //$this->load->library('Twitter_lib');
    }

	public function index()
	{
        $this->session->is_signed_in($this, false);

        echo '<p>Screen Name: ' . $this->session->twitter_user_screen_name . '</p>';
		$this->load->view('welcome_message');
	}

	public function ddb()
	{
		$sdk = new Aws\Sdk([
    		'region'   => 'eu-west-1', // US West (Oregon) Region
    		'version'  => 'latest',
    		'credentials' => [
        		'key'    => 'my-access-key-id',
        		'secret' => 'my-secret-access-key',
    		],
		]);

		// Create a new DynamoDB client
		$dynamodb = $sdk->createDynamoDb();

		$result = $client->listTables([
    		'ExclusiveStartTableName' => '<string>',
    		'Limit' => 10,
		]);
	}
}
