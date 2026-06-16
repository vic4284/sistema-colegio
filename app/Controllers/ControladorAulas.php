<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AulaModelo;
class ControladorAulas extends BaseController
{
    private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarDatosAula($nombreAula, $capacidad)
    {
        if ($nombreAula === '') {
            return 'El nombre del aula es obligatorio.';
        }

        if (mb_strlen($nombreAula) < 3) {
            return 'El nombre del aula debe tener al menos 3 caracteres.';
        }

        if (mb_strlen($nombreAula) > 50) {
            return 'El nombre del aula no debe superar los 50 caracteres.';
        }

        if (!preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-]+$/u', $nombreAula)) {
            return 'El nombre del aula solo debe contener letras, números, espacios y guion.';
        }

        if ($capacidad === '') {
            return 'La capacidad es obligatoria.';
        }

        if (!is_numeric($capacidad)) {
            return 'La capacidad debe ser un número.';
        }

        if ((int)$capacidad < 1) {
            return 'La capacidad debe ser mayor a 0.';
        }

        if ((int)$capacidad > 100) {
            return 'La capacidad no debe superar 100 estudiantes.';
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

        $modelo = new AulaModelo();

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        if ($buscar !== '') {
            $aulas = $modelo->buscarAulas($buscar);
        } else {
            $aulas = $modelo->orderBy('id_aula', 'ASC')->findAll();
        }

        return view('aulas/index', [
            'aulas' => $aulas
        ]);
    }

    public function insertar()
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $modelo = new AulaModelo();

        $nombreAula = trim($this->request->getPost('nombre_aula') ?? '');
        $capacidad  = trim($this->request->getPost('capacidad') ?? '');

        $error = $this->validarDatosAula($nombreAula, $capacidad);

        if ($error !== null) {
            return redirect()->to(base_url('/aulas'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeAula($nombreAula)) {
            return redirect()->to(base_url('/aulas'))
                ->with('error', 'Ya existe un aula registrada con ese nombre.')
                ->withInput();
        }

        $modelo->insert([
            'nombre_aula' => $nombreAula,
            'capacidad'   => $capacidad,
            'estado'      => 1
        ]);

        return redirect()->to(base_url('/aulas'))
            ->with('success', 'Aula registrada correctamente.');
    }

    public function actualizar($id = null)
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/aulas'))
                ->with('error', 'ID de aula no válido.');
        }

        $modelo = new AulaModelo();

        $aula = $modelo->find($id);

        if (!$aula) {
            return redirect()->to(base_url('/aulas'))
                ->with('error', 'Aula no encontrada.');
        }

        $nombreAula = trim($this->request->getPost('nombre_aula') ?? '');
        $capacidad  = trim($this->request->getPost('capacidad') ?? '');

        $error = $this->validarDatosAula($nombreAula, $capacidad);

        if ($error !== null) {
            return redirect()->to(base_url('/aulas'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeAula($nombreAula, $id)) {
            return redirect()->to(base_url('/aulas'))
                ->with('error', 'Ya existe otra aula registrada con ese nombre.')
                ->withInput();
        }

        $modelo->update($id, [
            'nombre_aula'         => $nombreAula,
            'capacidad'           => $capacidad,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/aulas'))
            ->with('success', 'Aula actualizada correctamente.');
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
            return redirect()->to(base_url('/aulas'))
                ->with('error', 'ID de aula no válido.');
        }

        $modelo = new AulaModelo();

        if (!$modelo->find($id)) {
            return redirect()->to(base_url('/aulas'))
                ->with('error', 'Aula no encontrada.');
        }

        $modelo->update($id, ['estado' => $estado]);

        return redirect()->to(base_url('/aulas'))
            ->with('success', 'Aula ' . $texto . ' correctamente.');
    }
}
