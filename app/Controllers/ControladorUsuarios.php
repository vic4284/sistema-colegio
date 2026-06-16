<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuariosModelo;

class ControladorUsuarios extends BaseController
{
    private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarDatosUsuario($nombreUsuario, $correoElectronico, $imagen = null)
    {
        if ($nombreUsuario === '') {
            return 'El nombre de usuario es obligatorio.';
        }

        if (mb_strlen($nombreUsuario) < 3) {
            return 'El nombre de usuario debe tener al menos 3 caracteres.';
        }

        if (mb_strlen($nombreUsuario) > 50) {
            return 'El nombre de usuario no debe superar los 50 caracteres.';
        }

        if ($correoElectronico === '') {
            return 'El correo electrónico es obligatorio.';
        }

        if (!filter_var($correoElectronico, FILTER_VALIDATE_EMAIL)) {
            return 'Debe ingresar un correo electrónico válido.';
        }

        if (mb_strlen($correoElectronico) > 100) {
            return 'El correo electrónico no debe superar los 100 caracteres.';
        }

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
            $extension = strtolower($imagen->getExtension());
            $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($extension, $permitidas)) {
                return 'Solo se permiten imágenes JPG, JPEG, PNG o WEBP.';
            }

            if ($imagen->getSize() > 2 * 1024 * 1024) {
                return 'La imagen no debe superar los 2 MB.';
            }
        }

        return null;
    }

    public function index()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para acceder a este módulo.');
        }

        $modelo = new UsuariosModelo();

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        if ($buscar !== '') {
            $usuarios = $modelo->buscarUsuarios($buscar);
        } else {
            $usuarios = $modelo->listarUsuarios();
        }

        return view('Usuarios/index', [
            'usuarios' => $usuarios
        ]);
    }

    public function actualizar($idUsuario = null)
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($idUsuario === null || !is_numeric($idUsuario)) {
            return redirect()->to(base_url('/usuarios'))
                ->with('error', 'ID de usuario no válido.');
        }

        $modelo = new UsuariosModelo();
        $usuario = $modelo->obtenerUsuarioPorId($idUsuario);

        if (!$usuario) {
            return redirect()->to(base_url('/usuarios'))
                ->with('error', 'Usuario no encontrado.');
        }

        $nombreUsuario = trim($this->request->getPost('nombre_usuario') ?? '');
        $correoElectronico = trim($this->request->getPost('correo_electronico') ?? '');
        $imagen = $this->request->getFile('imagen');

        $error = $this->validarDatosUsuario($nombreUsuario, $correoElectronico, $imagen);

        if ($error !== null) {
            return redirect()->to(base_url('/usuarios'))
                ->with('error', $error)
                ->withInput();
        }

        if ($modelo->existeNombreUsuario($nombreUsuario, $idUsuario)) {
            return redirect()->to(base_url('/usuarios'))
                ->with('error', 'El nombre de usuario ya está en uso.')
                ->withInput();
        }

        if ($modelo->existeCorreoElectronico($correoElectronico, $idUsuario)) {
            return redirect()->to(base_url('/usuarios'))
                ->with('error', 'El correo electrónico ya está en uso.')
                ->withInput();
        }

        $datosActualizar = [
            'nombre_usuario'       => $nombreUsuario,
            'correo_electronico'   => $correoElectronico,
            'fecha_actualizacion'  => date('Y-m-d H:i:s')
        ];

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
            $nuevoNombre = $imagen->getRandomName();
            $imagen->move(ROOTPATH . 'public/assets/img/usuarios', $nuevoNombre);

            if (!empty($usuario['imagen'])) {
                $rutaAnterior = ROOTPATH . 'public/assets/img/usuarios/' . $usuario['imagen'];

                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior);
                }
            }

            $datosActualizar['imagen'] = $nuevoNombre;
        }

        $modelo->update($idUsuario, $datosActualizar);

        return redirect()->to(base_url('/usuarios'))
            ->with('success', 'Cuenta actualizada correctamente.');
    }

    public function activar($idUsuario = null)
    {
        return $this->cambiarEstado($idUsuario, 1, 'activado');
    }

    public function desactivar($idUsuario = null)
    {
        if ((int)$idUsuario === (int)session()->get('id_usuario')) {
            return redirect()->to(base_url('/usuarios'))
                ->with('error', 'No puede desactivar su propia cuenta.');
        }

        return $this->cambiarEstado($idUsuario, 0, 'desactivado');
    }

    private function cambiarEstado($idUsuario, $estado, $texto)
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($idUsuario === null || !is_numeric($idUsuario)) {
            return redirect()->to(base_url('/usuarios'))
                ->with('error', 'ID de usuario no válido.');
        }

        $modelo = new UsuariosModelo();
        $usuario = $modelo->obtenerUsuarioPorId($idUsuario);

        if (!$usuario) {
            return redirect()->to(base_url('/usuarios'))
                ->with('error', 'Usuario no encontrado.');
        }

        $actualizado = $modelo->cambiarEstadoUsuarioYPersona($idUsuario, $estado);

        if (!$actualizado) {
            return redirect()->to(base_url('/usuarios'))
                ->with('error', 'No se pudo cambiar el estado del usuario.');
        }

        return redirect()->to(base_url('/usuarios'))
            ->with('success', 'Usuario ' . $texto . ' correctamente.');
    }
}