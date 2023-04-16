<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8" />
        <title>
            Donate for the less fortunate
        </title>
        <style>
            .payment{  
              border-radius: 40px;
              margin: auto;
              margin-bottom: 100px;
              background-color: blueviolet;
              display: flex;
              flex-direction: column;
              width: 45%;
              height: fit-content;
              display: none;
            }
      </style>
<body>
  <!- Displaying of Mpesa form -> 
        <div class="payment" id="m-pesa-form">
        <button onclick="payment(0)">X</button>
            <form  method="POST">
                <div style="border-radius: 10px; background-color: rgb(255, 0, 251);font-weight: bold;font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;justify-content: center;">
                    <h2>M-Pesa Payment</h2>
                </div>
                <label for="name">Enter M-pesa name:</label>
                <center><input type="text" name="m_name" id="m_name" placeholder="eg jane doe" required></center>
                <br>
                <br>
                <label for="phone_no">Phone Number:</label>
                <center><input type="number" id="m_phone_no" name="m_phone_no"placeholder="eg 254712345678" required></center>
                <br>
                <br>
                <label for="amount">Amount:</label>
                <center><input type="number" id="m_amount" name="m_amount" placeholder="Enter amount" required></center>
                <br>
                <br>
                <label for="email">Email:</label>
                <center><input type="text" id="m_email" placeholder="eg jane_doe@gmail.com" name="m_email"  required></center>
                <br>
                <br>
                <center><button onclick="verify()" name="mpesa" id="mpesa" style="background-color: greenyellow; border-radius:20px;font-size:25px;width:100px">Send</button></center>
            </form>
        </div>
         <?php
          if(isset($_POST['mpesa'])) {
            //stkpush
            echo "<script>
            var w = document.getElementById('spinner');
            w.style.display = 'block';
            document.getElementById('svg-para').innerHTML = 'Payment in progress'; 
            
            </script>";
            date_default_timezone_set('Africa/Nairobi');
            $dateTime = new DateTime();
            $time_stamp = $dateTime->format('YmdHis');
            #Getting data from html
            $amount = $_POST['m_amount'];
            $phone_no = $_POST['m_phone_no'];
            $name = $_POST['m_name'];
            # business short code
            $business_short_code = "174379";
            # access token
            function access_token(){
              $consumer_key = ""; ///Enter user consumer key
              $secret_key = ""; ///Enter user secrete key
              $access_token_url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
              $access = curl_init($access_token_url);
              curl_setopt($access, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($access, CURLOPT_HEADER, false);
              curl_setopt($access, CURLOPT_USERPWD, $consumer_key . ':' . $secret_key);
              $response = curl_exec($access);
              $status = curl_getinfo($access, CURLINFO_HTTP_CODE);
              $response = json_decode($response);
              $access_token = $response->access_token;
              curl_close($access);
              return $access_token; 
            }
            $time_stamp = date('YmdHis');
            # generating password
            function password(){
              global $time_stamp;
              $pass_key = ""; ///Enter passkey
              $business_short_code = "174379";
              $password = base64_encode($business_short_code . $pass_key . $time_stamp);  
              return $password;
            }
            # initiating the payment
            $process_request_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
            $process_request_header = array(
                'Authorization: Bearer '. access_token(),
                'Content-Type: application/json'
            );
            # parameters to be passed
            $data = json_encode(array(
                "TransactionType" => "CustomerBuyGoodsOnline",
                "Timestamp" => $time_stamp,
                "PartyA" => $phone_no,
                "PartyB" => "8190950",
                "ShortCode" => $business_short_code,
                "BusinessShortCode" => $business_short_code,
                "Password" => password(),
                "PhoneNumber" => $phone_no,
                "Amount" => $amount,
                "Remarks" => "check payment",
                "AccountReference" => $name,
                "CallBackURL" => "https://46d1-105-160-59-248.eu.ngrok.io/result",
                "TransactionDesc" => "Donation"
            ));
            # initiating requests
            $process = curl_init($process_request_url);
            curl_setopt($process, CURLOPT_URL, $process_request_url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $process_request_header);
            curl_setopt($process, CURLOPT_POST, true);
            curl_setopt($process, CURLOPT_POSTFIELDS, $data);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($process);
            if(curl_errno($process)){
                $error_message = curl_error($process);
                echo "<script> 
                var w = document.getElementById('spinner');
                w.style.display = 'block';
                document.getElementById('svg-para').innerHTML = 'Payment in progress'; 
                setTimeout(spinner, 5000);
                var i = document.getElementById('svg-spinner');
                i.style.display = 'none';
               </script>";
                // Handle the error here
            }
            curl_close($process);
            $output = json_decode($output);
            $response_code = $output->ResponseCode;
            
          }
        ?>
</body>