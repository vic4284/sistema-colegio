<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Visualización de Alertas Emocionales</h1>

    <div class="tarjeta-info">
        En esta sección se visualizan los análisis emocionales registrados por el chatbot SEA,
        mostrando emoción, intención, nivel emocional, confianza y recomendación de seguimiento.
    </div>

    <div class="card-resumen" style="margin-bottom: 25px;">
        <h3>Filtrar alertas por estudiante</h3>

        <form method="get" action="<?= base_url('/alertas') ?>">
            <div class="grupo filtro-estudiante">
                <label for="id_estudiante">Seleccione un estudiante</label>

                <select name="id_estudiante" id="id_estudiante" onchange="this.form.submit()">
                    <option value="">Todos los estudiantes</option>

                    <?php if (!empty($estudiantes)): ?>
                        <?php foreach ($estudiantes as $estudiante): ?>
                            <option value="<?= esc($estudiante['id_estudiante']) ?>"
                                <?= ($id_estudiante == $estudiante['id_estudiante']) ? 'selected' : '' ?>>
                                <?= esc($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </form>
    </div>

    <h2>Resumen estadístico emocional</h2>

    <div class="grid-resumen">
        <div class="card-resumen">
            <h3>Niveles emocionales</h3>
            <?php if (!empty($resumenNivel)): ?>
                <?php foreach ($resumenNivel as $nivel): ?>
                    <div class="item-resumen">
                        <strong><?= esc($nivel['nombre'] ?? 'Sin nivel') ?></strong>
                        <span><?= esc($nivel['total']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No existen datos.</p>
            <?php endif; ?>
        </div>

        <div class="card-resumen">
            <h3>Emociones detectadas</h3>
            <?php if (!empty($resumenEmocion)): ?>
                <?php foreach ($resumenEmocion as $emocion): ?>
                    <div class="item-resumen">
                        <strong><?= esc($emocion['nombre'] ?? 'Sin emoción') ?></strong>
                        <span><?= esc($emocion['total']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No existen datos.</p>
            <?php endif; ?>
        </div>

        <div class="card-resumen">
            <h3>Intenciones detectadas</h3>
            <?php if (!empty($resumenIntencion)): ?>
                <?php foreach ($resumenIntencion as $intencion): ?>
                    <div class="item-resumen">
                        <strong><?= esc($intencion['nombre'] ?? 'Sin intención') ?></strong>
                        <span><?= esc($intencion['total']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No existen datos.</p>
            <?php endif; ?>
        </div>
    </div>

    <h2>Gráficos estadísticos</h2>

    <div class="grid-graficos">
        <div class="card-grafico">
            <h3>Niveles emocionales</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoNivel"></canvas>
            </div>
        </div>

        <div class="card-grafico">
            <h3>Emociones detectadas</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoEmocion"></canvas>
            </div>
        </div>

        <div class="card-grafico">
            <h3>Intenciones detectadas</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoIntencion"></canvas>
            </div>
        </div>

        <div class="card-grafico">
            <h3>Alertas por fecha</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoFecha"></canvas>
            </div>
        </div>

        <div class="card-grafico">
            <h3>Confianza promedio por emoción</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoConfianza"></canvas>
            </div>
        </div>

        <div class="card-grafico">
            <h3>Alertas por paralelo</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoParalelo"></canvas>
            </div>
        </div>

        <div class="card-grafico">
            <h3>Top estudiantes con más alertas</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoEstudiantes"></canvas>
            </div>
        </div>

        <div class="card-grafico">
            <h3>Confianza por registro reciente</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoConfianzaReciente"></canvas>
            </div>
        </div>
    </div>

    <h2>Detalle de análisis emocionales</h2>

    <div class="tabla-responsive">
        <table id="tablaAlertas" class="display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Paralelo</th>
                    <th>Emoción</th>
                    <th>Nivel</th>
                    <th>Intención</th>
                    <th>Confianza</th>
                    <th>Recomendación</th>
                    <th>Seguimiento</th>
                    <th>Fecha</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($alertas)): ?>
                    <?php foreach ($alertas as $alerta): ?>
                        <?php
                            $nivelTexto = strtoupper($alerta['nivel_emocional'] ?? $alerta['nombre_nivel'] ?? 'SIN NIVEL');
                            $claseNivel = 'badge';

                            if ($nivelTexto == 'ESTABLE') {
                                $claseNivel .= ' badge-estable';
                            } elseif ($nivelTexto == 'LEVE') {
                                $claseNivel .= ' badge-leve';
                            } elseif ($nivelTexto == 'MODERADO') {
                                $claseNivel .= ' badge-moderado';
                            } elseif ($nivelTexto == 'ALTO') {
                                $claseNivel .= ' badge-alto';
                            } elseif ($nivelTexto == 'CRITICO' || $nivelTexto == 'CRÍTICO') {
                                $claseNivel .= ' badge-critico';
                            }
                        ?>

                        <tr>
                            <td><?= esc($alerta['id_analisis']) ?></td>
                            <td><?= esc($alerta['nombres'] . ' ' . $alerta['apellidos']) ?></td>
                            <td><?= esc($alerta['nombre_paralelo'] ?? 'Sin paralelo') ?></td>
                            <td><?= esc($alerta['nombre_emocion']) ?></td>
                            <td><span class="<?= $claseNivel ?>"><?= esc($nivelTexto) ?></span></td>
                            <td><?= esc($alerta['intencion_detectada'] ?? 'No registrada') ?></td>
                            <td><?= esc($alerta['puntaje_confianza'] ?? '0') ?>%</td>
                            <td><?= esc($alerta['recomendacion'] ?? 'Sin recomendación') ?></td>
                            <td><?= esc($alerta['estado_seguimiento'] ?? 'PENDIENTE') ?></td>
                            <td><?= esc($alerta['fecha_analisis']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
const labelsNivel = <?= json_encode(array_column($resumenNivel ?? [], 'nombre')) ?>;
const datosNivel = <?= json_encode(array_column($resumenNivel ?? [], 'total')) ?>;

const labelsEmocion = <?= json_encode(array_column($resumenEmocion ?? [], 'nombre')) ?>;
const datosEmocion = <?= json_encode(array_column($resumenEmocion ?? [], 'total')) ?>;

const labelsIntencion = <?= json_encode(array_column($resumenIntencion ?? [], 'nombre')) ?>;
const datosIntencion = <?= json_encode(array_column($resumenIntencion ?? [], 'total')) ?>;

const alertas = <?= json_encode($alertas ?? []) ?>;

const colores = [
    '#6ba5e7',
    '#e87b95',
    '#f2b35d',
    '#f6d365',
    '#8ecac7',
    '#b28cf0',
    '#c9c9c9',
    '#9bbcf2',
    '#f7a6a6',
    '#a3d9a5'
];

function contarPorCampo(campo) {
    const resultado = {};
    alertas.forEach(a => {
        let valor = a[campo] || 'Sin dato';
        resultado[valor] = (resultado[valor] || 0) + 1;
    });
    return resultado;
}

function convertirObjeto(objeto) {
    return {
        labels: Object.keys(objeto),
        datos: Object.values(objeto)
    };
}

function topObjeto(objeto, limite = 10) {
    const entradas = Object.entries(objeto)
        .sort((a, b) => b[1] - a[1])
        .slice(0, limite);

    return {
        labels: entradas.map(e => e[0]),
        datos: entradas.map(e => e[1])
    };
}

function confianzaPromedioPorEmocion() {
    const suma = {};
    const cantidad = {};

    alertas.forEach(a => {
        const emocion = a.nombre_emocion || 'Sin emoción';
        const confianza = parseFloat(a.puntaje_confianza || 0);

        suma[emocion] = (suma[emocion] || 0) + confianza;
        cantidad[emocion] = (cantidad[emocion] || 0) + 1;
    });

    const resultado = {};

    Object.keys(suma).forEach(emocion => {
        resultado[emocion] = (suma[emocion] / cantidad[emocion]).toFixed(2);
    });

    return resultado;
}

const datosFechaObj = {};

alertas.forEach(a => {
    const fecha = (a.fecha_analisis || 'Sin fecha').substring(0, 10);
    datosFechaObj[fecha] = (datosFechaObj[fecha] || 0) + 1;
});

const datosFechaOrdenado = {};

Object.keys(datosFechaObj).sort().forEach(fecha => {
    datosFechaOrdenado[fecha] = datosFechaObj[fecha];
});

const fechaFinal = convertirObjeto(datosFechaOrdenado);
const confianzaFinal = convertirObjeto(confianzaPromedioPorEmocion());
const paraleloFinal = convertirObjeto(contarPorCampo('nombre_paralelo'));

const estudiantesConteo = {};

alertas.forEach(a => {
    const estudiante = ((a.nombres || '') + ' ' + (a.apellidos || '')).trim() || 'Sin estudiante';
    estudiantesConteo[estudiante] = (estudiantesConteo[estudiante] || 0) + 1;
});

const estudiantesFinal = topObjeto(estudiantesConteo, 10);

const alertasRecientes = [...alertas]
    .sort((a, b) => new Date(a.fecha_analisis) - new Date(b.fecha_analisis))
    .slice(-15);

const labelsConfianzaReciente = alertasRecientes.map(a => a.fecha_analisis);
const datosConfianzaReciente = alertasRecientes.map(a => parseFloat(a.puntaje_confianza || 0));

new Chart(document.getElementById('graficoNivel'), {
    type: 'pie',
    data: {
        labels: labelsNivel,
        datasets: [{
            data: datosNivel,
            backgroundColor: colores
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

new Chart(document.getElementById('graficoEmocion'), {
    type: 'doughnut',
    data: {
        labels: labelsEmocion,
        datasets: [{
            data: datosEmocion,
            backgroundColor: colores
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '55%',
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

new Chart(document.getElementById('graficoIntencion'), {
    type: 'bar',
    data: {
        labels: labelsIntencion,
        datasets: [{
            label: 'Cantidad',
            data: datosIntencion,
            backgroundColor: '#6ba5e7'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

new Chart(document.getElementById('graficoFecha'), {
    type: 'line',
    data: {
        labels: fechaFinal.labels,
        datasets: [{
            label: 'Cantidad de alertas',
            data: fechaFinal.datos,
            tension: 0.35,
            fill: false,
            borderColor: '#6ba5e7',
            backgroundColor: '#6ba5e7'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

new Chart(document.getElementById('graficoConfianza'), {
    type: 'bar',
    data: {
        labels: confianzaFinal.labels,
        datasets: [{
            label: 'Confianza promedio %',
            data: confianzaFinal.datos,
            backgroundColor: '#8ecac7'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});

new Chart(document.getElementById('graficoParalelo'), {
    type: 'bar',
    data: {
        labels: paraleloFinal.labels,
        datasets: [{
            label: 'Cantidad',
            data: paraleloFinal.datos,
            backgroundColor: '#f2b35d'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

new Chart(document.getElementById('graficoEstudiantes'), {
    type: 'bar',
    data: {
        labels: estudiantesFinal.labels,
        datasets: [{
            label: 'Cantidad de alertas',
            data: estudiantesFinal.datos,
            backgroundColor: '#e87b95'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

new Chart(document.getElementById('graficoConfianzaReciente'), {
    type: 'line',
    data: {
        labels: labelsConfianzaReciente,
        datasets: [{
            label: 'Confianza %',
            data: datosConfianzaReciente,
            tension: 0.35,
            fill: false,
            borderColor: '#e87b95',
            backgroundColor: '#e87b95'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});

$(document).ready(function () {
    $('#tablaAlertas').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[9, 'desc']],
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "No existen registros",
            infoFiltered: "(filtrado de _MAX_ registros)",
            zeroRecords: "No se encontraron resultados",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        }
    });
});
</script>

<?= view('layout/footer') ?>