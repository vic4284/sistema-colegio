<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\NotaModelo;

class ControladorNotasProfesor extends BaseController
{
       public function index()
    {
       
      if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (session()->get('rol') !== 'PROFESOR') {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para acceder a este módulo.');
        }
    $modelo = new NotaModelo();

        $idUsuario = session()->get('id_usuario');
        $buscar = trim($this->request->getGet('buscar'));

        if ($buscar !== '') {
            $data['notas'] = $modelo->buscarNotasProfesor($idUsuario, $buscar);
        } else {
            $data['notas'] = $modelo->listarNotasProfesor($idUsuario);
        }

        $data['asignaciones_profesor'] = $modelo->listarAsignacionesProfesor($idUsuario);
        $data['asignaciones_estudiante'] = $modelo->listarAsignacionesEstudiantesCompatibles($idUsuario);

        return view('notas_profesor/index', $data);
    }

    public function insertar()
    {
        $modelo = new NotaModelo();

        $idAsignacion = $this->request->getPost('id_asignacion');
        $idAsignacionEstudiante = $this->request->getPost('id_asignacion_estudiante');

        if (!$modelo->validarCoincidenciaAsignaciones($idAsignacion, $idAsignacionEstudiante)) {
            return redirect()->to(base_url('notas-profesor'))
                             ->with('error', 'El estudiante no pertenece al paralelo o gestión de esta asignación.');
        }

        $primer = $this->request->getPost('primer_trimestre');
        $segundo = $this->request->getPost('segundo_trimestre');
        $tercer = $this->request->getPost('tercer_trimestre');

        $promedio = $this->calcularPromedio($primer, $segundo, $tercer);

        try {
            $modelo->insert([
                'id_asignacion' => $idAsignacion,
                'id_asignacion_estudiante' => $idAsignacionEstudiante,
                'primer_trimestre' => $primer,
                'segundo_trimestre' => $segundo,
                'tercer_trimestre' => $tercer,
                'promedio' => $promedio,
                'observacion' => $this->request->getPost('observacion'),
                'estado' => 1
            ]);

            return redirect()->to(base_url('notas-profesor'))
                             ->with('success', 'Nota registrada correctamente.');

        } catch (\Exception $e) {
            return redirect()->to(base_url('notas-profesor'))
                             ->with('error', 'No se pudo registrar. Ya existe una nota para este estudiante en esta asignación.');
        }
    }

    public function actualizar($id)
    {
        $modelo = new NotaModelo();

        $idAsignacion = $this->request->getPost('id_asignacion');
        $idAsignacionEstudiante = $this->request->getPost('id_asignacion_estudiante');

        if (!$modelo->validarCoincidenciaAsignaciones($idAsignacion, $idAsignacionEstudiante)) {
            return redirect()->to(base_url('notas-profesor'))
                             ->with('error', 'El estudiante no pertenece al paralelo o gestión de esta asignación.');
        }

        $primer = $this->request->getPost('primer_trimestre');
        $segundo = $this->request->getPost('segundo_trimestre');
        $tercer = $this->request->getPost('tercer_trimestre');

        $promedio = $this->calcularPromedio($primer, $segundo, $tercer);

        try {
            $modelo->update($id, [
                'id_asignacion' => $idAsignacion,
                'id_asignacion_estudiante' => $idAsignacionEstudiante,
                'primer_trimestre' => $primer,
                'segundo_trimestre' => $segundo,
                'tercer_trimestre' => $tercer,
                'promedio' => $promedio,
                'observacion' => $this->request->getPost('observacion'),
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to(base_url('notas-profesor'))
                             ->with('success', 'Nota actualizada correctamente.');

        } catch (\Exception $e) {
            return redirect()->to(base_url('notas-profesor'))
                             ->with('error', 'No se pudo actualizar. Verifique si la nota está duplicada.');
        }
    }

    public function eliminar($id)
    {
        $modelo = new NotaModelo();

        $modelo->delete($id);

        return redirect()->to(base_url('notas-profesor'))
                         ->with('success', 'Nota eliminada correctamente.');
    }

    private function calcularPromedio($primer, $segundo, $tercer)
    {
        $notas = [];

        if ($primer !== '' && $primer !== null) {
            $notas[] = (float)$primer;
        }

        if ($segundo !== '' && $segundo !== null) {
            $notas[] = (float)$segundo;
        }

        if ($tercer !== '' && $tercer !== null) {
            $notas[] = (float)$tercer;
        }

        if (count($notas) === 0) {
            return null;
        }

        return round(array_sum($notas) / count($notas), 2);
    }
}
