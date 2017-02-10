<?php
use Abraham\TwitterOAuth\TwitterOAuth;
/**
 * Twitter_lib short summary.
 *
 * Twitter_lib description.
 * https://twitteroauth.com/
 *
 * @version 1.0
 */
class Twitter_lib {
    protected $CI;
    public $lock;
    
    public function __construct() {
        // Do something with $params
        $this->CI =& get_instance();

        $this->CI->load->helper('string');
    }

    public function app_auth_url() {
        try {
            $connection = new TwitterOAuth($this->CI->config->item('twitter_consumer_key'), $this->CI->config->item('twitter_consumer_secret'));

            $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $this->CI->config->item('twitter_auth_callback')));

            $aSessionData['twitter_app_oauth_token'] = $request_token['oauth_token'];
            $aSessionData['twitter_app_oauth_token_secret'] = $request_token['oauth_token_secret'];

            $this->CI->session->set_userdata($aSessionData);

            $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

            return '<a href="'.$url.'">Authorise this app</a>';
        }
        catch(Exception $ex) {
            log_message('error', 'DCI-TWL-AAU-0001 ' . $ex->getMessage());
            return false;
        }
    }

    public function auth_response() {
        $oauth_verifier = filter_input(INPUT_GET, 'oauth_verifier');
        
        if (empty($oauth_verifier) ||
            empty($_SESSION['twitter_app_oauth_token']) ||
            empty($_SESSION['twitter_app_oauth_token_secret'])
        ) {
            log_message('error', 'DCI-TWL-AUR-0001 Missing OAuth tokens or verifier');
            return false;
        }

        try {
            // connect with application token
            $connection = new TwitterOAuth(
                $this->CI->config->item('twitter_consumer_key'),
                $this->CI->config->item('twitter_consumer_secret'),
                $this->CI->session->twitter_app_oauth_token,
                $this->CI->session->twitter_app_oauth_token_secret
            );
            
            // request user token
            $oUserTokenZ = $connection->oauth(
                'oauth/access_token', [
                    'oauth_verifier' => $oauth_verifier
                ]
            );

            $oTwitterZ = new TwitterOAuth(
                $this->CI->config->item('twitter_consumer_key'),
                $this->CI->config->item('twitter_consumer_secret'),
                $oUserTokenZ['oauth_token'],
                $oUserTokenZ['oauth_token_secret']
            );

            $oCredentialsZ = $oTwitterZ->get("account/verify_credentials");

            if(property_exists($oCredentialsZ, 'screen_name')) {
                $aSessionData['twitter_user_oauth_token'] = $oUserTokenZ['oauth_token'];
                $aSessionData['twitter_user_oauth_token_secret'] = $oUserTokenZ['oauth_token_secret'];
                $aSessionData['twitter_user_screen_name'] = $oCredentialsZ->screen_name;

                $this->CI->session->set_userdata($aSessionData);

                return true;
            }
            else {
                log_message('error', 'DCI-TWL-AUR-0002 Screen_name property missing from verify_credentials response');
                return false;
            }
        }
        catch(Exception $ex) {
            log_message('error', 'DCI-TWL-AUR-0003 ' . $ex->getMessage());
            return false;
        }
    }

    public function is_signed_in() {
        try {
            $oTwitterZ = new TwitterOAuth(
                $this->CI->config->item('twitter_consumer_key'),
                $this->CI->config->item('twitter_consumer_secret'),
                $this->CI->session->twitter_user_oauth_token,
                $this->CI->session->twitter_user_oauth_token_secret
            );

            $oCredentialsZ = $oTwitterZ->get("account/verify_credentials");
            
            if ($oTwitterZ->getLastHttpCode() == 200) {
                return true;
            } else {
                log_message('error', 'DCI-TWL-ISI-0001 ' . $oTwitterZ->getLastHttpCode());
                return false;
            }
        }
        catch(Exception $ex) {
            log_message('error', 'DCI-TWL-ISI-0002 ' . $ex->getMessage());
            return false;
        }
    }
}