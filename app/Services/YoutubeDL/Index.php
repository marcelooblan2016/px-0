<?php

namespace App\Services\YoutubeDL;

use Illuminate\Support\Facades\Schema;
use App\Services\YoutubeDL\Interfaces\YoutubeDLInterface;
use GuzzleHttp\ClientInterface;
use Exception;
use ChrisUllyott\FileSize;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Index implements YoutubeDLInterface
{
    public function getInfoByJson($url)
    {
        $command_string = (
            vsprintf(
                "%s %s %s %s", [
                    "youtube-dl",
                    $url,
                    "--dump-json",
                    "--no-cache-dir",
                ]
            )
        );
        
        $json_string = trim( shell_exec($command_string) );
        $json_data = json_decode($json_string, true);
        
        return $json_data;
    }

    public function getInfoWithFormats($url)
    {
        $commandString = (
            vsprintf(
                "%s %s %s %s", [
                    "youtube-dl",
                    "-F",
                    $url,
                    // "--dump-json",
                    "--no-cache-dir",
                ]
            )
        );

        $response = trim( shell_exec($commandString) );
        
        $responseData = explode("\n", $response);
        
        $responseRows = [];
        collect($responseData)->each( function ($rowStr) use (&$responseRows) {
            $responseRows[] = preg_split("/\s+/", $rowStr);
        });
        
        if (count($responseRows) >= 1) {
            $responseRows = collect($responseRows)->filter( function ($row) {
                
                return !empty($row[0]) && is_numeric($row[0]);
            })
            ->map( function ($row) {
                if (count($row) == 11) {
                    if ($row[2] == 'audio') {

                        return [
                            'id' => $row[0],
                            'resolution' => null,
                            'type' => 'audio',
                            'quality' => $row[9],
                            'fps' => null,
                            'file_type' => $row[1],
                            'size' => $row[10],
                            'raw_details' => $row,
                            'raw_string' => implode(' ', $row),
                        ];
                    }
                    else {
                        return [
                            'id' => $row[0],
                            'resolution' => $row[2],
                            'type' => 'video',
                            'quality' => $row[3],
                            'fps' => $row[4],
                            'file_type' => $row[1],
                            'size' => $row[10],
                            'raw_details' => $row,
                            'raw_string' => implode(' ', $row),
                        ];
                    }

                }
                else if (count($row) == 12) {
                    if ($row[2] == 'audio') {
                        return [
                            'id' => $row[0],
                            'resolution' => null,
                            'type' => 'audio',
                            'quality' => $row[10],
                            'fps' => null,
                            'file_type' => $row[1],
                            'size' => $row[11],
                            'raw_details' => $row,
                            'raw_string' => implode(' ', $row),
                        ];
                    }
                    else {
                        return [
                            'id' => $row[0],
                            'resolution' => $row[2],
                            'type' => 'video',
                            'quality' => $row[3],
                            'fps' => $row[9],
                            'file_type' => $row[1],
                            'size' => $row[11],
                            'raw_details' => $row,
                            'raw_string' => implode(' ', $row),
                        ];
                    }

                }
                else {

                    return [
                        'id' => $row[0],
                        'resolution' => null,
                        'type' => $row[2],
                        'quality' => $row[3],
                        'fps' => $row[9],
                        'raw_details' => $row,
                        'raw_string' => implode(' ', $row),
                    ];
                }

            })
            ->filter( function ($row) {
                // valid fileType for merging
                $validFileTypes = ['mp4', 'm4a'];
                
                return (!empty($row['file_type']) && in_array($row['file_type'], $validFileTypes)) &&
                Str::contains($row['size'], ['best']) === false;
            })
            ->values();

            $responseRows = collect($responseRows)
            ->map( function ($row) use ($responseRows) {

                $row['size_plus'] = $this->sizePlus($row, $responseRows);

                return $row;
            })
            ->toArray();
        }

        return $responseRows;
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