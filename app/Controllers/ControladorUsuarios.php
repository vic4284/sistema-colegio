<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsuariosModelo;
class ControladorUsuarios extends BaseController
{
      public function index()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (session()->get('rol') !== 'ADMINISTRATIVO') {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para acceder a este módulo.');
        }

        $modelo = new UsuariosModelo();

       $buscar = trim($this->request->getGet('buscar'));

if ($buscar !== '') {
    $usuarios = $modelo->buscarUsuarios($buscar);
} else {
    $usuarios = $modelo->listarUsuarios();
}

$data = [
    'usuarios' => $usuarios
];

        return view('Usuarios/index', $data);
    }

    public function actualizar($idUsuario = null)
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (session()->get('rol') !== 'ADMINISTRATIVO') {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($idUsuario === null) {
            return redirect()->to(base_url('/usuarios'))
                             ->with('error', 'ID de usuario no válido.');
        }

        $modelo = new UsuariosModelo();
        $usuario = $modelo->obtenerUsuarioPorId($idUsuario);

        if (!$usuario) {
            return redirect()->to(base_url('/usuarios'))
                             ->with('error', 'Usuario no encontrado.');
        }

        $nombreUsuario = trim($this->request->getPost('nombre_usuario'));
        $correoElectronico = trim($this->request->getPost('correo_electronico'));
        $imagen = $this->request->getFile('imagen');

        if ($nombreUsuario === '' || $correoElectronico === '') {
            return redirect()->to(base_url('/usuarios'))
                             ->with('error', 'Todos los campos son obligatorios.');
        }

        if ($modelo->existeNombreUsuario($nombreUsuario, $idUsuario)) {
            return redirect()->to(base_url('/usuarios'))
                             ->with('error', 'El nombre de usuario ya está en uso.');
        }

        if ($modelo->existeCorreoElectronico($correoElectronico, $idUsuario)) {
            return redirect()->to(base_url('/usuarios'))
                             ->with('error', 'El correo electrónico ya está en uso.');
        }

        $datosActualizar = [
    'nombre_usuario'     => $nombreUsuario,
    'correo_electronico' => $correoElectronico
    ];
    
    if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
    
        $extension = strtolower($imagen->getExtension());
    
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return redirect()->to(base_url('/usuarios'))
                             ->with('error', 'Solo se permiten imágenes JPG, JPEG o PNG.');
        }
    
        $nuevoNombre = $imagen->getRandomName();
        $imagen->move(ROOTPATH . 'public/assets/img/usuarios', $nuevoNombre);
    
        if (!empty($usuario['imagen']) && file_exists(ROOTPATH . 'public/assets/img/usuarios/' . $usuario['imagen'])) {
            unlink(ROOTPATH . 'public/assets/img/usuarios/' . $usuario['imagen']);
        }
    
        $datosActualizar['imagen'] = $nuevoNombre;
    }

$modelo->update($idUsuario, $datosActualizar);

        return redirect()->to(base_url('/usuarios'))
                         ->with('success', 'Cuenta actualizada correctamente.');
    }

    public function activar($idUsuario = null)
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (session()->get('rol') !== 'ADMINISTRATIVO') {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($idUsuario === null) {
            return redirect()->to(base_url('/usuarios'))
                             ->with('error', 'ID de usuario no válido.');
        }

        $modelo = new UsuariosModelo();
        $usuario = $modelo->obtenerUsuarioPorId($idUsuario);

        if (!$usuario) {
            return redirect()->to(base_url('/usuarios'))
                             ->with('error', 'Usuario no encontrado.');
        }

        $actualizado = $modelo->cambiarEstadoUsuarioYPersona($idUsuario, 1);
        if (!$actualizado) {
            return redirect()->to(base_url('/usuarios'))
                     ->with('error', 'Usuario no encontrado.');
}

        return redirect()->to(base_url('/usuarios'))
                         ->with('success', 'Usuario activado correctamente.');
    }

    public function desactivar($idUsuario = null)
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (session()->get('rol') !== 'ADMINISTRATIVO') {
            return redirect()->to(base_url('/dashboard'))
                             ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($idUsuario === null) {
            return redirect()->to(base_url('/usuarios'))
                             ->with('error', 'ID de usuario no válido.');
        }

        if ((int)$idUsuario === (int)session()->get('id_usuario')) {
            return redirect()->to(base_url('/usuarios'))
                             ->with('error', 'No puede desactivar su propia cuenta.');
        }

        $modelo = new UsuariosModelo();
        $usuario = $modelo->obtenerUsuarioPorId($idUsuario);

        if (!$usuario) {
            return redirect()->to(base_url('/usuarios'))
                             ->with('error', 'Usuario no encontrado.');
        }

       $actualizado = $modelo->cambiarEstadoUsuarioYPersona($idUsuario, 0);

if (!$actualizado) {
    return redirect()->to(base_url('/usuarios'))
                     ->with('error', 'Usuario no encontrado.');
}

        return redirect()->to(base_url('/usuarios'))
                         ->with('success', 'Usuario desactivado correctamente.');
    }
}

