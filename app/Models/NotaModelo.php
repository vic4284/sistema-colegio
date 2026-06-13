<?php

namespace App\Models;

use CodeIgniter\Model;

class NotaModelo extends Model
{
      protected $table = 'notas';
    protected $primaryKey = 'id_nota';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_asignacion',
        'id_asignacion_estudiante',
        'primer_trimestre',
        'segundo_trimestre',
        'tercer_trimestre',
        'promedio',
        'observacion',
        'estado',
        'fecha_actualizacion'
    ];

    public function listarNotasProfesor($idUsuario)
    {
        return $this->select('
                notas.*,
                estudiantes.nombres AS estudiante_nombres,
                estudiantes.apellidos AS estudiante_apellidos,
                materias.nombre_materia,
                niveles.nombre_nivel,
                grados.nombre_grado,
                secciones.nombre_seccion,
                gestiones.nombre_gestion
            ')
            ->join('asignacion_profesor ap', 'ap.id_asignacion = notas.id_asignacion')
            ->join('profesores', 'profesores.id_profesor = ap.id_profesor')
            ->join('materias', 'materias.id_materia = ap.id_materia')
            ->join('asignacion_estudiante ae', 'ae.id_asignacion_estudiante = notas.id_asignacion_estudiante')
            ->join('estudiantes', 'estudiantes.id_estudiante = ae.id_estudiante')
            ->join('paralelos', 'paralelos.id_paralelo = ap.id_paralelo')
            ->join('grados', 'grados.id_grado = paralelos.id_grado')
            ->join('niveles', 'niveles.id_nivel = grados.id_nivel')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion')
            ->join('gestiones', 'gestiones.id_gestion = ap.id_gestion')
            ->where('profesores.id_usuario', $idUsuario)
            
            ->where('ap.id_paralelo = ae.id_paralelo')
            ->where('ap.id_gestion = ae.id_gestion')
            ->orderBy('notas.id_nota', 'DESC')
            ->findAll();
    }

    public function buscarNotasProfesor($idUsuario, $buscar)
    {
        $buscar = trim($buscar);

        $builder = $this->builder();

        $builder->select('
                notas.*,
                estudiantes.nombres AS estudiante_nombres,
                estudiantes.apellidos AS estudiante_apellidos,
                materias.nombre_materia,
                niveles.nombre_nivel,
                grados.nombre_grado,
                secciones.nombre_seccion,
                gestiones.nombre_gestion
            ')
            ->join('asignacion_profesor ap', 'ap.id_asignacion = notas.id_asignacion')
            ->join('profesores', 'profesores.id_profesor = ap.id_profesor')
            ->join('materias', 'materias.id_materia = ap.id_materia')
            ->join('asignacion_estudiante ae', 'ae.id_asignacion_estudiante = notas.id_asignacion_estudiante')
            ->join('estudiantes', 'estudiantes.id_estudiante = ae.id_estudiante')
            ->join('paralelos', 'paralelos.id_paralelo = ap.id_paralelo')
            ->join('grados', 'grados.id_grado = paralelos.id_grado')
            ->join('niveles', 'niveles.id_nivel = grados.id_nivel')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion')
            ->join('gestiones', 'gestiones.id_gestion = ap.id_gestion')
            ->where('profesores.id_usuario', $idUsuario)
            ->groupStart()
                ->like('estudiantes.nombres', $buscar)
                ->orLike('estudiantes.apellidos', $buscar)
                ->orLike('materias.nombre_materia', $buscar)
                ->orLike('niveles.nombre_nivel', $buscar)
                ->orLike('grados.nombre_grado', $buscar)
                ->orLike('secciones.nombre_seccion', $buscar)
                ->orLike('gestiones.nombre_gestion', $buscar)
                ->orLike('notas.promedio', $buscar)
                ->orLike('notas.observacion', $buscar)
            ->groupEnd();

        return $builder->orderBy('notas.id_nota', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function listarAsignacionesProfesor($idUsuario)
    {
        return $this->db->table('asignacion_profesor ap')
            ->select('
                ap.id_asignacion,
                materias.nombre_materia,
                niveles.nombre_nivel,
                grados.nombre_grado,
                secciones.nombre_seccion,
                gestiones.nombre_gestion
            ')
            ->join('profesores', 'profesores.id_profesor = ap.id_profesor')
            ->join('materias', 'materias.id_materia = ap.id_materia')
            ->join('paralelos', 'paralelos.id_paralelo = ap.id_paralelo')
            ->join('grados', 'grados.id_grado = paralelos.id_grado')
            ->join('niveles', 'niveles.id_nivel = grados.id_nivel')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion')
            ->join('gestiones', 'gestiones.id_gestion = ap.id_gestion')
            ->where('profesores.id_usuario', $idUsuario)
            ->where('ap.estado', 1)
            ->orderBy('ap.id_asignacion', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function listarAsignacionesEstudiantesCompatibles($idUsuario)
    {
        return $this->db->table('asignacion_estudiante ae')
            ->select('
                ae.id_asignacion_estudiante,
                estudiantes.nombres,
                estudiantes.apellidos,
                niveles.nombre_nivel,
                grados.nombre_grado,
                secciones.nombre_seccion,
                gestiones.nombre_gestion
            ')
            ->join('estudiantes', 'estudiantes.id_estudiante = ae.id_estudiante')
            ->join('paralelos', 'paralelos.id_paralelo = ae.id_paralelo')
            ->join('grados', 'grados.id_grado = paralelos.id_grado')
            ->join('niveles', 'niveles.id_nivel = grados.id_nivel')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion')
            ->join('gestiones', 'gestiones.id_gestion = ae.id_gestion')
            ->join('asignacion_profesor ap', 'ap.id_paralelo = ae.id_paralelo AND ap.id_gestion = ae.id_gestion')
            ->join('profesores', 'profesores.id_profesor = ap.id_profesor')
            ->where('profesores.id_usuario', $idUsuario)
            ->where('ae.estado', 1)
            ->where('ap.estado', 1)
            ->groupBy('ae.id_asignacion_estudiante')
            ->orderBy('estudiantes.apellidos', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function validarCoincidenciaAsignaciones($idAsignacion, $idAsignacionEstudiante)
    {
        $resultado = $this->db->table('asignacion_profesor ap')
            ->select('ap.id_asignacion')
            ->join('asignacion_estudiante ae', 'ae.id_paralelo = ap.id_paralelo AND ae.id_gestion = ap.id_gestion')
            ->where('ap.id_asignacion', $idAsignacion)
            ->where('ae.id_asignacion_estudiante', $idAsignacionEstudiante)
            ->where('ap.estado', 1)
            ->where('ae.estado', 1)
            ->get()
            ->getRowArray();

        return !empty($resultado);
    }
}
