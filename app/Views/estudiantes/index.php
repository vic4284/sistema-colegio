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
                    <th>Género</th>
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
                            <td><?= !empty($estudiante['genero']) ? esc($estudiante['genero']) : 'Sin registro' ?></td>
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
                        <td colspan="11" class="sin-registros">No existen estudiantes registrados.</td>
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
                <label for="direccion">Dirección</label>
                <input type="text"
                       name="direccion"
                       id="direccion"
                       placeholder="Ingrese la dirección"
                       required
                       minlength="3"
                       maxlength="150"
                       pattern="[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ .,#-]+"
                       title="Solo se permiten letras, números, espacios, punto, coma, numeral y guion"
                       value="<?= esc(old('direccion')) ?>">
            </div>

            <div class="grupo">
                <label for="genero">Género</label>
                <select name="genero" id="genero" required>
                    <option value="">Seleccione el género</option>
                    <option value="MASCULINO" <?= old('genero') === 'MASCULINO' ? 'selected' : '' ?>>MASCULINO</option>
                    <option value="FEMENINO" <?= old('genero') === 'FEMENINO' ? 'selected' : '' ?>>FEMENINO</option>
                </select>
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
                        <label for="nombres_<?= $estudiante['id_estudiante'] ?>">Nombres</label>
                        <input type="text"
                               name="nombres"
                               id="nombres_<?= $estudiante['id_estudiante'] ?>"
                               value="<?= esc($estudiante['nombres']) ?>"
                               placeholder="Ingrese los nombres"
                               required
                               minlength="2"
                               maxlength="50"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                               title="Solo se permiten letras y espacios">
                    </div>

                    <div class="grupo">
                        <label for="apellidos_<?= $estudiante['id_estudiante'] ?>">Apellidos</label>
                        <input type="text"
                               name="apellidos"
                               id="apellidos_<?= $estudiante['id_estudiante'] ?>"
                               value="<?= esc($estudiante['apellidos']) ?>"
                               placeholder="Ingrese los apellidos"
                               required
                               minlength="2"
                               maxlength="50"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                               title="Solo se permiten letras y espacios">
                    </div>

                    <div class="grupo">
                        <label for="telefono_<?= $estudiante['id_estudiante'] ?>">Teléfono</label>
                        <input type="text"
                               name="telefono"
                               id="telefono_<?= $estudiante['id_estudiante'] ?>"
                               value="<?= esc($estudiante['telefono']) ?>"
                               placeholder="Ingrese el teléfono"
                               required
                               minlength="7"
                               maxlength="15"
                               pattern="[0-9]+"
                               title="Solo se permiten números">
                    </div>

                    <div class="grupo">
                        <label for="correo_<?= $estudiante['id_estudiante'] ?>">Correo</label>
                        <input type="email"
                               name="correo"
                               id="correo_<?= $estudiante['id_estudiante'] ?>"
                               value="<?= esc($estudiante['correo']) ?>"
                               placeholder="Ingrese el correo electrónico"
                               required
                               maxlength="100">
                    </div>

                    <div class="grupo">
                        <label for="direccion_<?= $estudiante['id_estudiante'] ?>">Dirección</label>
                        <input type="text"
                               name="direccion"
                               id="direccion_<?= $estudiante['id_estudiante'] ?>"
                               value="<?= esc($estudiante['direccion']) ?>"
                               placeholder="Ingrese la dirección"
                               required
                               minlength="3"
                               maxlength="150"
                               pattern="[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ .,#-]+"
                               title="Solo se permiten letras, números, espacios, punto, coma, numeral y guion">
                    </div>

                    <div class="grupo">
                        <label for="genero_<?= $estudiante['id_estudiante'] ?>">Género</label>
                        <select name="genero"
                                id="genero_<?= $estudiante['id_estudiante'] ?>"
                                required>
                            <option value="">Seleccione el género</option>
                            <option value="MASCULINO" <?= ($estudiante['genero'] ?? '') === 'MASCULINO' ? 'selected' : '' ?>>MASCULINO</option>
                            <option value="FEMENINO" <?= ($estudiante['genero'] ?? '') === 'FEMENINO' ? 'selected' : '' ?>>FEMENINO</option>
                        </select>
                    </div>

                    <div class="grupo">
                        <label>Estado de la cuenta</label>

                        <?php if ((int)$estudiante['bloqueado_activacion'] === 1): ?>
                            <input type="text" value="Bloqueado por intentos fallidos" disabled>
                            <input type="hidden" name="bloqueado_actual" value="1">

                            <div class="grupo-desbloqueo">
                                <input type="checkbox"
                                       id="desbloquear_<?= $estudiante['id_estudiante'] ?>"
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