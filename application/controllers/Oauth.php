<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Oauth extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Load user model (for DB operations)
        $this->load->model('User_model');

        // Load Google Client
        require_once APPPATH . '../vendor/autoload.php';
    }

    // STEP 1: Redirect to OAuth Provider
    function all($channel = false)
    {
        $url = '';

        switch ($channel) {

            case 'amazon':
                $this->load->library('channels/amazon');
                $url = $this->amazon->amazon_auth();
                break;

            case 'google':
                $client = new Google_Client();

                // ADD YOUR OWN DETAILS HERE
                $client->setClientId('326577446182-770059rjnvr830ct8hnqe913sfb8j8k2.apps.googleusercontent.com');
                $client->setClientSecret('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');
                $client->setRedirectUri(base_url('oauth/response/google'));

                $client->addScope('email');
                $client->addScope('profile');

                $url = $client->createAuthUrl();
                break;

            default:
                return redirect('/', true);
        }

        if (!empty($url)) {
            return redirect($url);
        }
    }

    // STEP 2: Handle OAuth Response
    function response($channel = false)
    {
        $response = '';

        switch ($channel) {

            case 'amazon':
                $response = $this->amazon_response($channel);
                break;

            case 'google':
                $response = $this->google_response();
                break;

            default:
                redirect('channels', true);
        }

        if (empty($response)) {
            redirect('channels', true);
        }
    }

    // GOOGLE RESPONSE FUNCTION
    function google_response()
    {
        $client = new Google_Client();

        // ADD YOUR OWN DETAILS HERE
        $client->setClientId('326577446182-770059rjnvr830ct8hnqe913sfb8j8k2.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri(base_url('oauth/response/google'));

        $client->addScope('email');
        $client->addScope('profile');

        $code = $this->input->get('code');
        if (empty($code)) {
            log_message('error', 'Google OAuth callback missing code. Query: ' . json_encode($this->input->get()));
            $this->session->set_flashdata('error', 'Google callback code missing. Please try again.');
            redirect('users/login');
            return;
        }

        $token = $client->fetchAccessTokenWithAuthCode($code);
        if (empty($token) || !is_array($token) || !isset($token['access_token'])) {
            $error_message = 'Google login failed. Please try again.';
            if (is_array($token) && !empty($token['error'])) {
                $error_message = !empty($token['error_description']) ? $token['error_description'] : $token['error'];
            }

            log_message('error', 'Google OAuth token error: ' . json_encode($token));
            $this->session->set_flashdata('error', $error_message);
            redirect('users/login');
            return;
        }

        $client->setAccessToken($token);

        $google_service = new Google_Service_Oauth2($client);
        $data = $google_service->userinfo->get();

        $email = !empty($data->email) ? $data->email : '';
        if (empty($email)) {
            $this->session->set_flashdata('error', 'Google account email not available.');
            redirect('users/login');
            return;
        }

        $user = $this->User_model->getByEmail($email);
        if (empty($user)) {
            $insertData = [
                'fname' => $data->name,
                'email' => $data->email,
                'status' => '1',
                'last_login_time' => time(),
            ];

            $this->User_model->insertUser($insertData);
            $user = $this->User_model->getByEmail($email);
        }

        if (empty($user) || empty($user->id)) {
            $this->session->set_flashdata('error', 'Unable to complete Google login.');
            redirect('users/login');
            return;
        }
//to check if the user is active or not
        if (isset($user->status) && (string) $user->status === '0') {
            $this->session->set_flashdata('error', 'Your account is inactive. Please contact support.');
            redirect('users/login');
            return;
        }

        $this->User_model->update($user->id, ['last_login_time' => time()]);

        if (empty($user->company_name) || empty($user->phone) || empty($user->shipping_volume)) {
            // Keep temporary user id for profile completion before full auth login.
            $this->session->set_userdata('google_user_id', $user->id);
            $this->session->set_userdata('user_id', $user->id);
            redirect('users/profile_form');
            return;
        }

        $save_session = (object) [
            'user_id' => $user->id,
            'expire' => time() + 14400,
        ];
        $this->auth->save_session($save_session);
        $this->session->set_userdata('user_id', $user->id);
        $this->session->unset_userdata('google_user_id');

        redirect('success');
    }

    // EXISTING AMAZON FUNCTION (UNCHANGED)
    function amazon_response($channel)
    {

        if (empty($_GET['spapi_oauth_code']))
            die('No spapi_oauth_code');

        if (empty($_GET['spapi_oauth_code']))
            die('No spapi_oauth_code');

        $partner_id = $_GET['selling_partner_id'];
        $oauth_code = $_GET['spapi_oauth_code'];

        $this->load->library('channels/amazon');

        $response = $this->amazon->amazonGetAccesstoken($oauth_code);

        if (!empty($response) && (empty($response->error))) {
            $this->load->library('channels_lib');
            $this->user = $this->auth->logged_in();
            $channels = $this->channels_lib->isChannelExists($this->user->user_id, $channel, $partner_id);
            if (empty($channels)) {
                $save = array(
                    'user_id' => $this->user->user_id,
                    'channel' => $channel,
                    'channel_name' => $channel,
                    'api_field_1' => $partner_id,
                    'api_field_2' => $response->access_token,
                    'api_field_6' => $response->refresh_token,
                );
                $new_id = $this->channels_lib->create($save);
                do_action('channel.create', $new_id);
            } else {
                $new_id = $channels->id;
            }
            $this->session->set_flashdata('success', 'Channel Integrated successfully.');
            redirect('channels/edit/' . $new_id . '?status=success', true);
        } else {
            $this->session->set_flashdata('error', $response->error_description);
        }
        redirect('channels', true);
    }
}