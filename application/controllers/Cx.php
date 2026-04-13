<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cx extends User_controller {
    public function index() {
        /* $this->load->view('cx/index'); */  // loads the new cx view
        $this->layout('cx/index');  // loads the new cx view
}
}