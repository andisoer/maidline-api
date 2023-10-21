<!DOCTYPE html>
<html>

<head>
    <title>OTP Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #007BFF;
            color: #fff;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 400px;
        }

        h1 {
            color: #007BFF;
            font-size: 24px;
        }

        .otp {
            font-size: 36px;
            color: #007BFF;
            margin: 20px 0;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>OTP Confirmation</h1>
        <p>Your OTP:</p>
        <p class="otp">{{ $otp }}</p>
        <p>Thank you for registering. You can now use this OTP to complete your Maidline account setup.</p>
    </div>
</body>

</html>