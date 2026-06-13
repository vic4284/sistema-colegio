<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AdministrativoModelo;

class ControladorAdministrativos extends BaseController
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
    
    $modelo = new AdministrativoModelo();

$buscar = trim($this->request->getGet('buscar'));

if ($buscar !== '') {
    $data['administrativos'] = $modelo->buscarAdministrativos($buscar);
} else {
    $data['administrativos'] = $modelo->findAll();
}
        return view('administrativos/index', $data);
    }

    public function insertar()
{
    $modelo = new AdministrativoModelo();

    $nombres   = trim($this->request->getPost('nombres'));
    $apellidos = trim($this->request->getPost('apellidos'));
    $correo    = trim($this->request->getPost('correo'));

    if ($modelo->existeCorreo($correo)) {
        return redirect()->to(base_url('/administrativos'))
                         ->with('error', 'Ya existe un administrativo registrado con ese correo.');
    }

    if ($modelo->existeNombreCompleto($nombres, $apellidos)) {
        return redirect()->to(base_url('/administrativos'))
                         ->with('error', 'Ya existe un administrativo registrado con ese nombre completo.');
    }

    $modelo->insert([
        'id_usuario' => null,
        'nombres'    => $nombres,
        'apellidos'  => $apellidos,
        'telefono'   => trim($this->request->getPost('telefono')),
        'correo'     => $correo,
        'cargo'      => trim($this->request->getPost('cargo')),
        'estado'     => 1
    ]);

    return redirect()->to(base_url('/administrativos'))
                     ->with('success', 'Administrativo registrado correctamente.');
}

    public function actualizar($id)
{
    $modelo = new AdministrativoModelo();

    $nombres   = trim($this->request->getPost('nombres'));
    $apellidos = trim($this->request->getPost('apellidos'));
    $correo    = trim($this->request->getPost('correo'));

    if ($modelo->existeCorreo($correo, $id)) {
        return redirect()->to(base_url('/administrativos'))
                         ->with('error', 'Ya existe otro administrativo registrado con ese correo.');
    }

    if ($modelo->existeNombreCompleto($nombres, $apellidos, $id)) {
        return redirect()->to(base_url('/administrativos'))
                         ->with('error', 'Ya existe otro administrativo registrado con ese nombre completo.');
    }

    $modelo->update($id, [
        'nombres'    => $nombres,
        'apellidos'  => $apellidos,
        'telefono'   => trim($this->request->getPost('telefono')),
        'correo'     => $correo,
        'cargo'      => trim($this->request->getPost('cargo')),
        'bloqueado_activacion'  => $this->request->getPost('bloqueado_activacion') ? 0 : $this->request->getPost('bloqueado_actual')
    ]);

    return redirect()->to(base_url('/administrativos'))
                     ->with('success', 'Administrativo actualizado correctamente.');
}

   public function activar($id)
{
    $modelo = new AdministrativoModelo();

    $actualizado = $modelo->cambiarEstadoAdministrativoYUsuario($id, 1);

    if (!$actualizado) {
        return redirect()->to(base_url('/administrativos'))
                         ->with('error', 'Administrativo no encontrado.');
    }

    return redirect()->to(base_url('/administrativos'))
                     ->with('success', 'Administrativo activado correctamente.');
}

    public function desactivar($id)
{
    $modelo = new AdministrativoModelo();

    $actualizado = $modelo->cambiarEstadoAdministrativoYUsuario($id, 0);

    if (!$actualizado) {
        return redirect()->to(base_url('/administrativos'))
                         ->with('error', 'Administrativo no encontrado.');
    }

    return redirect()->to(base_url('/administrativos'))
                     ->with('success', 'Administrativo desactivado correctamente.');
}
}
