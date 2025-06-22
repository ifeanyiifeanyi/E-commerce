<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Message from {{ config('app.name') }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background-color: #039935;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
        }

        .advertisement-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }

        .message-content {
            background-color: #e9f7ef;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }

        .cta-button {
            display: inline-block;
            background-color: #00ffd5;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
            font-weight: bold;
        }

        .cta-button:hover {
            background-color: #039b5c;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content, .header, .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name', 'Our Platform') }}</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px;">Admin Message</p>
        </div>

        <div class="content">
            <p style="font-size: 16px; margin-bottom: 20px;">
                Dear {{ $advertisement->vendor->name ?? 'Valued Vendor' }},
            </p>

            <p>You have received a new message from the <strong>{{ config('app.name') }} Admin Team</strong> regarding your advertisement.</p>

            <div class="advertisement-info">
                <h3 style="margin-top: 0; color: #1ff3c5;">Advertisement Details</h3>
                <p><strong>Title:</strong> {{ $advertisement->title ?? 'N/A' }}</p>
                <p><strong>Advertisement ID:</strong> #{{ $advertisement->id ?? 'N/A' }}</p>
                <p><strong>Status:</strong> {{ ucfirst($advertisement->status ?? 'Unknown') }}</p>
            </div>

            <div class="message-content">
                <h3 style="margin-top: 0; color: #28a745;">Admin Message:</h3>
                <p style="font-size: 16px; line-height: 1.6; margin-bottom: 0;">
                    {{ $adminMessage ?? 'No message content available.' }}
                </p>
            </div>

            <p>Please log in to your vendor dashboard to view more details and manage your advertisements.</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') . '/vendor/dashboard' }}" class="cta-button">
                    Access Your Dashboard
                </a>
            </div>

            <p style="font-size: 14px; color: #6c757d;">
                If you have any questions or need assistance, please don't hesitate to contact our support team.
            </p>
        </div>

        <div class="footer">
            <h4 style="margin-top: 0; color: #495057;">Contact Information</h4>
            <p style="margin: 5px 0;">
                <strong>Email:</strong> {{ config('app.email', 'admin@example.com') }}
            </p>
            @if(config('app.phone'))
            <p style="margin: 5px 0;">
                <strong>Phone:</strong> {{ config('app.phone') }}
            </p>
            @endif
            @if(config('app.address'))
            <p style="margin: 5px 0;">
                <strong>Address:</strong> {{ config('app.address') }}
            </p>
            @endif

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #dee2e6;">

            <p style="margin: 10px 0 0 0; font-size: 12px; color: #868e96;">
                This is an automated message from {{ config('app.name') }}. Please do not reply directly to this email.
            </p>
        </div>
    </div>
</body>
</html>
