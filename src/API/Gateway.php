<?php

namespace Tngnt\PBI\API;

use Tngnt\PBI\Client;

/**
 * Class Gateway
 *
 * @package Tngnt\PBI\API
 */
class Gateway
{
    const GATEWAY_URL = "https://api.powerbi.com/v1.0/myorg/gateways/%s/datasources/%s";
    const GATEWAY_GROUP_URL = "https://api.powerbi.com/v1.0/myorg/groups/%s/datasets/%s/Default.BindToGateway";
    const GATEWAY = "https://api.powerbi.com/v1.0/myorg/gateways";
    const GATEWAY_DATARESOURCE = "https://api.powerbi.com/v1.0/myorg/gateways/%s/datasources";

    /**
     * The SDK client
     *
     * @var Client
     */
    private $client;

    /**
     * Table constructor.
     *
     * @param Client $client The SDK client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Sets the credentials for a specified datasource
     *
     * @param string $gatewayId    The ID of the gateway
     * @param string $datasourceId The ID of the datasource
     * @param array  $credentials  The credentials in the following format: ['credentialsType => 'basic',
     *                             'basicCredentials' => ['username' => '', 'password' => '']]
     *
     * @return \Tngnt\PBI\Response
     */
    public function setCredentials($gatewayId, $datasourceId, array $credentials)
    {
        $url = sprintf(self::GATEWAY_URL, $gatewayId, $datasourceId);

        $response = $this->client->request(Client::METHOD_PATCH, $url, $credentials);

        return $this->client->generateResponse($response);
    }

    /**
     * Notes:
     * @author:hongkai - 9/10/2020 3:09 PM
     * @param $groupId
     * @param $datasetId
     * @param array $credentials
     *              datasourceObjectIds
     *              gatewayObjectId
     * @return \Tngnt\PBI\Response
     */
    public function setGroupCredentials($groupId, $datasetId, array $credentials)
    {
        $url = sprintf(self::GATEWAY_GROUP_URL, $groupId, $datasetId);

        $response = $this->client->requestFormUrlencoded(Client::METHOD_POST, $url, $credentials);

        return $this->client->generateResponse($response);
    }


    public function getGateways()
    {
        $url = self::GATEWAY;

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }


    public function getGatewayDataresources($gatewayId)
    {
        $url = sprintf(self::GATEWAY_DATARESOURCE, $gatewayId);

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }
}
