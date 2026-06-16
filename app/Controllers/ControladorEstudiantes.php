<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\EstudianteModelo;
class ControladorEstudiantes extends BaseController
{
    private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarDatosEstudiante($nombres, $apellidos, $telefono, $correo, $direccion, $genero)
    {
        if ($nombres === '') return 'Los nombres son obligatorios.';
        if (mb_strlen($nombres) < 2) return 'Los nombres deben tener al menos 2 caracteres.';
        if (mb_strlen($nombres) > 50) return 'Los nombres no deben superar los 50 caracteres.';
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $nombres)) return 'Los nombres solo deben contener letras y espacios.';

        if ($apellidos === '') return 'Los apellidos son obligatorios.';
        if (mb_strlen($apellidos) < 2) return 'Los apellidos deben tener al menos 2 caracteres.';
        if (mb_strlen($apellidos) > 50) return 'Los apellidos no deben superar los 50 caracteres.';
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $apellidos)) return 'Los apellidos solo deben contener letras y espacios.';

        if ($telefono === '') return 'El teléfono es obligatorio.';
        if (!preg_match('/^[0-9]+$/', $telefono)) return 'El teléfono solo debe contener números.';
        if (mb_strlen($telefono) < 7) return 'El teléfono debe tener al menos 7 dígitos.';
        if (mb_strlen($telefono) > 15) return 'El teléfono no debe superar los 15 dígitos.';

        if ($correo === '') return 'El correo es obligatorio.';
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) return 'Debe ingresar un correo válido.';
        if (mb_strlen($correo) > 100) return 'El correo no debe superar los 100 caracteres.';

        if ($direccion === '') return 'La dirección es obligatoria.';
        if (mb_strlen($direccion) < 3) return 'La dirección debe tener al menos 3 caracteres.';
        if (mb_strlen($direccion) > 150) return 'La dirección no debe superar los 150 caracteres.';
        if (!preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.\,\#\-]+$/u', $direccion)) return 'La dirección contiene caracteres no permitidos.';

        if ($genero === '') return 'Debe seleccionar el género.';
        if (!in_array($genero, ['MASCULINO', 'FEMENINO'])) return 'El género seleccionado no es válido.';

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

        $modelo = new EstudianteModelo();

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        if ($buscar !== '') {
            $estudiantes = $modelo->buscarEstudiantes($buscar);
        } else {
            $estudiantes = $modelo->findAll();
        }

        return view('estudiantes/index', [
            'estudiantes' => $estudiantes
        ]);
    }

    public function insertar()
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $modelo = new EstudianteModelo();

        $nombres   = trim($this->request->getPost('nombres') ?? '');
        $apellidos = trim($this->request->getPost('apellidos') ?? '');
        $telefono  = trim($this->request->getPost('telefono') ?? '');
        $correo    = trim($this->request->getPost('correo') ?? '');
        $direccion = trim($this->request->getPost('direccion') ?? '');
        $genero    = trim($this->request->getPost('genero') ?? '');

        $error = $this->validarDatosEstudiante($nombres, $apellidos, $telefono, $correo, $direccion, $genero);

        if ($error !== null) {
            return redirect()->to(base_url('/estudiantes'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeCorreo($correo)) {
            return redirect()->to(base_url('/estudiantes'))
                ->with('error', 'Ya existe un estudiante registrado con ese correo.')
                ->withInput();
        }

        if ($modelo->existeNombreCompleto($nombres, $apellidos)) {
            return redirect()->to(base_url('/estudiantes'))
                ->with('error', 'Ya existe un estudiante registrado con ese nombre completo.')
                ->withInput();
        }

        $modelo->insert([
            'id_usuario' => null,
            'nombres'    => $nombres,
            'apellidos'  => $apellidos,
            'telefono'   => $telefono,
            'correo'     => $correo,
            'direccion'  => $direccion,
            'genero'     => $genero,
            'estado'     => 1
        ]);

        return redirect()->to(base_url('/estudiantes'))
            ->with('success', 'Estudiante registrado correctamente.');
    }

    public function actualizar($id = null)
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/estudiantes'))
                ->with('error', 'ID de estudiante no válido.');
        }

        $modelo = new EstudianteModelo();
        $estudiante = $modelo->find($id);

        if (!$estudiante) {
            return redirect()->to(base_url('/estudiantes'))
                ->with('error', 'Estudiante no encontrado.');
        }

        $nombres   = trim($this->request->getPost('nombres') ?? '');
        $apellidos = trim($this->request->getPost('apellidos') ?? '');
        $telefono  = trim($this->request->getPost('telefono') ?? '');
        $correo    = trim($this->request->getPost('correo') ?? '');
        $direccion = trim($this->request->getPost('direccion') ?? '');
        $genero    = trim($this->request->getPost('genero') ?? '');

        $error = $this->validarDatosEstudiante($nombres, $apellidos, $telefono, $correo, $direccion, $genero);

        if ($error !== null) {
            return redirect()->to(base_url('/estudiantes'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeCorreo($correo, $id)) {
            return redirect()->to(base_url('/estudiantes'))
                ->with('error', 'Ya existe otro estudiante registrado con ese correo.')
                ->withInput();
        }

        if ($modelo->existeNombreCompleto($nombres, $apellidos, $id)) {
            return redirect()->to(base_url('/estudiantes'))
                ->with('error', 'Ya existe otro estudiante registrado con ese nombre completo.')
                ->withInput();
        }

        $modelo->update($id, [
            'nombres'              => $nombres,
            'apellidos'            => $apellidos,
            'telefono'             => $telefono,
            'correo'               => $correo,
            'direccion'            => $direccion,
            'genero'               => $genero,
            'bloqueado_activacion' => $this->request->getPost('bloqueado_activacion') ? 0 : $this->request->getPost('bloqueado_actual')
        ]);

        return redirect()->to(base_url('/estudiantes'))
            ->with('success', 'Estudiante actualizado correctamente.');
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
            return redirect()->to(base_url('/estudiantes'))
                ->with('error', 'ID de estudiante no válido.');
        }

        $modelo = new EstudianteModelo();

        $actualizado = $modelo->cambiarEstadoEstudianteYUsuario($id, $estado);

        if (!$actualizado) {
            return redirect()->to(base_url('/estudiantes'))
                ->with('error', 'Estudiante no encontrado.');
        }

        return redirect()->to(base_url('/estudiantes'))
            ->with('success', 'Estudiante ' . $texto . ' correctamente.');
    }
}
