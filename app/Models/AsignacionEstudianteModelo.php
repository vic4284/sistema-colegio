<?php

namespace App\Models;

use CodeIgniter\Model;

class AsignacionEstudianteModelo extends Model
{
   protected $table = 'asignacion_estudiante';
    protected $primaryKey = 'id_asignacion_estudiante';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_estudiante',
        'id_paralelo',
        'id_gestion',
        'estado',
        'fecha_actualizacion'
    ];

    public function listarAsignaciones()
    {
        return $this->select('
                asignacion_estudiante.*,
                estudiantes.nombres,
                estudiantes.apellidos,
                estudiantes.correo,
                niveles.nombre_nivel,
                grados.nombre_grado,
                secciones.nombre_seccion,
                gestiones.nombre_gestion
            ')
            ->join('estudiantes', 'estudiantes.id_estudiante = asignacion_estudiante.id_estudiante')
            ->join('paralelos', 'paralelos.id_paralelo = asignacion_estudiante.id_paralelo')
            ->join('grados', 'grados.id_grado = paralelos.id_grado')
            ->join('niveles', 'niveles.id_nivel = grados.id_nivel')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion')
            ->join('gestiones', 'gestiones.id_gestion = asignacion_estudiante.id_gestion')
            ->orderBy('asignacion_estudiante.id_asignacion_estudiante', 'ASC')
            ->findAll();
    }

    public function buscarAsignaciones($buscar)
    {
        $buscar = trim($buscar);
        $buscarMinuscula = strtolower($buscar);

        $builder = $this->builder();

        $builder->select('
                asignacion_estudiante.*,
                estudiantes.nombres,
                estudiantes.apellidos,
                estudiantes.correo,
                niveles.nombre_nivel,
                grados.nombre_grado,
                secciones.nombre_seccion,
                gestiones.nombre_gestion
            ')
            ->join('estudiantes', 'estudiantes.id_estudiante = asignacion_estudiante.id_estudiante')
            ->join('paralelos', 'paralelos.id_paralelo = asignacion_estudiante.id_paralelo')
            ->join('grados', 'grados.id_grado = paralelos.id_grado')
            ->join('niveles', 'niveles.id_nivel = grados.id_nivel')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion')
            ->join('gestiones', 'gestiones.id_gestion = asignacion_estudiante.id_gestion');

        $builder->groupStart();

            $builder->like('asignacion_estudiante.id_asignacion_estudiante', $buscar)
                    ->orLike('estudiantes.nombres', $buscar)
                    ->orLike('estudiantes.apellidos', $buscar)
                    ->orLike('estudiantes.correo', $buscar)
                    ->orLike('niveles.nombre_nivel', $buscar)
                    ->orLike('grados.nombre_grado', $buscar)
                    ->orLike('secciones.nombre_seccion', $buscar)
                    ->orLike('gestiones.nombre_gestion', $buscar)
                    ->orLike('asignacion_estudiante.fecha_creacion', $buscar);

            if (
                $buscarMinuscula === 'inactivo' ||
                $buscarMinuscula === 'inact' ||
                $buscarMinuscula === 'activar'
            ) {
                $builder->orWhere('asignacion_estudiante.estado', 0);
            } elseif (
                $buscarMinuscula === 'activo' ||
                $buscarMinuscula === 'activ' ||
                $buscarMinuscula === 'desactivar'
            ) {
                $builder->orWhere('asignacion_estudiante.estado', 1);
            }

            if ($buscarMinuscula === 'editar') {
                $builder->orWhere('asignacion_estudiante.id_asignacion_estudiante IS NOT NULL');
            }

        $builder->groupEnd();

        return $builder->orderBy('asignacion_estudiante.id_asignacion_estudiante', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    public function obtenerHorarioPorAsignacionEstudiante($idAsignacionEstudiante)
    {
        return $this->db->table('asignacion_estudiante ae')
            ->select('
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                m.nombre_materia,
                p.nombres,
                p.apellidos,
                a.nombre_aula
            ')
            ->join('asignacion_profesor ap', 'ap.id_paralelo = ae.id_paralelo AND ap.id_gestion = ae.id_gestion')
            ->join('horarios h', 'h.id_horario = ap.id_horario')
            ->join('materias m', 'm.id_materia = ap.id_materia')
            ->join('profesores p', 'p.id_profesor = ap.id_profesor')
            ->join('aulas a', 'a.id_aula = ap.id_aula')
            ->where('ae.id_asignacion_estudiante', $idAsignacionEstudiante)
            ->where('ae.estado', 1)
            ->where('ap.estado', 1)
            ->orderBy('h.hora_inicio', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function listarEstudiantesActivos()
    {
        return $this->db->table('estudiantes')
            ->where('estado', 1)
            ->orderBy('id_estudiante', 'ASC')
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

    public function listarGestionesActivas()
    {
        return $this->db->table('gestiones')
            ->where('estado', 1)
            ->orderBy('id_gestion', 'ASC')
            ->get()
            ->getResultArray();
    }

    //ELIMINA NOTAS DEL ESTUDIANTE SI EL ADMINISTRADOR CAMBIA DE PARALELO EN LA ASIGNACION DEL ESTUDIANTE
    public function eliminarNotasPorAsignacionEstudiante($idAsignacionEstudiante)
{
    return $this->db->table('notas')
        ->where('id_asignacion_estudiante', $idAsignacionEstudiante)
        ->delete();
}
}
