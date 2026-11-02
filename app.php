<?php

use Bandwidth\Api\MFAApi;
use Bandwidth\Configuration;
use Bandwidth\Model\CodeRequest;
use Bandwidth\Model\VerifyCodeRequest;
use GuzzleHttp\Client;

require __DIR__ . '/vendor/autoload.php';

$missing = array_filter(
    ['BW_ACCOUNT_ID', 'BW_CLIENT_ID', 'BW_CLIENT_SECRET', 'BW_NUMBER', 'BW_VOICE_APPLICATION_ID', 'BW_MESSAGING_APPLICATION_ID'],
    fn ($name) => getenv($name) === false
);
if ($missing) {
    fwrite(STDERR, "Please set the environmental variables defined in the README\n");
    exit(1);
}

$BW_ACCOUNT_ID = getenv('BW_ACCOUNT_ID');
$BW_NUMBER = getenv('BW_NUMBER');
$BW_VOICE_APPLICATION_ID = getenv('BW_VOICE_APPLICATION_ID');
$BW_MESSAGING_APPLICATION_ID = getenv('BW_MESSAGING_APPLICATION_ID');

$config = Configuration::getDefaultConfiguration()
    ->setClientId(getenv('BW_CLIENT_ID'))
    ->setClientSecret(getenv('BW_CLIENT_SECRET'));
$mfaApi = new MFAApi(new Client(), $config);

const SCOPE = 'sample';

function prompt(string $text): string
{
    echo $text;
    $line = fgets(STDIN);
    if ($line === false) {
        exit(1);
    }
    return trim($line);
}

$phoneNumber = prompt('Enter the phone number to send the code to (E.164, e.g. +15554443333): ');
if (!preg_match('/^\+[1-9]\d{1,14}$/', $phoneNumber)) {
    fwrite(STDERR, "Phone number must be in E.164 format, e.g. +15554443333\n");
    exit(1);
}

$method = strtolower(prompt('Send the code by messaging or voice? [messaging]: ')) ?: 'messaging';
if ($method !== 'messaging' && $method !== 'voice') {
    fwrite(STDERR, "Method must be messaging or voice\n");
    exit(1);
}

echo "Sending a code by {$method} to {$phoneNumber}\n";
try {
    $codeRequest = new CodeRequest([
        'to' => $phoneNumber,
        'from' => $BW_NUMBER,
        'application_id' => $method === 'voice' ? $BW_VOICE_APPLICATION_ID : $BW_MESSAGING_APPLICATION_ID,
        'scope' => SCOPE,
        'message' => 'Your temporary {NAME} {SCOPE} code is {CODE}',
        'digits' => 6,
    ]);
    if ($method === 'voice') {
        $mfaApi->generateVoiceCode($BW_ACCOUNT_ID, $codeRequest);
    } else {
        $mfaApi->generateMessagingCode($BW_ACCOUNT_ID, $codeRequest);
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Failed to send code: ' . $e->getMessage() . "\n");
    exit(1);
}

$code = prompt('Enter the code you received: ');
try {
    $verifyRequest = new VerifyCodeRequest([
        'to' => $phoneNumber,
        'scope' => SCOPE,
        'expiration_time_in_minutes' => 3,
        'code' => $code,
    ]);
    $valid = $mfaApi->verifyCode($BW_ACCOUNT_ID, $verifyRequest)->getValid() === true;
} catch (Throwable $e) {
    fwrite(STDERR, 'Failed to verify code: ' . $e->getMessage() . "\n");
    exit(1);
}

echo $valid ? "Code verified\n" : "Code not verified\n";
exit($valid ? 0 : 1);
