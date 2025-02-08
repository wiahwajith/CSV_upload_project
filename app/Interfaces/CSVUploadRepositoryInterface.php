<?php

namespace App\Interfaces;

interface CSVUploadRepositoryInterface
{
    public function storeData(array $entities);
    public function getDataForReport();
    public function getOrderData();
}

