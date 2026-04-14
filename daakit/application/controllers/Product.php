<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Product extends User_controller
{
    private $allowed_image_extension;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('products_lib');
        $this->load->library("s3");
		$this->allowed_image_extension = array('jpg', 'jpeg', 'png');
        //$this->userHasAccess('products');
    }

    function all($page = 1)
    {


        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;
        $filter = $this->input->get('filter');
        $apply_filters = array();
        if (!empty($filter['search_query'])) {
            $apply_filters['search_query'] = $filter['search_query'];
        }
       
        if (!empty($filter['pid'])) {
            $apply_filters['pid'] = array_map('intval', explode(',', $filter['pid']));

        }
        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }


        $total_row = $this->products_lib->countProductDetailsId($this->user->account_id, $apply_filters);
        $total_row = isset($total_row->total)?$total_row->total:0;

        $config = array(
            'base_url' => base_url('product/all'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
            'cur_tag_open' => '<li class="paginate_button page-item active"><a class="page-link" href="' . current_url() . '">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li class="paginate_button page-item ">',
            'num_tag_close' => '</li>',
            'attributes' => array(
                'aria-controls' => 'example-multi',
                'tabindex' => '0',
                'class' => 'page-link',
            ),
            'next_link' => 'Next',
            'prev_link' => 'Prev',
        );
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $this->data["pagination"] = $this->pagination->create_links();
        $offset = $limit * ($page - 1);
        $this->data['total_records'] = $total_row;
        $this->data['limit'] = $limit;
        $this->data['offset'] = $offset;
        $this->data['filter'] = $filter;
        $orders = $this->products_lib->fetchByProductDetailsUserID($this->user->account_id, $limit, $offset, $apply_filters);
       
        $status_orders = array();
        $this->data['orders'] = $orders;
        
        $this->data['count_by_status'] = $status_orders;
        $this->layout('products/productDetailIndex');
    }


    function mapping($page = 1)
    {

        $per_page = $this->input->get('perPage');
        if (empty($per_page) || !is_numeric($per_page))
            $limit = 50;
        else
            $limit = $per_page;
        $filter = $this->input->post('filter');
        $apply_filters = array();
        if (!empty($filter['search_query'])) {
            $apply_filters['search_query'] = $filter['search_query'];
        }
       
        if (!empty($filter['pid'])) {
            $apply_filters['pid'] = array_map('intval', explode(',', $filter['pid']));

        }
        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }


        $total_row = $this->products_lib->countProductBillingDetailsId($this->user->account_id, $apply_filters);
        $total_row = isset($total_row->total)?$total_row->total:0;

        $config = array(
            'base_url' => base_url('product/mapping'),
            'total_rows' => $total_row,
            'per_page' => $limit,
            'num_links' => '3',
            'use_page_numbers' => true,
            'reuse_query_string' => true,
            'cur_tag_open' => '<li class="paginate_button page-item active"><a class="page-link" href="' . current_url() . '">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li class="paginate_button page-item ">',
            'num_tag_close' => '</li>',
            'attributes' => array(
                'aria-controls' => 'example-multi',
                'tabindex' => '0',
                'class' => 'page-link',
            ),
            'next_link' => 'Next',
            'prev_link' => 'Prev',
        );
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $this->data["pagination"] = $this->pagination->create_links();
        $offset = $limit * ($page - 1);
        $this->data['total_records'] = $total_row;
        $this->data['limit'] = $limit;
        $this->data['offset'] = $offset;
        $this->data['filter'] = $filter;
        $orders = $this->products_lib->fetchByProductDetailsBillingUserID($this->user->account_id, $limit, $offset, $apply_filters);
       
        $status_orders = array();
        $this->data['orders'] = $orders;
        
        $this->data['count_by_status'] = $status_orders;
        $this->layout('products/productBillingDetail');
    }

    function invoice_settings()
    {
        $userid = $this->user->account_id;
        $this->load->library('Profile_lib');
        $this->load->library('form_validation');
        $picture = $comp_prifix = $signatureimg = "";
        //pr($_FILES,1);
        if (!empty($_FILES['picture']['name'])) {
            $picture = $this->uploadFile('picture', 'seller_company_logo', true);
        }

      
        if (!empty($_FILES['signatureimg']['name'])) {
            $signatureimg = $this->uploadFile('signatureimg', 'seller_company_signatureimg');
        }

        $config = array(
            array(
                'field' => 'comp_prifix',
                'label' => 'Company Prefix for invoice',
                'rules' => 'trim'
            ),

        );


        if(!empty($_POST['column_name'] ) && $_POST['column_name'][0]!=''){
           
             $array =  array(
                'field' => 'column_name[]',
                'label' => 'column name ',
                'rules' => 'trim|required'
            );

            array_push($config, $array);
        }
        if( !empty($_POST['value'] ) && $_POST['value'][0]!=''){

            $array =  array(
                'field' => 'value[]',
                'label' => 'column value ',
                'rules' => 'trim|required'
           );

           array_push($config, $array);
       }


      
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run()) {

           $column_name='';
           $column_value='';
           $custom_name=$custom_value='';
          if(!empty($_POST['column_name'] )){
          
                foreach($_POST['column_name'] as $c_n)
                {
                    $column_name .=$c_n.",";
                }
                $custom_name= rtrim($column_name, ',');
             }
           
             if(!empty($_POST['value'] )){
                 foreach($_POST['value'] as $c_v)
                 {
                     $column_value .=$c_v.",";
                 }
                $custom_value= rtrim($column_value, ',');
                }
         
         
           $option='0';
              if(isset($_POST['option']))
              {
                $option='1';
              }
               
               $inv_data = array(
                        'seller_id'                => $userid,
                        'hide_compony'           =>  $option,
                        'invoice_prefix'         => $this->input->post('comp_prifix'),
                        'custom_name'            => $custom_name,
                        'custom_value'           => $custom_value,
                        'created'                => time(),
                        'modified'               => time(),
                    );

            if (!empty($picture)) {
                $inv_data['invoice_banner'] = $picture;
            }
            
            if (!empty($signatureimg)) {
                $inv_data['invoice_signature'] = $signatureimg;
            }
            ///pr($inv_data); die;

            $invoice_setting_data = $this->profile_lib->get_invoice_setting($userid);
            if(!empty($invoice_setting_data))
            {
              
              $this->profile_lib->update_invoice_setting($userid,$inv_data);
            }
            else
            {
               
                $this->profile_lib->insert_invoice_setting($inv_data);
            }
            $this->session->set_flashdata('success', 'Invoice Settings Saved  Successfully');
            redirect(base_url('product/invoice_settings'));
            

        }
        else {
            $this->data['error'] = validation_errors();
        }

       
        
        $invoice_setting_data = $this->profile_lib->get_invoice_setting($userid);
        //pr($invoice_setting_data);
        $this->data['invoice_setting_data'] = $invoice_setting_data;
        $this->layout('products/invoice_setting');


    }

    private function uploadFile($variable_name = null, $folder_name = null, $image_only = false)
	{
		if ($variable_name == null || $folder_name == null) {
			return '';
		}
		$returnval = '';
		$extension = strtolower(pathinfo($_FILES[$variable_name]['name'], PATHINFO_EXTENSION));

		$new_name = time() . rand(1111, 9999) . '.' . ($extension);

		if (($image_only && in_array($extension, $this->allowed_image_extension)) || (!$image_only && (in_array($extension, $this->allowed_image_extension) || in_array($extension, $this->allowed_document_extension)))) {
			$config['file_name'] = $new_name;

			$fileTempName = $_FILES[$variable_name]['tmp_name'];
			$image_name = $new_name;

			$file_name = $this->s3->amazonS3Upload($image_name, $fileTempName, $folder_name);
			if ($file_name) {
				$returnval = $file_name;
			}
		}
		return $returnval;
	}

     function productUpdate()
    {
       
        $this->load->library('form_validation');
        $length = $this->input->post('length');
        $product_name = $this->input->post('product_name');
        $product_sku = $this->input->post('product_sku');
        $product_qty = $this->input->post('product_qty');
        $breadth = $this->input->post('breadth');
        $height = $this->input->post('height');
        $weight = $this->input->post('weight');
        // $igst = $this->input->post('igst');
        // $hsn = $this->input->post('hsn');
        $product_id = $this->input->post('product_id');
        $is_weight = $this->input->post('is_weight');
        $is_weight = (empty($is_weight))?0:1;
        $required_if =  $required_ifs = '';

         if (empty($length) && empty($breadth) && empty($height)  && empty($weight) && ($is_weight)) {
           $required_if = '|required';
         }   

  
        if (!empty($length) || !empty($breadth) || !empty($height)  || !empty($weight)) {

            $required_if = '|required';
        }
        // if (!empty($igst) || !empty($hsn)) {

        //     $required_ifs = '|required';
        // }
        if (empty($length) && empty($breadth) && empty($height)  && empty($weight)) {
           
            $this->data['json'] = array('error' => 'Please fill one of field in order to update');
            $this->layout(false, 'json');
            exit;
        }

        if($weight<500){
            $this->data['json'] = array('error' => 'Weight value should be greater or equal to 500');
            $this->layout(false, 'json');
            exit;  
        }


        $this->form_validation->set_rules('product_name', 'Product SKU', 'trim|required');
        $this->form_validation->set_rules('length', 'Length', 'trim' . $required_if . '|integer|greater_than[0]');
        $this->form_validation->set_rules('breadth', 'Breadth', 'trim' . $required_if . '|integer|greater_than[0]');
        $this->form_validation->set_rules('height', 'Height', 'trim' . $required_if . '|integer|greater_than[0]');
        $this->form_validation->set_rules('weight', 'Weight', 'trim' . $required_if . '|greater_than[0]');
        // $this->form_validation->set_rules('igst', 'GST', 'trim' . $required_ifs . '|numeric|greater_than[0]|min_length[1]|max_length[20]');
        // $this->form_validation->set_rules('hsn', 'HSN', 'trim' . $required_ifs . '|numeric|greater_than[0]|min_length[1]|max_length[20]');
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'product_name' => $this->input->post('product_name'),
                'product_sku' => $this->input->post('product_sku'),
                'length' => $length,
                'breadth' => $breadth,
                'height' => $height,
                'weight' => $weight,
                'is_weight'=> $is_weight,
                'product_qty'=>$product_qty,
                'action_by'=>1,  
            );

           $products =  $this->products_lib->checkProductDetailsAndInsert($save,$this->user->account_id);
          
           if ($products == 1) {
                $this->data['json'] = array('success' => 'Record has been updated successfully');
            } else {
                $this->data['json'] = array('success' => 'Record has been updated successfully');
            }
        } else {
            $errors = validation_errors();
            $errors = str_ireplace('<p>', '', $errors);
            $errors = str_ireplace('</p>', '', $errors);

            $this->data['json'] = array('error' => $errors);
        }
        $this->layout(false, 'json');
    }


    function productBillingUpdate(){
       
        $this->load->library('form_validation');
        
        $igst = $this->input->post('igst');
        $hsn = $this->input->post('hsn');
        $required_if =  $required_ifs = '';

        if (!empty($igst) || !empty($hsn)) {

            $required_ifs = '|required';
        }
        if (empty($igst) && empty($hsn)) {
           
            $this->data['json'] = array('error' => 'Please fill both field in order to update');
            $this->layout(false, 'json');
            exit;
        }
        
        $this->form_validation->set_rules('igst', 'GST', 'trim' . $required_ifs . '|numeric|greater_than_equal_to[0]|min_length[1]|max_length[20]');
        $this->form_validation->set_rules('hsn', 'HSN', 'trim' . $required_ifs . '|numeric|greater_than[0]|min_length[1]|max_length[20]');
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'product_name' => $this->input->post('product_name'),
                'product_sku' => $this->input->post('product_sku'),
                'igst' => $igst,
                'hsn_code' => $hsn,
                
            );

           $products =  $this->products_lib->checkProductBillingDetailsAndInsert($save,$this->user->account_id);
          
           if ($products == 1) {
                $this->data['json'] = array('success' => 'Record has been updated successfully');
            } else {
                $this->data['json'] = array('success' => 'Record has been updated successfully');
            }
        } else {
            $errors = validation_errors();
            $errors = str_ireplace('<p>', '', $errors);
            $errors = str_ireplace('</p>', '', $errors);

            $this->data['json'] = array('error' => $errors);
        }
        $this->layout(false, 'json');
    }


    function exportProductSkuCSV()
    {
        $filter = $this->input->get('filter');

        $apply_filters = array();
        if (!empty($filter['search_query'])) {
            $apply_filters['search_query'] = $filter['search_query'];
        }
       
        if (!empty($filter['pid'])) {
            $apply_filters['pid'] = array_map('intval', explode(',', $filter['pid']));
        }
        if (!empty($filter['status'])) {
            $apply_filters['status'] = $filter['status'];
        }
        $this->load->library('catalog_lib');
        $final_result = $this->products_lib->exportproductDetails($this->user->account_id,$apply_filters);
        //pr($final_result);exit;

        $filename = 'product_sku_export_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "PID",
            "Product_Name",
            "Product_SKU",
            "product_quantity",
            "Length",
            "Breadth",
            "Height",
            "Weight",
            "Weight Freeze Status"
            
           
         );
        fputcsv($file, $header);
        $status = "";
        foreach($final_result as  $record){
             if (!empty($record->weight_locked)) {
                 $status =  ($record->weight_locked == 1) ? 'Requested' : (($record->weight_locked == '2') ? 'Accepted' : 'Rejected');
             }else{
                $status = "Not Requested";
             }
            $row = array(
                $record->pid,
                 $record->product_name,
                 $record->product_sku,
                 $record->product_qty,
                 $record->length,
                 $record->breadth,
                 $record->height,
                 $record->weight,
                 $status
            );  
                fputcsv($file, $row);} 
            
        fclose($file);
        
       
    }

     function exportProductSkuBillingCSV()
    {
        $filter = $this->input->get('filter');

        $apply_filters = array();
        if (!empty($filter['search_query'])) {
            $apply_filters['search_query'] = $filter['search_query'];
        }
       
        if (!empty($filter['pid'])) {
            $apply_filters['pid'] = array_map('intval', explode(',', $filter['pid']));
        }
        
        $this->load->library('catalog_lib');
        $final_result = $this->products_lib->exportproductBillingDetails($this->user->account_id,$apply_filters);

        $filename = 'product_sku_billing_export_' . time() . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array(
            "PID",
            "Product_Name",
            "Product_SKU",
            "GST",
            "HSN_Code"
            
            
           
         );
        fputcsv($file, $header);
        $status = "";
        foreach($final_result as  $record){
             if (!empty($record->weight_locked)) {
                 $status =  ($record->weight_locked == 1) ? 'Requested' : (($record->weight_locked == '2') ? 'Accepted' : 'Rejected');
             }
            $row = array(
                 $record->pid,
                 $record->product_name,
                 $record->product_sku,
                 $record->igst,
                 $record->hsn_code
            );  
                fputcsv($file, $row);} 
            
        fclose($file);
        
       
    }

    // function productUpdate()
    // {

        
    //     $length = $this->input->post('length');
    //     $breadth = $this->input->post('breadth');
    //     $height = $this->input->post('height');
    //     $weight = $this->input->post('weight');
    //     $igst = $this->input->post('igst');
    //     $hsn = $this->input->post('hsn');
    //     $product_id = $this->input->post('product_id');
    //     $is_weight = $this->input->post('is_weight');
    //     $is_weight = (empty($is_weight))?0:1;
        
    //     $save = array(
    //                     'length' => $length,
    //                     'breadth' =>$breadth,
    //                     'height' => $height,
    //                     'weight' => $weight,
    //                     'igst' => $igst,
    //                     'hsn_code' => $hsn,
    //                     'is_weight'=> $is_weight,
    //                     );
    //      $updates = 0; 
    //      if(empty($weight_locked)){
    //      $updates = $this->products_lib->productDetailsUpdate($save,$product_id);

    //      } 
        
    //      if ($updates == 1) {
    //             $this->data['json'] = array('success' => 'Record has been successfully updated');
    //         } 
    //     $this->layout(false, 'json');
    // }



    function inserthsn_gst(){
        $records = $this->products_lib->get_product_details();
                   foreach($records as $records_data){
                    $user_id =  isset($records_data->user_id)?$records_data->user_id:"";

                    $save_prod['product_name'] = isset($records_data->product_name)?$records_data->product_name:"";
                    $save_prod['product_sku'] = isset($records_data->product_sku)?$records_data->product_sku:"";
                    $igst = isset($records_data->igst)?$records_data->igst:"";
                    $hsn_code = isset($records_data->hsn_code)?$records_data->hsn_code:"";
                    $code = $this->products_lib->get_product_details_billing_code($user_id,$save_prod);
                    if(!empty($code) && !empty($igst) && !empty($hsn_code) ){
                       $save['user_id'] = $user_id;
                       $save['product_name'] = $save_prod['product_name'];
                       $save['product_sku'] = $save_prod['product_sku'];
                       $save['product_details_code'] = $code;
                       $save['igst'] =  $igst;
                       $save['hsn_code'] = $hsn_code;  
                       $this->products_lib->insertProductBilling($save);
                      
                    }
                    

                   }
                   
    }



     function modalproductUpdate()
    {
        
        
        $this->load->library('s3');
        $this->load->library('escalation_lib');
       
        $catalogPath ='';

       
  
        $filesCount =  (!empty($_FILES['importFile'])) ? count(array_filter($_FILES['importFile']['name'])) : 0;

        if((isset($_FILES['importFile']['error']['0'])) && ($_FILES['importFile']['error']['0']==1)){
           $this->data['json'] = array('error' => "File size not valid");
                    $this->layout(false, 'json');
                    return; 
        }
       $uploadData = array();
        $upload_folder = "escalations";
         $file_size_error = 0;
         $file_extension_error = 0;
        for ($i = 0; $i < $filesCount; $i++) {
            if (!empty($_FILES['importFile']['name'][$i])) {
                if($_FILES['importFile']['size'][$i] > 5000000){
                    $file_size_error = 1;
                    continue;
                }
                $extension = pathinfo($_FILES['importFile']['name'][$i], PATHINFO_EXTENSION);
                //pr($extension);exit;
                if(!in_array(strtolower($extension), array("pdf","jpeg", "png", "jpg"))){
                    $file_extension_error = 1;
                    continue;
                }
             //   echo $_FILES['importFile']['name'][$i] ."sdsd". $_FILES['importFile']['size'][$i];exit;
                $extension = explode(".", $_FILES['importFile']['name'][$i]);
                $new_name = time() . rand(100, 999) . '.' . end($extension);

                $config['file_name'] = $new_name;

                $fileTempName = $_FILES['importFile']['tmp_name'][$i];
                $image_name = $new_name;

                $file_name = $this->s3->amazonS3Upload($image_name, $fileTempName, $upload_folder);

                if ($file_name) {
                    $uploadData[] = $file_name;
                } else {
                    
                    $this->data['json'] = array('error' => "Unable to upload file");
                    $this->layout(false, 'json');
                    return;
                }
            }
        }
        
        if($file_size_error){
         $this->data['json'] = array('error' => "Unable to upload file due to file size");
                    $this->layout(false, 'json');
                    return;   
        }
        if($file_extension_error){
         $this->data['json'] = array('error' => "file format should be jpeg,png,pdf,jpg");
                    $this->layout(false, 'json');
                    return;   
        }
        $product_ids = $this->input->post('product_ids'); 
        $weight_freze_status = $this->input->post('weight_freze_status');
        $length_ids = $this->input->post('length_ids');
        $remarks = $this->input->post('remarks');
        if(empty(trim($remarks))){
            $this->data['json'] = array('error' => "please fill remarks field");
                    $this->layout(false, 'json');
                    return;
        }
        $breadth_ids = $this->input->post('breadth_ids');
        $height_ids = $this->input->post('height_ids');
        $weight_ids = $this->input->post('weight_ids');
        $is_weights = $this->input->post('is_weights');
        $product_sku_ids = $this->input->post('product_sku_ids');
       
        $escalation_status = "";
        if(!empty($product_ids)){
            $product_ids = explode(',',$product_ids);
            $length_ids = explode(',',$length_ids);
            $breadth_ids = explode(',',$breadth_ids);
            $height_ids = explode(',',$height_ids);
            $weight_ids = explode(',',$weight_ids);
            $is_weights = explode(',',$is_weights);
            $product_sku_ids = explode(',',$product_sku_ids);
            foreach ($product_ids as $key => $prod_id) {
                  $check_image_validation  = $this->products_lib->getBYPid($prod_id);
                  if(empty($filesCount) && empty($check_image_validation->weight_locked)){
                      $this->data['json'] = array('error' => "Please upload image in order to freeze request");
                     $this->layout(false, 'json');
                    continue;
                  }

                $save = array(
                    'id' => $prod_id,
                    'product_sku' => preg_replace('/[@]+/', ',', trim($product_sku_ids[$key])),
                    'weight_locked' => $weight_freze_status,
                    'length' => $length_ids[$key],
                    'breadth' => $breadth_ids[$key],
                    'height' => $height_ids[$key],
                    'weight' => !empty($weight_ids[$key]) ? $weight_ids[$key] : '500',
                    'is_weight' => (!empty($is_weights[$key]) && $is_weights[$key] !== 'false') ? '1' : '0',
                   
                   );


                if((strval($save['length']) !== strval(intval($save['length']))) || (strval($save['height']) !== strval(intval($save['height']))) || (strval($save['breadth']) !== strval(intval($save['breadth'])))){
                  $this->data['json'] = array('error' => "Product Length , Weight , Breadth should be Integer");
                    $this->layout(false, 'json');
                    return;

                }


                if($save['weight']<500){
                    $this->data['json'] = array('error' => "Product Weight should be greater or equal to 500");
                    $this->layout(false, 'json');
                    return;   
                }
                  
                
                $checkId =  $this->products_lib->getProducts($prod_id,$this->user->account_id);
                if (!empty($checkId)) {
                 if($weight_freze_status){
                    $escalation_status = 'new';
                 }

                 $ret = $this->products_lib->productDetailsUpdate($save,$checkId->id);
                 $products = $checkId->id;
                } else {
                 $products = $this->products_lib->ProductDetailsInsert($save);
                }

               

                $update = array(
                    'type' => 'weight_freeze',
                    'ref_id' => $products,
                    'remarks' => $remarks,
                    'action_by' => 'seller',
                    'attachments' => implode(',', $uploadData)
                );
                if(!empty($escalation_status)){
                    $update['status'] = 'new';
                }
                              
                $esc_id = $this->escalation_lib->getEscalationByRefIDType($products, 'weight_freeze', false);
                if(empty($esc_id)){
                   $update['subject'] = 'Weight freeze Request - '.$save['product_sku'];
                     $this->escalation_lib->create_escalation($this->user->account_id, $update);
                }else{
                    $this->escalation_lib->submit_action($esc_id->id, $update);
                  }
               }

               if ($products) {
                $this->data['json'] = array('success' => 'Weight freeze request sent successfully for approval');
            }
        }else{
               $this->data['json'] = array('success' => 'Please update data first');
        }
      
        $this->layout(false, 'json');
    }

    
     function import(){
        if((isset($_FILES['importFile']['tmp_name'])) &&(empty($_FILES['importFile']['tmp_name']))){
            $this->session->set_flashdata('error', 'please Upload file');
            redirect('product/all', true);
        }
            $ext = pathinfo($_FILES['importFile']['name'], PATHINFO_EXTENSION);
      
        if($ext!="csv"){
            $this->session->set_flashdata('error', 'please Upload only csv file');
            redirect('product/all', true);
        }
        if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
            $this->load->library('csvreader');
            $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
            if (empty($csvData)) {
                $this->session->set_flashdata('error', 'Blank CSV File');
                redirect('product/all', true);
            }
            $user_id=$this->user->account_id;
            $import_message_array = array();
            $this->load->library('form_validation');
             $error="";
             $products_name = array();
             $i = 2;
             $error_data = array();
            foreach ($csvData as $row_key => $row) {
                $str = array_change_key_case($row);



                if (!array_key_exists("pid",$str)){
                    $this->session->set_flashdata('error', 'Product id header is missing.');
                    redirect('product/all', true);
                }
                if (!array_key_exists("product_name",$str)){
                    $this->session->set_flashdata('error', 'Product_Name header is missing.');
                    redirect('product/all', true);
                }
              
                if (!array_key_exists("product_sku",$str)){
                    $this->session->set_flashdata('error', 'Product_SKU header is missing.');
                    redirect('product/all', true);
                }
                
                if (!array_key_exists("length",$str)){
                    $this->session->set_flashdata('error', 'Length header is missing.');
                    redirect('product/all', true);
                }
                
                if (!array_key_exists("breadth",$str)){
                     $this->session->set_flashdata('error', 'Breadth  header is missing.');
                     redirect('product/all', true);
                }
                if (!array_key_exists("height",$str)){
                   $this->session->set_flashdata('error', 'Height header is missing.');
                   redirect('product/all', true);
                }

                if (!array_key_exists("weight",$str)){
                    $this->session->set_flashdata('error', 'Weight header is missing.');
                    redirect('product/all', true);
                }

               

                if (!array_key_exists("product_quantity",$str)){
                    $this->session->set_flashdata('error', 'Product quantity header is missing.');
                    redirect('product/all', true);
                }

                


                $product_id = $str['pid'];
                $product_name = $str['product_name'];
                $product_sku = $str['product_sku'];
                $product_quantity = $str['product_quantity'];
                $length = $str['length'];
                $breadth =$str['breadth'];
                $height = $str['height'];
                $weight = $str['weight'];
                
               
               
                $required_if =  $required_ifs = '';
                
                $blank_errors = "";
                $counting = 0;
                //$length = count($str);
                $exest = 0;
                foreach($str as $checkblankkey => $checkblank){
                     if(!in_array($checkblankkey , array("pid"))){

                    if(empty($checkblank)){
                        $blank_errors .= strtolower(str_replace("_", " ", $checkblankkey));
                        if(!empty($blank_errors)){
                          $blank_errors .= " , ";
                        }
                        
                    } }

                }
                if(!empty($blank_errors)){
                 $blank_errors = rtrim(trim($blank_errors), ',');   
                }

                if(!empty($blank_errors)){
                    $error_data[] = "Row No ".$i." is not inserted due to empty value of ".$blank_errors;
                    $i++;
                    continue;
                }

                if((strval($length) !== strval(intval($length))) || (strval($height) !== strval(intval($height))) || (strval($breadth) !== strval(intval($breadth)))){
                    $error = "Product Length , Breadth , Height Should be numeric";
                    continue;
                } 
                
                if( $weight < 500){
                    $error = "Product Weight value should be greater or equal to 500";
                    continue; 
                }
                
                if((empty($product_name)) || (empty($product_sku)) || (empty($product_quantity)) || (empty($length)) || (empty($breadth)) || (empty($height)) || (empty($weight))){
                    
                    continue;
                }

                    $save = array(
                        'pid'=> $str['pid'],
                        'user_id' => $this->user->account_id,
                        'product_name' => $str['product_name'],
                        'product_sku' => $str['product_sku'], //product_sku
                        'length' => $str['length'],
                        'product_qty' => $str['product_quantity'],
                        'breadth' =>$str['breadth'],
                        'height' => $str['height'],
                        'weight' => $str['weight'],
                    );

                
                  $result =  $this->products_lib->bulkInsertUpdate($save,$this->user->account_id);
                  if( (isset($result['product_name'])) ){
                       //echo $result['product_name'];exit;
                      $products_name[] = $result['product_name'];
                  }
                  
               
            }
    }


    if(!empty($error_data)){
        $error_data = implode(" .</br> ",$error_data);
        $error = $error_data ; 
    }
    
     if((!empty($products_name))){
                    $product_name_s = implode(" , ",$products_name);
                    $error = "product ".$product_name_s." already exist please use pid to update them";
     }
     if(!empty($error)){
       
        $this->session->set_flashdata('error', $error);
        redirect('product/all', true);

     }
     $this->session->set_flashdata('success', 'Product details have been updated successfully');
     redirect('product/all', true);
    }


    function importbilling(){
        if((isset($_FILES['importFile']['tmp_name'])) &&(empty($_FILES['importFile']['tmp_name']))){
            $this->session->set_flashdata('error', 'please Upload file');
            redirect('product/mapping', true);
        }
            $ext = pathinfo($_FILES['importFile']['name'], PATHINFO_EXTENSION);
      
        if($ext!="csv"){
            $this->session->set_flashdata('error', 'please Upload only csv file');
            redirect('product/mapping', true);
        }
        if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
            $this->load->library('csvreader');
            $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
            if (empty($csvData)) {
                $this->session->set_flashdata('error', 'Blank CSV File');
                redirect('product/mapping', true);
            }
            $user_id=$this->user->account_id;
            $import_message_array = array();
            $this->load->library('form_validation');
             $error="";
             $products_name = array();
             $i = 2;
             $error_data = array();
            foreach ($csvData as $row_key => $row) {
                $str = array_change_key_case($row);



                if (!array_key_exists("pid",$str)){
                    $this->session->set_flashdata('error', 'Product id header is missing.');
                    redirect('product/mapping', true);
                }
                if (!array_key_exists("product_name",$str)){
                    $this->session->set_flashdata('error', 'Product_Name header is missing.');
                    redirect('product/mapping', true);
                }
              
                if (!array_key_exists("product_sku",$str)){
                    $this->session->set_flashdata('error', 'Product_SKU header is missing.');
                    redirect('product/mapping', true);
                }
                
               
                if (!array_key_exists("gst",$str)){
                    $this->session->set_flashdata('error', 'GST header is missing.');
                    redirect('product/mapping', true);
                }

                if (!array_key_exists("hsn_code",$str)){
                    $this->session->set_flashdata('error', 'HSN Code is missing.');
                    redirect('product/mapping', true);
                }
                


                $product_id = $str['pid'];
                $product_name = $str['product_name'];
                $product_sku = $str['product_sku'];
                $igst = $str['gst'];
                $hsn =$str['hsn_code'];
                $required_if =  $required_ifs = '';
                if((!(is_numeric($igst))) || (!(is_numeric($hsn)))){
                     $error =  "Please enter only numeric value of GST AND HSN";
                    continue;
                     
                }

                if((empty($product_name)) || (empty($product_sku)) ||($igst == '') || (empty($hsn)) ){
                    $error =  "Please enter Product name or product sku or gst or hsn code";
                    continue;
                }

                if($igst<0){
                    $error =  "Please enter postive value of gst";
                    continue;  
                }

               
                
                $blank_errors = "";
                $counting = 0;
                $length = count($str);
                $exest = 0;
                // foreach($str as $checkblankkey => $checkblank){
                //      if(!in_array($checkblankkey , array("pid","gst","hsn_code"))){

                //     if(empty($checkblank)){
                //         $blank_errors .= strtolower(str_replace("_", " ", $checkblankkey));
                //         if(!empty($blank_errors)){
                //           $blank_errors .= " , ";
                //         }
                        
                //     } }

                // }
                // if(!empty($blank_errors)){
                //  $blank_errors = rtrim(trim($blank_errors), ',');   
                // }

                // if(!empty($blank_errors)){
                //     $error_data[] = "Row No ".$i." is not inserted due to empty value of ".$blank_errors;
                // }

                 
                $i++;
               

                    $save = array(
                        'pid'=> $str['pid'],
                        'user_id' => $this->user->account_id,
                        'product_name' => $str['product_name'],
                        'product_sku' => $str['product_sku'], //product_sku
                        'igst' => $str['gst'],
                        'hsn_code' => $str['hsn_code'],
                        
                    );

                
                  $result =  $this->products_lib->bulkInsertUpdateBilling($save,$this->user->account_id);
                  if( (isset($result['product_name'])) ){
                       //echo $result['product_name'];exit;
                      $products_name[] = $result['product_name'];
                  }
                  
               
            }
    }


    // if(!empty($error_data)){
    //     $error_data = implode(" . ",$error_data);
    //     $error = $error_data ; 
    // }
    
     if((!empty($products_name))){
                    $product_name_s = implode(" , ",$products_name);
                    $error = "product ".$product_name_s." already exist please use pid to update them";
     }
     if(!empty($error)){
       
        $this->session->set_flashdata('error', $error);
        redirect('product/mapping', true);

     }
     $this->session->set_flashdata('success', 'Product details have been updated successfully');
     redirect('product/mapping', true);
    }

    function apply_weight(){
        if(isset($_POST) && (!empty($_POST))){
            $product_details_id = isset($_POST['id'])?$_POST['id']:"";
            $weight_apply_status = isset($_POST['weight_apply_status'])?$_POST['weight_apply_status']:"";
            $data = $this->products_lib->getProducts($product_details_id,$this->user->account_id);
            if(!empty($data)){
              $weight_freze_status = isset($data->weight_locked)?$data->weight_locked:"";
              if($weight_freze_status==2){
               $save['is_weight'] = $weight_apply_status;
               $this->products_lib->update($product_details_id,$save);
               $this->data['json'] = array('success' => 'Record Updated');
               $this->layout(false, 'json');
              }
            }
            $this->data['json'] = array('success' => 'Record is not Updated please check details');
            $this->layout(false, 'json');
        }
       
    }

    
}
