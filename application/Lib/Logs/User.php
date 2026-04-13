<?php

namespace App\Lib\Logs;

use App\Lib\Logs\Log;

class User extends Log
{
    public function __construct()
    {
        parent::__construct();
        $this->entity_type = 'user';
    }
}
