<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Communication_pricing extends Admin_controller {

    public function index() {
        $this->layout('communication_pricing/index');
}
}