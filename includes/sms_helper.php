<?php
function sendLiveSMS($mobile, $otp) {
    $apiKey = "hXYZmGkJvQO38sriAPoyb5L7HBql9RSDxpMTdWtwCKfj1IFzceGyRc1X6NJ5MVBY3QubCidrfUzAKZnk"; 
    
    // Fast2SMS অফিশিয়াল OTP রুট ফরম্যাট (শুধুমাত্র পাসওয়ার্ড বা ওটিপি সংখ্যা পাঠানো হবে)
    $field = array(
        "variables_values" => (string)$otp,
        "route" => "otp",
        "numbers" => $mobile
    );

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($field),
      CURLOPT_HTTPHEADER => array(
        "authorization: " . $apiKey,
        "accept: application/json",
        "content-type: application/json"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // ডিবাগ করার জন্য লগ ফাইলে রেসপন্স সেভ হবে
    file_put_contents('sms_log.txt', "Time: " . date('Y-m-d H:i:s') . " | Mobile: $mobile | Response: $response | Error: $err\n", FILE_APPEND);

    $result = json_decode($response, true);
    if (isset($result['return']) && $result['return'] == true) {
        return true;
    } else {
        return false;
    }
}
?>