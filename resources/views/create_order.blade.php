<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Thank you for your order!</h1>
    <p>Order ID: {{ $order->id }}</p>
    <p>Total Price: ${{ $order->total_price }}</p>
</body>
</html>
