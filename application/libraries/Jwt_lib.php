<?php

use \Firebase\JWT\JWT;

class JWT_lib
{

    var $key;
    var $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->key = base64_decode($this->CI->config->item('jwt_key'));
    }

    function validateAPI()
    {
        $token = $this->getJWTFromRequest();
        return $this->validateJWTToken($token);
    }

    function validateAPIS()
    {
        $token = $this->getJWTFromRequests();
        return $this->validateJWTToken($token);
    }

    function getJWTFromRequests(): string
{
    $authenticationHeader = $this->CI->input->get_request_header('Authorization');

    if (is_null($authenticationHeader) || empty(trim($authenticationHeader))) {
        throw new Exception('Missing or invalid Token in request');
    }

    $tokenParts = explode(' ', $authenticationHeader);

    if (count($tokenParts) === 2 && strtolower($tokenParts[0]) === 'bearer') {
        return trim($tokenParts[1]); // Bearer <token>
    } elseif (count($tokenParts) === 1) {
        return trim($tokenParts[0]); // Just <token>
    } else {
        throw new Exception('Missing or invalid Token in request');
    }
}


    function getJWTFromRequest(): string
    {
        $authenticationHeader = $this->CI->input->get_request_header('Authorization');
        if (is_null($authenticationHeader)) { //JWT is absent
            throw new Exception('Missing or invalid Token in request');
        }
        //JWT is sent from client in the format Bearer XXXXXXXXX
        $token =  explode(' ', $authenticationHeader);

        if (!isset($token[1])) { //JWT is absent
            throw new Exception('Missing or invalid Token in request');
        }

        return $token[1];
    }

    function validateJWTToken(string $encodedToken)
    {
        try {
            $data = $this->decode($encodedToken);
            if ($data)
                return $data->data;
            throw new Exception('Missing or invalid Token in request');
        } catch (Exception $e) {
            throw new Exception('Missing or invalid Token in request');
        }
    }

    function encode($jwt_data = array(), $time = 600)
    {
        $tokenId = base64_encode(random_bytes(32));
        $issuedAt = time();
        $notBefore = $issuedAt;
        $expire = $notBefore + $time;            // Adding 60 seconds
        //$serverName = ; // Retrieve the server name from config file

        /*
         * Create the token as an array
         */
        $data = [
            'iat' => $issuedAt, // Issued at: time when the token was generated
            'jti' => $tokenId, // Json Token Id: an unique identifier for the token
            //'iss' => $serverName, // Issuer
            'nbf' => $notBefore, // Not before
            'exp' => $expire, // Expire
            'data' => $jwt_data
        ];

        return $jwt = JWT::encode(
            $data, //Data to be encoded in the JWT
            $this->key, // The signing key
            'HS512'
        );
    }

    function decode($jwt = false)
    {
        try {
            $data = JWT::decode($jwt, $this->key, array('HS512'));
            return $data;
        } catch (Exception $e) {
            return false;
        }
    }
}
