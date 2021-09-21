<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'App\Http\Controllers\IndexController@index');

Route::get('test', function () {
    $class = new Class {
        public function test()
        {
            // $url = "https://www.youtube.com/watch?v=DaGxxmaxPPw";
            //$url = "https://www.youtube.com/watch?v=j7kFK4JeUwM";
            // $url = "https://music.youtube.com/watch?v=SPuN6ElyaRc&list=RDAMVMTJc2obR28zQ";
            // $url = "https://www.youtube.com/watch?v=8PlaqfnNLFA";
            $url = "https://www.youtube.com/watch?v=rZ3S_TNinwc";

            /**
             * Combine videoId + audioId for merging as mp4
             * youtube-dl -f 398+140 https://www.youtube.com/watch?v=rZ3S_TNinwc -o "/var/www/html/px-0/public/storage/test.mp4" --no-cache-dir --no-check-certificate (COMBINE ID - VIDEO + AUDIO)
             */
            $youtubeDL = app(\App\Services\YoutubeDL\Interfaces\YoutubeDLInterface::class);
            $response = $youtubeDL->getInfo($url);

            $ytId = 398;
            $saveInPath =  public_path("storage/test.mp4");
            $youtubeDL->downloadYoutube($url, $saveInPath, null, $ytId);
            dd($response);
        }
    };

    $class->test();
});