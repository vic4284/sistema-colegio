<?php

namespace App\Models;

use CodeIgniter\Model;

class AsignacionProfesorModelo extends Model
{
     protected $table = 'asignacion_profesor';
    protected $primaryKey = 'id_asignacion';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_profesor',
        'id_materia',
        'id_paralelo',
        'id_horario',
        'id_aula',
        'id_gestion',
        'estado',
        'fecha_actualizacion'
    ];

    public function listarAsignaciones()
    {
        return $this->select('
                asignacion_profesor.*,
                profesores.nombres,
                profesores.apellidos,
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
            ->orderBy('asignacion_profesor.id_asignacion', 'ASC')
            ->findAll();
    }

    public function buscarAsignaciones($buscar)
    {
        $buscar = trim($buscar);
        $buscarMinuscula = strtolower($buscar);

        $builder = $this->builder();

        $builder->select('
                asignacion_profesor.*,
                profesores.nombres,
                profesores.apellidos,
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
            ->join('gestiones', 'gestiones.id_gestion = asignacion_profesor.id_gestion');

        $builder->groupStart();

            $builder->like('asignacion_profesor.id_asignacion', $buscar)
                    ->orLike('profesores.nombres', $buscar)
                    ->orLike('profesores.apellidos', $buscar)
                    ->orLike('materias.nombre_materia', $buscar)
                    ->orLike('niveles.nombre_nivel', $buscar)
                    ->orLike('grados.nombre_grado', $buscar)
                    ->orLike('secciones.nombre_seccion', $buscar)
                    ->orLike('horarios.dia', $buscar)
                    ->orLike('horarios.hora_inicio', $buscar)
                    ->orLike('horarios.hora_fin', $buscar)
                    ->orLike('aulas.nombre_aula', $buscar)
                    ->orLike('aulas.capacidad', $buscar)
                    ->orLike('gestiones.nombre_gestion', $buscar)
                    ->orLike('asignacion_profesor.fecha_creacion', $buscar);

            if (
                $buscarMinuscula === 'inactivo' ||
                $buscarMinuscula === 'inact' ||
                $buscarMinuscula === 'activar'
            ) {
                $builder->orWhere('asignacion_profesor.estado', 0);
            } elseif (
                $buscarMinuscula === 'activo' ||
                $buscarMinuscula === 'activ' ||
                $buscarMinuscula === 'desactivar'
            ) {
                $builder->orWhere('asignacion_profesor.estado', 1);
            }

            if ($buscarMinuscula === 'editar') {
                $builder->orWhere('asignacion_profesor.id_asignacion IS NOT NULL');
            }

        $builder->groupEnd();

        return $builder->orderBy('asignacion_profesor.id_asignacion', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    public function obtenerHorarioPorParalelo($idParalelo)
    {
        return $this->select('
                horarios.dia,
                horarios.hora_inicio,
                horarios.hora_fin,
                materias.nombre_materia,
                profesores.nombres,
                profesores.apellidos,
                aulas.nombre_aula
            ')
            ->join('horarios', 'horarios.id_horario = asignacion_profesor.id_horario')
            ->join('materias', 'materias.id_materia = asignacion_profesor.id_materia')
            ->join('profesores', 'profesores.id_profesor = asignacion_profesor.id_profesor')
            ->join('aulas', 'aulas.id_aula = asignacion_profesor.id_aula')
            ->where('asignacion_profesor.id_paralelo', $idParalelo)
            ->where('asignacion_profesor.estado', 1)
            ->orderBy('horarios.hora_inicio', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function listarProfesoresActivos()
    {
        return $this->db->table('profesores')
            ->where('estado', 1)
            ->orderBy('id_profesor', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function listarMateriasActivas()
    {
        return $this->db->table('materias')
            ->where('estado', 1)
            ->orderBy('id_materia', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function listarParalelosActivos()
    {
        return $this->db->table('paralelos p')
            ->select('
                p.id_paralelo,
                niveles.nombre_nivel,
                grados.nombre_grado,
                secciones.nombre_seccion
            ')
            ->join('grados', 'grados.id_grado = p.id_grado')
            ->join('niveles', 'niveles.id_nivel = grados.id_nivel')
            ->join('secciones', 'secciones.id_seccion = p.id_seccion')
            ->where('p.estado', 1)
            ->orderBy('niveles.id_nivel', 'ASC')
            ->orderBy('grados.id_grado', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function listarHorariosActivos()
    {
        return $this->db->table('horarios')
            ->where('estado', 1)
            ->orderBy('id_horario', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function listarAulasActivas()
    {
        return $this->db->table('aulas')
            ->where('estado', 1)
            ->orderBy('id_aula', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function listarGestionesActivas()
    {
        return $this->db->table('gestiones')
            ->where('estado', 1)
            ->orderBy('id_gestion', 'ASC')
            ->get()
            ->getResultArray();
    }
}
