<?php

namespace Itl\ItlSearchBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TeamTNT\TNTSearch\TNTSearch;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class SearchService
{
    /**
     * SearchHelper constructor.
     */
    public function __construct()
    {
        $this->tnt = new TNTSearch();
    }

    /**
     * @param $indexName
     * @param $query
     * @param $config
     * @param $storage
     */
    public function startIndex($indexName, $query, $config, $storage)
    {
        $this->tnt->loadConfig($config);

        $fs = new Filesystem();

        try {
            $fs->mkdir($storage);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at " . $e->getPath();
        }

        $indexer = $this->tnt->createIndex($indexName);
        $indexer->query($query);
        $indexer->run();
    }

    /**
     * @param     $search
     * @param     $indexName
     * @param     $config
     * @param int $maximumResults
     *
     * @return mixed
     */
    public function getSearchResults($search, $indexName, $config, $maximumResults = 100)
    {
        $this->tnt->loadConfig($config);
        $this->tnt->selectIndex($indexName);
        $results =  $this->tnt->search($search, $maximumResults);

        return $results;
    }
}