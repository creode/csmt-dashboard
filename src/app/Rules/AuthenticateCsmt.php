<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AuthenticateCsmt implements Rule
{
    private $error = 'There was an error communicating with CSMT';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        array &$credentials,
        $usernameField,
        $passwordField
    ) {
        //
        $this->credentials = &$credentials;
        $this->usernameField = $usernameField;
        $this->passwordField = $passwordField;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // just makes things a bit easier to read..
        $url = $value;

        if (isset($this->credentials[$this->usernameField]) && isset($this->credentials[$this->passwordField])) {
            return $this->handshakeWithCredentials($url);    
        } else {
            return $this->handshakeWithoutCredentials($url);
        }
    }


    private function handshakeWithCredentials($url) {
        $client = new \GuzzleHttp\Client();

        try {
            $requestParams = array(
                'stream' => false,
                'auth' => [
                    $this->credentials[$this->usernameField], 
                    $this->credentials[$this->passwordField]
                ]   
            );

            $res = $client->get(
                $url . '?command=version',
                $requestParams
            );

            $body = json_decode(strip_tags($res->getBody()));

            if ($res->getStatusCode() !== 200) {
                throw new \Exception($body, $res->getStatusCode());
            }
        } catch (\Exception $e) {
            switch($e->getCode()) {
                case 401:
                    $this->error = 'Authentication - Failed. Are your credentials correct? ' . $e->getMessage();
                    break;
                case 404:
                    $this->error = 'Authentication - Not found';
                    break;
                default:
                    $this->error = 'Authentication - Error: '. $e->getMessage();
                    break;
            }
            return false;
        }

        return true;
    }




    private function handshakeWithoutCredentials($url) {
        $client = new \GuzzleHttp\Client();

        try {
            $requestParams = array(
                'stream' => false
            );

            $res = $client->get(
                $url . '?command=handshake',
                $requestParams
            );

            $body = json_decode(strip_tags($res->getBody()));

            if ($res->getStatusCode() !== 200) {
                throw new \Exception($body, $res->getStatusCode());
            }

            if (!isset($body->user) || !isset($body->pass)) {
                throw new \Exception($body->message, $res->getStatusCode());
            }

            $this->credentials[$this->usernameField] = $body->user;
            $this->credentials[$this->passwordField] = $body->pass;

        } catch (\Exception $e) {
            switch($e->getCode()) {
                case 401:
                    $this->error = 'Authentication - Failed. Tool must be public accessible to perform handshake. Is authentication already setup? ' . $e->getMessage();
                    break;
                case 404:
                    $this->error = 'Authentication - Not found';
                    break;
                default:
                    $this->error = 'Authentication - Error: '. $e->getMessage();
                    break;
            }
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->error;
    }
}
