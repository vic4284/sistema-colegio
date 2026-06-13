<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MateriaModelo;
class ControladorMaterias extends BaseController
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
    $modelo = new MateriaModelo();

        $data['materias'] = $modelo->orderBy('id_materia', 'ASC')->findAll();

        return view('materias/index', $data);
    }

    public function insertar()
    {
        $modelo = new MateriaModelo();

        $modelo->insert([
            'nombre_materia' => $this->request->getPost('nombre_materia'),
            'descripcion'    => $this->request->getPost('descripcion'),
            'estado'         => 1
        ]);

        return redirect()->to(base_url('/materias'))->with('success', 'Materia registrada correctamente.');
    }

    public function actualizar($id)
    {
        $modelo = new MateriaModelo();

        $modelo->update($id, [
            'nombre_materia'      => $this->request->getPost('nombre_materia'),
            'descripcion'         => $this->request->getPost('descripcion'),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/materias'))->with('success', 'Materia actualizada correctamente.');
    }

    public function activar($id)
    {
        $modelo = new MateriaModelo();
        $modelo->update($id, ['estado' => 1]);

        return redirect()->to(base_url('/materias'))->with('success', 'Materia activada correctamente.');
    }

    public function desactivar($id)
    {
        $modelo = new MateriaModelo();
        $modelo->update($id, ['estado' => 0]);

        return redirect()->to(base_url('/materias'))->with('success', 'Materia desactivada correctamente.');
    }
}
