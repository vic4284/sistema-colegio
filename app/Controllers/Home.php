<?php

namespace App\Controllers;

class Home extends BaseController
{
      public function index()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        $data = [
            'usuario' => session('nombre_usuario'),
            'rol'     => session('rol')
        ];

        return view('dashboard/index', $data);
    }
}
