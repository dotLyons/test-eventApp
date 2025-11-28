<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Cierre de Caja</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            color: #666;
        }

        .box {
            border: 1px solid #eee;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }

        .row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .label {
            display: table-cell;
            font-weight: bold;
            width: 60%;
        }

        .value {
            display: table-cell;
            text-align: right;
            width: 40%;
            font-family: monospace;
            font-size: 14px;
        }

        .total-row {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 16px;
        }

        .total-value {
            font-weight: bold;
            color: #000;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #999;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        /* Tabla de desglose simple */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            text-align: left;
            border-bottom: 1px solid #333;
            padding: 5px;
        }

        td {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Reporte de Cierre de Caja (Z)</h1>
            <p>Fecha de emisión: {{ $date }}</p>
            <p>Operador: Sistema Automático</p>
        </div>

        <div class="box">
            <div class="row">
                <span class="label">Ventas en Efectivo:</span>
                <span class="value">$ {{ number_format($cash, 2, ',', '.') }}</span>
            </div>
            <div class="row">
                <span class="label">Ventas por Transferencia:</span>
                <span class="value">$ {{ number_format($transfer, 2, ',', '.') }}</span>
            </div>

            <div class="row total-row">
                <span class="label total-value">TOTAL RECAUDADO:</span>
                <span class="value total-value">$ {{ number_format($total, 2, ',', '.') }}</span>
            </div>
        </div>

        <h3>Detalles del Arqueo</h3>
        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Movimientos Registrados</td>
                    <td>{{ $count }} transacciones</td>
                </tr>
                <tr>
                    <td>Estado de Caja</td>
                    <td>CERRADA / RENDIDA</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Este documento es un comprobante interno de cierre de caja.</p>
            <p>Generado por Sistema Fiesta v1.0</p>
        </div>
    </div>
</body>

</html>
