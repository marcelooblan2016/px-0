<?php

namespace App\Http\Abilities;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
            // https://youtu.be/jRn48HxssPI
            if (Str::contains($url, ['/youtu.be/']) === true) {
                return $url;
            }
            else {
                $urlSplitted = explode('?', $url);
        
                parse_str($urlSplitted[1], $urlParams);
    
                return vsprintf("%swatch?v=%s", [
                    $this->youtubeBaseUrl,
                    $urlParams['v']
                ]);
            }
        } catch (Exception $e) {
            return null;
        }
    }
}
