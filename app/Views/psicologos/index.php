<?= view('layout/header') ?>

<?php
    $modalFormulario = session()->getFlashdata('modal_formulario');
    $idModalFormulario = session()->getFlashdata('id_modal_formulario');
    $errorFormulario = session()->getFlashdata('error_formulario');
?>

<div class="contenedor">
    <h1>🧠 Módulo de Psicólogos</h1>

    <?php if(session()->getFlashdata('error') && empty($errorFormulario)): ?>
        <div class="mensaje-error">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="mensaje-ok">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="acciones-superiores">
        <a class="btn btn-guardar" href="#modal-insertar-psicologo">
            ➕ Nuevo Psicólogo
        </a>
    </div>

    <div class="tabla-responsive">

        <form method="get" action="<?= base_url('/psicologos') ?>" class="tabla-controles">
            <input type="hidden" name="orden" value="<?= esc($orden) ?>">
            <input type="hidden" name="direccion" value="<?= esc($direccion) ?>">
            <input type="hidden" name="pagina" value="1">

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

                <button type="submit" class="btn btn-buscar btn-aplicar">
                    Aplicar
                </button>
            </div>

            <div class="tabla-control-derecha">
                <label for="buscar">Buscar:</label>

                <div class="buscador-tabla">
                    <input type="text"
                           name="buscar"
                           id="buscar"
                           value="<?= esc($buscar) ?>"
                           maxlength="80"
                           placeholder="Buscar en este módulo...">

                    <button type="submit" class="btn btn-buscar">
                        🔍 Buscar
                    </button>

                    <?php if (!empty($buscar)): ?>
                        <a href="<?= base_url('/psicologos') ?>" class="btn btn-cancelar">
                            🧹 Limpiar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <?php foreach ($columnasOrden as $columna): ?>
                        <th>
                            <a class="enlace-orden" href="<?= esc($columna['url']) ?>">
                                <span><?= esc($columna['texto']) ?></span>
                                <span class="flecha-orden"><?= esc($columna['flecha']) ?></span>
                            </a>
                        </th>
                    <?php endforeach; ?>
                    <th>⚙️ Acciones</th>
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

                            <td>
                                <?php if ((int)$psicologo['bloqueado_activacion'] === 1): ?>
                                    <span class="estado-inactivo">🔒 Bloqueado</span>
                                <?php elseif (!empty($psicologo['id_usuario'])): ?>
                                    <span class="estado-activo">✅ Cuenta activada</span>
                                <?php else: ?>
                                    <span class="estado-pendiente">🟡 Pendiente</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ((int)$psicologo['estado'] === 1): ?>
                                    <span class="estado-activo">✅ Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">⛔ Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td><?= esc($psicologo['fecha_creacion']) ?></td>

                            <td>
                                <div class="acciones-tabla">
                                    <a class="btn btn-editar" href="#modal-editar-<?= $psicologo['id_psicologo'] ?>">
                                        ✏️ Editar
                                    </a>

                                    <?php if ((int)$psicologo['estado'] === 1): ?>
                                        <a class="btn btn-desactivar"
                                           href="<?= base_url('/psicologos/desactivar/' . $psicologo['id_psicologo']) ?>"
                                           onclick="return confirm('¿Desea desactivar este psicólogo?')">
                                            🚫 Desactivar
                                        </a>
                                    <?php else: ?>
                                        <a class="btn btn-activar"
                                           href="<?= base_url('/psicologos/activar/' . $psicologo['id_psicologo']) ?>"
                                           onclick="return confirm('¿Desea activar este psicólogo?')">
                                            ✅ Activar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="sin-registros">No existen psicólogos registrados.</td>
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
                        'por_pagina' => $porPagina,
                        'orden' => $orden,
                        'direccion' => $direccion
                    ];
                ?>

                <?php if ($pagina > 1): ?>
                    <a href="<?= base_url('/psicologos?' . http_build_query(array_merge($queryBase, ['pagina' => $pagina - 1]))) ?>">
                        ◀ Anterior
                    </a>
                <?php else: ?>
                    <span>◀ Anterior</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <?php if ($i == $pagina): ?>
                        <span class="pagina-activa"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= base_url('/psicologos?' . http_build_query(array_merge($queryBase, ['pagina' => $i]))) ?>">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagina < $totalPaginas): ?>
                    <a href="<?= base_url('/psicologos?' . http_build_query(array_merge($queryBase, ['pagina' => $pagina + 1]))) ?>">
                        Siguiente ▶
                    </a>
                <?php else: ?>
                    <span>Siguiente ▶</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="modal-insertar-psicologo" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>
        <h2>➕ Registrar Psicólogo</h2>

        <?php if ($modalFormulario === 'insertar' && !empty($errorFormulario)): ?>
            <div class="mensaje-error-modal">
                <?= esc($errorFormulario) ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/psicologos/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="nombres">👤 Nombres</label>
                <input type="text" name="nombres" id="nombres" placeholder="Ingrese los nombres" required minlength="2" maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" title="Solo se permiten letras y espacios" value="<?= esc(old('nombres')) ?>">
            </div>

            <div class="grupo">
                <label for="apellidos">👥 Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" placeholder="Ingrese los apellidos" required minlength="2" maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" title="Solo se permiten letras y espacios" value="<?= esc(old('apellidos')) ?>">
            </div>

            <div class="grupo">
                <label for="telefono">📞 Teléfono</label>
                <input type="text" name="telefono" id="telefono" placeholder="Ingrese el teléfono" required minlength="7" maxlength="15" pattern="[0-9]+" title="Solo se permiten números" value="<?= esc(old('telefono')) ?>">
            </div>

            <div class="grupo">
                <label for="correo">✉️ Correo</label>
                <input type="email" name="correo" id="correo" placeholder="Ingrese el correo electrónico" required maxlength="100" pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.(com|net|org|edu|bo|com\.bo)" title="El correo debe terminar en .com, .net, .org, .edu, .bo o .com.bo" value="<?= esc(old('correo')) ?>">
            </div>

            <button type="submit" class="btn btn-guardar">💾 Guardar</button>
            <a href="#" class="btn btn-cancelar">❌ Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($psicologos)): ?>
    <?php foreach ($psicologos as $psicologo): ?>
        <?php
            $esModalEditado = $modalFormulario === 'editar' && (int)$idModalFormulario === (int)$psicologo['id_psicologo'];

            $nombresEditar = $esModalEditado ? old('nombres', $psicologo['nombres']) : $psicologo['nombres'];
            $apellidosEditar = $esModalEditado ? old('apellidos', $psicologo['apellidos']) : $psicologo['apellidos'];
            $telefonoEditar = $esModalEditado ? old('telefono', $psicologo['telefono']) : $psicologo['telefono'];
            $correoEditar = $esModalEditado ? old('correo', $psicologo['correo']) : $psicologo['correo'];
        ?>

        <div id="modal-editar-<?= $psicologo['id_psicologo'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>
                <h2>✏️ Editar Psicólogo</h2>

                <?php if ($esModalEditado && !empty($errorFormulario)): ?>
                    <div class="mensaje-error-modal">
                        <?= esc($errorFormulario) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/psicologos/actualizar/' . $psicologo['id_psicologo']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label for="nombres_<?= $psicologo['id_psicologo'] ?>">👤 Nombres</label>
                        <input type="text" name="nombres" id="nombres_<?= $psicologo['id_psicologo'] ?>" value="<?= esc($nombresEditar) ?>" placeholder="Ingrese los nombres" required minlength="2" maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" title="Solo se permiten letras y espacios">
                    </div>

                    <div class="grupo">
                        <label for="apellidos_<?= $psicologo['id_psicologo'] ?>">👥 Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos_<?= $psicologo['id_psicologo'] ?>" value="<?= esc($apellidosEditar) ?>" placeholder="Ingrese los apellidos" required minlength="2" maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" title="Solo se permiten letras y espacios">
                    </div>

                    <div class="grupo">
                        <label for="telefono_<?= $psicologo['id_psicologo'] ?>">📞 Teléfono</label>
                        <input type="text" name="telefono" id="telefono_<?= $psicologo['id_psicologo'] ?>" value="<?= esc($telefonoEditar) ?>" placeholder="Ingrese el teléfono" required minlength="7" maxlength="15" pattern="[0-9]+" title="Solo se permiten números">
                    </div>

                    <div class="grupo">
                        <label for="correo_<?= $psicologo['id_psicologo'] ?>">✉️ Correo</label>
                        <input type="email" name="correo" id="correo_<?= $psicologo['id_psicologo'] ?>" value="<?= esc($correoEditar) ?>" placeholder="Ingrese el correo electrónico" required maxlength="100" pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.(com|net|org|edu|bo|com\.bo)" title="El correo debe terminar en .com, .net, .org, .edu, .bo o .com.bo">
                    </div>

                    <div class="grupo">
                        <label>🔐 Estado de la cuenta</label>

                        <?php if ((int)$psicologo['bloqueado_activacion'] === 1): ?>
                            <input type="text" value="Bloqueado por intentos fallidos" disabled>
                            <input type="hidden" name="bloqueado_actual" value="1">

                            <div class="grupo-desbloqueo">
                                <input type="checkbox" id="desbloquear_<?= $psicologo['id_psicologo'] ?>" name="bloqueado_activacion" value="1">
                                <label for="desbloquear_<?= $psicologo['id_psicologo'] ?>">🔓 Desbloquear activación de cuenta</label>
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
                        <label>📌 Estado actual</label>
                        <input type="text" value="<?= (int)$psicologo['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">💾 Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">❌ Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>