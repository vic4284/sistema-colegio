<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Estudiantes</h1>

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

    <div style="margin-bottom: 20px;">
        <a class="btn btn-guardar" href="#modal-insertar-estudiante">Nuevo Estudiante</a>
    </div>

    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Dirección</th>
                    <th>Usuario vinculado</th>
                    <th>Estado</th>
                    <th>Fecha de creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($estudiantes)): ?>
                    <?php foreach ($estudiantes as $estudiante): ?>
                        <tr>
                            <td><?= esc($estudiante['id_estudiante']) ?></td>
                            <td><?= esc($estudiante['nombres']) ?></td>
                            <td><?= esc($estudiante['apellidos']) ?></td>
                            <td><?= esc($estudiante['telefono']) ?></td>
                            <td><?= esc($estudiante['correo']) ?></td>
                            <td><?= esc($estudiante['direccion']) ?></td>
                            <td>
    <?php if ((int)$estudiante['bloqueado_activacion'] === 1): ?>
        <span class="estado-inactivo">Bloqueado</span>
    <?php elseif (!empty($estudiante['id_usuario'])): ?>
        <span class="estado-activo">Cuenta activada</span>
    <?php else: ?>
        <span class="estado-pendiente">Pendiente</span>
    <?php endif; ?>
</td>
                            <td>
                                <?php if ((int)$estudiante['estado'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($estudiante['fecha_creacion']) ?></td>
                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $estudiante['id_estudiante'] ?>">Editar</a>

                                <?php if ((int)$estudiante['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/estudiantes/desactivar/' . $estudiante['id_estudiante']) ?>"
                                       onclick="return confirm('¿Desea desactivar este estudiante?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/estudiantes/activar/' . $estudiante['id_estudiante']) ?>"
                                       onclick="return confirm('¿Desea activar este estudiante?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="sin-registros">No existen estudiantes registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-insertar-estudiante" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>
        <h2>Registrar Estudiante</h2>

        <form action="<?= base_url('/estudiantes/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label>Nombres</label>
                <input type="text" name="nombres" required>
            </div>

            <div class="grupo">
                <label>Apellidos</label>
                <input type="text" name="apellidos" required>
            </div>

            <div class="grupo">
                <label>Teléfono</label>
                <input type="text" name="telefono">
            </div>

            <div class="grupo">
                <label>Correo</label>
                <input type="email" name="correo" required>
            </div>

            <div class="grupo">
                <label>Dirección</label>
                <input type="text" name="direccion">
            </div>

            <button type="submit" class="btn btn-guardar">Guardar</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($estudiantes)): ?>
    <?php foreach ($estudiantes as $estudiante): ?>
        <div id="modal-editar-<?= $estudiante['id_estudiante'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>
                <h2>Editar Estudiante</h2>

                <form action="<?= base_url('/estudiantes/actualizar/' . $estudiante['id_estudiante']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label>Nombres</label>
                        <input type="text" name="nombres" value="<?= esc($estudiante['nombres']) ?>" required>
                    </div>

                    <div class="grupo">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" value="<?= esc($estudiante['apellidos']) ?>" required>
                    </div>

                    <div class="grupo">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="<?= esc($estudiante['telefono']) ?>">
                    </div>

                    <div class="grupo">
                        <label>Correo</label>
                        <input type="email" name="correo" value="<?= esc($estudiante['correo']) ?>" required>
                    </div>

                    <div class="grupo">
                        <label>Dirección</label>
                        <input type="text" name="direccion" value="<?= esc($estudiante['direccion']) ?>">
                    </div>

                    <div class="grupo">
    <label>Estado de la cuenta</label>

    <?php if ((int)$estudiante['bloqueado_activacion'] === 1): ?>
        <input type="text" value="Bloqueado por intentos fallidos" disabled>

        <input type="hidden" name="bloqueado_actual" value="1">

        <div class="grupo-desbloqueo">
    <input type="checkbox" id="desbloquear_<?= $estudiante['id_estudiante'] ?>"
           name="bloqueado_activacion"
           value="1">

    <label for="desbloquear_<?= $estudiante['id_estudiante'] ?>">
        Desbloquear activación de cuenta
    </label>
</div>

    <?php elseif (!empty($estudiante['id_usuario'])): ?>
        <input type="text" value="Cuenta activada en el sistema" disabled>
        <input type="hidden" name="bloqueado_actual" value="0">

    <?php else: ?>
        <input type="text" value="Pendiente de activación por el usuario" disabled>
        <input type="hidden" name="bloqueado_actual" value="0">
    <?php endif; ?>
</div>

                    <div class="grupo">
                        <label>Estado actual</label>
                        <input type="text" value="<?= (int)$estudiante['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>