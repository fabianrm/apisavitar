<html>
<head>
    <title>Resumen Histórico del Cliente</title>
    <style>
        body {
            font-family: sans-serif;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Resumen Histórico del Cliente</h1>
    <p><strong>Cliente:</strong> {{ $summary['customer']['name'] }}</p>
    <p><strong>Documento:</strong> {{ $summary['customer']['document_number'] }}</p>
    <p><strong>Dirección:</strong> {{ $summary['customer']['address'] }}</p>

    <h2>Servicios</h2>
    @foreach($summary['services'] as $serviceData)
        <h3>Servicio #{{ $serviceData['service']['id'] }}</h3>
        <p><strong>Plan:</strong> {{ $serviceData['service']['plans']['name'] }}</p>
        <p><strong>Estado:</strong> {{ $serviceData['service']['status'] }}</p>

        <h4>Facturas</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Monto</th>
                    <th>Fecha de Emisión</th>
                    <th>Fecha de Pago</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceData['invoices'] as $invoice)
                    <tr>
                        <td>{{ $invoice['id'] }}</td>
                        <td>{{ $invoice['amount'] }}</td>
                        <td>{{ $invoice['start_date'] }}</td>
                        <td>{{ $invoice['paid_dated'] }}</td>
                        <td>{{ $invoice['status'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h4>Suspensiones</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha de Suspensión</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceData['suspensions'] as $suspension)
                    <tr>
                        <td>{{ $suspension['id'] }}</td>
                        <td>{{ $suspension['suspension_date'] }}</td>
                        <td>{{ $suspension['reason'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <h2>Tickets</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Asunto</th>
                <th>Estado</th>
                <th>Fecha de Creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary['tickets'] as $ticket)
                <tr>
                    <td>{{ $ticket['id'] }}</td>
                    <td>{{ $ticket['subject'] }}</td>
                    <td>{{ $ticket['status'] }}</td>
                    <td>{{ $ticket['created_at'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
