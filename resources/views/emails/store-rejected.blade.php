<!DOCTYPE html>
<html>
<head>
    <title>Store Rejection Notification</title>
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
            background-color: #f44336;
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
            background-color: #4285F4;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .reason-box {
            background-color: #f9f9f9;
            border-left: 4px solid #f44336;
            padding: 10px 15px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Store Application Update</h2>
    </div>

    <div class="content">
        <p>Dear {{ $store->vendor->name }},</p>

        <p>Thank you for your interest in becoming a vendor on our platform. After careful review of your store application for <strong>{{ $store->store_name }}</strong>, we regret to inform you that we were unable to approve it at this time.</p>

        <div class="reason-box">
            <p><strong>Reason for rejection:</strong></p>
            <p>{{ $store->rejection_reason }}</p>
        </div>

        <p>You are welcome to update your store information and reapply. Please address the issues mentioned above before submitting a new application.</p>

        <p>If you have any questions or need further clarification, please don't hesitate to contact our support team.</p>

        <a href="{{ url('/vendor/store/edit') }}" class="button">Update Store Information</a>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
