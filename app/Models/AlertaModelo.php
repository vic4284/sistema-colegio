<?php

namespace App\Models;

use CodeIgniter\Model;

class AlertaModelo extends Model
{
 protected $table = 'analisis_emociones';
    protected $primaryKey = 'id_analisis';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_estudiante',
        'id_emocion',
        'id_nivel_alerta',
        'intencion_detectada',
        'nivel_emocional',
        'puntaje_confianza',
        'recomendacion',
        'estado_seguimiento',
        'fecha_analisis'
    ];

    private function consultaBase()
    {
        return $this->select("
                analisis_emociones.id_analisis,
                estudiantes.nombres,
                estudiantes.apellidos,
                CONCAT(grados.nombre_grado, ' ', secciones.nombre_seccion) AS nombre_paralelo,
                emociones.nombre_emocion,
                niveles_alerta.nombre_nivel,
                analisis_emociones.intencion_detectada,
                analisis_emociones.nivel_emocional,
                analisis_emociones.puntaje_confianza,
                analisis_emociones.recomendacion,
                analisis_emociones.estado_seguimiento,
                analisis_emociones.fecha_analisis
            ")
            ->join('estudiantes', 'estudiantes.id_estudiante = analisis_emociones.id_estudiante')
            ->join('asignacion_estudiante', 'asignacion_estudiante.id_estudiante = estudiantes.id_estudiante', 'left')
            ->join('paralelos', 'paralelos.id_paralelo = asignacion_estudiante.id_paralelo', 'left')
            ->join('grados', 'grados.id_grado = paralelos.id_grado', 'left')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion', 'left')
            ->join('emociones', 'emociones.id_emocion = analisis_emociones.id_emocion')
            ->join('niveles_alerta', 'niveles_alerta.id_nivel_alerta = analisis_emociones.id_nivel_alerta');
    }

    public function listarAlertas()
    {
        return $this->consultaBase()
            ->orderBy('analisis_emociones.id_analisis', 'DESC')
            ->findAll();
    }

    public function listarAlertasPorEstudiante($id)
    {
        return $this->consultaBase()
            ->where('analisis_emociones.id_estudiante', $id)
            ->orderBy('analisis_emociones.id_analisis', 'DESC')
            ->findAll();
    }

    public function listarEstudiantes()
    {
        return $this->db->table('estudiantes')
            ->select('id_estudiante, nombres, apellidos')
            ->where('estado', 1)
            ->orderBy('nombres', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function resumenPorNivel()
    {
        return $this->select('analisis_emociones.nivel_emocional AS nombre, COUNT(*) AS total')
            ->groupBy('analisis_emociones.nivel_emocional')
            ->orderBy('total', 'DESC')
            ->findAll();
    }

    public function resumenNivelPorEstudiante($id)
    {
        return $this->select('analisis_emociones.nivel_emocional AS nombre, COUNT(*) AS total')
            ->where('analisis_emociones.id_estudiante', $id)
            ->groupBy('analisis_emociones.nivel_emocional')
            ->orderBy('total', 'DESC')
            ->findAll();
    }

    public function resumenPorParalelo()
    {
        return $this->select("
                CONCAT(grados.nombre_grado, ' ', secciones.nombre_seccion) AS nombre,
                COUNT(*) AS total
            ")
            ->join('estudiantes', 'estudiantes.id_estudiante = analisis_emociones.id_estudiante')
            ->join('asignacion_estudiante', 'asignacion_estudiante.id_estudiante = estudiantes.id_estudiante', 'left')
            ->join('paralelos', 'paralelos.id_paralelo = asignacion_estudiante.id_paralelo', 'left')
            ->join('grados', 'grados.id_grado = paralelos.id_grado', 'left')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion', 'left')
            ->groupBy('paralelos.id_paralelo')
            ->findAll();
    }

    public function resumenParaleloPorEstudiante($id)
    {
        return $this->select("
                CONCAT(grados.nombre_grado, ' ', secciones.nombre_seccion) AS nombre,
                COUNT(*) AS total
            ")
            ->join('estudiantes', 'estudiantes.id_estudiante = analisis_emociones.id_estudiante')
            ->join('asignacion_estudiante', 'asignacion_estudiante.id_estudiante = estudiantes.id_estudiante', 'left')
            ->join('paralelos', 'paralelos.id_paralelo = asignacion_estudiante.id_paralelo', 'left')
            ->join('grados', 'grados.id_grado = paralelos.id_grado', 'left')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion', 'left')
            ->where('analisis_emociones.id_estudiante', $id)
            ->groupBy('paralelos.id_paralelo')
            ->findAll();
    }

    public function resumenPorEmocion()
    {
        return $this->select('emociones.nombre_emocion AS nombre, COUNT(*) AS total')
            ->join('emociones', 'emociones.id_emocion = analisis_emociones.id_emocion')
            ->groupBy('emociones.nombre_emocion')
            ->orderBy('total', 'DESC')
            ->findAll();
    }

    public function resumenEmocionPorEstudiante($id)
    {
        return $this->select('emociones.nombre_emocion AS nombre, COUNT(*) AS total')
            ->join('emociones', 'emociones.id_emocion = analisis_emociones.id_emocion')
            ->where('analisis_emociones.id_estudiante', $id)
            ->groupBy('emociones.nombre_emocion')
            ->orderBy('total', 'DESC')
            ->findAll();
    }

    public function resumenPorIntencion()
    {
        return $this->select('analisis_emociones.intencion_detectada AS nombre, COUNT(*) AS total')
            ->where('analisis_emociones.intencion_detectada IS NOT NULL')
            ->groupBy('analisis_emociones.intencion_detectada')
            ->orderBy('total', 'DESC')
            ->findAll();
    }

    public function resumenIntencionPorEstudiante($id)
    {
        return $this->select('analisis_emociones.intencion_detectada AS nombre, COUNT(*) AS total')
            ->where('analisis_emociones.id_estudiante', $id)
            ->where('analisis_emociones.intencion_detectada IS NOT NULL')
            ->groupBy('analisis_emociones.intencion_detectada')
            ->orderBy('total', 'DESC')
            ->findAll();
    }
}
