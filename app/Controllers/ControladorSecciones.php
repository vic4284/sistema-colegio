<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SeccionModelo;
class ControladorSecciones extends BaseController
{
      private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarDatosSeccion($nombreSeccion)
    {
        if ($nombreSeccion === '') {
            return 'El nombre de la sección es obligatorio.';
        }

        if (mb_strlen($nombreSeccion) > 2) {
            return 'El nombre de la sección no debe superar los 2 caracteres.';
        }

        if (!preg_match('/^[A-Za-z]$/', $nombreSeccion)) {
            return 'La sección solo debe contener una letra. Ejemplo: A, B, C o D.';
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

        $modelo = new SeccionModelo();

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        if ($buscar !== '') {
            $secciones = $modelo->buscarSecciones($buscar);
        } else {
            $secciones = $modelo->orderBy('id_seccion', 'ASC')->findAll();
        }

        return view('secciones/index', [
            'secciones' => $secciones
        ]);
    }

    public function insertar()
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $modelo = new SeccionModelo();

        $nombreSeccion = strtoupper(trim($this->request->getPost('nombre_seccion') ?? ''));

        $error = $this->validarDatosSeccion($nombreSeccion);

        if ($error !== null) {
            return redirect()->to(base_url('/secciones'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeSeccion($nombreSeccion)) {
            return redirect()->to(base_url('/secciones'))
                ->with('error', 'Ya existe una sección registrada con ese nombre.')
                ->withInput();
        }

        $modelo->insert([
            'nombre_seccion' => $nombreSeccion,
            'estado'         => 1
        ]);

        return redirect()->to(base_url('/secciones'))
            ->with('success', 'Sección registrada correctamente.');
    }

    public function actualizar($id = null)
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/secciones'))
                ->with('error', 'ID de sección no válido.');
        }

        $modelo = new SeccionModelo();

        $seccion = $modelo->find($id);

        if (!$seccion) {
            return redirect()->to(base_url('/secciones'))
                ->with('error', 'Sección no encontrada.');
        }

        $nombreSeccion = strtoupper(trim($this->request->getPost('nombre_seccion') ?? ''));

        $error = $this->validarDatosSeccion($nombreSeccion);

        if ($error !== null) {
            return redirect()->to(base_url('/secciones'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeSeccion($nombreSeccion, $id)) {
            return redirect()->to(base_url('/secciones'))
                ->with('error', 'Ya existe otra sección registrada con ese nombre.')
                ->withInput();
        }

        $modelo->update($id, [
            'nombre_seccion'      => $nombreSeccion,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/secciones'))
            ->with('success', 'Sección actualizada correctamente.');
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
            return redirect()->to(base_url('/secciones'))
                ->with('error', 'ID de sección no válido.');
        }

        $modelo = new SeccionModelo();

        $seccion = $modelo->find($id);

        if (!$seccion) {
            return redirect()->to(base_url('/secciones'))
                ->with('error', 'Sección no encontrada.');
        }

        $modelo->update($id, [
            'estado' => $estado
        ]);

        return redirect()->to(base_url('/secciones'))
            ->with('success', 'Sección ' . $texto . ' correctamente.');
    }
}
