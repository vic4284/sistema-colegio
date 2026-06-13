<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\EstudianteModelo;
class ControladorEstudiantes extends BaseController
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
    
    $modelo = new EstudianteModelo();

$buscar = trim($this->request->getGet('buscar'));

if ($buscar !== '') {
    $data['estudiantes'] = $modelo->buscarEstudiantes($buscar);
} else {
    $data['estudiantes'] = $modelo->findAll();
}
        return view('estudiantes/index', $data);
    }

   public function insertar()
{
    $modelo = new EstudianteModelo();

    $nombres   = trim($this->request->getPost('nombres'));
    $apellidos = trim($this->request->getPost('apellidos'));
    $correo    = trim($this->request->getPost('correo'));

    if ($modelo->existeCorreo($correo)) {
        return redirect()->to(base_url('/estudiantes'))
                         ->with('error', 'Ya existe un estudiante registrado con ese correo.');
    }

    if ($modelo->existeNombreCompleto($nombres, $apellidos)) {
        return redirect()->to(base_url('/estudiantes'))
                         ->with('error', 'Ya existe un estudiante registrado con ese nombre completo.');
    }

    $modelo->insert([
        'id_usuario' => null,
        'nombres'    => $nombres,
        'apellidos'  => $apellidos,
        'telefono'   => trim($this->request->getPost('telefono')),
        'correo'     => $correo,
        'direccion'  => trim($this->request->getPost('direccion')),
        'estado'     => 1
    ]);

    return redirect()->to(base_url('/estudiantes'))
                     ->with('success', 'Estudiante registrado correctamente.');
}

   public function actualizar($id)
{
    $modelo = new EstudianteModelo();

    $nombres   = trim($this->request->getPost('nombres'));
    $apellidos = trim($this->request->getPost('apellidos'));
    $correo    = trim($this->request->getPost('correo'));

    if ($modelo->existeCorreo($correo, $id)) {
        return redirect()->to(base_url('/estudiantes'))
                         ->with('error', 'Ya existe otro estudiante registrado con ese correo.');
    }

    if ($modelo->existeNombreCompleto($nombres, $apellidos, $id)) {
        return redirect()->to(base_url('/estudiantes'))
                         ->with('error', 'Ya existe otro estudiante registrado con ese nombre completo.');
    }

    $modelo->update($id, [
        'nombres'    => $nombres,
        'apellidos'  => $apellidos,
        'telefono'   => trim($this->request->getPost('telefono')),
        'correo'     => $correo,
        'direccion'  => trim($this->request->getPost('direccion')),
        'bloqueado_activacion'  => $this->request->getPost('bloqueado_activacion') ? 0 : $this->request->getPost('bloqueado_actual')
    ]);

    return redirect()->to(base_url('/estudiantes'))
                     ->with('success', 'Estudiante actualizado correctamente.');
}

    public function activar($id)
{
    $modelo = new EstudianteModelo();

    $actualizado = $modelo->cambiarEstadoEstudianteYUsuario($id, 1);

    if (!$actualizado) {
        return redirect()->to(base_url('/estudiantes'))
                         ->with('error', 'Estudiante no encontrado.');
    }

    return redirect()->to(base_url('/estudiantes'))
                     ->with('success', 'Estudiante activado correctamente.');
}

    public function desactivar($id)
{
    $modelo = new EstudianteModelo();

    $actualizado = $modelo->cambiarEstadoEstudianteYUsuario($id, 0);

    if (!$actualizado) {
        return redirect()->to(base_url('/estudiantes'))
                         ->with('error', 'Estudiante no encontrado.');
    }

    return redirect()->to(base_url('/estudiantes'))
                     ->with('success', 'Estudiante desactivado correctamente.');
}
}
