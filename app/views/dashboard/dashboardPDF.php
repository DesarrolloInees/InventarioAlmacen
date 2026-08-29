```php
<?php if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado."); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Panel de Control Almacén</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .header p {
            color: #666;
            margin-top: 5px;
        }

        .metricas {
            width: 100%;
            margin-bottom: 25px;
        }

        .metrica {
            width: 31%;
            display: inline-block;
            vertical-align: top;
            border: 1px solid #ddd;
            padding: 12px;
            margin-right: 1%;
            box-sizing: border-box;
        }

        .metrica:last-child {
            margin-right: 0;
        }

        .metrica-titulo {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }

        .metrica-valor {
            font-size: 20px;
            font-weight: bold;
            margin-top: 5px;
        }

        .seccion {
            margin-top: 20px;
        }

        .seccion h2 {
            font-size: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #eeeeee;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 7px;
            text-align: left;
        }

        .alerta {
            color: #b91c1c;
            font-weight: bold;
        }

        .sin-datos {
            text-align: center;
            color: #777;
            padding: 15px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #777;
        }
    </style>
</head>

<body>

    <!-- ENCABEZADO -->

    <div class="header">

        <h1>
            Panel de Control Almacén
        </h1>

        <p>
            Reporte generado el <?= date('d/m/Y H:i:s') ?>
        </p>

    </div>


    <!-- MÉTRICAS -->

    <div class="metricas">

        <div class="metrica">

            <div class="metrica-titulo">
                Stock Total
            </div>

            <div class="metrica-valor">
                <?= number_format($data['metricas']['total_unidades'] ?? 0) ?>
                Und.
            </div>

        </div>


        <div class="metrica">

            <div class="metrica-titulo">
                Stock Bajo
            </div>

            <div class="metrica-valor">
                <?= $data['metricas']['items_bajo_stock'] ?? 0 ?>
                Items
            </div>

        </div>


        <div class="metrica">

            <div class="metrica-titulo">
                Catálogo Global
            </div>

            <div class="metrica-valor">
                <?= $data['metricas']['total_catalogo'] ?? 0 ?>
                Ref.
            </div>

        </div>

    </div>


    <!-- MOVIMIENTOS -->

    <div class="seccion">

        <h2>
            Flujo de Inventario - Últimos 7 días
        </h2>


        <?php if (!empty($data['grafico'])): ?>

            <table>

                <thead>

                    <tr>
                        <th>Fecha</th>
                        <th>Entradas</th>
                        <th>Salidas</th>
                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($data['grafico'] as $movimiento): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($movimiento['fecha']) ?>
                            </td>

                            <td>
                                <?= number_format($movimiento['entradas']) ?>
                            </td>

                            <td>
                                <?= number_format($movimiento['salidas']) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="sin-datos">
                No hay movimientos registrados en los últimos 7 días.
            </div>

        <?php endif; ?>

    </div>


    <!-- ALERTAS -->

    <div class="seccion">

        <h2>
            Artículos por Agotarse
        </h2>


        <?php if (!empty($data['alertas'])): ?>

            <table>

                <thead>

                    <tr>
                        <th>Artículo</th>
                        <th>Código</th>
                        <th>Cantidad disponible</th>
                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($data['alertas'] as $alerta): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($alerta['nombre_articulo']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($alerta['codigo']) ?>
                            </td>

                            <td class="alerta">
                                <?= htmlspecialchars($alerta['cantidad_total']) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="sin-datos">
                No hay artículos por agotarse. El stock se encuentra saludable.
            </div>

        <?php endif; ?>

    </div>


    <!-- PIE -->

    <div class="footer">

        Reporte generado automáticamente desde el
        Panel de Control Almacén.

    </div>

</body>

</html>
```
