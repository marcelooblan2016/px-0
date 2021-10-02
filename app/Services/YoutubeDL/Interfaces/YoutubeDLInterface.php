<?php

namespace App\Services\YoutubeDL\Interfaces;

interface YoutubeDLInterface
{
    public function getInfoByJson($url, $convertType);
}
