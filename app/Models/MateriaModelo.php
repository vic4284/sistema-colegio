<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriaModelo extends Model
{
   protected $table = 'materias';
    protected $primaryKey = 'id_materia';
    protected $returnType = 'array';

    protected $allowedFields = [
        'nombre_materia',
        'descripcion',
        'estado',
        'fecha_actualizacion'
    ];

    public function existeMateria($nombreMateria, $idMateria = null)
    {
        $builder = $this->where('nombre_materia', $nombreMateria);

        if ($idMateria !== null) {
            $builder->where('id_materia !=', $idMateria);
        }

        return $builder->first();
    }
}
