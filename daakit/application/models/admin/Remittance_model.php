<?php

class Remittance_model extends MY_model
{

    public function __construct()
    {
        parent::__construct();
        $this->receipts_table = 'cod_receipts';
        $this->table = 'remittance';
        $this->table_rem_det = 'remittance_detail';
        $this->slave = $this->load->database('slave', TRUE);
    }

    function saveCODUpload($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->receipts_table, $save);
        return $this->db->insert_id();
    }

    function getReceiptByID($id = false)
    {
        if (!$id)
            return false;

        $this->db->where('id', $id);
        $q = $this->db->get($this->receipts_table);
        return $q->row();
    }

    function codUploadHistory($limit = 50, $offset = 0, $filter = array())
    {

        $this->slave->select("cod_receipts.*, courier.name as courier_name");

        if (!empty($filter['courier_id'])) {
            $this->slave->where("courier_id", $filter['courier_id']);
        }

        if (!empty($filter['utr_no'])) {
            $this->slave->where_in("utr_number", $filter['utr_no']);
        }

        $this->slave->limit($limit);
        $this->slave->offset($offset);
        $this->slave->order_by('cod_receipts.id', 'desc');
        $this->slave->join('courier', 'courier.id = cod_receipts.courier_id');
        $q = $this->slave->get($this->receipts_table);
        return $q->result();
    }

    function countCodUploadHistory($filter = array())
    {

        $this->slave->select('count(*) as total');

        if (!empty($filter['courier_id'])) {
            $this->slave->where("courier_id", $filter['courier_id']);
        }

        if (!empty($filter['utr_no'])) {
            $this->slave->like("utr_number", $filter['utr_no']);
        }

        $this->slave->join('courier', 'courier.id = cod_receipts.courier_id');
        $q = $this->slave->get($this->receipts_table);
        return $q->row()->total;
    }

    function codUploadHistoryByUTR($courier_id = false, $utr = false)
    {
        if (!$courier_id || !$utr)
            return false;

        $this->db->limit('1');
        $this->db->order_by('id', 'desc');
        $this->db->where('courier_id', $courier_id);
        $this->db->where('utr_number', $utr);
        $q = $this->db->get($this->receipts_table);
        return $q->row();
    }

    function createRemittance($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table, $save);
        return $this->db->insert_id();
    }

    function createRemittanceDetail($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert($this->table_rem_det, $save);
        return $this->db->insert_id();
    }

    
    function remittanceHistory($limit = 50, $offset = 0, $filter = array())
    {
        if (!empty($filter['date_type_field']) && $filter['date_type_field'] == 'payment_date') {
            if (!empty($filter['start_date'])) {
                $this->db->where("remittance.payment_date >= '" . $filter['start_date'] . "'");
            }
            if (!empty($filter['end_date'])) {
                $this->db->where("remittance.payment_date <= '" . $filter['end_date'] . "'");
            }
        } else {
            if (!empty($filter['start_date'])) {
                $this->db->where("remittance.created >= '" . $filter['start_date'] . "'");
            }

            if (!empty($filter['end_date'])) {
                $this->db->where("remittance.created <= '" . $filter['end_date'] . "'");
            }
        }

        if (!empty($filter['remittance_id'])) {
            $this->db->where_in('remittance.id', $filter['remittance_id']);
        }

        if (!empty($filter['utr_no'])) {
            $this->db->where_in("utr_number", $filter['utr_no']);
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('remittance.user_id', $filter['seller_id']);
        }
        if (!empty($filter['created_by'])) {
            if ($filter['created_by'] == 'seller')
                $this->db->where('seller_created', '1');

            if ($filter['created_by'] == 'delta')
                $this->db->where('seller_created', '0');
        }

        if (!empty($filter['created_by_user'])) {
            $this->db->where('created_by', $filter['created_by_user']);
        }

        if (!empty($filter['paid_status'])) {
            if ($filter['paid_status'] == 'yes')
                $this->db->where('paid', '1');

            if ($filter['paid_status'] == 'no')
                $this->db->where('paid', '0');
        }

        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->select("remittance.*, users.fname as user_fname, users.lname as user_lname, users.company_name as user_company, users.is_postpaid, users.early_cod_charges, users.wallet_balance as wallet_balance, company_details.cmp_accntholder as account_name, company_details.cmp_accno as account_number, company_details.cmp_accifsc as ifsc_code, user_creater.fname as createdby_fname, user_creater.lname as createdby_lname");
        $this->db->order_by('remittance.id', 'desc');
        $this->db->join('users', 'users.id = remittance.user_id', 'LEFT');
        $this->db->join('users user_creater', 'user_creater.id = remittance.created_by', 'LEFT');
        $this->db->join('company_details', 'company_details.user_id = remittance.user_id', 'LEFT');
        $this->db->group_by('remittance.id');
        $q = $this->db->get($this->table);
        return $q->result();
    }

    function countRemittanceHistory($filter = array())
    {
        if (!empty($filter['date_type_field']) && $filter['date_type_field'] == 'payment_date') {
            if (!empty($filter['start_date'])) {
                $this->db->where("remittance.payment_date >= '" . $filter['start_date'] . "'");
            }
            if (!empty($filter['end_date'])) {
                $this->db->where("remittance.payment_date <= '" . $filter['end_date'] . "'");
            }
        } else {
            if (!empty($filter['start_date'])) {
                $this->db->where("remittance.created >= '" . $filter['start_date'] . "'");
            }

            if (!empty($filter['end_date'])) {
                $this->db->where("remittance.created <= '" . $filter['end_date'] . "'");
            }
        }

        if (!empty($filter['remittance_id'])) {
            $this->db->where_in('remittance.id', $filter['remittance_id']);
        }

        if (!empty($filter['utr_no'])) {
            $this->db->where_in("utr_number", $filter['utr_no']);
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('remittance.user_id', $filter['seller_id']);
        }

        if (!empty($filter['paid_status'])) {
            if ($filter['paid_status'] == 'yes')
                $this->db->where('paid', '1');

            if ($filter['paid_status'] == 'no')
                $this->db->where('paid', '0');
        }

        if (!empty($filter['created_by'])) {
            if ($filter['created_by'] == 'seller')
                $this->db->where('seller_created', '1');

            if ($filter['created_by'] == 'delta')
                $this->db->where('seller_created', '0');
        }

        if (!empty($filter['created_by_user'])) {
            $this->db->where('created_by', $filter['created_by_user']);
        }

        $this->db->select('count(*) as total');
        $this->db->order_by('remittance.id', 'desc');
        $this->db->join('users', 'users.id = remittance.user_id', 'LEFT');
        $q = $this->db->get($this->table);

        return $q->row()->total;
    }

    function update($id = false, $update = false)
    {
        if (!$id || !$update)
            return false;

        $this->db->where('id', $id);
        $this->db->set($update);
        $this->db->update($this->table);
        return true;
    }

    function remittanceMissingBankDetails()
    {

        $this->slave->select('users.id as user_id, users.fname as user_fname, users.lname as user_lname, users.company_name as user_company, users.phone as user_phone, users.wallet_balance as wallet_balance, '
            . "sum(case when (order_shipping.ship_status = 'delivered' and order_shipping.remittance_id = 0 and order_shipping.payment_type = 'COD') then order_shipping.order_total_amount else 0 end) as projected_remittance,");

        $this->slave->where('company_details.cmp_accno', '');

        //$this->slave->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');
        $this->slave->join('users', 'users.id = order_shipping.user_id', 'LEFT');
        $this->slave->join('company_details', 'company_details.user_id = order_shipping.user_id', 'LEFT');


        $this->slave->having('projected_remittance > ', '0');

        $this->slave->group_by('users.id');

        $this->slave->order_by('projected_remittance', 'desc');
        $q = $this->slave->get('order_shipping');

        return $q->result();
    }

    function bulkremittanceHistory($limit = 50, $offset = 0, $filter = array())
    {

        if (!empty($filter['date_type_field']) && $filter['date_type_field'] == 'payment_date') {
            if (!empty($filter['start_date'])) {
                $this->db->where("remittance.payment_date >= '" . $filter['start_date'] . "'");
            }
            if (!empty($filter['end_date'])) {
                $this->db->where("remittance.payment_date <= '" . $filter['end_date'] . "'");
            }
        } else {
            if (!empty($filter['start_date'])) {
                $this->db->where("remittance.created >= '" . $filter['start_date'] . "'");
            }

            if (!empty($filter['end_date'])) {
                $this->db->where("remittance.created <= '" . $filter['end_date'] . "'");
            }
        }

        if (!empty($filter['remittance_id'])) {
            $this->db->where_in('remittance.id', $filter['remittance_id']);
        }

        if (!empty($filter['utr_no'])) {
            $this->db->where_in("utr_number", $filter['utr_no']);
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where('remittance.user_id', $filter['seller_id']);
        }
        if (!empty($filter['created_by'])) {
            if ($filter['created_by'] == 'seller')
                $this->db->where('seller_created', '1');

            if ($filter['created_by'] == 'delta')
                $this->db->where('seller_created', '0');
        }

        if (!empty($filter['created_by_user'])) {
            $this->db->where('created_by', $filter['created_by_user']);
        }

        if (!empty($filter['paid_status'])) {
            if ($filter['paid_status'] == 'yes')
                $this->db->where('paid', '1');

            if ($filter['paid_status'] == 'no')
                $this->db->where('paid', '0');
        }

        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->select("remittance.*, users.id as seller_id, users.fname as user_fname, users.lname as user_lname, users.company_name as user_company,users.email as seller_email,users.phone as seller_phone,courier.name as courier,order_shipping.awb_number as awb_number, order_shipping.created  as shipment_date, order_shipping.delivered_time as delivered_date,orders.order_amount as amount, order_shipping.ship_status as shipment_status, users.remittance_cycle as remittance_cycle, orders.order_payment_type as order_type,orders.order_no,order_shipping.id as shipment_id");
        $this->db->order_by('remittance.id', 'desc');
        $this->db->join('users', 'users.id = remittance.user_id', 'LEFT');
        $this->db->join('order_shipping', 'order_shipping.remittance_id = remittance.id', 'LEFT');
        $this->db->join('courier', 'courier.id = order_shipping.courier_id', 'LEFT');
        $this->db->join('orders', 'orders.id = order_shipping.order_id', 'LEFT');
        $this->db->from($this->table);
        return $query =   $this->db->get_compiled_select();
    }

    function getdisctinctremmitance()
    {
        $this->db->select('DISTINCT(remittance_cycle)');
        $this->db->order_by('remittance_cycle','asc');
        $q = $this->db->get('users');
        return $q->result();
    }

    function remittanceDetailByAwbNumber($awb = false)
    {
        if (!$awb)
            return false;

        $this->db->where('awb_number', $awb);
        $this->db->select("*");
        $q = $this->db->get($this->table_rem_det);
        return $q->result();
    }

    function createDebitAwbNo($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();
        $existStatus=$this->checkAlreadyDebitAwb($save['awb_number']);
        if($existStatus->total_count > '0')
        {
            $this->db->where(['awb_number'=>$save['awb_number'],'deduct'=>'N']);
            $this->db->set(['amount'=>$save['amount'],'created_by'=>$save['created_by']]);
            $this->db->update($this->table_rem_debit);
            if ($this->db->affected_rows() > 0)
                return '2';
            else
                return '0';
        }
        else{
            $this->db->insert($this->table_rem_debit, $save);
            $insert_id=$this->db->insert_id();
            return '1'; 
        }
    }

    function checkAlreadyDebitAwb($awb_no)
    {
        if (empty($awb_no))
            return false;

        $this->db->where('awb_number', $awb_no);
        $this->db->select('count(id) as total_count');
        $q = $this->db->get($this->table_rem_debit);
        return $q->row();
    }

    function deleteTmpAwb($userId)
    {
        if (empty($userId))
            return false;

        $this->db->where('created_by', $userId);
        $this->db->delete('awb_operation_tmp');
        return true;
    }

    function createMisAwbVerify($save = array())
    {
        if (empty($save))
            return false;

        $save['created'] = time();

        $this->db->insert('awb_operation_tmp', $save);
        return $this->db->insert_id();
    }

    function getUploadAwbDetails($filter = array())
    {
        if (!empty($filter['status'])) {
            $this->slave->where_in('status', $filter['status']);
        }
        if (!empty($filter['user_id'])) {
            $this->slave->where('created_by', $filter['user_id']);
        }
        if (!empty($filter['awb_number'])) {
            $this->slave->where('awb_number', $filter['awb_number']);
        }
        $this->slave->select('shipping_id,courier_id,awb_number,shipping_status,csv_status,remark,status,created_by');
        $q = $this->slave->get('awb_operation_tmp');
        return $q->result();
    }
    function getDebitRemittanceDetsByAwbSellerId($limit = 50, $offset = 0, $filter = array())
    {
        if (!empty($filter['awb_nos'])) {
            $this->slave->where_in('remittance_debit.awb_number', $filter['awb_nos']);
        }
        if (!empty($filter['seller_ids'])) {
            $this->slave->where_in('remittance_debit.user_id', $filter['seller_ids']);
        }
        $this->slave->limit($limit);
        $this->slave->offset($offset);
        $this->slave->select('remittance_debit.user_id,remittance_debit.remittance_id,remittance_debit.awb_number,remittance_debit.amount,remittance_debit.deduct,remittance_debit.created,CONCAT_WS(" ",sales_user.fname,sales_user.lname) as user,sales_user.company_name,CONCAT_WS(" ",debit_user.fname,debit_user.lname) as created_by');
        $this->slave->join('users sales_user', 'remittance_debit.user_id = sales_user.id');
        $this->slave->join('users debit_user', 'remittance_debit.created_by = debit_user.id');
        $this->slave->order_by('user', 'asc');
        $this->slave->order_by('deduct', 'desc');
        $q = $this->slave->get($this->table_rem_debit);
        return $q->result();
    }
    function countDebitRemittance($filter = array())
    {
        if (!empty($filter['awb_nos'])) {
            $this->slave->where_in('remittance_debit.awb_number', $filter['awb_nos']);
        }
        if (!empty($filter['seller_ids'])) {
            $this->slave->where_in('remittance_debit.user_id', $filter['seller_ids']);
        }

        $this->slave->select('count(*) as total');
        $q = $this->slave->get($this->table_rem_debit);
        return $q->row()->total;
    }

    
    function countByUserID($user_id = false, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select('count(DISTINCT awb_operation_verify.shipping_id) as total');

        if (!empty($filter['start_date'])) {
            $this->db->where("awb_operation_verify.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("awb_operation_verify.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('awb_operation_verify.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('awb_operation_verify.awb_number', $filter['awb_no']);
        }

        if (!empty($filter['seller_id'])) {
            $this->db->where_in('users.id', $filter['seller_id']);
        }

        $this->db->join('order_shipping', 'awb_operation_verify.shipping_id=order_shipping.id');
        $this->db->join('users', 'order_shipping.user_id=users.id');
        $this->db->join('courier', 'awb_operation_verify.courier_id=courier.id');
        $this->db->order_by('awb_operation_verify.created', 'desc');
        $q = $this->db->get('awb_operation_verify ');
        
        return $q->row()->total;
    }

    
    function getByUserID($user_id = false, $limit = 50, $offset = 0, $filter = array())
    {
        if (!$user_id)
            return false;

        $this->db->select("awb_operation_verify.id,courier.name as courier_name ,awb_operation_verify.shipping_id,users.fname,users.lname ,awb_operation_verify.awb_number,users.id as user_id,users.company_name");

        if (!empty($filter['start_date'])) {
            $this->db->where("awb_operation_verify.created >= '" . $filter['start_date'] . "'");
        }

        if (!empty($filter['end_date'])) {
            $this->db->where("awb_operation_verify.created <= '" . $filter['end_date'] . "'");
        }

        if (!empty($filter['courier_id'])) {
            $this->db->where('awb_operation_verify.courier_id', $filter['courier_id']);
        }

        if (!empty($filter['awb_no'])) {
            $this->db->where_in('awb_operation_verify.awb_number', $filter['awb_no']);
        }
        if (!empty($filter['seller_id'])) {
            $this->db->where_in('users.id', $filter['seller_id']);
        }


        $this->db->limit($limit);
        $this->db->offset($offset);
        //$this->db->where('order_shipping.user_id', $user_id);
        $this->db->join('order_shipping', 'awb_operation_verify.shipping_id=order_shipping.id');
        $this->db->join('users', 'order_shipping.user_id=users.id');
        $this->db->join('courier', 'awb_operation_verify.courier_id=courier.id');
        $this->db->order_by('awb_operation_verify.created', 'desc');

        $q = $this->db->get('awb_operation_verify');
        if ($q->result())
            return $q->result();
        else
            return false;
    }


}
