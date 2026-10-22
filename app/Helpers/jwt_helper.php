<?php
/*
PT ITSHOP BISNIS DIGITAL
Website: https://itshop.biz.id
Toko Online: ITSHOP Purwokerto (https://Tokopedia.com/itshoppwt, https://Shopee.co.id/itshoppwt, https://toco.id/store/itshop-purwokerto)
Dibuat oleh: Hari Wicaksono, S.Kom
06-2022
*/

use App\Modules\User\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function publicKey()
{
    $pubKeyPath = WRITEPATH . 'keys/public.pem';
    if (file_exists($pubKeyPath)) {
        return file_get_contents($pubKeyPath);
    }
    // Fallback to embedded key if file is not found
    $publicKey = <<<EOD
		-----BEGIN PUBLIC KEY-----
		MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjDM10aqzPPDHDKDG2BDX
		ys2cG92xCAf1lKJyouHcUF9BYGgopGvdBqGNoUIA6E3/hu8kyUfRnKzF4vmSDOtI
		SB0AWL//er4XitmVH9BAauMQ80/VitxJGaI/OUWj1evcmZ1u9B7VeZtNVz4+6wwQ
		Mjh633hzFAd3hOfXoNrTRxzEdco2g8BEdFkmi+GBRu9PZhr1qL+uHKWEBkr9PkZl
		Hbf5bAw5iC0qH5aHvfBYZ2lVTY4vUkkWTycBfLQNc9m2r8603Xrnx3bm9ncqrfGt
		oaFcLn02CRURVPEqIJBN+zP1uxjK2lkWbdVMG1it8AUrbvPVrzC3RVR96PHocitm
		TwIDAQAB
		-----END PUBLIC KEY-----
		EOD;
    return $publicKey;
}

function privateKey()
{
    $privKeyPath = WRITEPATH . 'keys/private.pem';
    if (file_exists($privKeyPath)) {
        return file_get_contents($privKeyPath);
    }
    // Fallback to embedded key if file is not found
    $privateKey = <<<EOD
    -----BEGIN PRIVATE KEY-----
    MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCMMzXRqrM88McM
    oMbYENfKzZwb3bEIB/WUonKi4dxQX0FgaCika90GoY2hQgDoTf+G7yTJR9GcrMXi
    +ZIM60hIHQBYv/96vheK2ZUf0EBq4xIzT9WK3EkZoj85RaPV69yZnW70HtV5m01X
    Pj7rDBAyOHrfeHMUB3eE59eg2tNHHMR1yjaDwER0WSaL4YFG709mGvWov64cpYQG
    Sv0+RmUdt/lsDDmILSofloe98FhnaVVNji9SSRZPJwF8tA1z2bavzrTdeufHdub2
    dyqt8a2hoVwufTYJFRFU8SogkE37M/W7GMraWRZt1UwbWK3wBStu89WvMLdFVH3o
    8ehyK2ZPAgMBAAECggEANmOthtPqqgMcrVz4Fx1doesnGzZjdeKklo942pC1qCZ0
    FeMClVrVCbwF1l/lOzn7Hg1LX5yxiE8Mi/PXAZULWxKJLmT5x1PUjUTA0fE39F93
    lXTMuoyf/UZQsba/Cv/v+lY1k5YcqgsiM2zN+uCYrer0XBp9xnDpIJHSuK/70/OV
    dgippiVqdIGK9oZPYpue/yxw5Z89kQ7YfkukCGsbT1bMJm4BpEXRTrRsd0TcGT/4
    1VixA3BBt2kRqhdgvnjvu76RxqfZr+RR4jpqAotfHz35K1gOF80/rnpDdz8llPwu
    TMYzShBU/eZyACy1b6PLNktkozJevvzd+Rsk2VhStQKBgQDFGmQ2SODNidLg/2R9
    HkWpzY1C+GmdKIgGdh0lZvZdXJPUR9ZSmoAOvd3aqnfP6K0ubJlB/SqRgQxNOSTF
    s4S1HjOq9X4plZHg71ysAJSqYo6NMphX7N9301/IfZ6U6/Kcl6UHJu+7FqwzoyYU
    hC4+ux/3u1bZ4RJz6RwVZN+4FQKBgQC2F/W9Yjegvl8wZeqLAlW5udjtk67y2/m/
    /LY2+P9E9F3FLmtB0uIHaiRNXOvOA/uUtBV4uhBDtlGCVcOZwUWNmhnQBaoaXUWc
    6qusNvztY/H+lQQ5JAOz5llu8+NUuGwfo8bkyj8zxuRisnrorl5LS4gT/0TsoKQK
    ktTxnyI50wKBgF0zHoGgslPeQM3NVann/47MOGibZ+09NhzACwYdio8Y8HnnQgur
    LLRLyTTVeHPiFMmn5kwStuc4eyWkyKOHQFAYKNxE2tQ0+la/ODXIzINIuRlA/FJP
    Glfhph/6blsCuqSa/BjGq4LutJxyH9aBrhBRkUsl1WqPB5exeWsQWM0FAoGAEibf
    S0qwKujvSj7Om57W9tWs5znylakSA25op6A4qiIF+vL+pCgmegC9r6yUoQvKl29A
    OLFgj2RsSabNAdW/Xjc/XXpDE6+6kcLe7CLrIar145/tNNj/OX676ZCMV9SuipTj
    7civdjTcbW5HTEydf7n+zaeDsmbtE1TBv3hSjBsCgYEAhLAkv4apisz8Nkv/rK4T
    g2mscrBfe8a2C4kRDIlKVp/0OsvzIcgNTMT5CxeWNkjjnpv4YEb73ONv+/KcFs76
    phJhoK853LzZHIu4m5xkkmFpV4d8iO0Ph2fcdqrpisQCGprLaFnKGlPyN6W1UrE6
    ByJroWTDEdwAJwkSX1giw48=
    -----END PRIVATE KEY-----
    EOD;
    return $privateKey;
}

