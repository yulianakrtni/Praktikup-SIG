<?php

namespace App\Controllers;

use App\Models\UserModel;

class Home extends BaseController
{
    public function index(): string
    {
        return view('login');
    }

    public function map(): string
    {
        return view('dashboard');
    }

    public function auth()
{
    $session = session();
    $model = new UserModel();
    $username = $this->request->getVar('username');
    $password = $this->request->getVar('password');
    $data = $model->where('username', $username)->first();
    
    if ($data) {
        // Periksa password tanpa hash
        if ($password === $data['password']) {
            // Jika verifikasi sukses, simpan data sesi
            $ses_data = [
                'id_user'    => $data['id_user'],
                'username'   => $data['username'],
                'nama'       => $data['nama'],
                'logged_in'  => TRUE
            ];
            $session->set($ses_data);
            return redirect()->to('/'); // Redirect ke halaman peta
        } else {
            $session->setFlashdata('msg', 'Password Salah');
            return redirect()->to('/map'); // Redirect ke halaman login
        }
    } else {
        $session->setFlashdata('msg', 'Username Tidak Ditemukan');
        return redirect()->to('/map'); // Redirect ke halaman login
    }
}

    public function logout()
    {
        $session = session();
        $session->destroy(); // Hancurkan sesi
        return redirect()->to('/'); // Redirect ke halaman login
    }
}
