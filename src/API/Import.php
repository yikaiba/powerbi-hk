<?php

namespace Tngnt\PBI\API;

use Tngnt\PBI\Client;
use Tngnt\PBI\Model\Import as ImportModel;

/**
 * Class Import
 *
 * @package Tngnt\PBI\API
 */
class Import
{
    const IMPORT_URL = "https://api.powerbi.com/v1.0/myorg/imports";
    const IMPORT_GROUP_URL = "https://api.powerbi.com/v1.0/myorg/groups/%s/imports?datasetDisplayName=%s";

    const GET_IMPORT_IN_GROUP_URL = "https://api.powerbi.com/v1.0/myorg/groups/%s/imports/%s";


    const CONFLICT_IGNORE = 'Ignore';
    const CONFLICT_ABORT = 'Abort';
    const CONFLICT_OVERWRITE = 'Overwrite';

    /**
     * The SDK client
     *
     * @var Client
     */
    private $client;

    private $conflict = ['Abort', 'CreateOrOverwrite', 'GenerateUniqueName', 'Ignore', 'Overwrite'];

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
     * Creates an import on PowerBI
     *
     * @param ImportModel $import The import to create
     * @param string $datasetDisplayName A custom display name. Blank to ignore.
     * @param string $nameConflict What to do when there is a name conflict. Either "Ignore", "Abort", or
     *                                         "Overwrite"
     * @param string $preferClientRouting Avoid request redirecting between clusters and timeouts.
     *
     * @return \Tngnt\PBI\Response
     */
    public function createImport(
        ImportModel $import,
        $datasetDisplayName = '',
        $nameConflict = 'Ignore',
        $preferClientRouting = 'false'
    )
    {
        // Validate the nameConflict parameter
        if (
            $nameConflict != self::CONFLICT_ABORT &&
            $nameConflict != self::CONFLICT_IGNORE &&
            $nameConflict != self::CONFLICT_OVERWRITE) {
            throw new \InvalidArgumentException(
                'The nameConflict parameter should either be "Ignore", "Abort", or "Overwrite"'
            );
        }

        // Validate the preferClientRouting parameter
        if ($preferClientRouting !== 'false' && $preferClientRouting !== 'true') {
            throw new \InvalidArgumentException(
                'The preferClientRouting parameter should be a string with a boolean value'
            );
        }

        // Generate the URL
        $url = self::IMPORT_URL . "?nameConflict=$nameConflict&PreferClientRouting=$preferClientRouting";
        if ($datasetDisplayName !== '') {
            $url .= "&datasetDisplayName=$datasetDisplayName";
        }
        $url = 'https://api.powerbi.com/v1.0/myorg/groups/1793ce6c-f006-4026-9b57-d9072ed6552c/imports?datasetDisplayName=test hk';
        $response = $this->client->request(Client::METHOD_POST, $url, $import);

        return $this->client->generateResponse($response);
    }

    public function createGroupImport($ossType, $groupId, $absoluteFilepathOrFileContent, $datasetDisplayName = '', $nameConflict = 'Ignore', $skipReport = 'false')
    {
        // Validate the nameConflict parameter
        if (!in_array($nameConflict, $this->conflict)) {
            throw new \InvalidArgumentException(
                'The nameConflict parameter should either be "Ignore", "Abort", or "Overwrite"'
            );
        }
        // Validate the preferClientRouting parameter
        if ($skipReport !== 'false' && $skipReport !== 'true') {
            throw new \InvalidArgumentException(
                'The preferClientRouting parameter should be a string with a boolean value'
            );
        }

        // Generate the URL
        $url = sprintf(self::IMPORT_GROUP_URL, $groupId, $datasetDisplayName) . "&nameConflict={$nameConflict}";
//        $url = sprintf(self::IMPORT_GROUP_URL, $groupId, $datasetDisplayName);
        $body = [
            [
                'name' => $datasetDisplayName,
                'contents' => $ossType === 'azure' ? $absoluteFilepathOrFileContent : fopen($absoluteFilepathOrFileContent, 'r')
            ]
        ];
        $response = $this->client->requestFile(Client::METHOD_POST, $url, $body);

        return $this->client->generateResponse($response);
    }


    public function getImportInGroup($groupId, $importId)
    {
        // Generate the URL
        $url = sprintf(self::GET_IMPORT_IN_GROUP_URL, $groupId, $importId);

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }


    /**
     * Returns a list of imports from PowerBI
     *
     * @return \Tngnt\PBI\Response
     */
    public function getImports()
    {
        $response = $this->client->request(Client::METHOD_GET, self::IMPORT_URL);

        return $this->client->generateResponse($response);
    }

    /**
     * Retrieves a specific import from PowerBI
     *
     * @param string $importId The ID of the import
     *
     * @return \Tngnt\PBI\Response
     */
    public function getImport($importId)
    {
        $url = self::IMPORT_URL . "/$importId";

        $response = $this->client->request(Client::METHOD_GET, $url);

        return $this->client->generateResponse($response);
    }
}
