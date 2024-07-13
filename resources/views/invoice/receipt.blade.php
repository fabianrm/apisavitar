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
            background-image: url('images/bg_savitar.jpg');
            background-position: center center;
            background-size: contain;
            background-repeat: no-repeat;
            font-size: 11px;
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
            display: flex;
            justify-content: center;
        }


        .datos-head {
            text-align: center;
            line-height: .5;
            font-size: 12px;
            margin-top: -5px;
        }

        .divider {
            line-height: 1px;
        }
    </style>
</head>

<body>

    <div class="logo">
        <img src="{{ public_path('images/logo_savitar.png') }}" alt="Logo" />
    </div>

    <div class="datos-head">
        <p style="font-size:8px; line-height: 1px;  text-align: center;">JR MARAVILLA, TRANSVERSAL MANCO CAPAC - PNC</p>
        <p>RUC: 20601788285</p>
        <p>TEL: 921 799 850</p>
    </div>
    <div class="receipt">
        <h2 class="receipt-header">Recibo Nro. {{ $receipt }}</h2>
        <p><strong>Cod.Contrato:</strong> {{ $service_id }}</p>
        <p><strong>Cliente:</strong> {{ $customer_name }}</p>
        <p><strong>Servicio:</strong> {{ $plan_name }}</p>
        <p class="divider">..................................................................</p>

        <h3>Periodo:</h3>
        <p><strong>Desde:</strong> {{ $start_date }}</p>
        <p><strong>Hasta:</strong> {{ $end_date }}</p>
        <p><strong>Fecha de pago:</strong> {{ $paid_dated }}</p>

        <p class="divider">..................................................................</p>
        <p class="totales"><strong>Subtotal:</strong> {{ $price }}</p>
        <p class="totales"><strong>Descuento:</strong> {{ $discount }}</p>
        <p class="totales"><strong>Total:</strong> {{ $total }}</p>
        <p class="divider">..................................................................</p>
        <p><strong>Nota:</strong> {{ $note }}</p>
        <p class="divider">..................................................................</p>
    </div>
    <div class="receipt-footer">
        <p style="font-size:11px">Gracias por navegar con Savitar Perú!</p>
    </div>
</body>

</html>