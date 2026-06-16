<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Usuarios</h1>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="mensaje-error">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="mensaje-ok">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre de usuario</th>
                    <th>Correo electrónico</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último inicio de sesión</th>
                    <th>Fecha de creación</th>
                    <th>Acciones</th> 
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= esc($usuario['id_usuario']) ?></td>
                            
                            <td>
                                <?php if (!empty($usuario['imagen'])): ?>
                                    <img src="<?= base_url('assets/img/usuarios/' . $usuario['imagen']) ?>"
                                         alt="Imagen de usuario"
                                         class="imagen-usuario-tabla">
                                <?php else: ?>
                                    <span class="sin-imagen">Sin imagen</span>
                                <?php endif; ?>
                            </td>

                            <td><?= esc($usuario['nombre_usuario']) ?></td>
                            <td><?= esc($usuario['correo_electronico']) ?></td>
                            <td><?= esc($usuario['nombre_rol']) ?></td>
                            <td>
                                <?php if ((int)$usuario['estado'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= !empty($usuario['ultimo_inicio_sesion']) ? esc($usuario['ultimo_inicio_sesion']) : 'Sin registro' ?>
                            </td>
                            <td><?= esc($usuario['fecha_creacion']) ?></td>
                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $usuario['id_usuario'] ?>">
                                    Editar
                                </a>

                                <?php if ((int)$usuario['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/usuarios/desactivar/' . $usuario['id_usuario']) ?>"
                                       onclick="return confirm('¿Desea desactivar este usuario?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/usuarios/activar/' . $usuario['id_usuario']) ?>"
                                       onclick="return confirm('¿Desea activar este usuario?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="sin-registros">No existen usuarios registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($usuarios)): ?>
    <?php foreach ($usuarios as $usuario): ?>
        <div id="modal-editar-<?= $usuario['id_usuario'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>

                <h2>Editar Cuenta</h2>

                <form action="<?= base_url('/usuarios/actualizar/' . $usuario['id_usuario']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label for="nombre_usuario_<?= $usuario['id_usuario'] ?>">Nombre de usuario</label>
                        <input type="text"
                               name="nombre_usuario"
                               id="nombre_usuario_<?= $usuario['id_usuario'] ?>"
                               value="<?= esc($usuario['nombre_usuario']) ?>"
                               placeholder="Ingrese el nombre de usuario"
                               required
                               minlength="3"
                               maxlength="50">
                    </div>

                    <div class="grupo">
                        <label for="correo_electronico_<?= $usuario['id_usuario'] ?>">Correo electrónico</label>
                        <input type="email"
                               name="correo_electronico"
                               id="correo_electronico_<?= $usuario['id_usuario'] ?>"
                               value="<?= esc($usuario['correo_electronico']) ?>"
                               placeholder="Ingrese el correo electrónico"
                               required
                               maxlength="100">
                    </div>

                    <div class="grupo">
                        <label>Imagen actual</label>
                        <div class="contenedor-imagen-editar">
                            <?php if (!empty($usuario['imagen'])): ?>
                                <img src="<?= base_url('assets/img/usuarios/' . $usuario['imagen']) ?>"
                                     alt="Imagen actual"
                                     class="imagen-usuario-editar">
                            <?php else: ?>
                                <span class="sin-imagen">Sin imagen</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grupo">
                        <label for="imagen_<?= $usuario['id_usuario'] ?>">Cambiar imagen (opcional)</label>
                        <input type="file"
                               name="imagen"
                               id="imagen_<?= $usuario['id_usuario'] ?>"
                               accept="image/png, image/jpeg, image/jpg, image/webp">
                    </div>

                    <div class="grupo">
                        <label>Rol</label>
                        <input type="text" value="<?= esc($usuario['nombre_rol']) ?>" disabled>
                    </div>

                    <div class="grupo">
                        <label>Estado actual</label>
                        <input type="text" value="<?= (int)$usuario['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>