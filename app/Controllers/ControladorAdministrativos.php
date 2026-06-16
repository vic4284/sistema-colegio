<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdministrativoModelo;

class ControladorAdministrativos extends BaseController
{
    private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarDatosAdministrativo($nombres, $apellidos, $telefono, $correo, $cargo)
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

        if ($apellidos === '') {
            return 'Los apellidos son obligatorios.';
        }

        if (mb_strlen($apellidos) < 2) {
            return 'Los apellidos deben tener al menos 2 caracteres.';
        }

        if (mb_strlen($apellidos) > 50) {
            return 'Los apellidos no deben superar los 50 caracteres.';
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

        if ($cargo === '') {
            return 'El cargo es obligatorio.';
        }

        if (mb_strlen($cargo) < 3) {
            return 'El cargo debe tener al menos 3 caracteres.';
        }

        if (mb_strlen($cargo) > 80) {
            return 'El cargo no debe superar los 80 caracteres.';
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

        $modelo = new AdministrativoModelo();

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        if ($buscar !== '') {
            $administrativos = $modelo->buscarAdministrativos($buscar);
        } else {
            $administrativos = $modelo->findAll();
        }

        return view('administrativos/index', [
            'administrativos' => $administrativos
        ]);
    }

    public function insertar()
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $modelo = new AdministrativoModelo();

        $nombres   = trim($this->request->getPost('nombres') ?? '');
        $apellidos = trim($this->request->getPost('apellidos') ?? '');
        $telefono  = trim($this->request->getPost('telefono') ?? '');
        $correo    = trim($this->request->getPost('correo') ?? '');
        $cargo     = trim($this->request->getPost('cargo') ?? '');

        $error = $this->validarDatosAdministrativo($nombres, $apellidos, $telefono, $correo, $cargo);

        if ($error !== null) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeCorreo($correo)) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'Ya existe un administrativo registrado con ese correo.')
                ->withInput();
        }

        if ($modelo->existeNombreCompleto($nombres, $apellidos)) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'Ya existe un administrativo registrado con ese nombre completo.')
                ->withInput();
        }

        $modelo->insert([
            'id_usuario' => null,
            'nombres'    => $nombres,
            'apellidos'  => $apellidos,
            'telefono'   => $telefono,
            'correo'     => $correo,
            'cargo'      => $cargo,
            'estado'     => 1
        ]);

        return redirect()->to(base_url('/administrativos'))
            ->with('success', 'Administrativo registrado correctamente.');
    }

    public function actualizar($id = null)
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'ID de administrativo no válido.');
        }

        $modelo = new AdministrativoModelo();
        $administrativo = $modelo->find($id);

        if (!$administrativo) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'Administrativo no encontrado.');
        }

        $nombres   = trim($this->request->getPost('nombres') ?? '');
        $apellidos = trim($this->request->getPost('apellidos') ?? '');
        $telefono  = trim($this->request->getPost('telefono') ?? '');
        $correo    = trim($this->request->getPost('correo') ?? '');
        $cargo     = trim($this->request->getPost('cargo') ?? '');

        $error = $this->validarDatosAdministrativo($nombres, $apellidos, $telefono, $correo, $cargo);

        if ($error !== null) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeCorreo($correo, $id)) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'Ya existe otro administrativo registrado con ese correo.')
                ->withInput();
        }

        if ($modelo->existeNombreCompleto($nombres, $apellidos, $id)) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'Ya existe otro administrativo registrado con ese nombre completo.')
                ->withInput();
        }

        $modelo->update($id, [
            'nombres'               => $nombres,
            'apellidos'             => $apellidos,
            'telefono'              => $telefono,
            'correo'                => $correo,
            'cargo'                 => $cargo,
            'bloqueado_activacion'  => $this->request->getPost('bloqueado_activacion') ? 0 : $this->request->getPost('bloqueado_actual')
        ]);

        return redirect()->to(base_url('/administrativos'))
            ->with('success', 'Administrativo actualizado correctamente.');
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
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'ID de administrativo no válido.');
        }

        $modelo = new AdministrativoModelo();

        $actualizado = $modelo->cambiarEstadoAdministrativoYUsuario($id, $estado);

        if (!$actualizado) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'Administrativo no encontrado.');
        }

        return redirect()->to(base_url('/administrativos'))
            ->with('success', 'Administrativo ' . $texto . ' correctamente.');
    }
}