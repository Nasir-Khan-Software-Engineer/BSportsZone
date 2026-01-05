<?php
namespace App\Services\Pos;

use App\Repositories\Pos\IPosRepository;

interface IPosService{
    public function getPosPageItems($posId, $type = 'Service');
}

