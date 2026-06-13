<?php

namespace App\Models;

use CodeIgniter\Model;

class PsicologoModelo extends Model
{
    protected $table = 'psicologos';
    protected $primaryKey = 'id_psicologo';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_usuario',
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'numero_registro',
        'estado',
         'bloqueado_activacion'
    ];

    public function cambiarEstadoPsicologoYUsuario($idPsicologo, $estado)
{
    $psicologo = $this->find($idPsicologo);

    if (!$psicologo) {
        return false;
    }

    $this->update($idPsicologo, [
        'estado' => $estado
    ]);

    if (!empty($psicologo['id_usuario'])) {
        $db = \Config\Database::connect();

        $db->table('usuarios')
           ->where('id_usuario', $psicologo['id_usuario'])
           ->update([
               'estado' => $estado
           ]);
    }

    return true;
}
public function buscarPsicologos($buscar)
{
    $buscar = trim($buscar);
    $buscarMinuscula = strtolower($buscar);

    $builder = $this->builder();

    $builder->groupStart();

        $builder->like('id_psicologo', $buscar)
                ->orLike('nombres', $buscar)
                ->orLike('apellidos', $buscar)
                ->orLike('telefono', $buscar)
                ->orLike('correo', $buscar)
                ->orLike('numero_registro', $buscar)
                ->orLike('fecha_creacion', $buscar);

        if (
            $buscarMinuscula === 'bloqueado' ||
            $buscarMinuscula === 'bloq'
        ) {
            $builder->orWhere('bloqueado_activacion', 1);
        }

        if (
            $buscarMinuscula === 'pendiente' ||
            $buscarMinuscula === 'pend'
        ) {
            $builder->orWhere('id_usuario', null)
                    ->where('bloqueado_activacion', 0);
        }

        if (
            $buscarMinuscula === 'cuenta activada' ||
            $buscarMinuscula === 'activada'
        ) {
            $builder->orWhere('id_usuario IS NOT NULL')
                    ->where('bloqueado_activacion', 0);
        }

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
            $builder->orWhere('id_psicologo IS NOT NULL');
        }

    $builder->groupEnd();

    return $builder->orderBy('id_psicologo', 'DESC')
                   ->get()
                   ->getResultArray();
}


public function existeCorreo($correo, $idPsicologo = null)
{
    $builder = $this->where('correo', $correo);

    if ($idPsicologo !== null) {
        $builder->where('id_psicologo !=', $idPsicologo);
    }

    return $builder->first();
}

public function existeNombreCompleto($nombres, $apellidos, $idPsicologo = null)
{
    $builder = $this->where('nombres', $nombres)
                    ->where('apellidos', $apellidos);

    if ($idPsicologo !== null) {
        $builder->where('id_psicologo !=', $idPsicologo);
    }

    return $builder->first();
}

public function existeNumeroRegistro($numeroRegistro, $idPsicologo = null)
{
    $builder = $this->where('numero_registro', $numeroRegistro);

    if ($idPsicologo !== null) {
        $builder->where('id_psicologo !=', $idPsicologo);
    }

    return $builder->first();
}
}
