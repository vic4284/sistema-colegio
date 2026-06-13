<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AsignacionEstudianteModelo;
class ControladorAsignacionEstudiantes extends BaseController
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
    $modelo = new AsignacionEstudianteModelo();

        $buscar = trim($this->request->getGet('buscar'));
        $asignacionHorario = $this->request->getGet('asignacion_horario');

        if ($buscar !== '') {
            $data['asignaciones'] = $modelo->buscarAsignaciones($buscar);
        } else {
            $data['asignaciones'] = $modelo->listarAsignaciones();
        }

        $data['estudiantes'] = $modelo->listarEstudiantesActivos();
        $data['paralelos']   = $modelo->listarParalelosActivos();
        $data['gestiones']   = $modelo->listarGestionesActivas();

        $data['asignacion_seleccionada'] = $asignacionHorario;

        if (!empty($asignacionHorario)) {
            $data['horario_estudiante'] = $modelo->obtenerHorarioPorAsignacionEstudiante($asignacionHorario);
        } else {
            $data['horario_estudiante'] = [];
        }

        return view('asignacion_estudiantes/index', $data);
    }

    public function insertar()
    {
        $modelo = new AsignacionEstudianteModelo();

        try {
            $modelo->insert([
                'id_estudiante' => $this->request->getPost('id_estudiante'),
                'id_paralelo'   => $this->request->getPost('id_paralelo'),
                'id_gestion'    => $this->request->getPost('id_gestion'),
                'estado'        => 1
            ]);

            return redirect()->to(base_url('asignacion-estudiantes'))
                             ->with('success', 'Asignación registrada correctamente.');

        } catch (\Exception $e) {
            return redirect()->to(base_url('asignacion-estudiantes'))
                             ->with('error', 'No se pudo registrar. El estudiante ya puede estar asignado en esa gestión.');
        }
    }

    public function actualizar($id)
{
    $modelo = new AsignacionEstudianteModelo();

    $asignacionActual = $modelo->find($id);

    if (!$asignacionActual) {
        return redirect()->to(base_url('asignacion-estudiantes'))
                         ->with('error', 'Asignación no encontrada.');
    }

    $nuevoEstudiante = $this->request->getPost('id_estudiante');
    $nuevoParalelo   = $this->request->getPost('id_paralelo');
    $nuevaGestion    = $this->request->getPost('id_gestion');

    try {
        if (
            (int)$asignacionActual['id_estudiante'] !== (int)$nuevoEstudiante ||
            (int)$asignacionActual['id_paralelo'] !== (int)$nuevoParalelo ||
            (int)$asignacionActual['id_gestion'] !== (int)$nuevaGestion
        ) {
            $modelo->eliminarNotasPorAsignacionEstudiante($id);
        }

        $modelo->update($id, [
            'id_estudiante'       => $nuevoEstudiante,
            'id_paralelo'         => $nuevoParalelo,
            'id_gestion'          => $nuevaGestion,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('asignacion-estudiantes'))
                         ->with('success', 'Asignación actualizada correctamente.');

    } catch (\Exception $e) {
        return redirect()->to(base_url('asignacion-estudiantes'))
                         ->with('error', 'No se pudo actualizar. Puede existir una asignación duplicada.');
    }
}

    public function activar($id)
    {
        $modelo = new AsignacionEstudianteModelo();

        $modelo->update($id, [
            'estado' => 1
        ]);

        return redirect()->to(base_url('asignacion-estudiantes'))
                         ->with('success', 'Asignación activada correctamente.');
    }

    public function desactivar($id)
    {
        $modelo = new AsignacionEstudianteModelo();

        $modelo->update($id, [
            'estado' => 0
        ]);

        return redirect()->to(base_url('asignacion-estudiantes'))
                         ->with('success', 'Asignación desactivada correctamente.');
    }
}
