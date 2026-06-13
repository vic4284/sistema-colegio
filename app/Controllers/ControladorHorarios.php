<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\HorarioModelo;

class ControladorHorarios extends BaseController
{
     public function index()
    {
       if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (session()->get('rol') !== 'ADMINISTRATIVO') {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para acceder a este módulo.');
        }       
    $modelo = new HorarioModelo();

        $buscar = trim($this->request->getGet('buscar'));

        if ($buscar !== '') {
            $data['horarios'] = $modelo->buscarHorarios($buscar);
        } else {
            $data['horarios'] = $modelo->orderBy('id_horario', 'ASC')->findAll();
        }

        return view('horarios/index', $data);
    }

    public function insertar()
    {
        $modelo = new HorarioModelo();

        $modelo->insert([
            'dia'         => $this->request->getPost('dia'),
            'hora_inicio' => $this->request->getPost('hora_inicio'),
            'hora_fin'    => $this->request->getPost('hora_fin'),
            'estado'      => 1
        ]);

        return redirect()->to(base_url('horarios'));
    }

    public function actualizar($id)
    {
        $modelo = new HorarioModelo();

        $modelo->update($id, [
            'dia'                 => $this->request->getPost('dia'),
            'hora_inicio'         => $this->request->getPost('hora_inicio'),
            'hora_fin'            => $this->request->getPost('hora_fin'),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('horarios'));
    }

    public function activar($id)
    {
        $modelo = new HorarioModelo();

        $modelo->update($id, ['estado' => 1]);

        return redirect()->to(base_url('horarios'))
                         ->with('success', 'Horario activado correctamente.');
    }

    public function desactivar($id)
    {
        $modelo = new HorarioModelo();

        $modelo->update($id, ['estado' => 0]);

        return redirect()->to(base_url('horarios'))
                         ->with('success', 'Horario desactivado correctamente.');
    }
}
