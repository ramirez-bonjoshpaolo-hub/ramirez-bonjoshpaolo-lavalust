<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UsersController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('UsersModel');
    }

    public function index()
    {
        $users = $this->UsersModel->all();

        foreach ($users as &$user) {
            if (isset($user['email'])) {
                $user['email'] = preg_replace('/@example\.com$/i', '@gmail.com', $user['email']);
            }
        }
        unset($user);

        $this->call->view('users/index', [
            'title' => 'Community Directory',
            'users' => $users,
        ]);
    }
}
