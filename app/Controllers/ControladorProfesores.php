<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProfesorModelo;
class ControladorProfesores extends BaseController
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
    $modelo = new ProfesorModelo();

$buscar = trim($this->request->getGet('buscar'));

if ($buscar !== '') {
    $data['profesores'] = $modelo->buscarProfesores($buscar);
} else {
    $data['profesores'] = $modelo->findAll();
}
        return view('profesores/index', $data);
    }

   public function insertar()
{
    $modelo = new ProfesorModelo();

    $nombres   = trim($this->request->getPost('nombres'));
    $apellidos = trim($this->request->getPost('apellidos'));
    $correo    = trim($this->request->getPost('correo'));

    if ($modelo->existeCorreo($correo)) {
        return redirect()->to(base_url('/profesores'))
                         ->with('error', 'Ya existe un profesor registrado con ese correo.');
    }

    if ($modelo->existeNombreCompleto($nombres, $apellidos)) {
        return redirect()->to(base_url('/profesores'))
                         ->with('error', 'Ya existe un profesor registrado con ese nombre completo.');
    }

    $modelo->insert([
        'id_usuario'    => null,
        'nombres'       => $nombres,
        'apellidos'     => $apellidos,
        'telefono'      => trim($this->request->getPost('telefono')),
        'correo'        => $correo,
        'especialidad'  => trim($this->request->getPost('especialidad')),
        'estado'        => 1
    ]);

    return redirect()->to(base_url('/profesores'))
                     ->with('success', 'Profesor registrado correctamente.');
}

    public function actualizar($id)
{
    $modelo = new ProfesorModelo();

    $nombres   = trim($this->request->getPost('nombres'));
    $apellidos = trim($this->request->getPost('apellidos'));
    $correo    = trim($this->request->getPost('correo'));

    if ($modelo->existeCorreo($correo, $id)) {
        return redirect()->to(base_url('/profesores'))
                         ->with('error', 'Ya existe otro profesor registrado con ese correo.');
    }

    if ($modelo->existeNombreCompleto($nombres, $apellidos, $id)) {
        return redirect()->to(base_url('/profesores'))
                         ->with('error', 'Ya existe otro profesor registrado con ese nombre completo.');
    }

    $modelo->update($id, [
        'nombres'       => $nombres,
        'apellidos'     => $apellidos,
        'telefono'      => trim($this->request->getPost('telefono')),
        'correo'        => $correo,
        'especialidad'  => trim($this->request->getPost('especialidad')),
        'bloqueado_activacion'  => $this->request->getPost('bloqueado_activacion') ? 0 : $this->request->getPost('bloqueado_actual')
    ]);

    return redirect()->to(base_url('/profesores'))
                     ->with('success', 'Profesor actualizado correctamente.');
}

    public function activar($id)
{
    $modelo = new ProfesorModelo();

    $actualizado = $modelo->cambiarEstadoProfesorYUsuario($id, 1);

    if (!$actualizado) {
        return redirect()->to(base_url('profesores'))
                         ->with('error', 'Profesor no encontrado.');
    }

    return redirect()->to(base_url('profesores'))
                     ->with('success', 'Profesor activado correctamente.');
}

    public function desactivar($id)
{
    $modelo = new ProfesorModelo();

    $actualizado = $modelo->cambiarEstadoProfesorYUsuario($id, 0);

    if (!$actualizado) {
        return redirect()->to(base_url('profesores'))
                         ->with('error', 'Profesor no encontrado.');
    }

    return redirect()->to(base_url('profesores'))
                     ->with('success', 'Profesor desactivado correctamente.');
}

}
