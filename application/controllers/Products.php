<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Products extends User_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('products_lib');
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

        $total_row = $this->products_lib->countByProductUserID($this->user->account_id, $apply_filters);
        $config = array(
            'base_url' => base_url('products/all'),
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
        $orders = $this->products_lib->fetchByProductUserID($this->user->account_id, $limit, $offset, $apply_filters);
        $status_orders = array();
        $this->data['orders'] = $orders;
        $this->data['count_by_status'] = $status_orders;
        $this->layout('products/index');
    }

    function productUpdate()
    {
        $this->load->library('form_validation');
        $length = $this->input->post('length');
        $breadth = $this->input->post('breadth');
        $height = $this->input->post('height');
        $weight = $this->input->post('weight');
        $igst = $this->input->post('igst');
        $hsn = $this->input->post('hsn');
        $required_if =  $required_ifs = '';
        if (!empty($length) || !empty($breadth) || !empty($height)  || !empty($weight)) {

            $required_if = '|required';
        }
        if (!empty($igst) || !empty($hsn)) {

            $required_ifs = '|required';
        }

        $this->form_validation->set_rules('product_name', 'Product SKU', 'trim|required');
        $this->form_validation->set_rules('product_sku', 'Product SKU', 'trim|required');
        $this->form_validation->set_rules('length', 'Length', 'trim' . $required_if . '|integer|greater_than[0]');
        $this->form_validation->set_rules('breadth', 'Breadth', 'trim' . $required_if . '|integer|greater_than[0]');
        $this->form_validation->set_rules('height', 'Height', 'trim' . $required_if . '|integer|greater_than[0]');
        $this->form_validation->set_rules('weight', 'Weight', 'trim' . $required_if . '|integer|greater_than[0]');
        $this->form_validation->set_rules('igst', 'Igst', 'trim' . $required_ifs . '|numeric|greater_than[0]|min_length[1]|max_length[20]');
        $this->form_validation->set_rules('hsn', 'Hsn', 'trim' . $required_ifs . '|numeric|greater_than[0]|min_length[1]|max_length[20]');
        if ($this->form_validation->run()) {
            $save = array(
                'user_id' => $this->user->account_id,
                'product_name' => $this->input->post('product_name'),
                'product_sku' => $this->input->post('product_sku'),
                'length' => $length,
                'breadth' => $breadth,
                'height' => $height,
                'weight' => $weight,
                'igst' => $igst,
                'hsn_code' => $hsn,
                'product_id' => $this->input->post('product_id'),
            );
           // pr($save); die;
            $products = $this->products_lib->productInsert($save);
            if ($products == 1) {
                $this->data['json'] = array('success' => 'Record has been successfully updated');
            } else {
                $this->data['json'] = array('success' => 'Record has been successfully saved');
            }
        } else {
            $errors = validation_errors();
            $errors = str_ireplace('<p>', '', $errors);
            $errors = str_ireplace('</p>', '', $errors);

            $this->data['json'] = array('error' => $errors);
        }
        $this->layout(false, 'json');
    }

    function import()
    {
        $logdata=[];
        $logdata1=[];
           if (is_uploaded_file($_FILES['importFile']['tmp_name'])) {
                // Load CSV reader library
                $this->load->library('csvreader');
                // Parse data from CSV file
                $csvData = $this->csvreader->parse_csv($_FILES['importFile']['tmp_name']);
                if (empty($csvData)) {
                    $this->session->set_flashdata('error', 'Blank CSV File');
                    redirect('products/all', true);
                }

                $user_id=$this->user->account_id;
                $import_message_array = array();
                $this->load->library('form_validation');
                foreach ($csvData as $row_key => $row) {
                    $str = array_change_key_case($row);
                //  echo "<pre>";print_r($str); die;
                    if (!array_key_exists("product_name",$str))
                    {
                      
                        $this->session->set_flashdata('error', 'Product_Name header is missing.');
                        redirect('products/all', true);
                    }
                  
                    if (!array_key_exists("product_sku",$str))
                    {
                        $this->session->set_flashdata('error', 'Product_SKU header is missing.');
                        redirect('products/all', true);
                    }
                    if (!array_key_exists("length",$str))
                    {
                       
                        $this->session->set_flashdata('error', 'Lenght header is missing.');
                        redirect('products/all', true);
                    }
                    if (!array_key_exists("breadth",$str))
                    {
                        
                        $this->session->set_flashdata('error', 'Breadth  header is missing.');
                         redirect('products/all', true);
                    }
                    if (!array_key_exists("height",$str))
                    {
                       
                        $this->session->set_flashdata('error', 'Height header is missing.');
                        redirect('products/all', true);
                    }
                    if (!array_key_exists("weight",$str))
                    {
                       
                        $this->session->set_flashdata('error', 'Weight header is missing.');
                        redirect('products/all', true);
                    }

                    if (!array_key_exists("gst",$str))
                    {
                        
                        $this->session->set_flashdata('error', 'GST header is missing.');
                          redirect('products/all', true);
                    }

                    if (!array_key_exists("hsn_code",$str))
                    {
                       
                        $this->session->set_flashdata('error', 'HSN Code is missing.');
                        redirect('products/all', true);
                    }
                    if (array_key_exists("status",$str))
                    {
                       
                        $this->session->set_flashdata('error', 'Unknown cloumn Status.');
                        redirect('products/all', true);
                    }


                    $this->form_validation->set_data($str);

                    $this->load->library('form_validation');
                    $product_name = $str['product_name'];
                    $product_sku = $str['product_sku'];
                    $length = $str['length'];
                    $breadth =$str['breadth'];
                    $height = $str['height'];
                    $weight = $str['weight'];
                    $igst = $str['gst'];
                    $hsn =$str['hsn_code'];
                    $required_if =  $required_ifs = '';
                    if (!empty($length) || !empty($breadth) || !empty($height)  || !empty($weight)) {

                        $required_if = '|required';
                    }
                    if (!empty($igst) || !empty($hsn)) {

                        $required_ifs = '|required';
                    }

                    $this->form_validation->set_rules('Product_Name', 'Product SKU', 'trim');
                    $this->form_validation->set_rules('Prduct_SKU', 'Product SKU', 'trim');
                
                    if ($this->form_validation->run()) {
                      
                       $products = $this->products_lib->getProductId($user_id,$str['product_name'],$str['product_sku']);
                   if(empty($products))
                   {
                         $save = array(
                                'user_id' => $this->user->account_id,
                                'product_name' => $str['product_name'],
                                'product_sku' => $str['product_sku'], //product_sku
                                'length' => $str['length'],
                                'breadth' =>$str['breadth'],
                                'height' => $str['height'],
                                'weight' => $str['weight'],
                                'igst' => $str['gst'],
                                'hsn_code' => $str['hsn_code'],
                                'product_id' => "",
                                
                            );

                            $logdata[]= array(
                                'user_id' => $this->user->account_id,
                                'product_name' => $str['product_name'],
                                'product_sku' => $str['product_sku'], //product_sku
                                'length' => $str['length'],
                                'breadth' =>$str['breadth'],
                                'height' => $str['height'],
                                'weight' => $str['weight'],
                                'gst' => $str['gst'],
                                'hsn_code' => $str['hsn_code'],
                                'status' => "Failed Sku not found",
                               
                            );
                            $products1 = $this->products_lib->productInsert($save);         
                     }
                     else
                      {
                      
                               $save = array(
                                'user_id' => $this->user->account_id,
                                'product_name' => $str['product_name'],
                                'product_sku' => $str['product_sku'], //product_sku
                                'length' => $str['length'],
                                'breadth' =>$str['breadth'],
                                'height' => $str['height'],
                                'weight' => $str['weight'],
                                'igst' => $str['gst'],
                                'hsn_code' => $str['hsn_code'],
                                'product_id' => $products[0]->prod_id,
                        );
                        $logdata[]= array(
                            'user_id' => $this->user->account_id,
                            'product_name' => $str['product_name'],
                            'product_sku' => $str['product_sku'], //product_sku
                            'length' => $str['length'],
                            'breadth' =>$str['breadth'],
                            'height' => $str['height'],
                            'weight' => $str['weight'],
                            'gst' => $str['gst'],
                            'hsn_code' => $str['hsn_code'],
                            'status' => "Success",
                        );
                        $products1 = $this->products_lib->productInsert($save);
                      } // end else 
                      

                    }
                    else {
                      
                        $errors = validation_errors();
                        $errors = str_ireplace('<p>', '', $errors);
                        $errors = str_ireplace('</p>', '', $errors);
                        $logdata1[] = array(
                            'user_id' => $this->user->account_id,
                            'product_name' => $str['product_name'],
                            'product_sku' => $str['product_sku'], //product_sku
                            'length' => $str['length'],
                            'breadth' =>$str['breadth'],
                            'height' => $str['height'],
                            'weight' => $str['weight'],
                            'gst' => $str['gst'],
                            'hsn_code' => $str['hsn_code'],
                            'status' => "Falure ".$errors,
                        );
                        
                    }
                }

                $resultarray=array_merge($logdata,$logdata1);
                $csv = new Csv_lib();
                $csv->add_row(array("Product_Name","Product_SKU","Length","Breadth","Height","Weight","GST","HSN_Code","Status"));
                    /**Start File export */
                    $req_row = array();
                    foreach ($resultarray as $row_data => $row) {
                       $req_row['Product_Name'] = $row['product_name'];
                       $req_row['Prduct_SKU'] =$row['product_sku'];
                       $req_row['Length'] = $row['length'];
                       $req_row['Breadth'] = $row['breadth'];
                       $req_row['Height'] =$row['height'];
                       $req_row['Weight'] = $row['weight'];
                       $req_row['GST'] = $row['gst'];
                       $req_row['HSN_Code'] =$row['hsn_code'];
                       $req_row['Status'] =$row['status'];
                       $csv->add_row($req_row);
                    }
                   
                    $csv->export_csv_bulk_product_sku_mapp();
                    $paths=base_url();
                  //  header("Location:all");
                  redirect($_SERVER['REQUEST_URI'], 'refresh'); 
                     if($csv->export_csv_bulk_product_sku_mapp()=='true') {
                    //  $this->session->set_flashdata('success', 'Product SKU Mapping Import is Completed. ');
                        // $paths=base_url();
                        header("Location:all ");
                     }
                   
                   //redirect('products/all', true);
            }
       
    }
}
