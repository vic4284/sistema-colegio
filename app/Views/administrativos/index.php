<?= view('layout/header') ?>

<div class="contenedor">
    <h1>👥 Módulo de Administrativos</h1>

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
        <a class="btn btn-guardar" href="#modal-insertar-administrativo">
            ➕ Nuevo Administrativo
        </a>
    </div>

    <div class="tabla-responsive">

        <form method="get" action="<?= base_url('/administrativos') ?>" class="tabla-controles">
            <div class="tabla-control-izquierda">
                <label>
                    Mostrar
                    <select name="por_pagina">
                        <option value="10" <?= (int)$porPagina === 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= (int)$porPagina === 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= (int)$porPagina === 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= (int)$porPagina === 100 ? 'selected' : '' ?>>100</option>
                    </select>
                    registros
                </label>
            </div>

            <div class="tabla-control-derecha">
                <label for="buscar">Buscar:</label>
                <input type="text"
                       name="buscar"
                       id="buscar"
                       value="<?= esc($buscar) ?>"
                       maxlength="80"
                       placeholder="Buscar...">

                <button type="submit" class="btn btn-buscar">🔍 Buscar</button>

                <?php if (!empty($buscar)): ?>
                    <a href="<?= base_url('/administrativos') ?>" class="btn btn-cancelar">
                        🧹 Limpiar
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>🆔 ID</th>
                    <th>👤 Nombres</th>
                    <th>👥 Apellidos</th>
                    <th>📞 Teléfono</th>
                    <th>✉️ Correo</th>
                    <th>💼 Cargo</th>
                    <th>🔐 Usuario vinculado</th>
                    <th>📌 Estado</th>
                    <th>📅 Fecha de creación</th>
                    <th>⚙️ Acciones</th>
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
                                    <span class="estado-inactivo">🔒 Bloqueado</span>
                                <?php elseif (!empty($administrativo['id_usuario'])): ?>
                                    <span class="estado-activo">✅ Cuenta activada</span>
                                <?php else: ?>
                                    <span class="estado-pendiente">🟡 Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$administrativo['estado'] === 1): ?>
                                    <span class="estado-activo">✅ Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">⛔ Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($administrativo['fecha_creacion']) ?></td>
                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $administrativo['id_administrativo'] ?>">
                                    ✏️ Editar
                                </a>

                                <?php if ((int)$administrativo['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/administrativos/desactivar/' . $administrativo['id_administrativo']) ?>"
                                       onclick="return confirm('¿Desea desactivar este administrativo?')">
                                        🚫 Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/administrativos/activar/' . $administrativo['id_administrativo']) ?>"
                                       onclick="return confirm('¿Desea activar este administrativo?')">
                                        ✅ Activar
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

        <div class="tabla-pie">
            <div>
                Mostrando <?= esc($desde) ?> a <?= esc($hasta) ?> de <?= esc($totalRegistros) ?> registros
            </div>

            <div class="paginacion">
                <?php
                    $queryBase = [
                        'buscar' => $buscar,
                        'por_pagina' => $porPagina
                    ];
                ?>

                <?php if ($pagina > 1): ?>
                    <a href="<?= base_url('/administrativos?' . http_build_query(array_merge($queryBase, ['pagina' => $pagina - 1]))) ?>">
                        ◀ Anterior
                    </a>
                <?php else: ?>
                    <span>◀ Anterior</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <?php if ($i == $pagina): ?>
                        <span class="pagina-activa"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= base_url('/administrativos?' . http_build_query(array_merge($queryBase, ['pagina' => $i]))) ?>">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagina < $totalPaginas): ?>
                    <a href="<?= base_url('/administrativos?' . http_build_query(array_merge($queryBase, ['pagina' => $pagina + 1]))) ?>">
                        Siguiente ▶
                    </a>
                <?php else: ?>
                    <span>Siguiente ▶</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="modal-insertar-administrativo" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>
        <h2>➕ Registrar Administrativo</h2>

        <form action="<?= base_url('/administrativos/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="nombres">👤 Nombres</label>
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
                <label for="apellidos">👥 Apellidos</label>
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
                <label for="telefono">📞 Teléfono</label>
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
                <label for="correo">✉️ Correo</label>
                <input type="email"
                       name="correo"
                       id="correo"
                       placeholder="Ingrese el correo electrónico"
                       required
                       maxlength="100"
                       value="<?= esc(old('correo')) ?>">
            </div>

            <div class="grupo">
                <label for="cargo">💼 Cargo</label>
                <input type="text"
                       name="cargo"
                       id="cargo"
                       placeholder="Ingrese el cargo"
                       minlength="3"
                       maxlength="80"
                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                       title="Solo se permiten letras y espacios"
                       value="<?= esc(old('cargo')) ?>">
            </div>

            <button type="submit" class="btn btn-guardar">💾 Guardar</button>
            <a href="#" class="btn btn-cancelar">❌ Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($administrativos)): ?>
    <?php foreach ($administrativos as $administrativo): ?>
        <div id="modal-editar-<?= $administrativo['id_administrativo'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>
                <h2>✏️ Editar Administrativo</h2>

                <form action="<?= base_url('/administrativos/actualizar/' . $administrativo['id_administrativo']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label for="nombres_<?= $administrativo['id_administrativo'] ?>">👤 Nombres</label>
                        <input type="text"
                               name="nombres"
                               id="nombres_<?= $administrativo['id_administrativo'] ?>"
                               value="<?= esc($administrativo['nombres']) ?>"
                               placeholder="Ingrese los nombres"
                               required
                               minlength="2"
                               maxlength="50"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                               title="Solo se permiten letras y espacios">
                    </div>

                    <div class="grupo">
                        <label for="apellidos_<?= $administrativo['id_administrativo'] ?>">👥 Apellidos</label>
                        <input type="text"
                               name="apellidos"
                               id="apellidos_<?= $administrativo['id_administrativo'] ?>"
                               value="<?= esc($administrativo['apellidos']) ?>"
                               placeholder="Ingrese los apellidos"
                               required
                               minlength="2"
                               maxlength="50"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                               title="Solo se permiten letras y espacios">
                    </div>

                    <div class="grupo">
                        <label for="telefono_<?= $administrativo['id_administrativo'] ?>">📞 Teléfono</label>
                        <input type="text"
                               name="telefono"
                               id="telefono_<?= $administrativo['id_administrativo'] ?>"
                               value="<?= esc($administrativo['telefono']) ?>"
                               placeholder="Ingrese el teléfono"
                               required
                               minlength="7"
                               maxlength="15"
                               pattern="[0-9]+"
                               title="Solo se permiten números">
                    </div>

                    <div class="grupo">
                        <label for="correo_<?= $administrativo['id_administrativo'] ?>">✉️ Correo</label>
                        <input type="email"
                               name="correo"
                               id="correo_<?= $administrativo['id_administrativo'] ?>"
                               value="<?= esc($administrativo['correo']) ?>"
                               placeholder="Ingrese el correo electrónico"
                               required
                               maxlength="100">
                    </div>

                    <div class="grupo">
                        <label for="cargo_<?= $administrativo['id_administrativo'] ?>">💼 Cargo</label>
                        <input type="text"
                               name="cargo"
                               id="cargo_<?= $administrativo['id_administrativo'] ?>"
                               value="<?= esc($administrativo['cargo']) ?>"
                               placeholder="Ingrese el cargo"
                               minlength="3"
                               maxlength="80"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                               title="Solo se permiten letras y espacios">
                    </div>

                    <div class="grupo">
                        <label>🔐 Estado de la cuenta</label>

                        <?php if ((int)$administrativo['bloqueado_activacion'] === 1): ?>
                            <input type="text" value="Bloqueado por intentos fallidos" disabled>
                            <input type="hidden" name="bloqueado_actual" value="1">

                            <div class="grupo-desbloqueo">
                                <input type="checkbox"
                                       id="desbloquear_<?= $administrativo['id_administrativo'] ?>"
                                       name="bloqueado_activacion"
                                       value="1">

                                <label for="desbloquear_<?= $administrativo['id_administrativo'] ?>">
                                    🔓 Desbloquear activación de cuenta
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
                        <label>📌 Estado actual</label>
                        <input type="text" value="<?= (int)$administrativo['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">💾 Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">❌ Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>