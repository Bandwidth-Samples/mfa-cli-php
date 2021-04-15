<?php

require __DIR__ . '/../vendor/autoload.php';

$config = new BandwidthLib\Configuration(
    array(
        'twoFactorAuthBasicAuthUserName' => getenv("BW_USERNAME"),
        'twoFactorAuthBasicAuthPassword' => getenv("BW_PASSWORD"),
    )
);

$client = new BandwidthLib\BandwidthClient($config);
$authClient = $client->getTwoFactorAuth()->getClient();
$BW_ACCOUNT_ID = getenv("BW_ACCOUNT_ID");
$BW_PHONE_NUMBER = getenv("BW_PHONE_NUMBER");
$BW_VOICE_APPLICATION_ID = getenv("BW_VOICE_APPLICATION_ID");
$BW_MESSAGING_APPLICATION_ID = getenv("BW_MESSAGING_APPLICATION_ID");

$recipient_phone_number = readline("Please enter your phone number in E164 format (+15554443333): ");

$delivery_method = readline("Select your method to receive your 2FA request. Please enter 'voice' or 'messaging': ");


if (strcmp($delivery_method, "messaging") == 0) {

  $fromPhone = $BW_PHONE_NUMBER;
  $toPhone = $recipient_phone_number;
  $applicationId = $BW_MESSAGING_APPLICATION_ID;
  $scope = 'scope';
  $digits = 6;

  $body = new BandwidthLib\TwoFactorAuth\Models\TwoFactorCodeRequestSchema();
  $body->from = $fromPhone;
  $body->to = $toPhone;
  $body->applicationId = $applicationId;
  $body->scope = $scope;
  $body->digits = $digits;
  $body->message = "Your temporary {NAME} {SCOPE} code is {CODE}";

  $authClient->createMessagingTwoFactor($BW_ACCOUNT_ID, $body);

  $code = readline("Please enter your code: ");

  $body = new BandwidthLib\TwoFactorAuth\Models\TwoFactorVerifyRequestSchema();
  $body->from = $fromPhone;
  $body->to = $toPhone;
  $body->applicationId = $applicationId;
  $body->scope = $scope;
  $body->code = $code;
  $body->digits = $digits;
  $body->expirationTimeInMinutes = 3;

  $response = $authClient->createVerifyTwoFactor($BW_ACCOUNT_ID, $body);
  $strn = "Auth status: " . var_export($response->getResult()->valid, true) . "\n";
  echo $strn;

} elseif (strcmp($delivery_method, "voice") == 0) {

  $fromPhone = $BW_PHONE_NUMBER;
  $toPhone = $recipient_phone_number;
  $applicationId = $BW_VOICE_APPLICATION_ID;
  $scope = 'scope';
  $digits = 6;

  $body = new BandwidthLib\TwoFactorAuth\Models\TwoFactorCodeRequestSchema();
  $body->from = $fromPhone;
  $body->to = $toPhone;
  $body->applicationId = $applicationId;
  $body->scope = $scope;
  $body->digits = $digits;
  $body->message = "Your temporary {NAME} {SCOPE} code is {CODE}";

  $authClient->createVoiceTwoFactor($BW_ACCOUNT_ID, $body);

  $code = readline("Please enter your code: ");

  $body = new BandwidthLib\TwoFactorAuth\Models\TwoFactorVerifyRequestSchema();
  $body->from = $fromPhone;
  $body->to = $toPhone;
  $body->applicationId = $applicationId;
  $body->scope = $scope;
  $body->code = $code;
  $body->digits = $digits;
  $body->expirationTimeInMinutes = 3;

  $response = $authClient->createVerifyTwoFactor($BW_ACCOUNT_ID, $body);
  $strn = "Auth status: " . var_export($response->getResult()->valid, true) . "\n";
  echo $strn;

}

?>
