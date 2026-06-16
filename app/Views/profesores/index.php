<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Profesores</h1>

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
        <a class="btn btn-guardar" href="#modal-insertar-profesor">Nuevo ProfesorEE</a>
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
                    <th>Especialidad</th>
                    <th>Usuario vinculado</th>
                    <th>Estado</th>
                    <th>Fecha de creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($profesores)): ?>
                    <?php foreach ($profesores as $profesor): ?>
                        <tr>
                            <td><?= esc($profesor['id_profesor']) ?></td>
                            <td><?= esc($profesor['nombres']) ?></td>
                            <td><?= esc($profesor['apellidos']) ?></td>
                            <td><?= esc($profesor['telefono']) ?></td>
                            <td><?= esc($profesor['correo']) ?></td>
                            <td><?= esc($profesor['especialidad']) ?></td>
                            <td>
    <?php if ((int)$profesor['bloqueado_activacion'] === 1): ?>
        <span class="estado-inactivo">Bloqueado</span>
    <?php elseif (!empty($profesor['id_usuario'])): ?>
        <span class="estado-activo">Cuenta activada</span>
    <?php else: ?>
        <span class="estado-pendiente">Pendiente</span>
    <?php endif; ?>
</td>
<td>
                                <?php if ((int)$profesor['estado'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($profesor['fecha_creacion']) ?></td>
                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $profesor['id_profesor'] ?>">
                                    Editar
                                </a>

                                <?php if ((int)$profesor['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/profesores/desactivar/' . $profesor['id_profesor']) ?>"
                                       onclick="return confirm('¿Desea desactivar este profesor?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/profesores/activar/' . $profesor['id_profesor']) ?>"
                                       onclick="return confirm('¿Desea activar este profesor?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="sin-registros">No existen profesores registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-insertar-profesor" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>

        <h2>Registrar Profesor</h2>

        <form action="<?= base_url('/profesores/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="nombres">Nombres</label>
                <input type="text" name="nombres" id="nombres" placeholder="Ingrese los nombres" required>
            </div>

            <div class="grupo">
                <label for="apellidos">Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" placeholder="Ingrese los apellidos" required>
            </div>

            <div class="grupo">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" placeholder="Ingrese el teléfono">
            </div>

            <div class="grupo">
                <label for="correo">Correo</label>
                <input type="email" name="correo" id="correo" placeholder="Ingrese el correo" required>
            </div>

            <div class="grupo">
                <label for="especialidad">Especialidad</label>
                <input type="text" name="especialidad" id="especialidad" placeholder="Ingrese la especialidad">
            </div>

            <button type="submit" class="btn btn-guardar">Guardar</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($profesores)): ?>
    <?php foreach ($profesores as $profesor): ?>
        <div id="modal-editar-<?= $profesor['id_profesor'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>

                <h2>Editar Profesor</h2>

                <form action="<?= base_url('/profesores/actualizar/' . $profesor['id_profesor']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label for="nombres_<?= $profesor['id_profesor'] ?>">Nombres</label>
                        <input type="text"
                               name="nombres"
                               id="nombres_<?= $profesor['id_profesor'] ?>"
                               value="<?= esc($profesor['nombres']) ?>"
                               placeholder="Ingrese los nombres"
                               required>
                    </div>

                    <div class="grupo">
                        <label for="apellidos_<?= $profesor['id_profesor'] ?>">Apellidos</label>
                        <input type="text"
                               name="apellidos"
                               id="apellidos_<?= $profesor['id_profesor'] ?>"
                               value="<?= esc($profesor['apellidos']) ?>"
                               placeholder="Ingrese los apellidos"
                               required>
                    </div>

                    <div class="grupo">
                        <label for="telefono_<?= $profesor['id_profesor'] ?>">Teléfono</label>
                        <input type="text"
                               name="telefono"
                               id="telefono_<?= $profesor['id_profesor'] ?>"
                               value="<?= esc($profesor['telefono']) ?>"
                               placeholder="Ingrese el teléfono">
                    </div>

                    <div class="grupo">
                        <label for="correo_<?= $profesor['id_profesor'] ?>">Correo</label>
                        <input type="email"
                               name="correo"
                               id="correo_<?= $profesor['id_profesor'] ?>"
                               value="<?= esc($profesor['correo']) ?>"
                               placeholder="Ingrese el correo"
                               required>
                    </div>

                    <div class="grupo">
                        <label for="especialidad_<?= $profesor['id_profesor'] ?>">Especialidad</label>
                        <input type="text"
                               name="especialidad"
                               id="especialidad_<?= $profesor['id_profesor'] ?>"
                               value="<?= esc($profesor['especialidad']) ?>"
                               placeholder="Ingrese la especialidad">
                    </div>

                    <div class="grupo">
    <label>Estado de la cuenta</label>

    <?php if ((int)$profesor['bloqueado_activacion'] === 1): ?>
        <input type="text" value="Bloqueado por intentos fallidos" disabled>

        <input type="hidden" name="bloqueado_actual" value="1">

      <div class="grupo-desbloqueo">
    <input type="checkbox" id="desbloquear_<?= $profesor['id_profesor'] ?>"
           name="bloqueado_activacion"
           value="1">

    <label for="desbloquear_<?= $profesor['id_profesor'] ?>">
        Desbloquear activación de cuenta
    </label>
</div>

    <?php elseif (!empty($profesor['id_usuario'])): ?>
        <input type="text" value="Cuenta activada en el sistema" disabled>
        <input type="hidden" name="bloqueado_actual" value="0">

    <?php else: ?>
        <input type="text" value="Pendiente de activación por el usuario" disabled>
        <input type="hidden" name="bloqueado_actual" value="0">
    <?php endif; ?>
</div>

                    <div class="grupo">
                        <label>Estado actual</label>
                        <input type="text" value="<?= (int)$profesor['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>