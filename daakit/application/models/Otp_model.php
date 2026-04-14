<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Otp_model extends MY_model
{
    public $table = 'user_otps';

    public function __construct()
    {
        parent::__construct();
    }

    public function insertOtp($data = array())
    {
        if (empty($data['phone']) || empty($data['otp_hash']) || empty($data['otp_type']) || empty($data['expires_at'])) {
            return false;
        }

        $this->db->trans_start();

        $this->db->where('phone', $data['phone']);
        $this->db->where('otp_type', $data['otp_type']);
        $this->db->where('is_used', 0);

        if (!empty($data['user_id'])) {
            $this->db->where('user_id', $data['user_id']);
        }

        $this->db->update($this->table, array('is_used' => 1));

        $this->db->insert($this->table, array(
            'user_id' => !empty($data['user_id']) ? $data['user_id'] : null,
            'phone' => $data['phone'],
            'otp_hash' => $data['otp_hash'],
            'otp_type' => $data['otp_type'],
            'expires_at' => $data['expires_at'],
            'attempts' => isset($data['attempts']) ? (int) $data['attempts'] : 0,
            'is_used' => isset($data['is_used']) ? (int) $data['is_used'] : 0,
        ));

        $insertId = $this->db->insert_id();

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            return false;
        }

        return $insertId;
    }

    public function getLatestOtp($phone = '', $otpType = '', $userId = null)
    {
        if (empty($phone) || empty($otpType)) {
            return false;
        }

        $this->db->from($this->table);
        $this->db->where('phone', $phone);
        $this->db->where('otp_type', $otpType);
        if (!empty($userId)) {
            $this->db->where('user_id', $userId);
        }
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getLastOtpTime($phone = '', $otpType = '', $userId = null)
    {
        if (empty($phone) || empty($otpType)) {
            return false;
        }

        $this->db->select('created_at');
        $this->db->from($this->table);
        $this->db->where('phone', $phone);
        $this->db->where('otp_type', $otpType);
        if (!empty($userId)) {
            $this->db->where('user_id', $userId);
        }
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function countRecentOtps($phone = '', $otpType = '', $minutes = 10, $userId = null)
    {
        if (empty($phone) || empty($otpType)) {
            return 0;
        }

        $minutes = (int) $minutes;
        if ($minutes <= 0) {
            $minutes = 10;
        }

        $fromDateTime = date('Y-m-d H:i:s', strtotime('-' . $minutes . ' minutes'));

        $this->db->from($this->table);
        $this->db->where('phone', $phone);
        $this->db->where('otp_type', $otpType);
        if (!empty($userId)) {
            $this->db->where('user_id', $userId);
        }
        $this->db->where('created_at >=', $fromDateTime);

        return (int) $this->db->count_all_results();
    }

    public function incrementAttempts($otpId = 0)
    {
        if (empty($otpId)) {
            return false;
        }

        $this->db->where('id', $otpId);
        $this->db->set('attempts', 'attempts + 1', false);
        $this->db->update($this->table);

        return ($this->db->affected_rows() >= 0);
    }

    public function markUsed($otpId = 0)
    {
        if (empty($otpId)) {
            return false;
        }

        $this->db->where('id', $otpId);
        $this->db->update($this->table, array('is_used' => 1));

        return ($this->db->affected_rows() >= 0);
    }
}
