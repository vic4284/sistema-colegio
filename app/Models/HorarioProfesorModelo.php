<?php

namespace App\Models;

use CodeIgniter\Model;

class HorarioProfesorModelo extends Model
{
     protected $table = 'asignacion_profesor';
    protected $primaryKey = 'id_asignacion';
    protected $returnType = 'array';

    public function listarHorariosProfesor($idUsuario)
    {
        return $this->select('
                asignacion_profesor.*,
                materias.nombre_materia,
                niveles.nombre_nivel,
                grados.nombre_grado,
                secciones.nombre_seccion,
                horarios.dia,
                horarios.hora_inicio,
                horarios.hora_fin,
                aulas.nombre_aula,
                aulas.capacidad,
                gestiones.nombre_gestion
            ')
            ->join('profesores', 'profesores.id_profesor = asignacion_profesor.id_profesor')
            ->join('materias', 'materias.id_materia = asignacion_profesor.id_materia')
            ->join('paralelos', 'paralelos.id_paralelo = asignacion_profesor.id_paralelo')
            ->join('grados', 'grados.id_grado = paralelos.id_grado')
            ->join('niveles', 'niveles.id_nivel = grados.id_nivel')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion')
            ->join('horarios', 'horarios.id_horario = asignacion_profesor.id_horario')
            ->join('aulas', 'aulas.id_aula = asignacion_profesor.id_aula')
            ->join('gestiones', 'gestiones.id_gestion = asignacion_profesor.id_gestion')
            ->where('profesores.id_usuario', $idUsuario)
            ->where('asignacion_profesor.estado', 1)
            ->orderBy('horarios.id_horario', 'ASC')
            ->findAll();
    }

    public function buscarHorariosProfesor($idUsuario, $buscar)
    {
        $builder = $this->builder();

        $builder->select('
                asignacion_profesor.*,
                materias.nombre_materia,
                niveles.nombre_nivel,
                grados.nombre_grado,
                secciones.nombre_seccion,
                horarios.dia,
                horarios.hora_inicio,
                horarios.hora_fin,
                aulas.nombre_aula,
                aulas.capacidad,
                gestiones.nombre_gestion
            ')
            ->join('profesores', 'profesores.id_profesor = asignacion_profesor.id_profesor')
            ->join('materias', 'materias.id_materia = asignacion_profesor.id_materia')
            ->join('paralelos', 'paralelos.id_paralelo = asignacion_profesor.id_paralelo')
            ->join('grados', 'grados.id_grado = paralelos.id_grado')
            ->join('niveles', 'niveles.id_nivel = grados.id_nivel')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion')
            ->join('horarios', 'horarios.id_horario = asignacion_profesor.id_horario')
            ->join('aulas', 'aulas.id_aula = asignacion_profesor.id_aula')
            ->join('gestiones', 'gestiones.id_gestion = asignacion_profesor.id_gestion')
            ->where('profesores.id_usuario', $idUsuario)
            ->where('asignacion_profesor.estado', 1)
            ->groupStart()
                ->like('materias.nombre_materia', $buscar)
                ->orLike('niveles.nombre_nivel', $buscar)
                ->orLike('grados.nombre_grado', $buscar)
                ->orLike('secciones.nombre_seccion', $buscar)
                ->orLike('horarios.dia', $buscar)
                ->orLike('horarios.hora_inicio', $buscar)
                ->orLike('horarios.hora_fin', $buscar)
                ->orLike('aulas.nombre_aula', $buscar)
                ->orLike('gestiones.nombre_gestion', $buscar)
            ->groupEnd();

        return $builder->orderBy('horarios.id_horario', 'ASC')
                       ->get()
                       ->getResultArray();
    }
}
