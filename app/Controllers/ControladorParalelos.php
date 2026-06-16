<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ParaleloModelo;
class ControladorParalelos extends BaseController
{
       private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarDatosParalelo($idGrado, $idSeccion)
    {
        if ($idGrado === '' || !is_numeric($idGrado)) {
            return 'Debe seleccionar un nivel y grado válido.';
        }

        if ($idSeccion === '' || !is_numeric($idSeccion)) {
            return 'Debe seleccionar una sección válida.';
        }

        return null;
    }

    private function cargarCombos()
    {
        $db = \Config\Database::connect();

        $grados = $db->table('grados g')
            ->select('g.id_grado, g.nombre_grado, n.nombre_nivel')
            ->join('niveles n', 'n.id_nivel = g.id_nivel')
            ->where('g.estado', 1)
            ->where('n.estado', 1)
            ->orderBy('n.id_nivel', 'ASC')
            ->orderBy('g.id_grado', 'ASC')
            ->get()
            ->getResultArray();

        $secciones = $db->table('secciones')
            ->where('estado', 1)
            ->orderBy('id_seccion', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'grados'    => $grados,
            'secciones' => $secciones
        ];
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

        $modelo = new ParaleloModelo();

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        if ($buscar !== '') {
            $paralelos = $modelo->buscarParalelos($buscar);
        } else {
            $paralelos = $modelo->listarParalelos();
        }

        $combos = $this->cargarCombos();

        return view('paralelos/index', [
            'paralelos' => $paralelos,
            'grados'    => $combos['grados'],
            'secciones' => $combos['secciones']
        ]);
    }

    public function insertar()
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $modelo = new ParaleloModelo();

        $idGrado   = trim($this->request->getPost('id_grado') ?? '');
        $idSeccion = trim($this->request->getPost('id_seccion') ?? '');

        $error = $this->validarDatosParalelo($idGrado, $idSeccion);

        if ($error !== null) {
            return redirect()->to(base_url('/paralelos'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeParalelo($idGrado, $idSeccion)) {
            return redirect()->to(base_url('/paralelos'))
                ->with('error', 'Ya existe un paralelo registrado con ese grado y sección.')
                ->withInput();
        }

        $modelo->insert([
            'id_grado'   => $idGrado,
            'id_seccion' => $idSeccion,
            'estado'     => 1
        ]);

        return redirect()->to(base_url('/paralelos'))
            ->with('success', 'Paralelo registrado correctamente.');
    }

    public function actualizar($id = null)
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/paralelos'))
                ->with('error', 'ID de paralelo no válido.');
        }

        $modelo = new ParaleloModelo();

        $paralelo = $modelo->find($id);

        if (!$paralelo) {
            return redirect()->to(base_url('/paralelos'))
                ->with('error', 'Paralelo no encontrado.');
        }

        $idGrado   = trim($this->request->getPost('id_grado') ?? '');
        $idSeccion = trim($this->request->getPost('id_seccion') ?? '');

        $error = $this->validarDatosParalelo($idGrado, $idSeccion);

        if ($error !== null) {
            return redirect()->to(base_url('/paralelos'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeParalelo($idGrado, $idSeccion, $id)) {
            return redirect()->to(base_url('/paralelos'))
                ->with('error', 'Ya existe otro paralelo registrado con ese grado y sección.')
                ->withInput();
        }

        $modelo->update($id, [
            'id_grado'            => $idGrado,
            'id_seccion'          => $idSeccion,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/paralelos'))
            ->with('success', 'Paralelo actualizado correctamente.');
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
            return redirect()->to(base_url('/paralelos'))
                ->with('error', 'ID de paralelo no válido.');
        }

        $modelo = new ParaleloModelo();

        $paralelo = $modelo->find($id);

        if (!$paralelo) {
            return redirect()->to(base_url('/paralelos'))
                ->with('error', 'Paralelo no encontrado.');
        }

        $modelo->update($id, [
            'estado' => $estado
        ]);

        return redirect()->to(base_url('/paralelos'))
            ->with('success', 'Paralelo ' . $texto . ' correctamente.');
    }
}
