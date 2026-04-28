<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayMongo Payment</title>

    <style>
      @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap');

         * {
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', sans-serif;    
         }

         body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: linear-gradient(to right, #43e97b 0%, #38f9d7 100%);
         }

         .container {
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            background-color: #fff;
         }

         form {
            display: flex;
            flex-direction: column;
         }

         h2 {
            font-size: 30px;
            margin-bottom: 20px;
         }

         input {
            text-align: center;
            font-size: 20px;
            padding: 5px;
            border: none;
            border-bottom: 1px solid;
            outline: none;
         }

         button {
            font-size: 18px;
            padding: 7px;
            background-color: #009039;
            border: none;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
         }

         button:hover {
            background-color: #00bf4c;
         }
    </style>
</head>
<body>
    <div class="container">
        <h2>Complete Your Payment (min. 100)</h2>
        <form action="create_payment.php" method="POST">
            <label for="amount">Amount (PHP):</label>
            <input type="number" name="amount" required><br>
            <button type="submit">Pay Now</button>
        </form>
    </div>
</body>
</html>
