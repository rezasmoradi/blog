<?php


namespace Core;


use App\Models\User;

class Auth extends Model
{
    protected string $label = 'Auth';

    protected array $fillables = ['token'];

    public static function login($username, $password)
    {
        $result = User::builder()->where(['username' => $username])->first();
        $user = $result[0];
        $user = (object)$user->properties();

        if ($user) {
            return password_verify($password, $user->password) ? $user : false;
        } else {
            return false;
        }
    }

    public static function user()
    {
        $auth = explode('.', self::get_bearer_token());
        $username = json_decode(base64_decode($auth[1]), true)['username'];
        $result = User::builder()->where(['username' => $username])->first();
        $user = array_flatten($result)[0];
        return (object)$user->properties();
    }

    public static function generate_jwt($headers, $payload, $secret = '5af1f5d9160beba119a5622cd58190e6629632bafeb7154fb84f569ed1445814')
    {
        $headers_encoded = self::base64url_encode(json_encode($headers));

        $payload_encoded = self::base64url_encode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = self::base64url_encode($signature);

        return "$headers_encoded.$payload_encoded.$signature_encoded";
    }

    public static function check($jwt, $secret = '5af1f5d9160beba119a5622cd58190e6629632bafeb7154fb84f569ed1445814')
    {
        if ($jwt) {
            // split the jwt
            $tokenParts = explode('.', $jwt);
            $header = base64_decode($tokenParts[0]);
            $payload = base64_decode($tokenParts[1]);
            $signature_provided = $tokenParts[2];

            // check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
            $expiration = json_decode($payload)->exp;
            $is_token_expired = ($expiration - time()) < 0;

            // build a signature based on the header and payload using the secret
            $base64_url_header = self::base64url_encode($header);
            $base64_url_payload = self::base64url_encode($payload);
            $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
            $base64_url_signature = self::base64url_encode($signature);

            // verify it matches the signature provided in the jwt
            $is_signature_valid = ($base64_url_signature === $signature_provided);

            return !$is_token_expired || $is_signature_valid;
        } else {
            return false;
        }
    }

    public static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function get_authorization_header()
    {
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } else if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        return $headers;
    }

    public static function get_bearer_token()
    {
        $headers = self::get_authorization_header();

        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}