<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\HorarioProfesorModelo;
class ControladorHorariosProfesor extends BaseController
{
   public function index()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (session()->get('rol') !== 'PROFESOR') {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para acceder a este módulo.');
        }
    $modelo = new HorarioProfesorModelo();

        $idUsuario = session()->get('id_usuario');
        $buscar = trim($this->request->getGet('buscar'));

        if ($buscar !== '') {
            $data['horarios'] = $modelo->buscarHorariosProfesor($idUsuario, $buscar);
        } else {
            $data['horarios'] = $modelo->listarHorariosProfesor($idUsuario);
        }

        return view('horarios_profesor/index', $data);
    }
}
