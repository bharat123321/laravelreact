<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }
        .header {
            background-color: #007BFF;
            color: #ffffff;
            padding: 10px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            padding: 20px;
        }
        .code-box {
            background-color: #f1f1f1;
            border: 1px dashed #007BFF;
            color: #333333;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            margin: 20px 0;
            letter-spacing: 4px;
        }
        .footer {
            text-align: center;
            color: #888888;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>NoteSharing Verification Code</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>We received a request to reset your password. Please use the code below to complete the process:</p>
            <div class="code-box">
                {{ $code }}
            </div>
            <p>If you didn't request this code, please ignore this email or contact our support team.</p>
        </div>
        <div class="footer">
            <p>&copy; 2024 NoteSharing. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
