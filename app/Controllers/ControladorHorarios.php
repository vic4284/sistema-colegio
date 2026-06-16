<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\HorarioModelo;

class ControladorHorarios extends BaseController
{
   private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarDatosHorario($dia, $horaInicio, $horaFin)
    {
        $diasValidos = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        if ($dia === '') {
            return 'Debe seleccionar un día.';
        }

        if (!in_array($dia, $diasValidos)) {
            return 'El día seleccionado no es válido.';
        }

        if ($horaInicio === '') {
            return 'Debe ingresar la hora de inicio.';
        }

        if ($horaFin === '') {
            return 'Debe ingresar la hora de fin.';
        }

        if ($horaInicio >= $horaFin) {
            return 'La hora de inicio debe ser menor que la hora de fin.';
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

        $modelo = new HorarioModelo();

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        if ($buscar !== '') {
            $horarios = $modelo->buscarHorarios($buscar);
        } else {
            $horarios = $modelo->orderBy('id_horario', 'ASC')->findAll();
        }

        return view('horarios/index', [
            'horarios' => $horarios
        ]);
    }

    public function insertar()
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $modelo = new HorarioModelo();

        $dia        = trim($this->request->getPost('dia') ?? '');
        $horaInicio = trim($this->request->getPost('hora_inicio') ?? '');
        $horaFin    = trim($this->request->getPost('hora_fin') ?? '');

        $error = $this->validarDatosHorario($dia, $horaInicio, $horaFin);

        if ($error !== null) {
            return redirect()->to(base_url('/horarios'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeHorario($dia, $horaInicio, $horaFin)) {
            return redirect()->to(base_url('/horarios'))
                ->with('error', 'Ya existe un horario registrado con ese día y rango de horas.')
                ->withInput();
        }

        if ($modelo->existeCruceHorario($dia, $horaInicio, $horaFin)) {
            return redirect()->to(base_url('/horarios'))
                ->with('error', 'El horario se cruza con otro horario registrado para ese día.')
                ->withInput();
        }

        $modelo->insert([
            'dia'         => $dia,
            'hora_inicio' => $horaInicio,
            'hora_fin'    => $horaFin,
            'estado'      => 1
        ]);

        return redirect()->to(base_url('/horarios'))
            ->with('success', 'Horario registrado correctamente.');
    }

    public function actualizar($id = null)
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/horarios'))
                ->with('error', 'ID de horario no válido.');
        }

        $modelo = new HorarioModelo();

        if (!$modelo->find($id)) {
            return redirect()->to(base_url('/horarios'))
                ->with('error', 'Horario no encontrado.');
        }

        $dia        = trim($this->request->getPost('dia') ?? '');
        $horaInicio = trim($this->request->getPost('hora_inicio') ?? '');
        $horaFin    = trim($this->request->getPost('hora_fin') ?? '');

        $error = $this->validarDatosHorario($dia, $horaInicio, $horaFin);

        if ($error !== null) {
            return redirect()->to(base_url('/horarios'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeHorario($dia, $horaInicio, $horaFin, $id)) {
            return redirect()->to(base_url('/horarios'))
                ->with('error', 'Ya existe otro horario registrado con ese día y rango de horas.')
                ->withInput();
        }

        if ($modelo->existeCruceHorario($dia, $horaInicio, $horaFin, $id)) {
            return redirect()->to(base_url('/horarios'))
                ->with('error', 'El horario se cruza con otro horario registrado para ese día.')
                ->withInput();
        }

        $modelo->update($id, [
            'dia'                 => $dia,
            'hora_inicio'         => $horaInicio,
            'hora_fin'            => $horaFin,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/horarios'))
            ->with('success', 'Horario actualizado correctamente.');
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
            return redirect()->to(base_url('/horarios'))
                ->with('error', 'ID de horario no válido.');
        }

        $modelo = new HorarioModelo();

        if (!$modelo->find($id)) {
            return redirect()->to(base_url('/horarios'))
                ->with('error', 'Horario no encontrado.');
        }

        $modelo->update($id, ['estado' => $estado]);

        return redirect()->to(base_url('/horarios'))
            ->with('success', 'Horario ' . $texto . ' correctamente.');
    }
}
