<?php
class Csv_lib
{

    protected $row = array();

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    function add_row(array $row)
    {
        $this->row[] = $row;
    }

    function export_csv()
    {
        $filename =  date('Y-m-d-H-i-s') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        
        foreach ($this->row as $single_row) {
            fputcsv($file, $single_row);
        }
        
        // $contLength = ob_get_length();
        // header('Content-Length: ' . $contLength);
        fclose($file);
        exit;
    }
    function export_csv_bulk_product_sku_mapp()
    {
        $filename =  date('Y-m-d-H-i-s') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        
        foreach ($this->row as $single_row) {
            fputcsv($file, $single_row);
        }
        fclose($file);
     
    }
}
