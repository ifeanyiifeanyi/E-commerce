<!DOCTYPE html>
<html>
<head>
    <title>Store Approval Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.8em;
            color: #777;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Congratulations! Your Store Has Been Approved</h2>
    </div>

    <div class="content">
        <p>Dear {{ $store->vendor->name }},</p>

        <p>We are pleased to inform you that your store <strong>{{ $store->store_name }}</strong> has been approved and is now live on our platform.</p>

        <p>You can now start adding products and managing your store through your vendor dashboard.</p>

        <p>Store Details:</p>
        <ul>
            <li>Store Name: {{ $store->store_name }}</li>
            <li>Store Email: {{ $store->store_email }}</li>
            <li>Approval Date: {{ now()->format('F j, Y') }}</li>
        </ul>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <a href="{{ url('/vendor/dashboard') }}" class="button">Go to Your Dashboard</a>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
