<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PsicologoModelo;
    
class ControladorPsicologos extends BaseController
{
     protected $psicologoModelo;

    public function __construct()
    {
        $this->psicologoModelo = new PsicologoModelo();
    }

    private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarAccesoModulo()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para acceder a este módulo.');
        }

        return null;
    }

    private function validarAccionAdministrativo()
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        return null;
    }

    private function validarDatosPsicologo($nombres, $apellidos, $telefono, $correo)
    {
        if ($nombres === '') return 'Los nombres son obligatorios.';
        if (mb_strlen($nombres) < 2) return 'Los nombres deben tener al menos 2 caracteres.';
        if (mb_strlen($nombres) > 50) return 'Los nombres no deben superar los 50 caracteres.';
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $nombres)) return 'Los nombres solo deben contener letras y espacios.';

        if ($apellidos === '') return 'Los apellidos son obligatorios.';
        if (mb_strlen($apellidos) < 2) return 'Los apellidos deben tener al menos 2 caracteres.';
        if (mb_strlen($apellidos) > 50) return 'Los apellidos no deben superar los 50 caracteres.';
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $apellidos)) return 'Los apellidos solo deben contener letras y espacios.';

        if ($telefono === '') return 'El teléfono es obligatorio.';
        if (!preg_match('/^[0-9]+$/', $telefono)) return 'El teléfono solo debe contener números.';
        if (mb_strlen($telefono) < 7) return 'El teléfono debe tener al menos 7 dígitos.';
        if (mb_strlen($telefono) > 15) return 'El teléfono no debe superar los 15 dígitos.';

        if ($correo === '') return 'El correo es obligatorio.';
        if (preg_match('/\s/', $correo)) return 'El correo no debe contener espacios.';
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) return 'Debe ingresar un correo válido.';
        if (mb_strlen($correo) > 100) return 'El correo no debe superar los 100 caracteres.';

        if (!preg_match('/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.(com|net|org|edu|bo|com\.bo)$/i', $correo)) {
            return 'El correo debe tener una terminación válida, por ejemplo: .com, .net, .org, .edu, .bo o .com.bo.';
        }

        return null;
    }

    private function obtenerDatosFormulario()
    {
        return [
            'nombres'   => trim($this->request->getPost('nombres') ?? ''),
            'apellidos' => trim($this->request->getPost('apellidos') ?? ''),
            'telefono'  => trim($this->request->getPost('telefono') ?? ''),
            'correo'    => strtolower(trim($this->request->getPost('correo') ?? ''))
        ];
    }

    private function redirigirErrorInsertar($mensaje)
    {
        return redirect()->to(base_url('/psicologos#modal-insertar-psicologo'))
            ->with('error_formulario', $mensaje)
            ->with('modal_formulario', 'insertar')
            ->withInput();
    }

    private function redirigirErrorEditar($id, $mensaje)
    {
        return redirect()->to(base_url('/psicologos#modal-editar-' . $id))
            ->with('error_formulario', $mensaje)
            ->with('modal_formulario', 'editar')
            ->with('id_modal_formulario', $id)
            ->withInput();
    }

    private function obtenerColumnasOrden($orden, $direccion, $buscar, $porPagina)
    {
        $columnas = [
            ['campo' => 'id_psicologo', 'texto' => '🆔 ID'],
            ['campo' => 'nombres', 'texto' => '👤 Nombres'],
            ['campo' => 'apellidos', 'texto' => '👥 Apellidos'],
            ['campo' => 'telefono', 'texto' => '📞 Teléfono'],
            ['campo' => 'correo', 'texto' => '✉️ Correo'],
            ['campo' => 'bloqueado_activacion', 'texto' => '🔐 Usuario vinculado'],
            ['campo' => 'estado', 'texto' => '📌 Estado'],
            ['campo' => 'fecha_creacion', 'texto' => '📅 Fecha de creación']
        ];

        foreach ($columnas as &$columna) {
            $nuevaDireccion = ($orden === $columna['campo'] && $direccion === 'asc') ? 'desc' : 'asc';

            $flecha = '↕';

            if ($orden === $columna['campo']) {
                $flecha = $direccion === 'asc' ? '▲' : '▼';
            }

            $columna['flecha'] = $flecha;
            $columna['url'] = base_url('/psicologos?' . http_build_query([
                'buscar' => $buscar,
                'por_pagina' => $porPagina,
                'pagina' => 1,
                'orden' => $columna['campo'],
                'direccion' => $nuevaDireccion
            ]));
        }

        return $columnas;
    }

    public function index()
    {
        $acceso = $this->validarAccesoModulo();

        if ($acceso !== null) {
            return $acceso;
        }

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        $porPagina = (int)($this->request->getGet('por_pagina') ?? 10);
        $pagina = (int)($this->request->getGet('pagina') ?? 1);

        if (!in_array($porPagina, [10, 25, 50, 100])) {
            $porPagina = 10;
        }

        if ($pagina < 1) {
            $pagina = 1;
        }

        $columnasPermitidas = [
            'id_psicologo',
            'nombres',
            'apellidos',
            'telefono',
            'correo',
            'bloqueado_activacion',
            'estado',
            'fecha_creacion'
        ];

        $orden = $this->request->getGet('orden') ?? 'id_psicologo';
        $direccion = strtolower($this->request->getGet('direccion') ?? 'desc');

        if (!in_array($orden, $columnasPermitidas)) {
            $orden = 'id_psicologo';
        }

        if (!in_array($direccion, ['asc', 'desc'])) {
            $direccion = 'desc';
        }

        $totalRegistros = $this->psicologoModelo->contarPsicologos($buscar);
        $totalPaginas = (int)ceil($totalRegistros / $porPagina);

        if ($totalPaginas > 0 && $pagina > $totalPaginas) {
            $pagina = $totalPaginas;
        }

        $offset = ($pagina - 1) * $porPagina;

        $psicologos = $this->psicologoModelo->listarPsicologosPaginado(
            $buscar,
            $porPagina,
            $offset,
            $orden,
            $direccion
        );

        $desde = $totalRegistros > 0 ? $offset + 1 : 0;
        $hasta = min($offset + $porPagina, $totalRegistros);

        $columnasOrden = $this->obtenerColumnasOrden($orden, $direccion, $buscar, $porPagina);

        return view('psicologos/index', [
            'psicologos' => $psicologos,
            'buscar' => $buscar,
            'porPagina' => $porPagina,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalRegistros' => $totalRegistros,
            'desde' => $desde,
            'hasta' => $hasta,
            'orden' => $orden,
            'direccion' => $direccion,
            'columnasOrden' => $columnasOrden
        ]);
    }

    public function insertar()
    {
        $acceso = $this->validarAccionAdministrativo();

        if ($acceso !== null) {
            return $acceso;
        }

        $datos = $this->obtenerDatosFormulario();

        $error = $this->validarDatosPsicologo(
            $datos['nombres'],
            $datos['apellidos'],
            $datos['telefono'],
            $datos['correo']
        );

        if ($error !== null) {
            return $this->redirigirErrorInsertar($error);
        }

        if ($this->psicologoModelo->existeCorreo($datos['correo'])) {
            return $this->redirigirErrorInsertar('Ya existe un psicólogo registrado con ese correo.');
        }

        if ($this->psicologoModelo->existeNombreCompleto($datos['nombres'], $datos['apellidos'])) {
            return $this->redirigirErrorInsertar('Ya existe un psicólogo registrado con ese nombre completo.');
        }

        $this->psicologoModelo->insert([
            'id_usuario' => null,
            'nombres'   => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'telefono'  => $datos['telefono'],
            'correo'    => $datos['correo'],
            'estado'    => 1
        ]);

        return redirect()->to(base_url('/psicologos'))
            ->with('success', 'Psicólogo registrado correctamente.');
    }

    public function actualizar($id = null)
    {
        $acceso = $this->validarAccionAdministrativo();

        if ($acceso !== null) {
            return $acceso;
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'ID de psicólogo no válido.');
        }

        $psicologo = $this->psicologoModelo->find($id);

        if (!$psicologo) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'Psicólogo no encontrado.');
        }

        $datos = $this->obtenerDatosFormulario();

        $error = $this->validarDatosPsicologo(
            $datos['nombres'],
            $datos['apellidos'],
            $datos['telefono'],
            $datos['correo']
        );

        if ($error !== null) {
            return $this->redirigirErrorEditar($id, $error);
        }

        if ($this->psicologoModelo->existeCorreo($datos['correo'], $id)) {
            return $this->redirigirErrorEditar($id, 'Ya existe otro psicólogo registrado con ese correo.');
        }

        if ($this->psicologoModelo->existeNombreCompleto($datos['nombres'], $datos['apellidos'], $id)) {
            return $this->redirigirErrorEditar($id, 'Ya existe otro psicólogo registrado con ese nombre completo.');
        }

        $this->psicologoModelo->update($id, [
            'nombres'              => $datos['nombres'],
            'apellidos'            => $datos['apellidos'],
            'telefono'             => $datos['telefono'],
            'correo'               => $datos['correo'],
            'bloqueado_activacion' => $this->request->getPost('bloqueado_activacion') ? 0 : $this->request->getPost('bloqueado_actual')
        ]);

        return redirect()->to(base_url('/psicologos'))
            ->with('success', 'Psicólogo actualizado correctamente.');
    }

    public function activar($id = null)
    {
        return $this->cambiarEstado($id, 1, 'activado');
    }

    public function desactivar($id = null)
    {
        return $this->cambiarEstado($id, 0, 'desactivado');
    }

    private function cambiarEstado($id, $estado, $texto)
    {
        $acceso = $this->validarAccionAdministrativo();

        if ($acceso !== null) {
            return $acceso;
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'ID de psicólogo no válido.');
        }

        $actualizado = $this->psicologoModelo->cambiarEstadoPsicologoYUsuario($id, $estado);

        if (!$actualizado) {
            return redirect()->to(base_url('/psicologos'))
                ->with('error', 'Psicólogo no encontrado.');
        }

        return redirect()->to(base_url('/psicologos'))
            ->with('success', 'Psicólogo ' . $texto . ' correctamente.');
    }
}
