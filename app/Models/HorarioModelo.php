<?php

namespace App\Models;

use CodeIgniter\Model;

class HorarioModelo extends Model
{
   protected $table = 'horarios';
    protected $primaryKey = 'id_horario';
    protected $returnType = 'array';

    protected $allowedFields = [
        'dia',
        'hora_inicio',
        'hora_fin',
        'estado',
        'fecha_actualizacion'
    ];

    public function buscarHorarios($buscar)
    {
        $buscar = trim($buscar);
        $buscarMinuscula = strtolower($buscar);

        $builder = $this->builder();

        $builder->groupStart();

            $builder->like('id_horario', $buscar)
                    ->orLike('dia', $buscar)
                    ->orLike('hora_inicio', $buscar)
                    ->orLike('hora_fin', $buscar)
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
                $builder->orWhere('id_horario IS NOT NULL');
            }

        $builder->groupEnd();

        return $builder->orderBy('id_horario', 'DESC')
                       ->get()
                       ->getResultArray();
    }
}
