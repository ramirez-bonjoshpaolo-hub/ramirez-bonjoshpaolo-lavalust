<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('session');
    }

    private function student_data()
    {
        return [
            'student_id' => getenv('STUDENT_ID') ?: 'MCC2023-1110',
            'name' => getenv('STUDENT_NAME') ?: 'Bon Josh Paolo D. Ramirez',
            'course' => getenv('STUDENT_COURSE') ?: 'BS Information Technology',
            'year_level' => getenv('STUDENT_YEAR_LEVEL') ?: '3rd Year',
            'section' => getenv('STUDENT_SECTION') ?: '3F2',
            'email' => getenv('STUDENT_EMAIL') ?: 'ramirezbonjoshpaolo00@gmail.com',
        ];
    }

    public function index()
    {
        $this->session->set_userdata('student_access', true);

        $this->call->view('student/index', [
            'student' => $this->student_data(),
            'title' => 'Ramirez Field Notes',
            'notice' => $this->session->flashdata('student_access_message'),
        ]);
    }

    public function profile()
    {
        $this->call->view('student/profile', [
            'student' => $this->student_data(),
            'title' => 'Bon Josh Paolo D. Ramirez | Student Profile',
        ]);
    }
}
