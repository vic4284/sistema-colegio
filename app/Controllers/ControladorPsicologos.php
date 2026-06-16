<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PsicologoModelo;
    
class ControladorPsicologos extends BaseController
{
     private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarDatosPsicologo($nombres, $apellidos, $telefono, $correo, $numeroRegistro)
    {
        if ($nombres === '') {
            return 'Los nombres son obligatorios.';
        }

        if (mb_strlen($nombres) < 2) {
            return 'Los nombres deben tener al menos 2 caracteres.';
        }

        if (mb_strlen($nombres) > 50) {
            return 'Los nombres no deben superar los 50 caracteres.';
        }

        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $nombres)) {
            return 'Los nombres solo deben contener letras y espacios.';
        }

        if ($apellidos === '') {
            return 'Los apellidos son obligatorios.';
        }

        if (mb_strlen($apellidos) < 2) {
            return 'Los apellidos deben tener al menos 2 caracteres.';
        }

        if (mb_strlen($apellidos) > 50) {
            return 'Los apellidos no deben superar los 50 caracteres.';
        }

        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $apellidos)) {
            return 'Los apellidos solo deben contener letras y espacios.';
        }

        if ($telefono === '') {
            return 'El teléfono es obligatorio.';
        }

        if (!preg_match('/^[0-9]+$/', $telefono)) {
            return 'El teléfono solo debe contener números.';
        }

        if (mb_strlen($telefono) < 7) {
            return 'El teléfono debe tener al menos 7 dígitos.';
        }

        if (mb_strlen($telefono) > 15) {
            return 'El teléfono no debe superar los 15 dígitos.';
        }

        if ($correo === '') {
            return 'El correo es obligatorio.';
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return 'Debe ingresar un correo válido.';
        }

        if (mb_strlen($correo) > 100) {
            return 'El correo no debe superar los 100 caracteres.';
        }

        if ($numeroRegistro === '') {
            return 'El número de registro es obligatorio.';
        }

        if (mb_strlen($numeroRegistro) < 3) {
            return 'El número de registro debe tener al menos 3 caracteres.';
        }

        if (mb_strlen($numeroRegistro) > 30) {
            return 'El número de registro no debe superar los 30 caracteres.';
        }

        if (!preg_match('/^[a-zA-Z0-9\-]+$/', $numeroRegistro)) {
            return 'El número de registro solo debe contener letras, números y guion.';
        }

        return null;
    }

    public function index()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para acceder a este módulo.');
        }

        $modelo = new PsicologoModelo();

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        if ($buscar !== '') {
            $psicologos = $modelo->buscarPsicologos($buscar);
        } else {
            $psicologos = $modelo->findAll();
        }

        return view('psicologos/index', [
            'psicologos' => $psicologos
        ]);
    }

    public function insertar()
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $modelo = new PsicologoModelo();

        $nombres        = trim($this->request->getPost('nombres') ?? '');
        $apellidos      = trim($this->request->getPost('apellidos') ?? '');
        $telefono       = trim($this->request->getPost('telefono') ?? '');
        $correo         = trim($this->request->getPost('correo') ?? '');
        $numeroRegistro = trim($this->request->getPost('numero_registro') ?? '');

        $error = $this->validarDatosPsicologo($nombres, $apellidos, $telefono, $correo, $numeroRegistro);

        if ($error !== null) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeCorreo($correo)) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'Ya existe un psicólogo registrado con ese correo.')
                ->withInput();
        }

        if ($modelo->existeNombreCompleto($nombres, $apellidos)) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'Ya existe un psicólogo registrado con ese nombre completo.')
                ->withInput();
        }

        if ($modelo->existeNumeroRegistro($numeroRegistro)) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'Ya existe un psicólogo registrado con ese número de registro.')
                ->withInput();
        }

        $modelo->insert([
            'id_usuario'       => null,
            'nombres'          => $nombres,
            'apellidos'        => $apellidos,
            'telefono'         => $telefono,
            'correo'           => $correo,
            'numero_registro'  => $numeroRegistro,
            'estado'           => 1
        ]);

        return redirect()->to(base_url('/psicologos'))
            ->with('success', 'Psicólogo registrado correctamente.');
    }

    public function actualizar($id = null)
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'ID de psicólogo no válido.');
        }

        $modelo = new PsicologoModelo();
        $psicologo = $modelo->find($id);

        if (!$psicologo) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'Psicólogo no encontrado.');
        }

        $nombres        = trim($this->request->getPost('nombres') ?? '');
        $apellidos      = trim($this->request->getPost('apellidos') ?? '');
        $telefono       = trim($this->request->getPost('telefono') ?? '');
        $correo         = trim($this->request->getPost('correo') ?? '');
        $numeroRegistro = trim($this->request->getPost('numero_registro') ?? '');

        $error = $this->validarDatosPsicologo($nombres, $apellidos, $telefono, $correo, $numeroRegistro);

        if ($error !== null) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeCorreo($correo, $id)) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'Ya existe otro psicólogo registrado con ese correo.')
                ->withInput();
        }

        if ($modelo->existeNombreCompleto($nombres, $apellidos, $id)) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'Ya existe otro psicólogo registrado con ese nombre completo.')
                ->withInput();
        }

        if ($modelo->existeNumeroRegistro($numeroRegistro, $id)) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'Ya existe otro psicólogo registrado con ese número de registro.')
                ->withInput();
        }

        $modelo->update($id, [
            'nombres'                => $nombres,
            'apellidos'              => $apellidos,
            'telefono'               => $telefono,
            'correo'                 => $correo,
            'numero_registro'        => $numeroRegistro,
            'bloqueado_activacion'   => $this->request->getPost('bloqueado_activacion') ? 0 : $this->request->getPost('bloqueado_actual')
        ]);

        return redirect()->to(base_url('/psicologos'))
            ->with('success', 'Psicólogo actualizado correctamente.');
    }

    public function activar($id = null)
    {
        return $this->cambiarEstado($id, 1, 'activado');
    }

    public function desactivar($id = null)
    {
        return $this->cambiarEstado($id, 0, 'desactivado');
    }

    private function cambiarEstado($id, $estado, $texto)
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'ID de psicólogo no válido.');
        }

        $modelo = new PsicologoModelo();

        $actualizado = $modelo->cambiarEstadoPsicologoYUsuario($id, $estado);

        if (!$actualizado) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'Psicólogo no encontrado.');
        }

        return redirect()->to(base_url('/psicologos'))
            ->with('success', 'Psicólogo ' . $texto . ' correctamente.');
    }
}
