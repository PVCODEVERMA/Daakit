<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Email_lib extends MY_lib {

    var $smtp_host;
    var $smtp_port;
    var $smtp_user;
    var $smtp_pass;
    var $from_email;
    var $from_name;
    var $send_to;
    var $send_cc;
    var $send_bcc;
    var $email_subject;
    var $email_message;
    var $attachment = array();

    public function __construct() {
        parent::__construct();
        $this->smtp_host = $this->CI->config->item('smtp_host');
        $this->smtp_port = $this->CI->config->item('smtp_port');
        $this->smtp_user = $this->CI->config->item('smtp_user');
        $this->smtp_pass = $this->CI->config->item('smtp_pass');
        $this->from_email = $this->CI->config->item('from_email');
        $this->from_name = $this->CI->config->item('from_name');
    }

    function to($emails = false) {
        if (!$emails)
            return false;
        if (is_array($emails)) {
            foreach ($emails as $email) {
                $this->to($email);
            }
        } else {
            $this->send_to[] = $emails;
        }
    }

    function subject($subject = false) {
        if (!$subject)
            return false;
        $this->email_subject = $subject;
    }

    function from($email = false) {
        if (!$email)
            return false;
        $this->from_email = $email;
    }

    function message($message = false) {
        if (!$message)
            return false;
        $this->email_message = $message;
    }

    function set_cc($email = false) {
        if (!$email)
            return false;
        $this->send_cc = $email;
    }
    
     function set_bcc($email = false) {
        if (!$email)
            return false;
        $this->send_bcc = $email;
    }

    function attach($attach = false) {
        if (!$attach)
            return false;
        if (is_array($attach)) {
            $this->attachment = $attach;
        } else {
            $this->attachment[] = $attach;
        }
    }

    function send() {
        if (empty($this->send_to) || empty($this->email_subject) || empty($this->email_message)) {
            $this->error = 'Fields missing';
            return false;
        }

        try {
            $config = Array(
                'protocol' => 'smtp',
                'smtp_host' => $this->smtp_host,
                'smtp_port' => $this->smtp_port,
                'smtp_user' => $this->smtp_user,
                'smtp_pass' => $this->smtp_pass,
                'mailtype' => 'html',
                'charset' => 'iso-8859-1',
                'wordwrap' => TRUE,
                //'smtp_crypto' => 'tls'
            );
            $this->CI->load->library('email');
            $this->CI->email->set_newline("\r\n");

            $this->CI->email->initialize($config);
            if (!empty($this->attachment)) {
                foreach ($this->attachment as $ath) {
                    $this->CI->email->attach($ath);
                }
            }

            if (!empty($this->send_cc))
                $this->CI->email->cc($this->send_cc);
            
            if (!empty($this->send_bcc))
                $this->CI->email->bcc($this->send_bcc);

            $this->CI->email->from($this->from_email, $this->from_name);
            $this->CI->email->to($this->send_to);

            $this->CI->email->subject($this->email_subject);
            $this->CI->email->message($this->email_message);

            $result = $this->CI->email->send(false);
            $this->CI->email->clear(TRUE);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}

?>
