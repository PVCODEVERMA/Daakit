<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('payment_lib');
    }

    function index()
    {
    }

    function captured($gateway = false)
    {
        if (!$gateway)
            return false;

        $gateway = strtolower($gateway);

        $headers = getallheaders();

        $data = file_get_contents('php://input');
        if (!$this->payment_lib->webhookCapturedPayment($gateway, $data, $headers))
            echo $this->payment_lib->get_error();
        else
            echo 'Complete';
    }

    function checkWebhookCapturedPaymenthdfcRzp($gateway = false)
    {
        //$gateway = strtolower($gateway);
        $gateway = 'hdfc_razorpay';
        $data = file_get_contents('php://input');
        $headers = getallheaders();

        if (empty($data) || empty($headers))
            return false;

        //$this->load->library('payment_lib');
        if (!$response = $this->payment_lib->webhookCapturedhdfcPayment($gateway, $data, $headers)) {
            echo $this->payment_lib->get_error();
        } else {
            echo 'Complete';
        }
    }



    function response($gateway = false)
    {
        if (!$this->payment_lib->paymentResponse($gateway)) {
            $this->session->set_flashdata('error', $this->payment_lib->get_error());
            redirect(base_url('dash'));
        }

        $this->session->set_flashdata('success', 'Wallet Credited Successfully');
        redirect(base_url('dash'));
    }


    public function api_response($id)
    {
    
        $success_rep = json_encode($_POST);
        

      if(isset($_POST['error']))
      {
           //$this->session->set_flashdata('error', $_POST['error']['code']);
           $this->session->set_flashdata('error',"Payment Failed");
           redirect(base_url('dash'));
      }
      if (!$id){
         $this->session->set_flashdata('error', 'Id is Missing');
         redirect(base_url('dash'));
      }
      
    
        $hmac_header = $_POST['razorpay_signature'];
        $calculated_hmac = hash_hmac('sha256', $_POST['razorpay_order_id']."|".$_POST['razorpay_payment_id'],  $this->config->item('hdfc_razorpay_secret'));
        $verified = hash_equals($hmac_header, $calculated_hmac);

        if (!$verified)
        {
            $this->session->set_flashdata('error', 'Payment is not Verified');
            redirect(base_url('dash'));
        }

        $payment = $this->payment_lib->getbyID($id);

          $amount= $this->get_amount_response($_POST['razorpay_payment_id']);
          
          $string1 = "Request : { Payment Id: ".$_POST['razorpay_payment_id']."} ";
          $string = "Response : ".json_encode($amount);
          $fp = fopen('deltalogs/hdfcapi' . $_SESSION['user_info']->user_id . '.log', 'a+');
          fwrite($fp, $string1.$string);
          fwrite($fp, $success_rep);
          fclose($fp);


          if(isset($amount->error) && !empty($amount->error))
          {
            
             $this->session->set_flashdata('error', $amount->error->description);
             redirect(base_url('dash'));
          }
          //pr($amount); 
          $final_amt=$amount->amount/100;// die;

          if($payment->amount!=$final_amt)
          {
              $this->session->set_flashdata('error', 'Mismatch Amount');
              redirect(base_url('dash'));
          }
    
          //$res=  $this->payment_lib->markAsPaymentreceived_hdfc($id, $_POST['razorpay_payment_id'], $final_amt, 'hdfc_razorpay');
          $res=  $this->payment_lib->markAsPaymentreceived($id, $_POST['razorpay_payment_id'], $final_amt, 'hdfc_razorpay');
            if($this->payment_lib->get_error())
            {
                $this->session->set_flashdata('error', $this->payment_lib->get_error());
                redirect(base_url('dash'));
            }
            else
            {
                $msg= 'Your payment of rs '.$final_amt.' for order number '.$_POST['razorpay_order_id'].' has been successfully credited to your wallet';
                $this->session->set_flashdata('success', $msg);
                //$this->session->set_flashdata('success', 'Wallet Credited Successfully');
                redirect(base_url('dash'));
            }

    }


    public function get_amount_response($payment_id)
    {
       
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.razorpay.com/v1/payments/'.$payment_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10, 
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERPWD => $this->config->item('hdfc_razorpay_key') . ":" . $this->config->item('hdfc_razorpay_secret'),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            )
            ));

            $response = curl_exec($curl);
            $response= json_decode($response);

            curl_close($curl);  
            return $response;


    }

    function easebuzz_response($gateway = false)
    {
        $gateway = 'easebuzz';
        $data = file_get_contents('php://input');
        $payment=json_decode($data);
        if($payment->status!='success'){
            $this->data['json'] = array('error' => 'Payment cancelled by user');
        }
        else if (!$this->payment_lib->webhookCapturedPayment($gateway,$data)) {
            $this->data['json'] = array('error' => $this->payment_lib->get_error());
        }else{
            $this->data['json'] = array('success' => 'Wallet Credited Successfully');
        }
        $this->layout(false, 'json');
    }

}
