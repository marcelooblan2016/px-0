<?php

namespace App\Services\YoutubeDL\Interfaces;

interface YoutubeDLInterface
{
    public function getInfoByJson($url);
    public function getInfoWithFormats($url);
}
