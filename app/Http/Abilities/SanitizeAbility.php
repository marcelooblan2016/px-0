<?php

namespace App\Http\Abilities;
use Illuminate\Support\Arr;
use Exception;

trait SanitizeAbility
{
    private $youtubeBaseUrl = "https://www.youtube.com/";
    /*
     * will clean url for youtube which came from music, mobile youtube, etc...
     **/
    public function youtubeSanitizeUrl($url)
    {
        try {
            $urlSplitted = explode('?', $url);
        
            parse_str($urlSplitted[1], $urlParams);

            return vsprintf("%s/watch?v=%s", [
                $this->youtubeBaseUrl,
                $urlParams['v']
            ]);

        } catch (Exception $e) {
            return null;
        }
    }
}
