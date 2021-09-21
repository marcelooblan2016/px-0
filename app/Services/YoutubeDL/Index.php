<?php

namespace App\Services\YoutubeDL;

use Illuminate\Support\Facades\Schema;
use App\Services\YoutubeDL\Interfaces\YoutubeDLInterface;
use GuzzleHttp\ClientInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class Index implements YoutubeDLInterface
{
    public function getInfo($url)
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
            ->toArray();
        }

        return $responseRows;
    }

    public function downloadYoutube($url, $saveInPath, $userAgent, $ytId)
    {
        $commandString = (
            vsprintf(
                "%s %s %s %s %s", [
                    "youtube-dl",
                    "-f $ytId",
                    $url,
                    '-o "'.$saveInPath.'"',
                    '--no-cache-dir --no-check-certificate',
                ]
            )
        );
        
        dd($commandString);
        $response = trim( shell_exec($commandString) );

        return $response;
    }

    private function youtubeSafeDownload($url, $downloadUrlOriginal, $userAgent, $quality)
    {

    }
}