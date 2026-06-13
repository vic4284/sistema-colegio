<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SeccionModelo;
class ControladorSecciones extends BaseController
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
    $modelo = new SeccionModelo();

        $buscar = trim($this->request->getGet('buscar'));

        if ($buscar !== '') {
            $data['secciones'] = $modelo->buscarSecciones($buscar);
        } else {
            $data['secciones'] = $modelo->orderBy('id_seccion', 'ASC')->findAll();
        }

        return view('secciones/index', $data);
    }

    public function insertar()
    {
        $modelo = new SeccionModelo();

        $modelo->insert([
            'nombre_seccion' => strtoupper(trim($this->request->getPost('nombre_seccion'))),
            'estado'         => 1
        ]);

        return redirect()->to(base_url('secciones'));
    }

    public function actualizar($id)
    {
        $modelo = new SeccionModelo();

        $modelo->update($id, [
            'nombre_seccion'      => strtoupper(trim($this->request->getPost('nombre_seccion'))),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('secciones'));
    }

    public function activar($id)
    {
        $modelo = new SeccionModelo();

        $modelo->update($id, [
            'estado' => 1
        ]);

        return redirect()->to(base_url('secciones'))
                         ->with('success', 'Sección activada correctamente.');
    }

    public function desactivar($id)
    {
        $modelo = new SeccionModelo();

        $modelo->update($id, [
            'estado' => 0
        ]);

        return redirect()->to(base_url('secciones'))
                         ->with('success', 'Sección desactivada correctamente.');
    }
}
