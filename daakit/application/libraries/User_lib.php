<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('user_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->user_model, $method)) {
            throw new Exception('Undefined method user_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->user_model, $method], $arguments);
    }

    function update_user($id = false, $save_data = array())
    {
        if (!$id || empty($save_data)) {
            $this->error = 'Invalid Data';
            return false;
        }

        $user = $this->getByID($id);

        //encode password
        if (!empty($save_data['password']))
            $save_data['password'] = sha1($save_data['password']);

        //check if account with email exists
        if (!empty($save_data['email']) && $save_data['email'] != $user->email)
            if ($this->CI->user_model->getByEmail($save_data['email'])) { //account exists
                $this->error = 'Account with this email already exists';
                return false;
            }

        $this->CI->user_model->update($id, $save_data);

        return true;
    }

    function create_user($save_data = array())
    {
        if (empty($save_data)) {
            $this->error = 'Invalid Data';
            return false;
        }

        //encode password
        if (!empty($save_data['password']))
	    $hashed = sha1($save_data['password']);
	    $save_data['password'] = $hashed;
	    $save_data['unicommerce_password'] = $hashed;

        //check if account with email exists
        if (!empty($save_data['email']))
            if ($this->CI->user_model->getByEmail($save_data['email'])) { //account exists
                $this->error = 'Account with this email already exists';
                return false;
            }

        //check if account with mobile exists
        if (!empty($save_data['phone']))
            if ($this->CI->user_model->getByMobile($save_data['phone'])) { //account exists
                $this->error = 'Account with this mobile number already exists';
                return false;
            }

        $this->CI->load->helper('cookie');
        $ref_id = get_cookie('ref_id');
        if (!empty($ref_id) && is_numeric($ref_id)) {
            $save_data['referral_id'] = $ref_id;
        }

        $lead_source = get_cookie('utm_source');
        if (!empty($lead_source)) {
            $save_data['lead_source'] = $lead_source;
        }
        $save_data['ivr_call_chargeable'] = 1;
        $save_data['allotted_free_minutes'] = $this->CI->config->item('free_unit');
        if (!$new_user_id = $this->CI->user_model->create($save_data)) {
            $this->error = 'Unable to create account';
            return false;
        }

        return $new_user_id;
    }

    function user_login($email = false, $password = false)
    {
        if (!$email || !$password) {
            $this->error = 'invalid email or password';
            return false;
        }

        // Legacy sha1 path.
        $user = $this->CI->user_model->userByEmailPassword($email, sha1($password));
        // New secure hash path.
        if (empty($user)) {
            $candidate = $this->CI->user_model->getByEmail($email);
            if (!empty($candidate) && !empty($candidate->password) && password_get_info($candidate->password)['algo'] !== 0) {
                if (password_verify($password, $candidate->password)) {
                    $user = $candidate;
                }
            }
        }
        if (empty($user) || $user->status == '0') {
            $this->error = 'Invalid email or password';
            return false;
        }

        $save_session = (object) array(
            'user_id' => $user->id,
            'expire' => time() + 14400,
        );

        $this->CI->auth->save_session($save_session);

        return $user;
    }

    function userApiLogin($email = false, $password = false, $time = 10800)
    {
        if (!$email || !$password) {
            $this->error = 'Inavlaid email or password';
            return false;
        }


        // Legacy sha1 path.
        $user = $this->CI->user_model->userByEmailPassword($email, sha1($password));
        if (empty($user)) {
            $user = $this->CI->user_model->userByEmailPassword($email, sha1($password), true);
        }
        // New secure hash path.
        if (empty($user)) {
            $candidate = $this->CI->user_model->getByEmail($email);
            if (!empty($candidate) && !empty($candidate->password) && password_get_info($candidate->password)['algo'] !== 0) {
                if (password_verify($password, $candidate->password)) {
                    $user = $candidate;
                }
            }
        }
        if (empty($user) || $user->status == '0') {
            $this->error = 'Invalid email or password';
            return false;
        }

        $this->CI->load->library('jwt_lib');
        $jwt_data = array(
            'user_id' => $user->id,
            'parent_id' => $user->parent_id
        );

        return    $token = $this->CI->jwt_lib->encode($jwt_data, $time);
    }

    function forgot_password($email = false)
    {
        if (!$email) {
            $this->error = 'Invalid Email';
            return false;
        }

        //check if user account exists with this email
        if (!$user = $this->CI->user_model->getByEmail($email)) { //No account exists
            $this->error = 'No account exists with this email';
            return false;
        }

        //get Token for this user

        $this->CI->load->library('jwt_lib');
        $jwt_data = array(
            'user_id' => $user->id,
        );
        $token = $this->CI->jwt_lib->encode($jwt_data);
        //send email with password reset link

        $send_data = array(
            'first_name' => $user->fname,
            'last_name' => $user->lname,
            'token' => $token,
        );

        $this->sendForgotEmail($user->email, $send_data);

        return true;
    }

    function sendForgotEmail($to = false, $data = array())
    {
        if (!$to || empty($data))
            return false;

        $this->CI->load->library('email_lib');
        $email = new Email_lib();
        $email->to($to);
        $email->subject('Reset Your Password');
        $email->message($this->CI->load->view('emails/forgot_password', $data, true));
        $email->send();

        return true;
    }

    function validatePasswordResetToken($token = false)
    {
        if (!$token) {
            $this->error = 'Token is expired';
            return false;
        }

        $this->CI->load->library('jwt_lib');
        $token_data = $this->CI->jwt_lib->decode($token);
        if (empty($token_data)) {
            $this->error = 'Token is expired';
            return false;
        }

        return $token_data->data;
    }

    function resetPassword($token = false, $password = false)
    {
        if (!$user = self::validatePasswordResetToken($token))
            return false;

        $user_id = $user->user_id;
        $password = sha1($password);

        if (!$this->CI->user_model->savePassword($user_id, $password)) {
            $this->error = 'Unable to update password';
            return false;
        }

        return true;
    }

    function userHaveAccess($user_id = false, $method = false)
    {
        if (!$user_id || !$method)
            return false;

        $method = strtolower($method);

        $user = $this->getByID($user_id);
        $permissions = explode(',', $user->permissions);

        if (empty($permissions) || !in_array($method, $permissions))
            return false;

        return true;
    }

    function createFetchAccountWithEmail($email = false, $data = array())
    {
        if (!$email)
            return false;

        if ($user = $this->getByEmail($email)) //account exists with email
            return $user->id;

        //create an account with this email
        $this->CI->load->helper('string_helper');
        $save = array(
            'email' => $email,
            'company_name' => !empty($data['company']) ? $data['company'] : '',
            'phone' => !empty($data['phone']) ? $data['phone'] : '',
            'password' => random_string('alnum', 8),
        );

        $user_id = $this->create_user($save);
        if (!$user_id)
            return false;

        return $user_id;
    }

    function logout()
    {
        $this->CI->auth->destroy();
        return true;
    }


    function create_lead($user_id = false)
    {
        if (!$user_id)
            return false;

        $user = $this->getByID($user_id);

        if (empty($user))
            return false;


        // delta to Sales CRM from API
        $this->CI->load->library('delta_sales_crm');

        $lead = new delta_sales_crm();
        $lead->setSellerID($user->id);
        $lead->setFName($user->fname);
        $lead->setLName($user->lname);
        $lead->setEmail($user->email);
        $lead->setPhone($user->phone);
        $lead->setVerified($user->verified);
        $lead->setCompany($user->company_name);
        $lead->setSource(($user->lead_source) ? $user->lead_source : 'organic');
        $lead->setStatus(($user->status == '1') ? 'Active' : 'New');
        $lead->setShippingVolume($user->shipping_volume);
        $lead->setShippingPartner($user->shipping_partner);
        $lead->setShippingType($user->industry_type);

        $lead->createLead();



        // if ($user->leadsquared_id != '')
        //     return false;

        // $this->CI->load->library('leadsquared');

        // $lead = new Leadsquared();
        // $lead->setFName($user->fname);
        // $lead->setLName($user->lname);
        // $lead->setEmail($user->email);
        // $lead->setPhone($user->phone);
        // $lead->setCompany($user->company_name);
        // $lead->setSource($user->lead_source);
        // $lead->setStatus(($user->status == '1') ? 'Active' : 'New');

        // $lead_id = $lead->createLead();
        // if (!$lead_id)
        //     return false;

        // $update = array(
        //     'leadsquared_id' => $lead_id,
        // );

        // $this->CI->user_lib->update($user_id, $update);
        // return $lead_id;

        return true;
    }

    function send_email($user_id, $doc_type, $type)
    {
        $user = $this->getByID($user_id);

        $message = "<h4>Dear <b>" . $user->fname . " " . $user->lname . "</b> ,</h4>";
        $message .= "<p>Your eKYC with <b>" . $doc_type . "</b> has been <b>" . $type . "</b> on our system.";
        $message .= " You can review your account by visiting the Profile section.</p><br>";
        $message .= "<p>Thanks & Regards</p>";
        $message .= "<p>delta Team</p>";
        $this->CI->load->library('email_lib');
        $email = new Email_lib();
        $email->to($user->email);
        $email->subject('deltagloabal - Your eKYC details..!! '  . ucfirst($type));
        $email->message($message);


        $email->send();
    }
    function sendOTPViaEmail($userId= false,$otp=false)
    {
        if (!$userId)
            return false;

        $user = $this->getByID($userId);
        $subject='deltagloabal Login OTP';
        $message = "<h4>Hi " . $user->fname . " " . $user->lname . "! </b></h4>";
        $message .= "<p>Use the following one-time password (OTP) to sign in to your deltagloabal account.";
        $message .= "<h4>" .$otp. "</b></b></h4>";
        $message .= "<p>Thanks & Regards</p>";
        $message .= "<p>deltagloabal Team</p>";
        $this->send_generate_email($user->email,$subject, $message);
        return true;
    }
    function send_generate_email($emailId,$subject, $message)
    {
        $this->CI->load->library('email_lib');
        $email = new Email_lib();
        $email->to($emailId);
        $email->subject($subject);
        $email->message($message);
        $email->send();
        return true;
    }
}
