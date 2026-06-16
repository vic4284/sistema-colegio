<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProfesorModelo;
class ControladorProfesores extends BaseController
{
     private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarDatosProfesor($nombres, $apellidos, $telefono, $correo, $especialidad)
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

        if ($especialidad === '') {
            return 'La especialidad es obligatoria.';
        }

        if (mb_strlen($especialidad) < 3) {
            return 'La especialidad debe tener al menos 3 caracteres.';
        }

        if (mb_strlen($especialidad) > 80) {
            return 'La especialidad no debe superar los 80 caracteres.';
        }

        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $especialidad)) {
            return 'La especialidad solo debe contener letras y espacios.';
        }

        return null;
    }

    public function index()
    {
        if (!$this->esAdministrativo()) {
            if (!session()->get('logueado')) {
                return redirect()->to(base_url('/login'));
            }

            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para acceder a este módulo.');
        }

        $modelo = new ProfesorModelo();

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        if ($buscar !== '') {
            $profesores = $modelo->buscarProfesores($buscar);
        } else {
            $profesores = $modelo->findAll();
        }

        return view('profesores/index', [
            'profesores' => $profesores
        ]);
    }

    public function insertar()
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $modelo = new ProfesorModelo();

        $nombres      = trim($this->request->getPost('nombres') ?? '');
        $apellidos    = trim($this->request->getPost('apellidos') ?? '');
        $telefono     = trim($this->request->getPost('telefono') ?? '');
        $correo       = trim($this->request->getPost('correo') ?? '');
        $especialidad = trim($this->request->getPost('especialidad') ?? '');

        $error = $this->validarDatosProfesor($nombres, $apellidos, $telefono, $correo, $especialidad);

        if ($error !== null) {
            return redirect()->to(base_url('/profesores'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeCorreo($correo)) {
            return redirect()->to(base_url('/profesores'))
                ->with('error', 'Ya existe un profesor registrado con ese correo.')
                ->withInput();
        }

        if ($modelo->existeNombreCompleto($nombres, $apellidos)) {
            return redirect()->to(base_url('/profesores'))
                ->with('error', 'Ya existe un profesor registrado con ese nombre completo.')
                ->withInput();
        }

        $modelo->insert([
            'id_usuario'   => null,
            'nombres'      => $nombres,
            'apellidos'    => $apellidos,
            'telefono'     => $telefono,
            'correo'       => $correo,
            'especialidad' => $especialidad,
            'estado'       => 1
        ]);

        return redirect()->to(base_url('/profesores'))
            ->with('success', 'Profesor registrado correctamente.');
    }

    public function actualizar($id = null)
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/profesores'))
                ->with('error', 'ID de profesor no válido.');
        }

        $modelo = new ProfesorModelo();
        $profesor = $modelo->find($id);

        if (!$profesor) {
            return redirect()->to(base_url('/profesores'))
                ->with('error', 'Profesor no encontrado.');
        }

        $nombres      = trim($this->request->getPost('nombres') ?? '');
        $apellidos    = trim($this->request->getPost('apellidos') ?? '');
        $telefono     = trim($this->request->getPost('telefono') ?? '');
        $correo       = trim($this->request->getPost('correo') ?? '');
        $especialidad = trim($this->request->getPost('especialidad') ?? '');

        $error = $this->validarDatosProfesor($nombres, $apellidos, $telefono, $correo, $especialidad);

        if ($error !== null) {
            return redirect()->to(base_url('/profesores'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeCorreo($correo, $id)) {
            return redirect()->to(base_url('/profesores'))
                ->with('error', 'Ya existe otro profesor registrado con ese correo.')
                ->withInput();
        }

        if ($modelo->existeNombreCompleto($nombres, $apellidos, $id)) {
            return redirect()->to(base_url('/profesores'))
                ->with('error', 'Ya existe otro profesor registrado con ese nombre completo.')
                ->withInput();
        }

        $modelo->update($id, [
            'nombres'              => $nombres,
            'apellidos'            => $apellidos,
            'telefono'             => $telefono,
            'correo'               => $correo,
            'especialidad'         => $especialidad,
            'bloqueado_activacion' => $this->request->getPost('bloqueado_activacion') ? 0 : $this->request->getPost('bloqueado_actual')
        ]);

        return redirect()->to(base_url('/profesores'))
            ->with('success', 'Profesor actualizado correctamente.');
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
            return redirect()->to(base_url('/profesores'))
                ->with('error', 'ID de profesor no válido.');
        }

        $modelo = new ProfesorModelo();

        $actualizado = $modelo->cambiarEstadoProfesorYUsuario($id, $estado);

        if (!$actualizado) {
            return redirect()->to(base_url('/profesores'))
                ->with('error', 'Profesor no encontrado.');
        }

        return redirect()->to(base_url('/profesores'))
            ->with('success', 'Profesor ' . $texto . ' correctamente.');
    }
}
