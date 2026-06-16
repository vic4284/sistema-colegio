<?php

namespace App\Models;

use CodeIgniter\Model;

class ParaleloModelo extends Model
{
   protected $table = 'paralelos';
    protected $primaryKey = 'id_paralelo';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_grado',
        'id_seccion',
        'estado',
        'fecha_actualizacion'
    ];

    public function listarParalelos()
    {
        return $this->select('
                paralelos.*,
                grados.nombre_grado,
                secciones.nombre_seccion,
                niveles.nombre_nivel
            ')
            ->join('grados', 'grados.id_grado = paralelos.id_grado')
            ->join('niveles', 'niveles.id_nivel = grados.id_nivel')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion')
            ->orderBy('paralelos.id_paralelo', 'ASC')
            ->findAll();
    }

    public function buscarParalelos($buscar)
    {
        $buscar = trim($buscar);
        $buscarMinuscula = strtolower($buscar);

        $builder = $this->builder();

        $builder->select('
                paralelos.*,
                grados.nombre_grado,
                secciones.nombre_seccion,
                niveles.nombre_nivel
            ')
            ->join('grados', 'grados.id_grado = paralelos.id_grado')
            ->join('niveles', 'niveles.id_nivel = grados.id_nivel')
            ->join('secciones', 'secciones.id_seccion = paralelos.id_seccion');

        $builder->groupStart();

        $builder->like('paralelos.id_paralelo', $buscar)
            ->orLike('niveles.nombre_nivel', $buscar)
            ->orLike('grados.nombre_grado', $buscar)
            ->orLike('secciones.nombre_seccion', $buscar)
            ->orLike('paralelos.fecha_creacion', $buscar);

        if (
            $buscarMinuscula === 'inactivo' ||
            $buscarMinuscula === 'inact' ||
            $buscarMinuscula === 'activar'
        ) {
            $builder->orWhere('paralelos.estado', 0);
        } elseif (
            $buscarMinuscula === 'activo' ||
            $buscarMinuscula === 'activ' ||
            $buscarMinuscula === 'desactivar'
        ) {
            $builder->orWhere('paralelos.estado', 1);
        }

        if ($buscarMinuscula === 'editar') {
            $builder->orWhere('paralelos.id_paralelo IS NOT NULL');
        }

        $builder->groupEnd();

        return $builder->orderBy('paralelos.id_paralelo', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function existeParalelo($idGrado, $idSeccion, $idParalelo = null)
    {
        $builder = $this->where('id_grado', $idGrado)
            ->where('id_seccion', $idSeccion);

        if ($idParalelo !== null) {
            $builder->where('id_paralelo !=', $idParalelo);
        }

        return $builder->first();
    }

    public function obtenerCombinacionesUsadas($idParaleloExcluir = null)
    {
        $builder = $this->select('id_grado, id_seccion');

        if ($idParaleloExcluir !== null) {
            $builder->where('id_paralelo !=', $idParaleloExcluir);
        }

        $resultados = $builder->findAll();

        $usadas = [];

        foreach ($resultados as $fila) {
            $usadas[] = $fila['id_grado'] . '-' . $fila['id_seccion'];
        }

        return $usadas;
    }
}
