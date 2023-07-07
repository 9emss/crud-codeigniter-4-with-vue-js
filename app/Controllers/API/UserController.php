<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class UserController extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        //
        return $this->respond([
            'statusCode' => 200,
            'message'   => 'OK',
            'data' => auth()->user()
        ]);
    }
}
