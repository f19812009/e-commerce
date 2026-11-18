<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333333;
            font-size: 13px;
            line-height: 1.5;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 2px solid #0d3b37;
            padding-bottom: 15px;
        }
        .brand-title {
            font-size: 24px;
            font-weight: bold;
            color: #0d3b37;
            margin: 0;
            text-transform: uppercase;
        }
        .brand-subtitle {
            font-size: 11px;
            color: #b58d3d;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #0d3b37;
            text-align: right;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .info-box {
            vertical-align: top;
            width: 50%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #0d3b37;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-size: 12px;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eeeeee;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9fbf9;
        }
        .totals-table {
            width: 40%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 10px;
        }
        .totals-table .grand-total {
            border-top: 2px solid #0d3b37;
            border-bottom: 2px solid #0d3b37;
            font-size: 15px;
            font-weight: bold;
            color: #0d3b37;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            background: #e6f4ea;
            color: #137333;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #e0e0e0;
            padding-top: 15px;
            text-align: center;
            font-size: 11px;
            color: #777777;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="brand-title">AL-AQSA MART</div>
                <div class="brand-subtitle">YOUR TRUSTED ONLINE SHOP</div>
                <div style="font-size: 11px; color: #666; margin-top: 4px;">
                    হটলাইন: 16297 | support@alaqsamart.com
                </div>
            </td>
            <td style="vertical-align: middle; text-align: right;">
                <div class="invoice-title">INVOICE</div>
                <div style="color: #666; font-size: 12px; margin-top: 4px;">
                    <strong>Invoice #:</strong> {{ $order->order_number }}<br>
                    <strong>Date:</strong> {{ $order->created_at->format('d M, Y h:i A') }}<br>
                    <strong>Status:</strong> <span class="badge">{{ strtoupper($order->status) }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Customer & Shipping Info -->
    <table class="meta-table">
        <tr>
            <td class="info-box">
                <strong style="color: #0d3b37; text-transform: uppercase; font-size: 11px;">Customer Information</strong>
                <p style="margin: 4px 0 0 0; line-height: 1.6;">
                    <strong>{{ $order->customer_name }}</strong><br>
                    Phone: {{ $order->customer_phone }}<br>
                    Email: {{ $order->customer_email }}
                </p>
            </td>
            <td class="info-box" style="text-align: right;">
                <strong style="color: #0d3b37; text-transform: uppercase; font-size: 11px;">Delivery Address</strong>
                <p style="margin: 4px 0 0 0; line-height: 1.6;">
                    {{ $order->shipping_address }}<br>
                    {{ $order->city }} {{ $order->postal_code ? '- ' . $order->postal_code : '' }}<br>
                    Payment: <strong>{{ strtoupper($order->payment_method) }}</strong>
                </p>
            </td>
        </tr>
    </table>

    <!-- Order Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 55%;">Product Description</th>
                <th style="width: 15%; text-align: right;">Unit Price</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->product_name }}</strong>
                    @if($item->product && $item->product->sku)
                        <br><span style="font-size: 10px; color: #777;">SKU: {{ $item->product->sku }}</span>
                    @endif
                </td>
                <td style="text-align: right;">Tk {{ number_format($item->price, 2) }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right; font-weight: bold;">Tk {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Table -->
    <table class="totals-table">
        <tr>
            <td style="color: #666;">Subtotal:</td>
            <td style="text-align: right; font-weight: bold;">Tk {{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td style="color: #666;">Shipping Fee:</td>
            <td style="text-align: right; font-weight: bold;">Tk {{ number_format($order->shipping, 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td>Grand Total:</td>
            <td style="text-align: right;">Tk {{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    <!-- Footer Notes -->
    <div class="footer">
        <p>আল-আকসা মার্টের সাথে কেনাকাটা করার জন্য ধন্যবাদ। যেকোনো প্রয়োজনে আমাদের সাথে যোগাযোগ করুন।</p>
        <p style="font-size: 10px; color: #999;">This is a computer-generated invoice. No signature required.</p>
    </div>

</body>
</html>
