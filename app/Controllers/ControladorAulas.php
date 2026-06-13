<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AulaModelo;
class ControladorAulas extends BaseController
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
    $modelo = new AulaModelo();

        $buscar = trim($this->request->getGet('buscar'));

        if ($buscar !== '') {
            $data['aulas'] = $modelo->buscarAulas($buscar);
        } else {
            $data['aulas'] = $modelo->orderBy('id_aula', 'ASC')->findAll();
        }

        return view('aulas/index', $data);
    }

    public function insertar()
    {
        $modelo = new AulaModelo();

        $modelo->insert([
            'nombre_aula' => trim($this->request->getPost('nombre_aula')),
            'capacidad'   => $this->request->getPost('capacidad'),
            'estado'      => 1
        ]);

        return redirect()->to(base_url('aulas'));
    }

    public function actualizar($id)
    {
        $modelo = new AulaModelo();

        $modelo->update($id, [
            'nombre_aula'         => trim($this->request->getPost('nombre_aula')),
            'capacidad'           => $this->request->getPost('capacidad'),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('aulas'));
    }

    public function activar($id)
    {
        $modelo = new AulaModelo();

        $modelo->update($id, ['estado' => 1]);

        return redirect()->to(base_url('aulas'))
                         ->with('success', 'Aula activada correctamente.');
    }

    public function desactivar($id)
    {
        $modelo = new AulaModelo();

        $modelo->update($id, ['estado' => 0]);

        return redirect()->to(base_url('aulas'))
                         ->with('success', 'Aula desactivada correctamente.');
    }
}
