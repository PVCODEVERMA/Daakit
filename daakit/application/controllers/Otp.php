<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Otp extends MY_Controller
{
    protected $allowedOtpTypes = array(
        'signup',
        'login',
        'forgot_password',
        'phone_verification',
    );

    protected $maxAttempts = 3;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Otp_model');
        $this->load->library('Otp_service');
        $this->load->helper('url');
    }

    public function send()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            show_404();
            return;
        }

        $rawPhone = $this->input->post('phone', true);
        $otpType = trim((string) $this->input->post('otp_type', true));
        $userId = $this->input->post('user_id', true);
        $verifyRedirect = $this->input->post('verify_redirect', true);

        if ($otpType === '') {
            $otpType = 'phone_verification';
        }

        if (!in_array($otpType, $this->allowedOtpTypes, true)) {
            $this->respond(false, 'Invalid otp_type.', array(), $verifyRedirect);
            return;
        }

        $phone = $this->otp_service->normalizePhone($rawPhone);
        if (!$this->otp_service->isValidPhone($phone)) {
            $this->respond(false, 'Invalid phone number.', array(), $verifyRedirect);
            return;
        }

        $resolvedUserId = !empty($userId) ? (int) $userId : null;
        $cooldownSeconds = $this->otp_service->getResendCooldownSeconds();
        $rateLimitWindowMinutes = $this->otp_service->getRateLimitWindowMinutes();
        $maxRequestsPerWindow = $this->otp_service->getMaxRequestsPerWindow();

        $lastOtp = $this->Otp_model->getLastOtpTime($phone, $otpType, $resolvedUserId);
        if (!empty($lastOtp) && !empty($lastOtp->created_at)) {
            $lastTime = strtotime($lastOtp->created_at);
            if ($lastTime > 0) {
                $elapsed = time() - $lastTime;
                if ($elapsed < $cooldownSeconds) {
                    $retryAfter = $cooldownSeconds - $elapsed;
                    $this->respond(false, 'Please wait before requesting OTP again.', array(
                        'cooldown' => $cooldownSeconds,
                        'retry_after' => $retryAfter,
                    ), $verifyRedirect);
                    return;
                }
            }
        }

        $recentCount = $this->Otp_model->countRecentOtps($phone, $otpType, $rateLimitWindowMinutes, $resolvedUserId);
        if ($recentCount >= $maxRequestsPerWindow) {
            $this->respond(false, 'Too many OTP requests. Please try again later.', array(
                'window_minutes' => $rateLimitWindowMinutes,
                'max_requests' => $maxRequestsPerWindow,
                'cooldown' => $cooldownSeconds,
            ), $verifyRedirect);
            return;
        }

        $otp = $this->otp_service->generateOtp();
        $otpHash = $this->otp_service->hashOtp($otp);
        $expiresAt = $this->otp_service->getExpiryDateTime();

        if ($otpHash === false) {
            $this->respond(false, 'Unable to generate OTP hash.', array(), $verifyRedirect);
            return;
        }

        $inserted = $this->Otp_model->insertOtp(array(
            'user_id' => $resolvedUserId,
            'phone' => $phone,
            'otp_hash' => $otpHash,
            'otp_type' => $otpType,
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'is_used' => 0,
        ));

        if (empty($inserted)) {
            $this->respond(false, 'Failed to save OTP.', array(), $verifyRedirect);
            return;
        }

        $sendResult = $this->otp_service->sendOtpMSG91($phone, $otp, $otpType);
        if (empty($sendResult['success'])) {
            log_message('error', 'MSG91 OTP send failed: ' . json_encode($sendResult));
            $this->respond(false, 'OTP could not be sent.', array('otp_id' => $inserted), $verifyRedirect);
            return;
        }

        $this->session->set_tempdata('otp_verify_context', array(
            'otp_id' => $inserted,
            'phone' => $phone,
            'otp_type' => $otpType,
            'user_id' => $resolvedUserId,
        ), 600);

        $this->respond(true, 'OTP sent successfully.', array(
            'otp_id' => $inserted,
            'phone' => $phone,
            'otp_type' => $otpType,
            'expires_at' => $expiresAt,
            'expires_in' => $this->otp_service->getOtpExpiryMinutes() * 60,
            'cooldown' => $cooldownSeconds,
            'window_minutes' => $rateLimitWindowMinutes,
            'max_requests' => $maxRequestsPerWindow,
        ), $verifyRedirect);
    }

    public function verify()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            show_404();
            return;
        }

        $rawPhone = $this->input->post('phone', true);
        $otpType = trim((string) $this->input->post('otp_type', true));
        $enteredOtp = trim((string) $this->input->post('otp', true));
        $userId = $this->input->post('user_id', true);
        $failureRedirect = $this->input->post('verify_redirect', true);
        $successRedirect = $this->input->post('success_redirect', true);

        if ($otpType === '') {
            $otpType = 'phone_verification';
        }

        if (!in_array($otpType, $this->allowedOtpTypes, true)) {
            $this->respond(false, 'Invalid otp_type.', array(), $failureRedirect);
            return;
        }

        if ($enteredOtp === '') {
            $this->respond(false, 'Please enter OTP.', array(), $failureRedirect);
            return;
        }

        $phone = $this->otp_service->normalizePhone($rawPhone);
        if (!$this->otp_service->isValidPhone($phone)) {
            $this->respond(false, 'Invalid phone number.', array(), $failureRedirect);
            return;
        }

        $latestOtp = $this->Otp_model->getLatestOtp($phone, $otpType, !empty($userId) ? (int) $userId : null);
        if (empty($latestOtp)) {
            $this->respond(false, 'No OTP found. Please request a new OTP.', array(), $failureRedirect);
            return;
        }

        if ((int) $latestOtp->is_used === 1) {
            $this->respond(false, 'OTP already used. Please request a new OTP.', array(), $failureRedirect);
            return;
        }

        if (strtotime($latestOtp->expires_at) < time()) {
            $this->respond(false, 'OTP expired. Please request a new OTP.', array(), $failureRedirect);
            return;
        }

        if ((int) $latestOtp->attempts >= $this->maxAttempts) {
            $this->respond(false, 'Maximum OTP attempts reached.', array(), $failureRedirect);
            return;
        }

        $isValidOtp = $this->otp_service->verifyOtpHash($enteredOtp, $latestOtp->otp_hash);
        if (!$isValidOtp) {
            $this->Otp_model->incrementAttempts((int) $latestOtp->id);
            $this->respond(false, 'Invalid OTP.', array(
                'remaining_attempts' => max(0, $this->maxAttempts - ((int) $latestOtp->attempts + 1)),
            ), $failureRedirect);
            return;
        }

        $this->Otp_model->markUsed((int) $latestOtp->id);

        $resolvedUserId = !empty($latestOtp->user_id) ? (int) $latestOtp->user_id : (!empty($userId) ? (int) $userId : 0);
        if ($resolvedUserId > 0 && $this->db->field_exists('phone_verified', 'users')) {
            $this->db->where('id', $resolvedUserId);
            $this->db->update('users', array(
                'phone_verified' => 1,
                'modified' => date('Y-m-d H:i:s'),
            ));
        }

        if (empty($successRedirect)) {
            $successRedirect = base_url('dash');
        }

        $payload = array(
            'user_id' => $resolvedUserId > 0 ? $resolvedUserId : null,
            'phone' => $phone,
            'otp_type' => $otpType,
            'redirect_url' => $successRedirect,
        );

        if (true) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => true,
                    'message' => 'OTP verified successfully.',
                    'data' => $payload,
                )));
            return;
        }

        $this->session->set_flashdata('success', 'OTP verified successfully.');
        redirect($successRedirect, true);
    }

    protected function respond($status, $message, $data = array(), $redirectUrl = '')
    {
        if (true) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => (bool) $status,
                    'message' => $message,
                    'data' => $data,
                )));
            return;
        }

        if ($status) {
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', $message);
        }

        if (empty($redirectUrl)) {
            $redirectUrl = base_url('users/login');
        }

        redirect($redirectUrl, true);
    }
}
