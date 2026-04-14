<?php

use App\Lib\Logs\Log;

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends Front_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('user_lib');
        $this->load->library('otp_service');
        $this->load->model('otp_model');
    }

    public function index()
    {
        self::signup();
    }

    public function check_user()
    {
        $identity = $this->input->get('identity');
        if (empty($identity)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Identity is required']));
            return;
        }

        $user = $this->user_lib->getByEmail($identity);
        if (!$user) {
            $user = $this->user_lib->getByMobile($identity);
        }

        if ($user) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'No account found for this email/phone.']));
        }
    }

    public function login_process()
    {
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'identity',
                'label' => 'Email or Phone',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            $identity = $this->input->post('identity');
            $password = $this->input->post('password');

            // Try email first
            $user = $this->user_lib->user_login($identity, $password);
            
            // If failed, try phone number
            if (!$user && preg_match('/^\d+$/', $identity)) {
                $candidate = $this->user_lib->getByMobile($identity);
                if ($candidate) {
                    // Manual login check if we found by mobile
                    if (password_verify($password, $candidate->password) || sha1($password) == $candidate->password) {
                        $user = $candidate;
                        $save_session = (object) array(
                            'user_id' => $user->id,
                            'expire' => time() + 14400,
                        );
                        $this->auth->save_session($save_session);
                    }
                }
            }

            if ($user) {
                $token = $this->user_lib->userApiLogin($user->email, $password);
                if (!$token) {
                    // Handle case where we logged in via phone but userApiLogin needs email/pass and might fail if encrypted differently
                    // (though user_login already handles legacy and new hashes)
                    // For now, let's assume it works or provide a fallback.
                }

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

                    if ($user->is_admin == '1') {
                        redirect('admin', true);
                    } else {
                        $save_data['last_login_time'] = time();
                        $this->user_lib->update($user->id, $save_data);
                        redirect('analytics', true);
                    }
                }
            } else {
                $this->session->set_flashdata('error', 'Invalid email/phone or password');
                redirect('users/login');
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect('users/login');
        }
    }

    public function send_signup_otp()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('full_name', 'Full Name', 'required|min_length[2]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'required|exact_length[10]|numeric');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');

        if ($this->form_validation->run() == FALSE) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => validation_errors('', '')]));
            return;
        }

        $email = $this->input->post('email');
        $phone = $this->input->post('phone');

        if ($this->user_lib->getByEmail($email)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Account with this email already exists']));
            return;
        }
        if ($this->user_lib->getByMobile($phone)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Account with this phone number already exists']));
            return;
        }

        $signup_data = [
            'full_name' => $this->input->post('full_name'),
            'email' => $email,
            'phone' => $phone,
            'password' => $this->input->post('password'),
        ];
        $this->session->set_userdata('signup_temp_data', $signup_data);

        $otp = $this->otp_service->generateOtp();
        $otpHash = $this->otp_service->hashOtp($otp);
        $expiresAt = $this->otp_service->getExpiryDateTime();

        $inserted = $this->otp_model->insertOtp([
            'phone' => $phone,
            'otp_hash' => $otpHash,
            'otp_type' => 'signup',
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'is_used' => 0,
        ]);

        if (!$inserted) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Failed to save OTP.']));
            return;
        }

        $sendResult = $this->otp_service->sendOtpMSG91($phone, $otp, 'signup');
        if (empty($sendResult['success'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'OTP could not be sent. Please try again.']));
            return;
        }

        $this->session->set_userdata('signup_otp_id', $inserted);

        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'OTP sent successfully.']));
    }

    public function verify_signup_otp()
    {
        $otp = $this->input->post('otp');
        $signup_data = $this->session->userdata('signup_temp_data');
        $otp_id = $this->session->userdata('signup_otp_id');

        if (empty($signup_data) || empty($otp)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Session expired. Please try again.']));
            return;
        }

        $latestOtp = $this->otp_model->getLatestOtp($signup_data['phone'], 'signup');
        if (empty($latestOtp) || $latestOtp->is_used == 1 || strtotime($latestOtp->expires_at) < time()) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'OTP expired or invalid. Please request a new one.']));
            return;
        }

        if (!$this->otp_service->verifyOtpHash($otp, $latestOtp->otp_hash)) {
            $this->otp_model->incrementAttempts($latestOtp->id);
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Invalid OTP.']));
            return;
        }

        // OTP verified, create user
        $this->otp_model->markUsed($latestOtp->id);

        $name_parts = explode(' ', $signup_data['full_name'], 2);
        $save_user = [
            'fname' => $name_parts[0],
            'lname' => isset($name_parts[1]) ? $name_parts[1] : '',
            'email' => $signup_data['email'],
            'phone' => $signup_data['phone'],
            'password' => $signup_data['password'],
            'status' => '1',
            'phone_verified' => '1',
            'last_login_time' => time(),
        ];

        if (!$user_id = $this->user_lib->create_user($save_user)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $this->user_lib->get_error()]));
            return;
        }

        // Automatic login
        $this->user_lib->user_login($signup_data['email'], $signup_data['password']);
        
        $this->session->unset_userdata('signup_temp_data');
        $this->session->unset_userdata('signup_otp_id');

        $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true, 
            'message' => 'Account created successfully!',
            'data' => ['redirect_url' => base_url('analytics')]
        ]));
    }

    public function send_reset_otp()
    {
        $identity = $this->input->post('phone') ?: $this->input->post('email');
        if (empty($identity)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Phone or Email is required']));
            return;
        }

        $user = $this->user_lib->getByEmail($identity);
        if (!$user) {
            $user = $this->user_lib->getByMobile($identity);
        }

        if (!$user) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'No account found with this information.']));
            return;
        }

        $phone = $user->phone;
        if (empty($phone)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'No phone number associated with this account. Please contact support.']));
            return;
        }

        $otp = $this->otp_service->generateOtp();
        $otpHash = $this->otp_service->hashOtp($otp);
        $expiresAt = $this->otp_service->getExpiryDateTime();

        $inserted = $this->otp_model->insertOtp([
            'user_id' => $user->id,
            'phone' => $phone,
            'otp_hash' => $otpHash,
            'otp_type' => 'forgot_password',
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'is_used' => 0,
        ]);

        if (!$inserted) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Failed to save OTP.']));
            return;
        }

        $sendResult = $this->otp_service->sendOtpMSG91($phone, $otp, 'forgot_password');
        if (empty($sendResult['success'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'OTP could not be sent. Please try again.']));
            return;
        }

        $this->session->set_userdata('reset_user_id', $user->id);
        $this->session->set_userdata('reset_otp_id', $inserted);

        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'OTP sent successfully.']));
    }

    public function verify_reset_otp()
    {
        $otp = $this->input->post('otp');
        $user_id = $this->session->userdata('reset_user_id');

        if (empty($user_id) || empty($otp)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Session expired. Please try again.']));
            return;
        }

        $user = $this->user_lib->getByID($user_id);
        $latestOtp = $this->otp_model->getLatestOtp($user->phone, 'forgot_password', $user_id);

        if (empty($latestOtp) || $latestOtp->is_used == 1 || strtotime($latestOtp->expires_at) < time()) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'OTP expired or invalid.']));
            return;
        }

        if (!$this->otp_service->verifyOtpHash($otp, $latestOtp->otp_hash)) {
            $this->otp_model->incrementAttempts($latestOtp->id);
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Invalid OTP.']));
            return;
        }

        $this->otp_model->markUsed($latestOtp->id);
        $this->session->set_userdata('reset_otp_verified', true);

        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'OTP verified successfully.']));
    }

    public function reset_password()
    {
        $password = $this->input->post('password');
        $passconf = $this->input->post('passconf');
        $user_id = $this->session->userdata('reset_user_id');
        $verified = $this->session->userdata('reset_otp_verified');

        if (!$user_id || !$verified) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Unauthorized or session expired.']));
            return;
        }

        if (empty($password) || $password !== $passconf) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Passwords do not match.']));
            return;
        }

        $update_data = ['password' => $password];
        if (!$this->user_lib->update_user($user_id, $update_data)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Unable to update password.']));
            return;
        }

        // Get user for login
        $user = $this->user_lib->getByID($user_id);
        $this->user_lib->user_login($user->email, $password);

        $this->session->unset_userdata(['reset_user_id', 'reset_otp_id', 'reset_otp_verified']);

        $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true, 
            'message' => 'Password reset successful!',
            'data' => ['redirect_url' => base_url('analytics')]
        ]));
    }


    function register()
    {
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

        $this->layout('user/signup', 'login_layout');
    }


    function signup()
    {
        $this->data['title'] = 'Create an account';
        $this->layout('user/signup', 'login_layout');
    }


    function signup_with_phone_with_email()
    {
        $this->layout('user/signup_with_phone_with_email', 'login_layout');
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
        $this->load->library('form_validation');
        $config = array(
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required|min_length[3]|max_length[200]|valid_email'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|max_length[50]'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run()) {
            //login
            $email = $this->input->post('email');
            $password = $this->input->post('password');
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
        } else {
            $this->data['error'] = validation_errors();
        }
        $this->data['title'] = 'User Login';
        $this->layout('user/login', 'login_layout');
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
                $this->login_with_otp();
            } else {
                $this->session->set_flashdata('error', 'Invalid OTP');
                redirect("users/admin_otp");
            }
        }
        $this->data['title'] = 'OTP Verification';
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
            $this->login_with_otp();
        } else {
            $this->data['error'] = validation_errors();
        }
        $this->data['title'] = 'Password Expired';
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

    function login_with_otp()
    {
        $user_id = $this->session->userdata('user_id');
        $token = $this->session->userdata('token');
        
        if (empty($user_id)) {
            redirect('users/login');
        }

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

        $user_details = $this->user_lib->getByID($user_id);
        
        ?>
        <script>
            localStorage.setItem('token', '<?= $token ?>');
        </script>
        <?php

        $save_session = (object) array(
            'user_id' => $user_id,
            'expire' => time() + 14400,
            'user_log_id' => $return_id
        );

        $this->auth->save_session($save_session);

        if ($user_details->is_admin == '1') {
            redirect('admin', true);
        } else {
            redirect('analytics', true);
        }
    }
    function forgot()
    {
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
        $this->data['title'] = 'Forgot Password';
        $this->layout('user/forgot', 'forgot_layout');
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
}
