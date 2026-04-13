<?php

use App\Lib\Logs\Log;

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends Front_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('user_lib');
    }

    public function index()
    {
        self::signup();
    }


    function register()
    {
        $this->data['title'] = 'User Signup';
        $this->load->library('form_validation');
        $config = array(
            // array(
            //     'field' => 'shipping_volume',
            //     'label' => 'Shipping Volume',
            //     'rules' => 'trim|required'
            // ),
            array(
                'field' => 'firstName',
                'label' => 'First Name',
                'rules' => 'trim|required|min_length[2]|max_length[40]|alpha_numeric_spaces'
            ),
            // array(
            //     'field' => 'lname',
            //     'label' => 'Last Name',
            //     'rules' => 'trim|required|min_length[2]|max_length[20]|alpha_numeric_spaces'
            // ),
            array(
                'field' => 'companyName',
                'label' => 'Company Name',
                'rules' => 'trim|required|min_length[2]|max_length[50]'
            ),
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required|min_length[3]|max_length[200]|valid_email'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|min_length[8]|max_length[50]|callback_check_strong_password'
            ),
            array(
                'field' => 'potential',
                'label' => 'Shipment Potential',
                'rules' => 'trim|required'
            ),
            // array(
            //     'field' => 'state',
            //     'label' => 'State',
            //     'rules' => 'trim|required'
            // ),
            array(
                'field' => 'phone',
                'label' => 'Contact Number',
                'rules' => 'trim|required|exact_length[10]|numeric|callback_check_phonenumber'
            ),
            array(
                'field' => 'is_agree',
                'label' => 'Privacy Statement Check box',
                'rules' => 'required'
            )
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $is_data_validate = $this->input->post('is_data_validate');

            if (!empty($is_data_validate)) {
                $this->data['error_message'] = 'Something is going wrong.Please try again.';
            } else {
                $save_data = array(
                    'fname' => $this->input->post('firstName'),
                    'lname' => $this->input->post('lastName'),
                    'company_name' => $this->input->post('companyName'),
                    'email' => $this->input->post('email'),
                    'password' => $this->input->post('password'),
                    'phone' => $this->input->post('phone'),
                    'shipping_volume' => $this->input->post('potential'),
                    // 'seller_region' => $this->input->post('state'),
                    'status' => '1',
                    'ivr_call_chargeable' => '0',
                    'allotted_free_minutes' => $this->config->item('free_unit'),
                    'last_login_time' => time(),
                );

                if (!$new_user_id = $this->user_lib->create_user($save_data)) {
                    $this->data['error_message'] = $this->user_lib->get_error();
                } else {
                    //do_action('users.signup', $new_user_id);
                    $this->login_with_signup($save_data);
                }
            }
        } else {
            $this->data['error'] = validation_errors();
        }

        $signupView = 'user/signup';
        $signupPath = APPPATH . 'views/user/signup.php';
        if (!file_exists($signupPath) || filesize($signupPath) === 0) {
            $signupView = 'user/signupnew';
        }

        $this->layout($signupView, 'login_layout');
    }

    function signup()
    {
        return $this->register();
    }



    function login_with_signup($arrData = false)
    {
        if (!empty($arrData)) {
            //login
            $email = $arrData['email'];
            $password = $arrData['password'];
            if ($user = $this->user_lib->user_login($email, $password)) {
                $token = $this->user_lib->userApiLogin($email, $password);

                ?>
                <script>
                    localStorage.setItem('token', '<?= $token ?>');
                </script>
                <?php
                if (!empty($r = $this->input->post('r'))) {
                    redirect(urldecode(($r)), true);
                } else {
                    if (strtolower($user->user_type) == 'telecaller')
                        redirect('caller', true);

                    if (!empty($this->session->tempdata('channel')) && $this->session->tempdata('channel') == 'amazon') {
                        $url = $this->session->tempdata('callback_uri');
                        $this->session->unset_tempdata('channel');
                        $this->session->unset_tempdata('callback_uri');
                        return redirect($url);
                    }

                    if ($user->is_admin == '1') {
                        $this->session->set_flashdata('success', 'Account has been created successfully. Welcome to dashboard.');

                        redirect('admin', true);
                    } else {
                        $save_data['last_login_time'] = time();
                        $this->user_lib->update($user->id, $save_data);
                        $this->session->set_flashdata('success', 'Account has been created successfully. Welcome to dashboard.');
                        redirect('success', true);
                    }
                }
            } else {
                $this->data['error'] = $this->user_lib->get_error();
            }
        }

        $this->layout('user/login', 'login_layout');
    }


    function login()
    {
        $identity = trim((string) $this->input->post('identity'));
        $emailInput = trim((string) $this->input->post('email'));
        $password = trim((string) $this->input->post('password'));

        // Backward-compatible input handling:
        // - old form posts `email`
        // - new form posts `identity` (email or phone)
        if (empty($identity)) {
            $identity = $emailInput;
        }

        if (empty($identity) || empty($password)) {
            $this->data['error'] = 'Email/phone and password are required.';
            $this->data['title'] = 'User Login';
            $this->layout('user/login', 'login_layout');
            return;
        }

        $email = $this->_resolveLoginEmail($identity);
        if (empty($email)) {
            $this->data['error'] = 'Invalid email/phone or password';
            $this->data['title'] = 'User Login';
            $this->layout('user/login', 'login_layout');
            return;
        }

        //****************************************Code to check admin OTP*************/
        $userDetails = $this->user_lib->userByEmailPassword($email, sha1($password));
        if (isset($userDetails->is_admin) && $userDetails->is_admin == '1') {
            $token = $this->user_lib->userApiLogin($email, $password);
            $userDetails->token = $token;
            self::login_with_admin($userDetails);
            return false;
        }

        if ($user = $this->user_lib->user_login($email, $password)) {
            $token = $this->user_lib->userApiLogin($email, $password);

            ?>
            <script>
                localStorage.setItem('token', '<?= $token ?>');
            </script>
            <?php
            if (!empty($r = $this->input->post('r'))) {
                redirect(urldecode(($r)), true);
            } else {
                if (strtolower($user->user_type) == 'telecaller')
                    redirect('caller', true);

                if ((!empty($this->session->tempdata('channel')) && $this->session->tempdata('channel') == 'amazon') || (!empty($this->session->tempdata('channel')) && $this->session->tempdata('channel') == 'shopify')) {
                    $url = $this->session->tempdata('callback_uri');
                    $this->session->unset_tempdata('channel');
                    $this->session->unset_tempdata('callback_uri');
                    return redirect($url);
                }

                if ($user->is_admin == '1') {
                    redirect('admin', true);
                } else {
                    $save_data['last_login_time'] = time();
                    $this->user_lib->update($user->id, $save_data);
                    redirect('analytics', true);
                }
            }
        } else {
            $this->data['error'] = $this->user_lib->get_error();
        }
        $this->data['title'] = 'User Login';
        $this->layout('user/login', 'login_layout');
    }

    public function login_process()
    {
        return $this->login();
    }

    public function check_user()
    {
        $payload = json_decode($this->input->raw_input_stream, true);
        $identity = '';

        if (is_array($payload) && isset($payload['identity'])) {
            $identity = trim((string) $payload['identity']);
        }
        if ($identity === '') {
            $identity = trim((string) $this->input->post('identity'));
        }
        if ($identity === '') {
            $identity = trim((string) $this->input->get('identity'));
        }

        $status = false;
        $message = 'No account found for this email/phone.';

        if ($identity !== '') {
            if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
                $status = (bool) $this->user_lib->getByEmail($identity);
            } else {
                $phone = $this->_normalizePhone($identity);
                if (!empty($phone)) {
                    $status = (bool) $this->user_lib->getByMobile($phone);
                }
            }
        }

        if ($status) {
            $message = 'Account found.';
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => $status,
                'message' => $message,
            ]));
    }

    private function _resolveLoginEmail($identity = '')
    {
        $identity = trim((string) $identity);
        if ($identity === '') {
            return '';
        }

        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            return $identity;
        }

        $phone = $this->_normalizePhone($identity);
        if (empty($phone)) {
            return '';
        }

        $user = $this->user_lib->getByMobile($phone);
        if (empty($user) || empty($user->email)) {
            return '';
        }

        return $user->email;
    }

    private function _normalizePhone($identity = '')
    {
        $digits = preg_replace('/\D+/', '', (string) $identity);

        if (strlen($digits) === 10) {
            return $digits;
        }

        if (strlen($digits) === 12 && strpos($digits, '91') === 0) {
            return substr($digits, 2);
        }

        return '';
    }
    function userAdminOtp($userDetails)
    {
        if (empty($userDetails->phone)) {
            $this->data['error'] = 'Mobile no. is not registered';
            $this->layout('user/login', 'login_layout');
            return false;
        }
        $this->load->library('sms');
        $otp = rand(1111, 9999);
        $this->data["otp"] = $otp;
        $update['user_login_otp'] = $otp;
        if (ENVIRONMENT != 'development') {
            $isOTPsent = $this->sms->send_sms($userDetails->phone, 'otp', (object) $this->data);
            $this->user_lib->sendOTPViaEmail($userDetails->id, $otp);
        } else {
            $update['user_login_otp'] = '1234'; // default OTP
        }

        $this->user_lib->update_user_otp($userDetails->id, $update);
        $this->session->set_userdata('user_id', $userDetails->id);
        $this->session->set_userdata('mobile_no', $userDetails->phone);
        $this->session->set_userdata('token', $userDetails->token);
        redirect("users/admin_otp");
    }
    function admin_otp()
    {
        $user_id = $this->session->userdata('user_id');
        $user_details = $this->user_lib->get_user_otp($user_id);
        if (!empty($this->input->post('resend'))) {
            $this->load->library('sms');
            $otp = rand(1111, 9999);
            $this->data["otp"] = $otp;
            $update['user_login_otp'] = $otp;
            if (ENVIRONMENT != 'development') {
                $isOTPsent = $this->sms->send_sms($user_details->phone, 'otp', (object) $this->data);
                $this->user_lib->sendOTPViaEmail($user_id, $otp);
            } else {
                $update['user_login_otp'] = '1234'; // default OTP     
            }

            $this->user_lib->update_user_otp($user_id, $update);
            $this->session->set_flashdata('error', 'OTP has been sent successfully');
            redirect("users/admin_otp");
        } else if (!empty($this->input->post('entered_otp'))) {
            $entered_otp = $this->input->post('entered_otp');
            if (!empty($user_details->user_login_otp) && $user_details->user_login_otp == $entered_otp) {

                //******************code to check password expiry date**************/
                $expiry_date = empty($user_details->expiry_date) ? strtotime($user_details->created) : $user_details->expiry_date;
                $current_date = time();
                $no_of_days = round(($current_date - $expiry_date) / 86400);
                $expiry_days = $this->config->item('password_expired');
                if ($no_of_days > $expiry_days) {
                    redirect("users/password_expired");
                }
                self::login_with_otp();
            } else {
                $this->session->set_flashdata('error', 'Invalid OTP');
                redirect("users/admin_otp");
            }
        }
        $this->layout('user/admin_otp', 'otp_layout');
    }
    function password_expired()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'password',
                'label' => 'New Password',
                'rules' => 'trim|required|min_length[8]|max_length[50]|callback_check_strong_password'
            ),
            array(
                'field' => 'passconf',
                'label' => 'Confirm Password',
                'rules' => 'trim|required|min_length[8]|max_length[50]|matches[password]'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $user_id = $this->session->userdata('user_id');
            $update_data['password'] = sha1($this->input->post('password'));
            $update_data['expiry_date'] = time();
            $this->user_lib->update($user_id, $update_data);
            $this->session->set_flashdata('success', 'Password updated successfully');
            self::login_with_otp();
        } else {
            $this->data['error'] = validation_errors();
        }
        $this->layout('user/password_expired', 'otp_layout');
    }
    function login_with_admin($user_details)
    {
        $user_id = $user_details->id;
        $token = $user_details->token;
        $headers = getallheaders();
        if (isset($headers['X-Forwarded-For']) && $headers['X-Forwarded-For'] != "")
            $ip_address = $headers['X-Forwarded-For'];
        else
            $ip_address = $_SERVER['REMOTE_ADDR'];

        $this->load->library('userlogs_lib');
        $login_from = "0";
        if ($ip_address == '182.79.98.221')
            $login_from = "1";

        $save = ['user_id' => $user_id, 'ip_address' => $ip_address, 'login_time' => time(), 'login_from' => $login_from, 'created_date' => time()];
        $return_id = $this->userlogs_lib->insertLogs($save);

        $save_data['last_login_time'] = time();
        $this->user_lib->update($user_id, $save_data);
        ?>
        <script>
            localStorage.setItem('token', '<?= $token ?>');
        </script>
        <?php
        // $this->session->unset_userdata('user_id');
        // $this->session->unset_userdata('mobile_no');
        // $this->session->unset_userdata('token');
        $save_session = (object) array(
            'user_id' => $user_id,
            'expire' => time() + 14400,
            'user_log_id' => $return_id
        );

        $this->auth->save_session($save_session);
        redirect('admin', true);
    }
    function forgot()
    {

        $this->data['title'] = 'Forgot Password';

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required|min_length[3]|max_length[200]|valid_email'
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            //login
            $email = $this->input->post('email');
            if ($this->user_lib->forgot_password($email)) {
                $this->session->set_flashdata('success', 'Password reset instructions sent.');
                redirect('users/login', true);
            } else {
                $this->data['error'] = $this->user_lib->get_error();
            }
        } else {
            $this->data['error'] = validation_errors();
        }
        $this->layout('user/forgot', 'login_layout');
    }

    function reset($token = false)
    {
        if (!$token || !$this->user_lib->validatePasswordResetToken($token)) {
            $this->session->set_flashdata('error', 'Password reset token is invalid or expired');
            redirect('users/forgot', true);
        }

        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|min_length[6]|max_length[50]'
            ),
            array(
                'field' => 'passconf',
                'label' => 'Confirm Password',
                'rules' => 'trim|required|min_length[6]|max_length[50]|matches[password]'
            ),
        );

        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {
            //login
            $password = $this->input->post('password');

            if (!$this->user_lib->resetPassword($token, $password)) {
                $this->data['error'] = $this->user_lib->get_error();
            } else {
                $this->session->set_flashdata('success', 'Password updated successfully');
                redirect('users/login', true);
            }
        } else {
            $this->data['error'] = validation_errors();
        }

        $this->layout('user/reset', 'login_layout');
    }

    function send_signup_otp()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            $this->json_response(false, 'Invalid request method.');
            return;
        }

        $this->load->model('User_model');
        $this->load->model('Otp_model');
        $this->load->library('Otp_service');

        $fullName = trim((string) $this->input->post('full_name', true));
        $email = strtolower(trim((string) $this->input->post('email', true)));
        $rawPhone = trim((string) $this->input->post('phone', true));
        $phone = $this->otp_service->normalizePhone($rawPhone);
        $password = (string) $this->input->post('password', false);
        $isAgree = (string) $this->input->post('is_agree', true);
        $otpType = 'signup';

        if ($fullName === '' || strlen($fullName) < 2) {
            $this->json_response(false, 'Please enter your full name.');
            return;
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json_response(false, 'Please enter a valid work email.');
            return;
        }

        if (!$this->otp_service->isValidPhone($phone)) {
            $this->json_response(false, 'Please enter a valid 10-digit mobile number.');
            return;
        }

        if (!$this->is_password_strong_enough($password)) {
            $this->json_response(false, 'Password must be at least 8 chars with 1 uppercase, 1 number and 1 special character.');
            return;
        }

        if ($isAgree !== '1') {
            $this->json_response(false, 'Please accept the terms and privacy policy.');
            return;
        }

        if ($this->User_model->getByEmail($email)) {
            $this->json_response(false, 'Account with this email already exists.');
            return;
        }

        if ($this->User_model->getByMobile($phone)) {
            $this->json_response(false, 'Account with this mobile number already exists.');
            return;
        }

        $cooldownSeconds = $this->otp_service->getResendCooldownSeconds();
        $rateLimitWindowMinutes = $this->otp_service->getRateLimitWindowMinutes();
        $maxRequestsPerWindow = $this->otp_service->getMaxRequestsPerWindow();

        $lastOtp = $this->Otp_model->getLastOtpTime($phone, $otpType);
        if (!empty($lastOtp) && !empty($lastOtp->created_at)) {
            $lastTime = strtotime($lastOtp->created_at);
            if ($lastTime > 0) {
                $elapsed = time() - $lastTime;
                if ($elapsed < $cooldownSeconds) {
                    $this->json_response(false, 'Please wait before requesting OTP again.', array(
                        'cooldown' => $cooldownSeconds,
                        'retry_after' => $cooldownSeconds - $elapsed,
                    ));
                    return;
                }
            }
        }

        $recentCount = $this->Otp_model->countRecentOtps($phone, $otpType, $rateLimitWindowMinutes);
        if ($recentCount >= $maxRequestsPerWindow) {
            $this->json_response(false, 'Too many OTP requests. Please try again later.', array(
                'window_minutes' => $rateLimitWindowMinutes,
                'max_requests' => $maxRequestsPerWindow,
            ));
            return;
        }

        $otp = $this->otp_service->generateOtp();
        $otpHash = $this->otp_service->hashOtp($otp);
        $expiresAt = $this->otp_service->getExpiryDateTime();

        if ($otpHash === false) {
            $this->json_response(false, 'Unable to generate OTP.');
            return;
        }

        $otpId = $this->Otp_model->insertOtp(array(
            'phone' => $phone,
            'otp_hash' => $otpHash,
            'otp_type' => $otpType,
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'is_used' => 0,
        ));

        if (empty($otpId)) {
            $this->json_response(false, 'Failed to save OTP.');
            return;
        }

        $sendResult = $this->otp_service->sendOtpMSG91($phone, $otp, $otpType);
        if (empty($sendResult['success'])) {
            log_message('error', 'Signup OTP send failed: ' . json_encode($sendResult));
            $this->json_response(false, 'OTP could not be sent.');
            return;
        }

        $context = array(
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'is_agree' => 1,
            'otp_type' => $otpType,
            'otp_verified' => false,
            'created_at' => time(),
        );
        $this->session->set_tempdata('signup_otp_context', $context, $this->get_reset_session_ttl());

        $this->json_response(true, 'OTP sent successfully.', array(
            'phone' => $phone,
            'expires_at' => $expiresAt,
            'expires_in' => $this->otp_service->getOtpExpiryMinutes() * 60,
            'cooldown' => $cooldownSeconds,
        ));
    }

    function verify_signup_otp()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            $this->json_response(false, 'Invalid request method.');
            return;
        }

        $this->load->model('Otp_model');
        $this->load->library('Otp_service');

        $context = $this->session->tempdata('signup_otp_context');
        if (empty($context) || empty($context['phone']) || empty($context['email']) || empty($context['password'])) {
            $this->json_response(false, 'Signup session expired. Please create account again.');
            return;
        }

        $enteredOtp = trim((string) $this->input->post('otp', true));
        if ($enteredOtp === '') {
            $this->json_response(false, 'Please enter OTP.');
            return;
        }

        $maxAttempts = 3;
        $latestOtp = $this->Otp_model->getLatestOtp($context['phone'], 'signup');
        if (empty($latestOtp)) {
            $this->json_response(false, 'No OTP found. Please request a new OTP.');
            return;
        }

        if ((int) $latestOtp->is_used === 1) {
            $this->json_response(false, 'OTP already used. Please request a new OTP.');
            return;
        }

        if (strtotime($latestOtp->expires_at) < time()) {
            $this->json_response(false, 'OTP expired. Please request a new OTP.');
            return;
        }

        if ((int) $latestOtp->attempts >= $maxAttempts) {
            $this->json_response(false, 'Maximum OTP attempts reached. Please request a new OTP.');
            return;
        }

        $isValidOtp = $this->otp_service->verifyOtpHash($enteredOtp, $latestOtp->otp_hash);
        if (!$isValidOtp) {
            $this->Otp_model->incrementAttempts((int) $latestOtp->id);
            $this->json_response(false, 'Invalid OTP.', array(
                'remaining_attempts' => max(0, $maxAttempts - ((int) $latestOtp->attempts + 1)),
            ));
            return;
        }

        $this->Otp_model->markUsed((int) $latestOtp->id);

        $saveData = array(
            'fname' => $context['full_name'],
            'lname' => '',
            'company_name' => '',
            'email' => $context['email'],
            'password' => $context['password'],
            'phone' => $context['phone'],
            'status' => '1',
            'last_login_time' => time(),
        );

        $newUserId = $this->user_lib->create_user($saveData);
        if (!$newUserId) {
            $this->json_response(false, $this->user_lib->get_error());
            return;
        }

        $save_session = (object) array(
            'user_id' => (int) $newUserId,
            'expire' => time() + 14400,
        );
        $this->auth->save_session($save_session);
        $this->session->set_userdata('user_id', (int) $newUserId);

        $context['otp_verified'] = true;
        $context['verified_at'] = time();
        $context['user_id'] = (int) $newUserId;
        $this->session->set_tempdata('signup_otp_context', $context, $this->get_reset_session_ttl());

        $this->json_response(true, 'Account created successfully.', array(
            'redirect_url' => base_url('analytics'),
        ));
    }

    // OTP-based forgot password flow (backend only)
    function forgot_password()
    {
        $this->json_response(true, 'Use send_reset_otp, verify_reset_otp and reset_password endpoints for OTP-based reset.');
    }

    function send_reset_otp()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            $this->json_response(false, 'Invalid request method.');
            return;
        }

        $this->load->model('User_model');
        $this->load->model('Otp_model');
        $this->load->library('Otp_service');

        $email = trim((string) $this->input->post('email', true));
        $rawPhone = trim((string) $this->input->post('phone', true));
        $phone = $this->otp_service->normalizePhone($rawPhone);
        $otpType = 'forgot_password';

        $associatedAccount = trim((string) $this->input->post('associated_account', true));
        $genericMessage = 'If the account exists, OTP has been sent to the registered phone number.';

        $user = false;
        if (!empty($associatedAccount)) {
            // Find user by the account identity carried from login
            if (filter_var($associatedAccount, FILTER_VALIDATE_EMAIL)) {
                $user = $this->User_model->getByEmail($associatedAccount);
            } else {
                $normAssocPhone = $this->_normalizePhone($associatedAccount);
                if ($normAssocPhone) {
                    $user = $this->User_model->getByMobile($normAssocPhone);
                }
            }

            if (!$user) {
                // If we explicitly have an associated account but can't find it
                $this->json_response(false, "No account found for: " . $associatedAccount);
                return;
            }

            // Verify if the phone provided in the forgot form matches this user's phone
            if ($phone !== '') {
                $userPhoneOnRecord = $this->otp_service->normalizePhone((string) $user->phone);
                if ($userPhoneOnRecord !== $phone) {
                    $this->json_response(false, "This phone number is not linked to " . $associatedAccount);
                    return;
                }
            }
        } elseif ($email !== '') {
            $user = $this->User_model->getByEmail($email);
        } elseif ($phone !== '') {
            $user = $this->User_model->getByMobile($phone);
        }

        if (empty($user)) {
            $this->json_response(true, $genericMessage, array(
                'masked' => true,
            ));
            return;
        }

        $isGoogleUser = empty($user->password);

        $userPhone = $this->otp_service->normalizePhone((string) $user->phone);
        if (!$this->otp_service->isValidPhone($userPhone)) {
            $this->json_response(false, 'User phone number is invalid or missing.');
            return;
        }

        $cooldownSeconds = $this->otp_service->getResendCooldownSeconds();
        $rateLimitWindowMinutes = $this->otp_service->getRateLimitWindowMinutes();
        $maxRequestsPerWindow = $this->otp_service->getMaxRequestsPerWindow();

        $lastOtp = $this->Otp_model->getLastOtpTime($userPhone, $otpType, (int) $user->id);
        if (!empty($lastOtp) && !empty($lastOtp->created_at)) {
            $lastTime = strtotime($lastOtp->created_at);
            if ($lastTime > 0) {
                $elapsed = time() - $lastTime;
                if ($elapsed < $cooldownSeconds) {
                    $retryAfter = $cooldownSeconds - $elapsed;
                    $this->json_response(false, 'Please wait before requesting OTP again.', array(
                        'cooldown' => $cooldownSeconds,
                        'retry_after' => $retryAfter,
                    ));
                    return;
                }
            }
        }

        $recentCount = $this->Otp_model->countRecentOtps($userPhone, $otpType, $rateLimitWindowMinutes, (int) $user->id);
        if ($recentCount >= $maxRequestsPerWindow) {
            $this->json_response(false, 'Too many OTP requests. Please try again later.', array(
                'window_minutes' => $rateLimitWindowMinutes,
                'max_requests' => $maxRequestsPerWindow,
                'cooldown' => $cooldownSeconds,
            ));
            return;
        }

        $otp = $this->otp_service->generateOtp();
        $otpHash = $this->otp_service->hashOtp($otp);
        $expiresAt = $this->otp_service->getExpiryDateTime();

        if ($otpHash === false) {
            $this->json_response(false, 'Unable to generate OTP hash.');
            return;
        }

        $otpId = $this->Otp_model->insertOtp(array(
            'user_id' => (int) $user->id,
            'phone' => $userPhone,
            'otp_hash' => $otpHash,
            'otp_type' => $otpType,
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'is_used' => 0,
        ));

        if (empty($otpId)) {
            $this->json_response(false, 'Failed to save OTP.');
            return;
        }

        $sendResult = $this->otp_service->sendOtpMSG91($userPhone, $otp, $otpType);
        if (empty($sendResult['success'])) {
            log_message('error', 'Forgot password OTP send failed: ' . json_encode($sendResult));
            $this->json_response(false, 'OTP could not be sent.');
            return;
        }

        $context = array(
            'user_id' => (int) $user->id,
            'phone' => $userPhone,
            'otp_type' => $otpType,
            'otp_verified' => false,
            'created_at' => time(),
        );
        $this->session->set_tempdata('reset_password_context', $context, $this->get_reset_session_ttl());

        $this->json_response(true, 'OTP sent successfully.', array(
            'user_id' => (int) $user->id,
            'phone' => $userPhone,
            'otp_type' => $otpType,
            'is_google_user' => $isGoogleUser,
            'expires_at' => $expiresAt,
            'expires_in' => $this->otp_service->getOtpExpiryMinutes() * 60,
            'cooldown' => $cooldownSeconds,
        ));
    }

    function verify_reset_otp()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            $this->json_response(false, 'Invalid request method.');
            return;
        }

        $this->load->model('Otp_model');
        $this->load->library('Otp_service');

        $context = $this->session->tempdata('reset_password_context');
        if (empty($context) || empty($context['user_id']) || empty($context['phone'])) {
            $this->json_response(false, 'Reset session expired. Please request OTP again.');
            return;
        }

        $enteredOtp = trim((string) $this->input->post('otp', true));
        if ($enteredOtp === '') {
            $this->json_response(false, 'Please enter OTP.');
            return;
        }

        $maxAttempts = 3;
        $latestOtp = $this->Otp_model->getLatestOtp($context['phone'], 'forgot_password', (int) $context['user_id']);
        if (empty($latestOtp)) {
            $this->json_response(false, 'No OTP found. Please request a new OTP.');
            return;
        }

        if ((int) $latestOtp->is_used === 1) {
            $this->json_response(false, 'OTP already used. Please request a new OTP.');
            return;
        }

        if (strtotime($latestOtp->expires_at) < time()) {
            $this->json_response(false, 'OTP expired. Please request a new OTP.');
            return;
        }

        if ((int) $latestOtp->attempts >= $maxAttempts) {
            $this->json_response(false, 'Maximum OTP attempts reached. Please request a new OTP.');
            return;
        }

        $isValidOtp = $this->otp_service->verifyOtpHash($enteredOtp, $latestOtp->otp_hash);
        if (!$isValidOtp) {
            $this->Otp_model->incrementAttempts((int) $latestOtp->id);
            $this->json_response(false, 'Invalid OTP.', array(
                'remaining_attempts' => max(0, $maxAttempts - ((int) $latestOtp->attempts + 1)),
            ));
            return;
        }

        $this->Otp_model->markUsed((int) $latestOtp->id);

        $context['otp_verified'] = true;
        $context['verified_at'] = time();
        $this->session->set_tempdata('reset_password_context', $context, $this->get_reset_session_ttl());

        $this->json_response(true, 'OTP verified successfully. You can reset your password now.', array(
            'reset_allowed' => true,
        ));
    }

    function reset_password()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            $this->json_response(false, 'Invalid request method.');
            return;
        }

        $this->load->model('User_model');

        $context = $this->session->tempdata('reset_password_context');
        if (empty($context) || empty($context['user_id']) || empty($context['otp_verified'])) {
            $this->json_response(false, 'Reset not allowed. Please verify OTP first.');
            return;
        }

        $password = (string) $this->input->post('password', false);
        $passconf = (string) $this->input->post('passconf', false);

        if ($password === '' || $passconf === '') {
            $this->json_response(false, 'Password and confirm password are required.');
            return;
        }

        if ($password !== $passconf) {
            $this->json_response(false, 'Password and confirm password do not match.');
            return;
        }

        if (!$this->check_strong_password($password)) {
            $this->json_response(false, 'Password must be at least 8 chars with 1 uppercase, 1 number and 1 special character.');
            return;
        }

        $user = $this->User_model->getUserById((int) $context['user_id']);
        if (empty($user)) {
            $this->json_response(false, 'Invalid reset session. Please request OTP again.');
            return;
        }

        $existingPassword = (string) ($user['password'] ?? '');
        if ($existingPassword !== '') {
            $isSamePassword = false;
            if (password_get_info($existingPassword)['algo'] !== 0) {
                $isSamePassword = password_verify($password, $existingPassword);
            } else {
                $isSamePassword = hash_equals($existingPassword, sha1($password));
            }
            if ($isSamePassword) {
                $this->json_response(false, 'New password must be different from your current password.');
                return;
            }
        }

        $this->db->trans_start();
        $updated = $this->User_model->savePassword((int) $context['user_id'], password_hash($password, PASSWORD_DEFAULT));
        $this->db->trans_complete();

        if (!$updated || $this->db->trans_status() === false) {
            $this->json_response(false, 'Unable to reset password. Please try again.');
            return;
        }

        $save_session = (object) array(
            'user_id' => (int) $context['user_id'],
            'expire' => time() + 14400,
        );
        $this->auth->save_session($save_session);
        $this->session->set_userdata('user_id', (int) $context['user_id']);
        $this->session->unset_tempdata('reset_password_context');
        $this->json_response(true, 'Password reset successful.', array(
            'redirect_url' => base_url('analytics'),
        ));
    }

    function login_with_token($token = false)
    {
        if (!$token || !$user_data = $this->user_lib->validatePasswordResetToken($token)) {
            $this->session->set_flashdata('error', 'Login token is invalid or expired');
            redirect('users', true);
        }
        if (!empty($this->input->get('admin_id'))) {
            $this->user_lib->enable_contact($this->input->get('admin_id'), $user_data->user_id, 'user_login');
        }

        $save_session = (object) array(
            'user_id' => $user_data->user_id,
            'expire' => time() + 14400,
        );

        $this->auth->save_session($save_session);
        $redirectURL = base_url('dash');
        if (!empty($r = $this->input->get('r'))) {
            $redirectURL = urldecode($r);
        }

        $this_user = new User_lib();
        $this_user_details = $this_user->getByID($user_data->user_id);

        $this->load->library('jwt_lib');
        $jwt_data = array(
            'user_id' => $user_data->user_id,
            'parent_id' => $this_user_details->parent_id
        );

        $token = $this->jwt_lib->encode($jwt_data, 10800);
        ?>
        <script>
            localStorage.setItem('token', '<?= $token ?> ');
            location.href = '<?= $redirectURL ?>';
        </script>
        <?php
    }

    function logout()
    {
        $this->session->unset_tempdata('channel');
        $this->session->unset_tempdata('callback_uri');
        $this->load->library('userlogs_lib');
        if (isset($this->session->userdata['user_info']->user_log_id)) {
            $user_log_id = $this->session->userdata['user_info']->user_log_id;
            $update = ['logout_time' => time(), 'is_active' => '0'];
            $this->userlogs_lib->upateLogs($user_log_id, $update);
        }
        $this->user_lib->logout();
        redirect('users');
    }
    public function check_strong_password($str)
    {
        $password = trim($str);
        $regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';

        if (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $password) < 1) {
            $this->form_validation->set_message('check_strong_password', 'The password field should have a minimum of 8 characters with at least 1 uppercase letter, 1 number & 1 special character');
            return false;
        } else if (preg_match_all($regex_special, $password) < 1) {
            $this->form_validation->set_message('check_strong_password', 'The password field should have a minimum of 8 characters with at least 1 uppercase letter, 1 number & 1 special character');
            return false;
        }
        return true;
    }
    public function check_phonenumber($num)
    {
        if (!preg_match('/^[0-9]*$/', $num)) {
            // Set the error message:
            $this->form_validation->set_message('check_phonenumber', 'The %s field should numbers only.');
            return false;
        } else {
            return true;
        }
    }
    // 🔥 ================= GOOGLE PROFILE COMPLETION =================

    // Show profile form
    function profile_form()
    {

        $this->data['title'] = 'Login ';
        // Allow profile form when coming from Google OAuth temporary session.
        $temp_user_id = $this->session->userdata('google_user_id');
        $user_id = $this->session->userdata('user_id');
        if (empty($temp_user_id) && empty($user_id)) {
            redirect('users/login');
        }

        $this->load->model('User_model');
        $profile_user_id = !empty($temp_user_id) ? $temp_user_id : $user_id;
        $this->data['google_user'] = $this->User_model->getUserById($profile_user_id);
        $this->data['otp_context'] = $this->session->tempdata('google_signup_context');

        $this->layout('user/google_profile', 'login_layout');
    }

    function send_google_signup_otp()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            $this->json_response(false, 'Invalid request method.');
            return;
        }

        $user_id = $this->session->userdata('google_user_id');
        if (empty($user_id)) {
            $user_id = $this->session->userdata('user_id');
        }

        if (empty($user_id)) {
            $this->json_response(false, 'Google signup session expired. Please sign in again.');
            return;
        }

        $this->load->model('User_model');
        $this->load->model('Otp_model');
        $this->load->library('Otp_service');

        $rawPhone = trim((string) $this->input->post('phone', true));
        $phone = $this->otp_service->normalizePhone($rawPhone);
        $otpType = 'phone_verification';

        if (!$this->otp_service->isValidPhone($phone)) {
            $this->json_response(false, 'Please enter a valid 10-digit mobile number.');
            return;
        }

        $existingUser = $this->User_model->getByMobile($phone);
        if (!empty($existingUser) && (int) $existingUser->id !== (int) $user_id) {
            $this->json_response(false, 'This phone number is already linked to another account.');
            return;
        }

        $cooldownSeconds = $this->otp_service->getResendCooldownSeconds();
        $rateLimitWindowMinutes = $this->otp_service->getRateLimitWindowMinutes();
        $maxRequestsPerWindow = $this->otp_service->getMaxRequestsPerWindow();

        $lastOtp = $this->Otp_model->getLastOtpTime($phone, $otpType, (int) $user_id);
        if (!empty($lastOtp) && !empty($lastOtp->created_at)) {
            $lastTime = strtotime($lastOtp->created_at);
            if ($lastTime > 0) {
                $elapsed = time() - $lastTime;
                if ($elapsed < $cooldownSeconds) {
                    $retryAfter = $cooldownSeconds - $elapsed;
                    $this->json_response(false, 'Please wait before requesting OTP again.', array(
                        'cooldown' => $cooldownSeconds,
                        'retry_after' => $retryAfter,
                    ));
                    return;
                }
            }
        }

        $recentCount = $this->Otp_model->countRecentOtps($phone, $otpType, $rateLimitWindowMinutes, (int) $user_id);
        if ($recentCount >= $maxRequestsPerWindow) {
            $this->json_response(false, 'Too many OTP requests. Please try again later.', array(
                'window_minutes' => $rateLimitWindowMinutes,
                'max_requests' => $maxRequestsPerWindow,
            ));
            return;
        }

        $otp = $this->otp_service->generateOtp();
        $otpHash = $this->otp_service->hashOtp($otp);
        $expiresAt = $this->otp_service->getExpiryDateTime();

        if ($otpHash === false) {
            $this->json_response(false, 'Unable to generate OTP.');
            return;
        }

        $otpId = $this->Otp_model->insertOtp(array(
            'user_id' => (int) $user_id,
            'phone' => $phone,
            'otp_hash' => $otpHash,
            'otp_type' => $otpType,
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'is_used' => 0,
        ));

        if (empty($otpId)) {
            $this->json_response(false, 'Failed to save OTP.');
            return;
        }

        $sendResult = $this->otp_service->sendOtpMSG91($phone, $otp, $otpType);
        if (empty($sendResult['success'])) {
            log_message('error', 'Google signup OTP send failed: ' . json_encode($sendResult));
            $this->json_response(false, 'OTP could not be sent.');
            return;
        }

        $context = array(
            'user_id' => (int) $user_id,
            'phone' => $phone,
            'otp_type' => $otpType,
            'otp_verified' => false,
            'created_at' => time(),
        );
        $this->session->set_tempdata('google_signup_context', $context, $this->get_reset_session_ttl());

        $this->json_response(true, 'OTP sent successfully.', array(
            'phone' => $phone,
            'expires_at' => $expiresAt,
            'expires_in' => $this->otp_service->getOtpExpiryMinutes() * 60,
            'cooldown' => $cooldownSeconds,
        ));
    }

    function verify_google_signup_otp()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            $this->json_response(false, 'Invalid request method.');
            return;
        }

        $this->load->model('Otp_model');
        $this->load->library('Otp_service');

        $context = $this->session->tempdata('google_signup_context');
        if (empty($context) || empty($context['user_id']) || empty($context['phone'])) {
            $this->json_response(false, 'OTP session expired. Please send OTP again.');
            return;
        }

        $enteredOtp = trim((string) $this->input->post('otp', true));
        if ($enteredOtp === '') {
            $this->json_response(false, 'Please enter OTP.');
            return;
        }

        $maxAttempts = 3;
        $latestOtp = $this->Otp_model->getLatestOtp($context['phone'], 'phone_verification', (int) $context['user_id']);
        if (empty($latestOtp)) {
            $this->json_response(false, 'No OTP found. Please request a new OTP.');
            return;
        }

        if ((int) $latestOtp->is_used === 1) {
            $this->json_response(false, 'OTP already used. Please request a new OTP.');
            return;
        }

        if (strtotime($latestOtp->expires_at) < time()) {
            $this->json_response(false, 'OTP expired. Please request a new OTP.');
            return;
        }

        if ((int) $latestOtp->attempts >= $maxAttempts) {
            $this->json_response(false, 'Maximum OTP attempts reached. Please request a new OTP.');
            return;
        }

        $isValidOtp = $this->otp_service->verifyOtpHash($enteredOtp, $latestOtp->otp_hash);
        if (!$isValidOtp) {
            $this->Otp_model->incrementAttempts((int) $latestOtp->id);
            $this->json_response(false, 'Invalid OTP.', array(
                'remaining_attempts' => max(0, $maxAttempts - ((int) $latestOtp->attempts + 1)),
            ));
            return;
        }

        $this->Otp_model->markUsed((int) $latestOtp->id);

        $this->load->model('User_model');
        $this->User_model->updateProfile((int) $context['user_id'], array(
            'phone' => $context['phone'],
            'last_login_time' => time(),
        ));

        $save_session = (object) array(
            'user_id' => (int) $context['user_id'],
            'expire' => time() + 14400,
        );
        $this->auth->save_session($save_session);
        $this->session->set_userdata('user_id', (int) $context['user_id']);
        $this->session->unset_userdata('google_user_id');

        $context['otp_verified'] = true;
        $context['verified_at'] = time();
        $this->session->set_tempdata('google_signup_context', $context, $this->get_reset_session_ttl());

        $this->json_response(true, 'Phone number verified successfully.', array(
            'phone' => $context['phone'],
            'verified' => true,
            'redirect_url' => base_url('analytics'),
        ));
    }


    // Save profile data
    function save_google_profile()
    {

        // Use temporary Google session ID first; fallback to user_id if present.
        $user_id = $this->session->userdata('google_user_id');
        if (empty($user_id)) {
            $user_id = $this->session->userdata('user_id');
        }

        if (!$user_id) {
            redirect('users/login');
        }

        $otpContext = $this->session->tempdata('google_signup_context');
        if (empty($otpContext) || empty($otpContext['otp_verified'])) {
            $this->session->set_flashdata('error', 'Please verify your phone number first.');
            redirect('users/profile_form');
        }

        // Get form data.
        $company_name = trim((string) $this->input->post('company_name', true));
        $phone = preg_replace('/\D+/', '', (string) $this->input->post('phone', true));
        $shipments = trim((string) $this->input->post('shipments', true));

        // Validation (basic).
        if (empty($company_name) || empty($phone) || empty($shipments)) {
            $this->session->set_flashdata('error', 'All fields are required');
            redirect('users/profile_form');
        }

        if (!$this->check_phonenumber($phone) || strlen($phone) !== 10) {
            $this->session->set_flashdata('error', 'Please enter a valid 10-digit mobile number.');
            redirect('users/profile_form');
        }

        if (empty($otpContext['phone']) || $otpContext['phone'] !== $phone) {
            $this->session->set_flashdata('error', 'Verified phone number does not match the submitted phone.');
            redirect('users/profile_form');
        }

        // Update user profile fields.
        $this->load->model('User_model');

        $updateData = [
            'company_name' => $company_name,
            'phone' => $phone,
            'shipping_volume' => $shipments
        ];

        $this->User_model->updateProfile($user_id, $updateData);

        // Complete login session now that profile is complete.
        $save_session = (object) [
            'user_id' => $user_id,
            'expire' => time() + 14400,
        ];
        $this->auth->save_session($save_session);
        $this->session->set_userdata('user_id', $user_id);
        $this->session->unset_userdata('google_user_id');
        $this->session->unset_tempdata('google_signup_context');

        // Redirect to dashboard.
        redirect('success'); // or analytics
    }

    private function json_response($status, $message, $data = array())
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => (bool) $status,
                'message' => $message,
                'data' => $data,
            )));
    }

    private function get_reset_session_ttl()
    {
        $ttl = (int) $this->config->item('otp_session_expiry');
        return ($ttl > 0) ? $ttl : 600;
    }

    private function is_password_strong_enough($password = '')
    {
        $password = trim((string) $password);
        if (strlen($password) < 8 || strlen($password) > 50) {
            return false;
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>§~]/', $password)) {
            return false;
        }

        return true;
    }
}
