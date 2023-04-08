<?php

require_once dirname(__FILE__) .
    DIRECTORY_SEPARATOR .
    "fresapagos_response.php";

/**
 * Fresapagos API.
 */

class FresapagosApi
{
    /**
     * @var string The Fresapagos API key
     */
    private $api_key;

    /**
     * @var string The Fresapagos access token
     */
    private $access_token;

    /**
     * Initializes the class.
     *
     * @param string $api_key The Fresapagos API key
     * @param string $access_token The Fresapagos access token
     */
    public function __construct($api_key, $access_token)
    {
        $this->api_key = $api_key;
        $this->access_token = $access_token;
    }

    private function apiRequest($method, array $params = [])
    {
        // Send request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $headers = [
            "Content-Type: application/json",
            "x-api-key: " . $this->api_key,
            "x-access-token: " . $this->access_token,
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Build POST request
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);

        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        // Execute request
        curl_setopt($ch, CURLOPT_URL, $method);
        $data = new stdClass();
        if (curl_errno($ch)) {
            $data->message = curl_error($ch);
        } else {
            $data = json_decode(curl_exec($ch));
        }
        curl_close($ch);

        return new FresapagosResponse($data);
    }

    /**
     * Build the payment request.
     *
     * @param  array $params An array containing information to generate the payment link
     * @return stdClass An object containing the api response
     */
    public function buildPayment($params)
    {
        return $this->apiRequest(
            "https://api.mobbex.com/p/checkout",
            $params
        );
    }

    /**
     * Validate this payment.
     *
     * @param  array $id The unique id code for this payment
     * @return stdClass An object containing the api response
     */
    public function checkPayment($id)
    {
        return $this->apiRequest(
            "https://api.mobbex.com/2.0/transactions/status",
            $id
        );
    }
}
