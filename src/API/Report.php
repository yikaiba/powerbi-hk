<?php

namespace Tngnt\PBI\API;

use Tngnt\PBI\Client;

/**
 * Class Report.
 */
class Report
{
    const REPORT_URL = "https://api.powerbi.com/v1.0/myorg/reports";
    const REPORT_URL_ONE = "https://api.powerbi.com/v1.0/myorg/reports/%s";

    const GROUP_REPORT_URL = "https://api.powerbi.com/v1.0/myorg/groups/%s/reports";
    const GROUP_REPORT_EMBED_URL = "https://api.powerbi.com/v1.0/myorg/groups/%s/reports/%s/GenerateToken";

    const DELETE_GROUP_REPORT_URL = "https://api.powerbi.com/v1.0/myorg/groups/%s/reports/%s";

    const PAGE_URL = 'https://api.powerbi.com/v1.0/myorg/reports/%s/pages';
    const GROUP_PAGE_URL = 'https://api.powerbi.com/v1.0/myorg/groups/%s/reports/%s/pages';

    const REPORT_EXPORT = "https://api.powerbi.com/v1.0/myorg/reports/%s/ExportTo";
    const REPORT_GROUP_EXPORT = "https://api.powerbi.com/v1.0/myorg/groups/%s/reports/%s/ExportTo";

    const REPORT_EXPORT_STATUS = "https://api.powerbi.com/v1.0/myorg/reports/%s/exports/%s";
    const REPORT_GROUP_EXPORT_STATUS = "https://api.powerbi.com/v1.0/myorg/groups/%s/reports/%s/exports/%s";

    const REPORT_EXPORT_FILE = "https://api.powerbi.com/v1.0/myorg/reports/%s/exports/%s/file";
    const REPORT_GROUP_EXPORT_FILE = "https://api.powerbi.com/v1.0/myorg/groups/%s/reports/%s/exports/%s/file";

    const DOWNLOAD__GROUP_PBIX_FILE = "https://api.powerbi.com/v1.0/myorg/groups/%s/reports/%s/Export";


    /**
     * The SDK client.
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

    public function exportTo($groupId, $reportId, $format = 'PDF', $identities = array())
    {
        $url = sprintf(self::REPORT_GROUP_EXPORT, $groupId, $reportId);

        $body = [
            'format' => 'PDF',
        ];
        if ($identities) {
            $body['powerBIReportConfiguration']['identities'] = $identities;
        }

        $response = $this->client->request(Client::METHOD_POST, $url, $body);

        return $this->client->generateResponse($response);
    }

    public function exportToStatus($groupId, $reportId, $exportId)
    {
        $url = sprintf(self::REPORT_GROUP_EXPORT_STATUS, $groupId, $reportId, $exportId);

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }

    public function exportToFile($groupId, $reportId, $exportId)
    {
        $url = sprintf(self::REPORT_GROUP_EXPORT_FILE, $groupId, $reportId, $exportId);

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }

    /**
     * Notes:
     * @param $groupId
     * @param $reportId
     * @return \Tngnt\PBI\Response
     * @author:hongkai - 9/28/2021 9:56 AM
     */
    public function downloaPbixFile($groupId, $reportId)
    {
        $url = sprintf(self::DOWNLOAD__GROUP_PBIX_FILE, $groupId, $reportId);

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }

    /**
     * Notes: get one reports
     * @param $dataId pbi report id (f05153fe-3705-48ad-8d7c-12af0a80ab16)
     * @return \Tngnt\PBI\Response
     * @author:hongkai - 8/20/2020 11:01 AM
     */
    public function getReportOne($dataId)
    {
        $url = sprintf(self::REPORT_URL_ONE, $dataId);

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }

    /**
     * Retrieves a list of reports on PowerBI.
     *
     * @param string|null $groupId An optional group ID
     *
     * @return \Tngnt\PBI\Response
     */
    public function getReports($groupDataId = null)
    {
        $url = $this->getUrlReports($groupDataId);

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }


    /**
     * Notes:Delete Report
     * @param $reportId
     * @param $groupId
     * @return \Tngnt\PBI\Response
     * @author:hongkai - 6/17/2021 3:33 PM
     */
    public function deleteReport($groupId, $reportId)
    {
        $url = sprintf(self::DELETE_GROUP_REPORT_URL, $groupId, $reportId);

        $response = $this->client->request(Client::METHOD_DELETE, $url);

        return $this->client->generateResponse($response);
    }

    /**
     * Retrieves the embed token for embedding a report
     *
     * @param string $reportId The report ID of the report
     * @param string $groupId The group ID of the report
     * @param null|string $accessLevel The access level used for the report
     *
     * @return \Tngnt\PBI\Response
     */
    public function getReportEmbedToken($reportId, $groupId, $accessLevel = 'view')
    {
        $url = sprintf(self::GROUP_REPORT_EMBED_URL, $groupId, $reportId);

        $body = [
            'accessLevel' => $accessLevel,
        ];

        $response = $this->client->request(Client::METHOD_POST, $url, $body);

        return $this->client->generateResponse($response);
    }

    /**
     * Retrieves a list of reports on PowerBI
     *
     * @param string|null $groupId An optional group ID
     *
     * @return \Tngnt\PBI\Response
     */
    public function getPages($reportId, $groupId = null)
    {
        $url = $this->getUrlPages($reportId, $groupId);

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }

    /**
     * Helper function to format the request URL
     *
     * @param string|null $groupId An optional group ID
     *
     * @return string
     */
    private function getUrlReports($groupId)
    {
        if ($groupId) {
            return sprintf(self::GROUP_REPORT_URL, $groupId);
        }

        return self::REPORT_URL;
    }

    /**
     * Helper function to format the request URL
     *
     * @param string $reportId id from report
     * @param string|null $groupId An optional group ID
     *
     * @return string
     */
    private function getUrlPages($reportId, $groupId)
    {
        if ($groupId) {
            return sprintf(self::GROUP_PAGE_URL, $groupId, $reportId);
        }

        return sprintf(self::PAGE_URL, $reportId);
    }
}
