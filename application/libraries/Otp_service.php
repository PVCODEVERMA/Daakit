<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Otp_service extends MY_lib
{
    protected $defaultOtpLength = 6;
    protected $defaultOtpExpiryMinutes = 5;
    protected $defaultResendCooldownSeconds = 30;
    protected $defaultRateLimitWindowMinutes = 10;
    protected $defaultMaxRequestsPerWindow = 5;

    public function __construct()
    {
        parent::__construct();
    }

    public function generateOtp($length = null)
    {
        $length = !empty($length) ? (int) $length : (int) $this->CI->config->item('otp_length');
        if ($length <= 0) {
            $length = $this->defaultOtpLength;
        }

        $min = (int) pow(10, $length - 1);
        $max = (int) pow(10, $length) - 1;

        return (string) random_int($min, $max);
    }

    public function hashOtp($otp = '')
    {
        if ($otp === '') {
            return false;
        }

        return password_hash((string) $otp, PASSWORD_BCRYPT);
    }

    public function verifyOtpHash($otp = '', $otpHash = '')
    {
        if ($otp === '' || $otpHash === '') {
            return false;
        }

        return password_verify((string) $otp, $otpHash);
    }

    public function getExpiryDateTime($minutes = null)
    {
        $minutes = !empty($minutes) ? (int) $minutes : (int) $this->CI->config->item('otp_expiry_minutes');
        if ($minutes <= 0) {
            $minutes = $this->defaultOtpExpiryMinutes;
        }

        return date('Y-m-d H:i:s', time() + ($minutes * 60));
    }

    public function getOtpExpiryMinutes()
    {
        $minutes = (int) $this->CI->config->item('otp_expiry_minutes');
        return ($minutes > 0) ? $minutes : $this->defaultOtpExpiryMinutes;
    }

    public function getResendCooldownSeconds()
    {
        $seconds = (int) $this->CI->config->item('otp_resend_cooldown_seconds');
        return ($seconds > 0) ? $seconds : $this->defaultResendCooldownSeconds;
    }

    public function getRateLimitWindowMinutes()
    {
        $minutes = (int) $this->CI->config->item('otp_rate_limit_window_minutes');
        return ($minutes > 0) ? $minutes : $this->defaultRateLimitWindowMinutes;
    }

    public function getMaxRequestsPerWindow()
    {
        $max = (int) $this->CI->config->item('otp_max_requests_per_window');
        return ($max > 0) ? $max : $this->defaultMaxRequestsPerWindow;
    }

    public function normalizePhone($phone = '')
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if (empty($digits)) {
            return '';
        }

        if (strlen($digits) === 12 && strpos($digits, '91') === 0) {
            $digits = substr($digits, 2);
        }

        return $digits;
    }

    public function isValidPhone($phone = '')
    {
        return (bool) preg_match('/^[6-9][0-9]{9}$/', $phone);
    }

    public function sendOtpMSG91($phone = '', $otp = '', $otpType = 'phone_verification')
    {
        if (empty($phone) || empty($otp)) {
            return array(
                'success' => false,
                'message' => 'Phone or OTP missing.',
            );
        }

        // Default auth key (can be overridden via config).
        $defaultAuthKey = '434505ATvL0aSVRtef67ebdcabP1';
        $authKey = trim((string) $this->CI->config->item('otp_msg91_auth_key'));
        if ($authKey === '') {
            // Optional fallback key name already used in some modules.
            // $config['msg91_api_key'] = 'YOUR_MSG91_AUTH_KEY';
            $authKey = trim((string) $this->CI->config->item('msg91_api_key'));
        }
        if ($authKey === '') {
            $authKey = $defaultAuthKey;
        }

        // Default template id (can be overridden via config).
        $defaultTemplateId = '6880922dd6fc055c695f9ab3';
        $templateId = trim((string) $this->CI->config->item('otp_msg91_template_id'));
        if ($templateId === '') {
            $templateId = $defaultTemplateId;
        }

        // Default MSG91 flow URL (can be overridden via config).
        $defaultFlowUrl = 'https://control.msg91.com/api/v5/flow';
        $url = trim((string) $this->CI->config->item('otp_msg91_flow_url'));
        if ($url === '') {
            $url = $defaultFlowUrl;
        }

        if ($authKey === '' || $templateId === '') {
            return array(
                'success' => false,
                'message' => 'MSG91 credentials/template not configured.',
            );
        }

        $payload = array(
            'template_id' => $templateId,
            'short_url' => '0',
            'realTimeResponse' => '1',
            'recipients' => array(
                array(
                    'mobiles' => '91' . $phone,
                    'var1' => (string) $otp,
                    'var2' => (string) $otpType,
                ),
            ),
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'authkey: ' . $authKey,
                'content-type: application/json',
            ),
            CURLOPT_TIMEOUT => 15,
        ));

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!empty($curlError)) {
            return array(
                'success' => false,
                'http_code' => $httpCode,
                'message' => $curlError,
            );
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            return array(
                'success' => false,
                'http_code' => $httpCode,
                'message' => 'MSG91 request failed.',
                'response' => $response,
            );
        }

        return array(
            'success' => true,
            'http_code' => $httpCode,
            'response' => $response,
        );
    }
}
