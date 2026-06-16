<?php

namespace App\Models;

use CodeIgniter\Model;

class AulaModelo extends Model
{
   protected $table = 'aulas';
    protected $primaryKey = 'id_aula';
    protected $returnType = 'array';

    protected $allowedFields = [
        'nombre_aula',
        'capacidad',
        'estado',
        'fecha_actualizacion'
    ];

    public function buscarAulas($buscar)
    {
        $buscar = trim($buscar);
        $buscarMinuscula = strtolower($buscar);

        $builder = $this->builder();

        $builder->groupStart();

            $builder->like('id_aula', $buscar)
                    ->orLike('nombre_aula', $buscar)
                    ->orLike('capacidad', $buscar)
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
                $builder->orWhere('id_aula IS NOT NULL');
            }

        $builder->groupEnd();

        return $builder->orderBy('id_aula', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function existeAula($nombreAula, $idAula = null)
    {
        $builder = $this->where('nombre_aula', $nombreAula);

        if ($idAula !== null) {
            $builder->where('id_aula !=', $idAula);
        }

        return $builder->first();
    }
}
