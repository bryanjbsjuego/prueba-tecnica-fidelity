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

    private function getCustomerAreaClient(): SoapClient
    {
        // Log::info($this->getCustomerAreaMethods());
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
                'foreignID' => null
            ];

            Log::info('SOAP Request - SynchroAndLogin', $params);

            $response = $client->__soapCall('SynchroAndLogin', [$params]);

            Log::info('SOAP Response - SynchroAndLogin', (array)$response);

            $result = $this->convertToArray($response);

            // Validar answerCode
            if (!isset($result['answerCode']) || $result['answerCode'] != 0) {
                throw new \Exception('Error en la sincronización con el servicio');
            }

            if (!isset($result['sessionID'])) {
                throw new \Exception('No se recibió sessionID del servicio');
            }

            return $result;

        } catch (SoapFault $e) {
            Log::error('SOAP Error - SynchroAndLogin: ' . $e->getMessage());
            throw new \Exception('Error en SynchroAndLogin: ' . $e->getMessage());
        }
    }

    /**
     * Método: SearchCustomerByPersonalData (Terminal)
     * Solo el email es obligatorio según los requerimientos
     */
    public function searchCustomerByPersonalData(string $sessionID, string $email): array
    {
        try {
            $client = $this->getTerminalClient();

            $params = [
                'sessionID' => $sessionID,
                'email' => $email,
                'name' => '',
                'surname' => '',
                'birthdate' => '',
                'birthdateDay' => 0,
                'birthdateMonth' => 0,
                'birthdateYear' => 0,
                'cellphone' => '',
                'facebookId' => '',
                'card' => '',
                'identityCard' => '',
                'pagination' => 0
            ];

            Log::info('SOAP Request - SearchCustomerByPersonalData', ['sessionID' => $sessionID, 'email' => $email]);

            $response = $client->__soapCall('SearchCustomerByPersonalData', [$params]);

            Log::info('SOAP Response - SearchCustomerByPersonalData', (array)$response);

            $result = $this->convertToArray($response);

            // Validar answerCode
            if (!isset($result['answerCode']) || $result['answerCode'] != 0) {
                throw new \Exception('Error al buscar el cliente');
            }

            return $result;

        } catch (SoapFault $e) {
            Log::error('SOAP Error - SearchCustomerByPersonalData: ' . $e->getMessage());
            throw new \Exception('Error en SearchCustomerByPersonalData: ' . $e->getMessage());
        }
    }

    /**
     * Validar el customer según los requerimientos
     */
    public function validateCustomer(array $searchResult): array
    {
        if (!isset($searchResult['customerList'])) {
            throw new \Exception('La cuenta no se encuentra registrada en nuestro sistema');
        }

        $customerList = $searchResult['customerList'];

        // Si customerList es un array de clientes, tomar el primero
        if (isset($customerList[0])) {
            $customer = $customerList[0];
        } else {
            $customer = $customerList;
        }

        // Validar status = 1
        if (!isset($customer['status']) || $customer['status'] != 1) {
            throw new \Exception('La cuenta no se encuentra registrada en nuestro sistema');
        }

        // Validar customer_area_status = 1 o 4
        if (!isset($customer['customer_area_status']) ||
            ($customer['customer_area_status'] != 1 && $customer['customer_area_status'] != 4)) {
            throw new \Exception('La cuenta no se encuentra registrada en nuestro sistema');
        }

        // Extraer userName
        if (!isset($customer['personalInfo']['userName'])) {
            throw new \Exception('No se pudo obtener el nombre de usuario');
        }

        return [
            'userName' => $customer['personalInfo']['userName'],
            'customer' => $customer
        ];
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

            $result = $this->convertToArray($response);

            // Validar answerCode
            if (!isset($result['answerCode']) || $result['answerCode'] != 0) {
                throw new \Exception('Error en la sincronización con Customer Area');
            }

            if (!isset($result['session'])) {
                throw new \Exception('No se recibió session de Customer Area');
            }

            return $result;

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
                'deviceType' => 0,
                'versionFW' => '',
                'rememberMe' => '',
                'calculateBillingData' => '',
                'playerID' => ''
            ];

            Log::info('SOAP Request - Login CA', ['session' => $session, 'username' => $username]);

            $response = $client->__soapCall('Login', [$params]);

            Log::info('SOAP Response - Login CA', (array)$response);

            $result = $this->convertToArray($response);

            // Validar answerCode
            if (!isset($result['answerCode']) || $result['answerCode'] != 0) {
                throw new \Exception('Credenciales incorrectas');
            }

            if (!isset($result['customer'])) {
                throw new \Exception('No se recibió información del cliente');
            }

            return $result;

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
                'sessionID' => $session,
                'catalogId' => 0,
                'prizeID' => 0,
                'categoryId' => 0,
                'prizeEnabled' => null,
                'prizeCode' => "",
                'useBalanceCustomer' => false,
                'pagination' => [
                    'InitLimit' => $initLimit,
                    'rowCount' => $rowCount,
                    'orders' => [
                        'columnName' => "",
                        'criterial' => ""
                    ]
                ],
                'onlyOutstandingPrize' => false,
                'filterKind' => 0,
                'lastDays' => 0,
                'prizesCount' => 0,
                'onlyForGift' => false,
                'showOnlyPrizeNotExceed' => false
            ];

            Log::info('SOAP Request - GetPrizesByCustomer', $params);

            $response = $client->__soapCall('GetPrizesByCustomer', [$params]);

            Log::info('SOAP Response - GetPrizesByCustomer');

            $result = $this->convertToArray($response);

            // Validar answerCode
            if (!isset($result['answerCode']) || $result['answerCode'] != 0) {
                throw new \Exception('Error al obtener los premios');
            }

            return $result;

        } catch (SoapFault $e) {
            Log::error('SOAP Error - GetPrizesByCustomer: ' . $e->getMessage());
            Log::error('SOAP Error en linea - GetPrizesByCustomer: ' . $e->getLine());
            throw new \Exception('Error en GetPrizesByCustomer: ' . $e->getMessage());
        }
    }

    private function convertToArray(mixed $data): mixed
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->convertToArray($value);
            }
            return $data;
        }

        return $data;
    }

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
