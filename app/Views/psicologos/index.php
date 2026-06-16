<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Psicólogos</h1>

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
        <a class="btn btn-guardar" href="#modal-insertar-psicologo">Nuevo Psicólogo</a>
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
                    <th>Número de registro</th>
                    <th>Usuario vinculado</th>
                    <th>Estado</th>
                    <th>Fecha de creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($psicologos)): ?>
                    <?php foreach ($psicologos as $psicologo): ?>
                        <tr>
                            <td><?= esc($psicologo['id_psicologo']) ?></td>
                            <td><?= esc($psicologo['nombres']) ?></td>
                            <td><?= esc($psicologo['apellidos']) ?></td>
                            <td><?= esc($psicologo['telefono']) ?></td>
                            <td><?= esc($psicologo['correo']) ?></td>
                            <td><?= esc($psicologo['numero_registro']) ?></td>
                            <td>
                                <?php if ((int)$psicologo['bloqueado_activacion'] === 1): ?>
                                    <span class="estado-inactivo">Bloqueado</span>
                                <?php elseif (!empty($psicologo['id_usuario'])): ?>
                                    <span class="estado-activo">Cuenta activada</span>
                                <?php else: ?>
                                    <span class="estado-pendiente">Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$psicologo['estado'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($psicologo['fecha_creacion']) ?></td>
                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $psicologo['id_psicologo'] ?>">Editar</a>

                                <?php if ((int)$psicologo['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/psicologos/desactivar/' . $psicologo['id_psicologo']) ?>"
                                       onclick="return confirm('¿Desea desactivar este psicólogo?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/psicologos/activar/' . $psicologo['id_psicologo']) ?>"
                                       onclick="return confirm('¿Desea activar este psicólogo?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="sin-registros">No existen psicólogos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-insertar-psicologo" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>
        <h2>Registrar Psicólogo</h2>

        <form action="<?= base_url('/psicologos/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="nombres">Nombres</label>
                <input type="text"
                       name="nombres"
                       id="nombres"
                       placeholder="Ingrese los nombres"
                       required
                       minlength="2"
                       maxlength="50"
                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                       title="Solo se permiten letras y espacios"
                       value="<?= esc(old('nombres')) ?>">
            </div>

            <div class="grupo">
                <label for="apellidos">Apellidos</label>
                <input type="text"
                       name="apellidos"
                       id="apellidos"
                       placeholder="Ingrese los apellidos"
                       required
                       minlength="2"
                       maxlength="50"
                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                       title="Solo se permiten letras y espacios"
                       value="<?= esc(old('apellidos')) ?>">
            </div>

            <div class="grupo">
                <label for="telefono">Teléfono</label>
                <input type="text"
                       name="telefono"
                       id="telefono"
                       placeholder="Ingrese el teléfono"
                       required
                       minlength="7"
                       maxlength="15"
                       pattern="[0-9]+"
                       title="Solo se permiten números"
                       value="<?= esc(old('telefono')) ?>">
            </div>

            <div class="grupo">
                <label for="correo">Correo</label>
                <input type="email"
                       name="correo"
                       id="correo"
                       placeholder="Ingrese el correo electrónico"
                       required
                       maxlength="100"
                       value="<?= esc(old('correo')) ?>">
            </div>

            <div class="grupo">
                <label for="numero_registro">Número de registro</label>
                <input type="text"
                       name="numero_registro"
                       id="numero_registro"
                       placeholder="Ingrese el número de registro"
                       required
                       minlength="3"
                       maxlength="30"
                       pattern="[A-Za-z0-9-]+"
                       title="Solo se permiten letras, números y guion"
                       value="<?= esc(old('numero_registro')) ?>">
            </div>

            <button type="submit" class="btn btn-guardar">Guardar</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($psicologos)): ?>
    <?php foreach ($psicologos as $psicologo): ?>
        <div id="modal-editar-<?= $psicologo['id_psicologo'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>
                <h2>Editar Psicólogo</h2>

                <form action="<?= base_url('/psicologos/actualizar/' . $psicologo['id_psicologo']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label for="nombres_<?= $psicologo['id_psicologo'] ?>">Nombres</label>
                        <input type="text"
                               name="nombres"
                               id="nombres_<?= $psicologo['id_psicologo'] ?>"
                               value="<?= esc($psicologo['nombres']) ?>"
                               placeholder="Ingrese los nombres"
                               required
                               minlength="2"
                               maxlength="50"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                               title="Solo se permiten letras y espacios">
                    </div>

                    <div class="grupo">
                        <label for="apellidos_<?= $psicologo['id_psicologo'] ?>">Apellidos</label>
                        <input type="text"
                               name="apellidos"
                               id="apellidos_<?= $psicologo['id_psicologo'] ?>"
                               value="<?= esc($psicologo['apellidos']) ?>"
                               placeholder="Ingrese los apellidos"
                               required
                               minlength="2"
                               maxlength="50"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                               title="Solo se permiten letras y espacios">
                    </div>

                    <div class="grupo">
                        <label for="telefono_<?= $psicologo['id_psicologo'] ?>">Teléfono</label>
                        <input type="text"
                               name="telefono"
                               id="telefono_<?= $psicologo['id_psicologo'] ?>"
                               value="<?= esc($psicologo['telefono']) ?>"
                               placeholder="Ingrese el teléfono"
                               required
                               minlength="7"
                               maxlength="15"
                               pattern="[0-9]+"
                               title="Solo se permiten números">
                    </div>

                    <div class="grupo">
                        <label for="correo_<?= $psicologo['id_psicologo'] ?>">Correo</label>
                        <input type="email"
                               name="correo"
                               id="correo_<?= $psicologo['id_psicologo'] ?>"
                               value="<?= esc($psicologo['correo']) ?>"
                               placeholder="Ingrese el correo electrónico"
                               required
                               maxlength="100">
                    </div>

                    <div class="grupo">
                        <label for="numero_registro_<?= $psicologo['id_psicologo'] ?>">Número de registro</label>
                        <input type="text"
                               name="numero_registro"
                               id="numero_registro_<?= $psicologo['id_psicologo'] ?>"
                               value="<?= esc($psicologo['numero_registro']) ?>"
                               placeholder="Ingrese el número de registro"
                               required
                               minlength="3"
                               maxlength="30"
                               pattern="[A-Za-z0-9-]+"
                               title="Solo se permiten letras, números y guion">
                    </div>

                    <div class="grupo">
                        <label>Estado de la cuenta</label>

                        <?php if ((int)$psicologo['bloqueado_activacion'] === 1): ?>
                            <input type="text" value="Bloqueado por intentos fallidos" disabled>
                            <input type="hidden" name="bloqueado_actual" value="1">

                            <div class="grupo-desbloqueo">
                                <input type="checkbox"
                                       id="desbloquear_<?= $psicologo['id_psicologo'] ?>"
                                       name="bloqueado_activacion"
                                       value="1">

                                <label for="desbloquear_<?= $psicologo['id_psicologo'] ?>">
                                    Desbloquear activación de cuenta
                                </label>
                            </div>

                        <?php elseif (!empty($psicologo['id_usuario'])): ?>
                            <input type="text" value="Cuenta activada en el sistema" disabled>
                            <input type="hidden" name="bloqueado_actual" value="0">

                        <?php else: ?>
                            <input type="text" value="Pendiente de activación por el usuario" disabled>
                            <input type="hidden" name="bloqueado_actual" value="0">
                        <?php endif; ?>
                    </div>

                    <div class="grupo">
                        <label>Estado actual</label>
                        <input type="text" value="<?= (int)$psicologo['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>