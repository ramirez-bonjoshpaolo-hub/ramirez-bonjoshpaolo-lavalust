<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UsersController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->call->database();
        $this->UsersModel = $this->call->model('UsersModel');
    }

    public function index()
    {
        $this->call->view('users', [
            'page_title' => 'User Management',
            'users'      => $this->UsersModel->all(),
        ]);
    }
}
