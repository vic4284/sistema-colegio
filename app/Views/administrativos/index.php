<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Administrativos</h1>

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
        <a class="btn btn-guardar" href="#modal-insertar-administrativo">Nuevo Administrativo</a>
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
                    <th>Cargo</th>
                    <th>Usuario vinculado</th>
                    <th>Estado</th>
                    <th>Fecha de creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($administrativos)): ?>
                    <?php foreach ($administrativos as $administrativo): ?>
                        <tr>
                            <td><?= esc($administrativo['id_administrativo']) ?></td>
                            <td><?= esc($administrativo['nombres']) ?></td>
                            <td><?= esc($administrativo['apellidos']) ?></td>
                            <td><?= esc($administrativo['telefono']) ?></td>
                            <td><?= esc($administrativo['correo']) ?></td>
                            <td><?= esc($administrativo['cargo']) ?></td>
                            <td>
                                <?php if ((int)$administrativo['bloqueado_activacion'] === 1): ?>
                                 <span class="estado-inactivo">Bloqueado</span>
                                <?php elseif (!empty($administrativo['id_usuario'])): ?>
                                    <span class="estado-activo">Cuenta activada</span>
                                <?php else: ?>
                                    <span class="estado-pendiente">Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$administrativo['estado'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($administrativo['fecha_creacion']) ?></td>
                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $administrativo['id_administrativo'] ?>">Editar</a>

                                <?php if ((int)$administrativo['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/administrativos/desactivar/' . $administrativo['id_administrativo']) ?>"
                                       onclick="return confirm('¿Desea desactivar este administrativo?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/administrativos/activar/' . $administrativo['id_administrativo']) ?>"
                                       onclick="return confirm('¿Desea activar este administrativo?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="sin-registros">No existen administrativos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-insertar-administrativo" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>
        <h2>Registrar Administrativo</h2>

        <form action="<?= base_url('/administrativos/insertar') ?>" method="post">
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
                <label>Cargo</label>
                <input type="text" name="cargo">
            </div>

            <button type="submit" class="btn btn-guardar">Guardar</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($administrativos)): ?>
    <?php foreach ($administrativos as $administrativo): ?>
        <div id="modal-editar-<?= $administrativo['id_administrativo'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>
                <h2>Editar Administrativo</h2>

                <form action="<?= base_url('/administrativos/actualizar/' . $administrativo['id_administrativo']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label>Nombres</label>
                        <input type="text" name="nombres" value="<?= esc($administrativo['nombres']) ?>" required>
                    </div>

                    <div class="grupo">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" value="<?= esc($administrativo['apellidos']) ?>" required>
                    </div>

                    <div class="grupo">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="<?= esc($administrativo['telefono']) ?>">
                    </div>

                    <div class="grupo">
                        <label>Correo</label>
                        <input type="email" name="correo" value="<?= esc($administrativo['correo']) ?>" required>
                    </div>

                    <div class="grupo">
                        <label>Cargo</label>
                        <input type="text" name="cargo" value="<?= esc($administrativo['cargo']) ?>">
                    </div>

                    <div class="grupo">
    <label>Estado de la cuenta</label>

    <?php if ((int)$administrativo['bloqueado_activacion'] === 1): ?>
        <input type="text" value="Bloqueado por intentos fallidos" disabled>

        <input type="hidden" name="bloqueado_actual" value="1">

        <div class="grupo-desbloqueo">
    <input type="checkbox" id="desbloquear_<?= $administrativo['id_administrativo'] ?>"
           name="bloqueado_activacion"
           value="1">

    <label for="desbloquear_<?= $administrativo['id_administrativo'] ?>">
        Desbloquear activación de cuenta
    </label>
</div>

    <?php elseif (!empty($administrativo['id_usuario'])): ?>
        <input type="text" value="Cuenta activada en el sistema" disabled>
        <input type="hidden" name="bloqueado_actual" value="0">

    <?php else: ?>
        <input type="text" value="Pendiente de activación por el usuario" disabled>
        <input type="hidden" name="bloqueado_actual" value="0">
    <?php endif; ?>
</div>

                    <div class="grupo">
                        <label>Estado actual</label>
                        <input type="text" value="<?= (int)$administrativo['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>