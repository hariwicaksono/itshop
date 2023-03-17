<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
	public function login()
	{
		if ($this->session->logged_in == true && $this->session->role == 1) {return redirect()->to('/admin');} 
		if ($this->session->logged_in == true && $this->session->role == 2) {return redirect()->to('/');}
		
		return view('login', [
			'title' => 'Login'
		]);
	}

	public function register()
	{
		if ($this->session->logged_in == true && $this->session->role == 1) {return redirect()->to('/admin');} 
		if ($this->session->logged_in == true && $this->session->role == 2) {return redirect()->to('/');}

		return view('register', [
			'title' => 'Register'
		]);
	}

	public function verifyEmail()
	{
		$input = $this->request->getVar();

		$rules = [
			'email' => [
				'rules'  => 'required',
				'errors' => []
			],
			'token' => [
				'rules'  => 'required',
				'errors' => []
			],
		];

		if (!$this->validate($rules)) {
			return redirect()->to(base_url());
		}

		$user_model = new UserModel();
		$user = $user_model->where(['email' => $input['email'], 'token' => $input['token']])->first();
		$user_data = [
			'active' => 1,
		];
		$user_model->update($user['user_id'], $user_data);
		return redirect()->to(base_url());
	}

	public function passwordReset()
    {
        if (isset($this->session->username)) return redirect()->to(base_url('dashboard'));
        return view('password/reset');
    }

	public function passwordChange()
    {
        if (isset($this->session->username)) return redirect()->to(base_url('dashboard'));
        $rules = [
            'email' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'token' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];
        if (!$this->validate($rules)) {
            return redirect()->to(base_url());
        }
        $data = $this->request->getVar();
        return view('password/change', $data);
    }

	public function logout()
	{
		$this->session->destroy();
		$this->session->setFlashdata('success', 'Berhasil Logout');
		return redirect()->to('/login');
	}
}
