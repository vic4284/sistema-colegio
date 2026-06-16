<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MateriaModelo;
class ControladorMaterias extends BaseController
{
      private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarDatosMateria($nombreMateria, $descripcion = '')
    {
        if ($nombreMateria === '') {
            return 'El nombre de la materia es obligatorio.';
        }

        if (mb_strlen($nombreMateria) < 3) {
            return 'El nombre de la materia debe tener al menos 3 caracteres.';
        }

        if (mb_strlen($nombreMateria) > 80) {
            return 'El nombre de la materia no debe superar los 80 caracteres.';
        }

        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $nombreMateria)) {
            return 'El nombre de la materia solo debe contener letras y espacios.';
        }

        if ($descripcion !== '' && mb_strlen($descripcion) > 200) {
            return 'La descripción no debe superar los 200 caracteres.';
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

        $modelo = new MateriaModelo();

        $data['materias'] = $modelo->orderBy('id_materia', 'ASC')->findAll();

        return view('materias/index', $data);
    }

    public function insertar()
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $modelo = new MateriaModelo();

        $nombreMateria = trim($this->request->getPost('nombre_materia') ?? '');
        $descripcion   = trim($this->request->getPost('descripcion') ?? '');

        $error = $this->validarDatosMateria($nombreMateria, $descripcion);

        if ($error !== null) {
            return redirect()->to(base_url('/materias'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeMateria($nombreMateria)) {
            return redirect()->to(base_url('/materias'))
                ->with('error', 'Ya existe una materia registrada con ese nombre.')
                ->withInput();
        }

        $modelo->insert([
            'nombre_materia' => $nombreMateria,
            'descripcion'    => $descripcion,
            'estado'         => 1
        ]);

        return redirect()->to(base_url('/materias'))
            ->with('success', 'Materia registrada correctamente.');
    }

    public function actualizar($id = null)
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/materias'))
                ->with('error', 'ID de materia no válido.');
        }

        $modelo = new MateriaModelo();

        $materia = $modelo->find($id);

        if (!$materia) {
            return redirect()->to(base_url('/materias'))
                ->with('error', 'Materia no encontrada.');
        }

        $nombreMateria = trim($this->request->getPost('nombre_materia') ?? '');
        $descripcion   = trim($this->request->getPost('descripcion') ?? '');

        $error = $this->validarDatosMateria($nombreMateria, $descripcion);

        if ($error !== null) {
            return redirect()->to(base_url('/materias'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeMateria($nombreMateria, $id)) {
            return redirect()->to(base_url('/materias'))
                ->with('error', 'Ya existe otra materia registrada con ese nombre.')
                ->withInput();
        }

        $modelo->update($id, [
            'nombre_materia'      => $nombreMateria,
            'descripcion'         => $descripcion,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/materias'))
            ->with('success', 'Materia actualizada correctamente.');
    }

    public function activar($id = null)
    {
        return $this->cambiarEstado($id, 1, 'activada');
    }

    public function desactivar($id = null)
    {
        return $this->cambiarEstado($id, 0, 'desactivada');
    }

    private function cambiarEstado($id, $estado, $texto)
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/materias'))
                ->with('error', 'ID de materia no válido.');
        }

        $modelo = new MateriaModelo();

        $materia = $modelo->find($id);

        if (!$materia) {
            return redirect()->to(base_url('/materias'))
                ->with('error', 'Materia no encontrada.');
        }

        $modelo->update($id, ['estado' => $estado]);

        return redirect()->to(base_url('/materias'))
            ->with('success', 'Materia ' . $texto . ' correctamente.');
    }
}
