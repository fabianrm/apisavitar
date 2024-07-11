<!DOCTYPE html>
<html>

<head>
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 58mm;
            margin: 0;
            padding: 0;
            /* background: lightblue; */
        }

        .receipt {
            width: 100%;
            padding: 3mm;
            box-sizing: border-box;
        }


        .totales {
            text-align: right;
            margin-right: 5mm;
        }

        .receipt-header,
        .receipt-footer {
            text-align: center;
        }

        .logo {
            text-align: center;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="logo">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" width="80" />
    </div>
    <div class="receipt">
        <h2 class="receipt-header">Recibo Nro. {{ $receipt }}</h2>
        <p><strong>Cod.Contrato:</strong> {{ $service_id }}</p>
        <p><strong>Cliente:</strong> {{ $customer_name }}</p>
        <p><strong>Plan:</strong> {{ $plan_name }}</p>

        <h3>Periodo:</h3>
        <p><strong>Desde:</strong> {{ $start_date }}</p>
        <p><strong>Hasta:</strong> {{ $end_date }}</p>
        <p><strong>Fecha de pago:</strong> {{ $paid_dated }}</p>

        <p class="totales"><strong>Subtotal:</strong> {{ $price }}</p>
        <p class="totales"><strong>Descuento:</strong> {{ $discount }}</p>
        <p class="totales"><strong>Total:</strong> {{ $total }}</p>
    </div>
    <div class="receipt-footer">
        <p>Gracias por navegar con Savitar!</p>
    </div>
</body>

</html>