/**
 * get access token from header
 * */
function getBearerToken(?string $authenticationHeader): ?string
{
    // HEADER: Get the access token from the header
    if (is_null($authenticationHeader)) { //JWT is absent
        throw new Exception('Missing or invalid JWT in request');
    }

    if (!empty($authenticationHeader)) {
        if (preg_match('/Bearer\s(\S+)/', $authenticationHeader, $matches)) {
            return $matches[1];
        } else {
            return $authenticationHeader;
        }
    }
    return null;
}

function getJWTFromRequest(?string $authenticationHeader): string
{
    if (is_null($authenticationHeader)) { //JWT is absent
        throw new Exception('Missing or invalid JWT in request');
    }
    //JWT is sent from client in the format Bearer XXXXXXXXX
    return explode(' ', $authenticationHeader)[1];
}

function validateJWTFromRequest(string $encodedToken)
{
    $key = publicKey();
    $decodedToken = JWT::decode($encodedToken, new Key($key, 'RS256'));
    $userModel = new UserModel();
    $userModel->findUserByEmailAddress($decodedToken->email);
}

function getSignedJWTForUser(string $email)
{
    $issuedAtTime = time();
    $tokenTimeToLive = env('JWT_TIME_TO_LIVE') ?? "28800000"; //Dalam milisecond = 480 menit atau 8 jam
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'email' => $email,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration,
    ];

    $secretKey = privateKey();
    $jwt = JWT::encode($payload, $secretKey, 'RS256');
    return $jwt;
}

function createJWT(string $email, $expMinutes = 15)
{
    $issuedAtTime = time();
    $tokenExpiration = $issuedAtTime + ($expMinutes * 60);
    $payload = [
        'email' => $email,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration,
    ];
    $secretKey = privateKey();
    return JWT::encode($payload, $secretKey, 'HS256');
}

function decodeJWT(string $token)
{
    $secretKey = privateKey();
    return JWT::decode($token, new Key($secretKey, 'HS256'));
}
