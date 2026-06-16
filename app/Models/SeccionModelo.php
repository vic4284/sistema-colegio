<?php

namespace App\Models;

use CodeIgniter\Model;

class SeccionModelo extends Model
{
     protected $table = 'secciones';
    protected $primaryKey = 'id_seccion';
    protected $returnType = 'array';

    protected $allowedFields = [
        'nombre_seccion',
        'estado',
        'fecha_actualizacion'
    ];

    public function buscarSecciones($buscar)
    {
        $buscar = trim($buscar);
        $buscarMinuscula = strtolower($buscar);

        $builder = $this->builder();

        $builder->groupStart();

            $builder->like('id_seccion', $buscar)
                    ->orLike('nombre_seccion', $buscar)
                    ->orLike('fecha_creacion', $buscar);

            if (
                $buscarMinuscula === 'inactivo' ||
                $buscarMinuscula === 'inact' ||
                $buscarMinuscula === 'activar'
            ) {
                $builder->orWhere('estado', 0);
            } elseif (
                $buscarMinuscula === 'activo' ||
                $buscarMinuscula === 'activ' ||
                $buscarMinuscula === 'desactivar'
            ) {
                $builder->orWhere('estado', 1);
            }

            if ($buscarMinuscula === 'editar') {
                $builder->orWhere('id_seccion IS NOT NULL');
            }

        $builder->groupEnd();

        return $builder->orderBy('id_seccion', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    public function existeSeccion($nombreSeccion, $idSeccion = null)
    {
        $builder = $this->where('nombre_seccion', $nombreSeccion);

        if ($idSeccion !== null) {
            $builder->where('id_seccion !=', $idSeccion);
        }

        return $builder->first();
    }
}
