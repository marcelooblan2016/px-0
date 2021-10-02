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
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ConvertController;
use App\Http\Controllers\DownloadController;

Route::get('/', [IndexController::class, 'index'])->name('home');

Route::get('/about', [IndexController::class, 'about'])->name('about');
Route::group(['prefix' => 'convert'], function () {
    Route::get('/', [ConvertController::class, 'index'])->name('convert.index');
    Route::get('/{convertRequestExternalId}/{convertRequestItem?}', [ConvertController::class, 'showConvertRequest'])->name('convert._convert_id_.show');
    Route::post('/', [ConvertController::class, 'convertNow'])->name('convert.now');
    Route::post('/{convertRequest}/{formatId}', [ConvertController::class, 'convertItemNow'])->name('convert.item.now');
});

Route::group(['prefix' => 'download'], function () {
    Route::get('/{fileId}/check', [DownloadController::class, 'checkAvailability'])->name('download._file_id_.check');
    Route::post('/{fileId}', [DownloadController::class, 'downloadNow'])->name('download._file_id_.post.download-now');
});

Route::get('test', function () {
    $class = new Class {
        use \App\Http\Abilities\SanitizeAbility;
        public function test() {
            // $url = "https://www.youtube.com/watch?v=9ySVRFV3aKg";
            // $url = "https://music.youtube.com/watch?v=XBVWALD96zE&list=PLMm2CwsN0fuCMGHOl0MUzEAqUoZlyho3c";
            // // $url = "https://www.youtube.com/watch?v=kQtFHZHAedo&list=PLMm2CwsN0fuDTUibsqywmg-3ZWL3y4HXM&index=1";
          
            // $url = $this->youtubeSanitizeUrl($url);
            // dd($url);
            $youtubeDL = app(\App\Services\YoutubeDL\Interfaces\YoutubeDLInterface::class);
            // $url = "https://www.facebook.com/watch/?v=815660161974823";
            $url = "https://www.facebook.com/100000871364705/videos/536121764255489/";
            $url = "https://www.facebook.com/watch/?v=815660161974823";
            // $url = "https://www.facebook.com/Mr.AllTimeHigh/videos/536121764255489";
            $url = "https://www.facebook.com/Mr.AllTimeHigh/videos/3299247553447591";
            $url = "https://fb.watch/8o5ymMJw4L";
            // $url = "https://www.youtube.com//watch?v=tqOGM_Mimjg";
            // $formats = $youtubeDL->getInfoWithFormats($url);
            $jsonData = $youtubeDL->getInfoByJson($url);
            dd($jsonData);
            // $size = new \ChrisUllyott\FileSize("12.53MiB");
            // $size->add("2.07GiB");
            // // dd($size->as("mb"));
            // dd($size->asAuto());
            // $pathDir = public_path('storage/trovago');

            // if (!\File::exists($pathDir)) {
            //     \File::makeDirectory($pathDir, $mode = 0777, true, true);
            // }
            
            // $this->convertRequestItem = \App\Models\ConvertRequestItem::whereId(10)->first();

            // $this->convertRequestItem->load('convertRequest');
          
            // $url = $this->convertRequestItem->convertRequest->url;
            // dd($url);
        }
    };

    $class->test();
});