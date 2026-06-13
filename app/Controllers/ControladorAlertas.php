<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AlertaModelo;
class ControladorAlertas extends BaseController
{
     public function index()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (session()->get('rol') !== 'PSICOLOGIA') {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para acceder.');
        }

        $modelo = new AlertaModelo();

        $id_estudiante = $this->request->getGet('id_estudiante');

        $data['id_estudiante'] = $id_estudiante;
        $data['estudiantes'] = $modelo->listarEstudiantes();

        if ($id_estudiante) {
            $data['alertas'] = $modelo->listarAlertasPorEstudiante($id_estudiante);
            $data['resumenNivel'] = $modelo->resumenNivelPorEstudiante($id_estudiante);
            $data['resumenParalelo'] = $modelo->resumenParaleloPorEstudiante($id_estudiante);
            $data['resumenEmocion'] = $modelo->resumenEmocionPorEstudiante($id_estudiante);
            $data['resumenIntencion'] = $modelo->resumenIntencionPorEstudiante($id_estudiante);
        } else {
            $data['alertas'] = $modelo->listarAlertas();
            $data['resumenNivel'] = $modelo->resumenPorNivel();
            $data['resumenParalelo'] = $modelo->resumenPorParalelo();
            $data['resumenEmocion'] = $modelo->resumenPorEmocion();
            $data['resumenIntencion'] = $modelo->resumenPorIntencion();
        }

        return view('alertas/index', $data);
    }
}
