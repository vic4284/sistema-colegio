<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PsicologoModelo;
    
class ControladorPsicologos extends BaseController
{
    public function index()
    {
        $modelo = new PsicologoModelo();

$buscar = trim($this->request->getGet('buscar'));

if ($buscar !== '') {
    $data['psicologos'] = $modelo->buscarPsicologos($buscar);
} else {
    $data['psicologos'] = $modelo->findAll();
}
        return view('psicologos/index', $data);
    }

   public function insertar()
{
    $modelo = new PsicologoModelo();

    $nombres        = trim($this->request->getPost('nombres'));
    $apellidos      = trim($this->request->getPost('apellidos'));
    $correo         = trim($this->request->getPost('correo'));
    $numeroRegistro = trim($this->request->getPost('numero_registro'));

    if ($modelo->existeCorreo($correo)) {
        return redirect()->to(base_url('/psicologos'))
                         ->with('error', 'Ya existe un psicólogo registrado con ese correo.');
    }

    if ($modelo->existeNombreCompleto($nombres, $apellidos)) {
        return redirect()->to(base_url('/psicologos'))
                         ->with('error', 'Ya existe un psicólogo registrado con ese nombre completo.');
    }

    if ($modelo->existeNumeroRegistro($numeroRegistro)) {
        return redirect()->to(base_url('/psicologos'))
                         ->with('error', 'Ya existe un psicólogo registrado con ese número de registro.');
    }

    $modelo->insert([
        'id_usuario'       => null,
        'nombres'          => $nombres,
        'apellidos'        => $apellidos,
        'telefono'         => trim($this->request->getPost('telefono')),
        'correo'           => $correo,
        'numero_registro'  => $numeroRegistro,
        'estado'           => 1
    ]);

    return redirect()->to(base_url('/psicologos'))
                     ->with('success', 'Psicólogo registrado correctamente.');
}

    public function actualizar($id)
{
    $modelo = new PsicologoModelo();

    $nombres        = trim($this->request->getPost('nombres'));
    $apellidos      = trim($this->request->getPost('apellidos'));
    $correo         = trim($this->request->getPost('correo'));
    $numeroRegistro = trim($this->request->getPost('numero_registro'));

    if ($modelo->existeCorreo($correo, $id)) {
        return redirect()->to(base_url('/psicologos'))
                         ->with('error', 'Ya existe otro psicólogo registrado con ese correo.');
    }

    if ($modelo->existeNombreCompleto($nombres, $apellidos, $id)) {
        return redirect()->to(base_url('/psicologos'))
                         ->with('error', 'Ya existe otro psicólogo registrado con ese nombre completo.');
    }

    if ($modelo->existeNumeroRegistro($numeroRegistro, $id)) {
        return redirect()->to(base_url('/psicologos'))
                         ->with('error', 'Ya existe otro psicólogo registrado con ese número de registro.');
    }

    $modelo->update($id, [
        'nombres'         => $nombres,
        'apellidos'       => $apellidos,
        'telefono'        => trim($this->request->getPost('telefono')),
        'correo'          => $correo,
        'numero_registro' => $numeroRegistro,
        'bloqueado_activacion'  => $this->request->getPost('bloqueado_activacion') ? 0 : $this->request->getPost('bloqueado_actual')
    ]);

    return redirect()->to(base_url('/psicologos'))
                     ->with('success', 'Psicólogo actualizado correctamente.');
}

    public function activar($id)
{
    $modelo = new PsicologoModelo();

    $actualizado = $modelo->cambiarEstadoPsicologoYUsuario($id, 1);

    if (!$actualizado) {
        return redirect()->to(base_url('/psicologos'))
                         ->with('error', 'Psicólogo no encontrado.');
    }

    return redirect()->to(base_url('/psicologos'))
                     ->with('success', 'Psicólogo activado correctamente.');
}

   public function desactivar($id)
{
    $modelo = new PsicologoModelo();

    $actualizado = $modelo->cambiarEstadoPsicologoYUsuario($id, 0);

    if (!$actualizado) {
        return redirect()->to(base_url('/psicologos'))
                         ->with('error', 'Psicólogo no encontrado.');
    }

    return redirect()->to(base_url('/psicologos'))
                     ->with('success', 'Psicólogo desactivado correctamente.');
}
}
