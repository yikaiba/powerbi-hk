<?php

namespace Tngnt\PBI\API;

use Tngnt\PBI\Client;
use Tngnt\PBI\Model\Dataset as DatasetModel;
use Tngnt\PBI\Response;

/**
 * Class Dataset.
 */
class Dataset
{
    const DATASET_URL = 'https://api.powerbi.com/v1.0/myorg/datasets';
    const GROUP_DATASET_URL = 'https://api.powerbi.com/v1.0/myorg/groups/%s/datasets';
    const GROUP_DATASET_ONE_URL = 'https://api.powerbi.com/v1.0/myorg/groups/%s/datasets/%s';

    const REFRESH_DATASET_URL = 'https://api.powerbi.com/v1.0/myorg/datasets/%s/refreshes';
    const GROUP_REFRESH_DATASET_URL = 'https://api.powerbi.com/v1.0/myorg/groups/%s/datasets/%s/refreshes';


    const GATEWAY_DATASOURCES_URL = 'https://api.powerbi.com/v1.0/myorg/datasets/%s/Default.GetBoundGatewayDatasources';
    const GROUP_GATEWAY_DATASOURCES_URL = 'https://api.powerbi.com/v1.0/myorg/groups/%s/datasets/%s/Default.GetBoundGatewayDatasources';


    const REFRESH_DATASET_SCHEDULE_URL = 'https://api.powerbi.com/v1.0/myorg/datasets/%s/refreshSchedule';
    const GROUP_REFRESH_DATASET_SCHEDULE_URL = 'https://api.powerbi.com/v1.0/myorg/groups/%s/datasets/%s/refreshSchedule';

    /**
     * The SDK client
     *
     * @var Client
     */
    private $client;

    /**
     * Dataset constructor.
     *
     * @param Client $client The SDK client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieves the datasets from the PowerBI API
     *
     * @param string|null $groupId An optional group ID
     *
     * @return Response
     */
    public function getDatasets($groupId = null)
    {
        $url = $this->getUrl($groupId);

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }

    /**
     * Notes:
     * @author:hongkai - 9/24/2020 1:50 PM
     * @param $groupId
     * @param $datasetId
     * @return Response
     */
    public function getDatasetOne($groupId,$datasetId)
    {

        $url = sprintf(self::GROUP_DATASET_ONE_URL, $groupId, $datasetId);

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }

    /**
     * Refresh the dataset from the PowerBI API
     *
     * @param string      $datasetId An dataset ID
     * @param string|null $groupId   An optional group ID
     * @param bool|null   $notify    set if user recibe notify mail
     *
     * @return Response
     */
    public function refreshDataset($datasetId, $groupId = null, $notify = true)
    {
        $url = $this->getRefreshUrl($groupId, $datasetId);
        if ($notify) {
            $response = $this->client->request(Client::METHOD_POST, $url, ['notifyOption' => 'MailOnFailure']);
        } else {
            $response = $this->client->request(Client::METHOD_POST, $url);
        }

        return $this->client->generateResponse($response);
    }

    /**
     * Notes:
     * @author:hongkai - 3/22/2022 1:54 PM
     * @param $datasetId
     * @param null $groupId
     * @param string $limit
     * @return Response
     */
    public function getRefreshDataset($datasetId, $groupId = null, $limit = '')
    {
        $url = $this->getRefreshUrl($groupId, $datasetId);
        if ($limit) {
            $url .= '?$top='.$limit;
        }
//        var_dump($url);exit;
        $response = $this->client->request(Client::METHOD_GET, $url);
        return $this->client->generateResponse($response);
    }

    public function getGatewayDatasources($datasetId, $groupId = null)
    {
        $url = $this->getGatewayDatasourcesUrl($groupId, $datasetId);
//        var_dump($url);exit;
        $response = $this->client->request(Client::METHOD_GET, $url);
        return $this->client->generateResponse($response);
    }

    /**
     * Notes:
     * @author:hongkai - 5/19/2022 2:21 PM
     * @param $groupId
     * @param $datasetId
     * @return string
     */
    private function getGatewayDatasourcesUrl($groupId, $datasetId)
    {
        if ($groupId) {
            return sprintf(self::GROUP_GATEWAY_DATASOURCES_URL, $groupId, $datasetId);
        }

        return sprintf(self::GATEWAY_DATASOURCES_URL, $datasetId);
    }

    /**
     * Notes:
     * @author:hongkai - 3/22/2022 1:54 PM
     * @param $datasetId
     * @param null $groupId
     * @return Response
     */
    public function getRefreshDatasetSchedule($datasetId, $groupId = null)
    {
        $url = $this->getRefreshScheduleUrl($groupId, $datasetId);
        $response = $this->client->request(Client::METHOD_GET, $url);
        return $this->client->generateResponse($response);
    }

    /**
     * Create a new dataset on PowerBI.
     *
     * @param DatasetModel $dataset The dataset model
     * @param string|null  $groupId An optional group ID
     *
     * @return Response
     */
    public function createDataset(DatasetModel $dataset, $groupId = null)
    {
        $url = $this->getUrl($groupId);

        $response = $this->client->request(client::METHOD_POST, $url, $dataset);

        return $this->client->generateResponse($response);
    }

    /**
     * Notes:
     * @author:hongkai - 6/18/2021 10:58 AM
     * @param $groupId
     * @param $datasetId
     * @return Response
     */
    public function deleteDataset($groupId,$datasetId)
    {
        $url = sprintf(self::GROUP_DATASET_ONE_URL, $groupId, $datasetId);

        $response = $this->client->request(Client::METHOD_DELETE, $url);

        return $this->client->generateResponse($response);
    }

    /**
     * Helper function to format the request URL.
     *
     * @param string|null $groupId An optional group ID
     *
     * @return string
     */
    private function getUrl($groupId)
    {
        if ($groupId) {
            return sprintf(self::GROUP_DATASET_URL, $groupId);
        }

        return self::DATASET_URL;
    }

    /**
     * Helper function to format the request URL.
     *
     * @param string      $datasetId id from dataset
     * @param string|null $groupId   An optional group ID
     *
     * @return string
     */
    private function getRefreshUrl($groupId, $datasetId)
    {
        if ($groupId) {
            return sprintf(self::GROUP_REFRESH_DATASET_URL, $groupId, $datasetId);
        }

        return sprintf(self::REFRESH_DATASET_URL, $datasetId);
    }


    /**
     * Notes:
     * @author:hongkai - 3/22/2022 1:55 PM
     * @param $groupId
     * @param $datasetId
     * @return string
     */
    private function getRefreshScheduleUrl($groupId, $datasetId)
    {
        if ($groupId) {
            return sprintf(self::GROUP_REFRESH_DATASET_SCHEDULE_URL, $groupId, $datasetId);
        }

        return sprintf(self::REFRESH_DATASET_SCHEDULE_URL, $datasetId);
    }
}
