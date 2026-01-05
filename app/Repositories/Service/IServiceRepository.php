<?php

namespace App\Repositories\Service;

interface IServiceRepository
{
    public function searchService($posId, $serviceName, $categoryId);
}
