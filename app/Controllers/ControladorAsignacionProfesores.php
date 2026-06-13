<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AsignacionProfesorModelo;

class ControladorAsignacionProfesores extends BaseController
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
    $modelo = new AsignacionProfesorModelo();

        $buscar = trim($this->request->getGet('buscar'));
        $paraleloHorario = $this->request->getGet('paralelo_horario');

        if ($buscar !== '') {
            $data['asignaciones'] = $modelo->buscarAsignaciones($buscar);
        } else {
            $data['asignaciones'] = $modelo->listarAsignaciones();
        }

        $data['profesores'] = $modelo->listarProfesoresActivos();
        $data['materias']   = $modelo->listarMateriasActivas();
        $data['paralelos']  = $modelo->listarParalelosActivos();
        $data['horarios']   = $modelo->listarHorariosActivos();
        $data['aulas']      = $modelo->listarAulasActivas();
        $data['gestiones']  = $modelo->listarGestionesActivas();

        $data['paralelo_seleccionado'] = $paraleloHorario;

        if (!empty($paraleloHorario)) {
            $data['horario_paralelo'] = $modelo->obtenerHorarioPorParalelo($paraleloHorario);
        } else {
            $data['horario_paralelo'] = [];
        }

        return view('asignacion_profesores/index', $data);
    }

    public function insertar()
    {
        $modelo = new AsignacionProfesorModelo();

        try {
            $modelo->insert([
                'id_profesor' => $this->request->getPost('id_profesor'),
                'id_materia'  => $this->request->getPost('id_materia'),
                'id_paralelo' => $this->request->getPost('id_paralelo'),
                'id_horario'  => $this->request->getPost('id_horario'),
                'id_aula'     => $this->request->getPost('id_aula'),
                'id_gestion'  => $this->request->getPost('id_gestion'),
                'estado'      => 1
            ]);

            return redirect()->to(base_url('asignacion-profesores'))
                             ->with('success', 'Asignación registrada correctamente.');

        } catch (\Exception $e) {
            return redirect()->to(base_url('asignacion-profesores'))
                             ->with('error', 'No se pudo registrar. Puede existir cruce de horario, aula ocupada o paralelo ya asignado en ese horario.');
        }
    }

    public function actualizar($id)
    {
        $modelo = new AsignacionProfesorModelo();

        try {
            $modelo->update($id, [
                'id_profesor'         => $this->request->getPost('id_profesor'),
                'id_materia'          => $this->request->getPost('id_materia'),
                'id_paralelo'         => $this->request->getPost('id_paralelo'),
                'id_horario'          => $this->request->getPost('id_horario'),
                'id_aula'             => $this->request->getPost('id_aula'),
                'id_gestion'          => $this->request->getPost('id_gestion'),
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to(base_url('asignacion-profesores'))
                             ->with('success', 'Asignación actualizada correctamente.');

        } catch (\Exception $e) {
            return redirect()->to(base_url('asignacion-profesores'))
                             ->with('error', 'No se pudo actualizar. Puede existir cruce de horario, aula ocupada o paralelo ya asignado en ese horario.');
        }
    }

    public function activar($id)
    {
        $modelo = new AsignacionProfesorModelo();

        $modelo->update($id, [
            'estado' => 1
        ]);

        return redirect()->to(base_url('asignacion-profesores'))
                         ->with('success', 'Asignación activada correctamente.');
    }

    public function desactivar($id)
    {
        $modelo = new AsignacionProfesorModelo();

        $modelo->update($id, [
            'estado' => 0
        ]);

        return redirect()->to(base_url('asignacion-profesores'))
                         ->with('success', 'Asignación desactivada correctamente.');
    }
}
