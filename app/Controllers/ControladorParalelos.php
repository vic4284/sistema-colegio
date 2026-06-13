<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ParaleloModelo;
class ControladorParalelos extends BaseController
{
      public function index()
    {
        $modelo = new ParaleloModelo();
        $db = \Config\Database::connect();

        $buscar = trim($this->request->getGet('buscar'));

        if ($buscar !== '') {
            $data['paralelos'] = $modelo->buscarParalelos($buscar);
        } else {
            $data['paralelos'] = $modelo->listarParalelos();
        }

        $data['grados'] = $db->table('grados g')
            ->select('g.id_grado, g.nombre_grado, n.nombre_nivel')
            ->join('niveles n', 'n.id_nivel = g.id_nivel')
            ->where('g.estado', 1)
            ->where('n.estado', 1)
            ->orderBy('n.id_nivel', 'ASC')
            ->orderBy('g.id_grado', 'ASC')
            ->get()
            ->getResultArray();

        $data['secciones'] = $db->table('secciones')
            ->where('estado', 1)
            ->get()
            ->getResultArray();

        return view('paralelos/index', $data);
    }

    public function insertar()
    {
        $modelo = new ParaleloModelo();

        $modelo->insert([
            'id_grado'   => $this->request->getPost('id_grado'),
            'id_seccion' => $this->request->getPost('id_seccion'),
            'estado'     => 1
        ]);

        return redirect()->to(base_url('paralelos'));
    }

    public function actualizar($id)
    {
        $modelo = new ParaleloModelo();

        $modelo->update($id, [
            'id_grado'            => $this->request->getPost('id_grado'),
            'id_seccion'          => $this->request->getPost('id_seccion'),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('paralelos'));
    }

    public function activar($id)
    {
        $modelo = new ParaleloModelo();

        $modelo->update($id, [
            'estado' => 1
        ]);

        return redirect()->to(base_url('paralelos'))
                         ->with('success', 'Paralelo activado correctamente.');
    }

    public function desactivar($id)
    {
        $modelo = new ParaleloModelo();

        $modelo->update($id, [
            'estado' => 0
        ]);

        return redirect()->to(base_url('paralelos'))
                         ->with('success', 'Paralelo desactivado correctamente.');
    }
}
