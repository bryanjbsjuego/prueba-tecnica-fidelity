<?php

namespace App\Services;

use SoapClient;
use SoapFault;
use Illuminate\Support\Facades\Log;

class SoapService
{
    private $terminalClient;
    private $customerAreaClient;

    private $terminalUrl;
    private $terminalSerial;
    private $terminalUser;
    private $terminalPassword;

    private $caUrl;
    private $caKind;
    private $caCampaignId;

    public function __construct()
    {
        $this->terminalUrl = config('services.soap.terminal_url');
        $this->terminalSerial = config('services.soap.terminal_serial');
        $this->terminalUser = config('services.soap.terminal_user');
        $this->terminalPassword = config('services.soap.terminal_password');

        $this->caUrl = config('services.soap.ca_url');
        $this->caKind = config('services.soap.ca_kind');
        $this->caCampaignId = config('services.soap.ca_campaign_id');
    }

    /**
     * Obtener cliente SOAP de Terminal
     */
    private function getTerminalClient(): SoapClient
    {
        if (!$this->terminalClient) {
            try {
                $this->terminalClient = new SoapClient($this->terminalUrl, [
                    'trace' => 1,
                    'exceptions' => true,
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'connection_timeout' => 30,
                    'soap_version' => SOAP_1_1,
                ]);
            } catch (SoapFault $e) {
                Log::error('Error creating Terminal SOAP client: ' . $e->getMessage());
                throw new \Exception('Error al conectar con el servicio Terminal');
            }
        }

        return $this->terminalClient;
    }

    /**
     * Obtener cliente SOAP de Customer Area
     */
    private function getCustomerAreaClient(): SoapClient
    {
        if (!$this->customerAreaClient) {
            try {
                $this->customerAreaClient = new SoapClient($this->caUrl, [
                    'trace' => 1,
                    'exceptions' => true,
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'connection_timeout' => 30,
                    'soap_version' => SOAP_1_1,
                ]);
            } catch (SoapFault $e) {
                Log::error('Error creating Customer Area SOAP client: ' . $e->getMessage());
                throw new \Exception('Error al conectar con el servicio Customer Area');
            }
        }

        return $this->customerAreaClient;
    }

    /**
     * Método: SynchroAndLogin (Terminal)
     */
    public function synchroAndLogin(): array
    {
        try {
            $client = $this->getTerminalClient();

            $params = [
                'serialNumber' => $this->terminalSerial,
                'username' => $this->terminalUser,
                'password' => $this->terminalPassword,
            ];

            Log::info('SOAP Request - SynchroAndLogin', $params);

            $response = $client->__soapCall('SynchroAndLogin', [$params]);

            Log::info('SOAP Response - SynchroAndLogin', (array)$response);

            return $this->convertToArray($response);

        } catch (SoapFault $e) {
            Log::error('SOAP Error - SynchroAndLogin: ' . $e->getMessage());
            throw new \Exception('Error en SynchroAndLogin: ' . $e->getMessage());
        }
    }

    /**
     * Método: SearchCustomerByPersonalData (Terminal)
     */
    public function searchCustomerByPersonalData(string $sessionID, string $email): array
    {
        try {
            $client = $this->getTerminalClient();

            $params = [
                'sessionID' => $sessionID,
                'email' => $email,
            ];

            Log::info('SOAP Request - SearchCustomerByPersonalData', $params);

            $response = $client->__soapCall('SearchCustomerByPersonalData', [$params]);

            Log::info('SOAP Response - SearchCustomerByPersonalData', (array)$response);

            return $this->convertToArray($response);

        } catch (SoapFault $e) {
            Log::error('SOAP Error - SearchCustomerByPersonalData: ' . $e->getMessage());
            throw new \Exception('Error en SearchCustomerByPersonalData: ' . $e->getMessage());
        }
    }

    /**
     * Método: Synchro (Customer Area)
     */
    public function synchroCustomerArea(): array
    {
        try {
            $client = $this->getCustomerAreaClient();

            $params = [
                'kind' => $this->caKind,
                'campaignId' => $this->caCampaignId,
            ];

            Log::info('SOAP Request - Synchro CA', $params);

            $response = $client->__soapCall('Synchro', [$params]);

            Log::info('SOAP Response - Synchro CA', (array)$response);

            return $this->convertToArray($response);

        } catch (SoapFault $e) {
            Log::error('SOAP Error - Synchro CA: ' . $e->getMessage());
            throw new \Exception('Error en Synchro CA: ' . $e->getMessage());
        }
    }

    /**
     * Método: Login (Customer Area)
     */
    public function loginCustomerArea(string $session, string $username, string $password): array
    {
        try {
            $client = $this->getCustomerAreaClient();

            $params = [
                'session' => $session,
                'username' => $username,
                'password' => $password,
            ];

            Log::info('SOAP Request - Login CA', ['session' => $session, 'username' => $username]);

            $response = $client->__soapCall('Login', [$params]);

            Log::info('SOAP Response - Login CA', (array)$response);

            return $this->convertToArray($response);

        } catch (SoapFault $e) {
            Log::error('SOAP Error - Login CA: ' . $e->getMessage());
            throw new \Exception('Error en Login CA: ' . $e->getMessage());
        }
    }

    /**
     * Método: GetPrizesByCustomer (Customer Area)
     */
    public function getPrizesByCustomer(string $session, int $initLimit = 0, int $rowCount = 100): array
    {
        try {
            $client = $this->getCustomerAreaClient();

            $params = [
                'session' => $session,
                'pagination' => [
                    'InitLimit' => $initLimit,
                    'rowCount' => $rowCount,
                ]
            ];

            Log::info('SOAP Request - GetPrizesByCustomer', $params);

            $response = $client->__soapCall('GetPrizesByCustomer', [$params]);

            Log::info('SOAP Response - GetPrizesByCustomer');

            return $this->convertToArray($response);

        } catch (SoapFault $e) {
            Log::error('SOAP Error - GetPrizesByCustomer: ' . $e->getMessage());
            throw new \Exception('Error en GetPrizesByCustomer: ' . $e->getMessage());
        }
    }

    /**
     * Convertir objeto SOAP a array
     */
    private function convertToArray($data): array
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            return array_map([$this, 'convertToArray'], $data);
        }

        return $data;
    }

    /**
     * Obtener último request XML (para debugging)
     */
    public function getLastRequest(): ?string
    {
        if ($this->terminalClient) {
            return $this->terminalClient->__getLastRequest();
        }

        if ($this->customerAreaClient) {
            return $this->customerAreaClient->__getLastRequest();
        }

        return null;
    }

    /**
     * Obtener último response XML (para debugging)
     */
    public function getLastResponse(): ?string
    {
        if ($this->terminalClient) {
            return $this->terminalClient->__getLastResponse();
        }

        if ($this->customerAreaClient) {
            return $this->customerAreaClient->__getLastResponse();
        }

        return null;
    }
}
