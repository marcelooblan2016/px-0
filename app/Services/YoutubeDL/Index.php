<?php

namespace App\Services\YoutubeDL;

use Illuminate\Support\Facades\Schema;
use App\Models\ConvertRequest as ModelConvertRequest;
use App\Services\YoutubeDL\Interfaces\YoutubeDLInterface;
use GuzzleHttp\ClientInterface;
use Exception;
use ChrisUllyott\FileSize;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Index implements YoutubeDLInterface
{
    public function getInfoByJson($url, $convertType = ModelConvertRequest::TYPE_YOUTUBE)
    {
        $commandString = (
            vsprintf(
                "%s %s %s %s", [
                    "youtube-dl",
                    $url,
                    "--dump-json",
                    "--no-cache-dir",
                ]
            )
        );
        
        $jsonString = trim( shell_exec($commandString) );
        
        $jsonData = json_decode($jsonString, true);

        $jsonData['available_download_options'] = $this->getAvailableOptionsFromFormats($jsonData);

        return $jsonData;
    }

    private function getAvailableOptionsFromFormats($jsonData)
    {
        $formats = collect(Arr::get($jsonData, 'formats'))
        ->map( function ($data) {
                
            switch($data['ext']) {
                case 'm4a':
                    $type = 'audio';
                    break;
                case 'mp4':
                case 'webm':
                default:
                    $type = 'video';
                    break;
            }

            $resolution = null;
            $formatNote = Arr::get($data, 'format_note');
            $fileType = Arr::get($data, 'ext');
            $fps = Arr::get($data, 'fps');
            $fileSize = Arr::get($data, 'filesize');

            if (!empty($data['width']) && !empty($data['height'])) {
                $resolution = vsprintf("%sx%s", [
                    $data['width'],
                    $data['height']
                ]);

                $formatNote = vsprintf("%sp", [
                    $data['height']
                ]);
            }

            return [
                'id' => Arr::get($data, 'format_id'),
                'resolution' => $resolution,
                'type' => $type,
                'quality' => $formatNote,
                'fps' => $fps,
                'file_type' => $fileType,
                'size' => $fileSize,
                'raw_details' => $data,
            ];
        })
        ->filter( function ($row) {
             // valid fileType for merging
            $validFileTypes = ['mp4', 'm4a'];
                
            return (!empty($row['file_type']) && in_array($row['file_type'], $validFileTypes)) &&
            Str::contains($row['size'], ['best']) === false;
        })
        ->values();

        $formats = collect($formats)
        ->map( function ($row) use ($formats) {

            $row['size_plus'] = $this->sizePlus($row, $formats);

            return $row;
        })
        ->toArray();

        return $formats;
    }

    private function sizePlus($row, $responseRows)
    {
        if (empty($row['size'])) return null;
        if (!empty($row['file_type']) && $row['file_type'] == 'mp4') {
            
            $mp3RowSize = collect($responseRows)
                ->filter( function ($row) {
                    return !empty($row['file_type']) &&
                    !empty($row['size']) &&
                    $row['type'] == 'audio' &&
                    $row['file_type'] == 'm4a';
                })
                ->first()['size'] ?? null;
            
            try {
                $size = new FileSize($row['size']);
                $size->add($mp3RowSize);
    
                return $size->asAuto();
            } catch (Exception $e) {}
        }
        else {
            try {
                $size = new FileSize($row['size']);
    
                return $size->asAuto();
            } catch (Exception $e) {}
        }

        return $row['size'];
    }

    public function downloadYoutube($url, $saveInPath, $formatIds)
    {
        /* 
            youtube-dl -f 398+140 https://www.youtube.com/watch?v=rZ3S_TNinwc -o 
            "/var/www/html/px-0/public/storage/test.mp4"
            --no-cache-dir --no-check-certificate
        */

        $commandString = (
            vsprintf(
                "youtube-dl -f %s %s -o \"%s\" --no-cache-dir --no-check-certificate", [
                    $formatIds,
                    $url,
                    $saveInPath,
                ]
            )
        );
        
        $response = trim( shell_exec($commandString) );
        
        return $response;
    }

    private function youtubeSafeDownload($url, $downloadUrlOriginal, $userAgent, $quality)
    {

    }
}