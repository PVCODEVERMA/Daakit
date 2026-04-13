<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_lib extends MY_lib
{

    public function __construct()
    {
        parent::__construct();

        $this->CI->load->model('invoice_model');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->invoice_model, $method)) {
            throw new Exception('Undefined method invoice_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->invoice_model, $method], $arguments);
    }

    function sendInvoiceEmail($invoice_id = false)
    {
        if (!$invoice_id)
            return false;

        $invoice = $this->getByID($invoice_id);

        if (empty($invoice))
            return false;

        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($invoice->user_id);
        $invoice_no =($invoice->invoice_no != '') ? $invoice->invoice_no : 'NPPL/'.sprintf('%03d', $invoice->id); 
        $this->CI->load->library('email_lib');
        $email = new Email_lib();
        $attachment_pdf = strpos($invoice->pdf_file, "amazonaws.com") ? $invoice->pdf_file : (base_url("assets/invoice/") . $invoice->pdf_file);
        $attachment_csv = strpos($invoice->csv_file, "amazonaws.com") ? $invoice->csv_file : (base_url("assets/invoice/") . $invoice->csv_file);

        $email->attach(array($attachment_csv, $attachment_pdf));

        $email->to($user->email);
        $email->set_bcc($this->CI->config->item('invoice_email_bcc'));


        $email->subject("deltagloabal Invoice No. {$invoice_no}");
        $email->message($this->CI->load->view('emails/send_invoice', array('invoice' => $invoice, 'user' => $user), true));
        $email->send();

        return true;
    }

    function sendInvoiceCreditNoteEmail($invoice_id = false)
    {
        if (!$invoice_id)
            return false;

        $invoice = $this->getInvoiceCNByID($invoice_id);

        if (empty($invoice))
            return false;

        $this->CI->load->library('user_lib');
        $user = $this->CI->user_lib->getByID($invoice->user_id);

        $this->CI->load->library('email_lib');
        $email = new Email_lib();
        $attachment_pdf = strpos($invoice->pdf_file, "amazonaws.com") ? $invoice->pdf_file : (base_url("assets/invoice/") . $invoice->pdf_file);
        $attachment_csv = strpos($invoice->csv_file, "amazonaws.com") ? $invoice->csv_file : (base_url("assets/invoice/") . $invoice->csv_file);
        $email->attach(array($attachment_csv, $attachment_pdf));

        $email->to($user->email);
        //$email->to('amitk@deltagloabal.com');
        $email->set_bcc($this->CI->config->item('invoice_email_bcc'));


        $email->subject("deltagloabal Credit Note No. NPPL/CN/{$invoice->id}");
        $email->message($this->CI->load->view('emails/send_invoice_credit_note', array('invoice' => $invoice, 'user' => $user), true));
        $email->send();

        return true;
    }
}
