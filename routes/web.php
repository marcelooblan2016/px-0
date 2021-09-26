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
    Route::post('/', [ConvertController::class, 'convertNow'])->name('convert.now');
    Route::post('/{convertRequest}/{formatId}', [ConvertController::class, 'convertItemNow'])->name('convert.item.now');
});

Route::group(['prefix' => 'download'], function () {
    Route::get('/{fileId}/check', [DownloadController::class, 'checkAvailability'])->name('download._file_id_.check');
    Route::post('/{fileId}', [DownloadController::class, 'downloadNow'])->name('download._file_id_.post.download-now');
});

Route::get('test', function () {
    $class = new Class {
        public function test() {
            $youtubeDL = app(\App\Services\YoutubeDL\Interfaces\YoutubeDLInterface::class);
            $url = "https://www.youtube.com/watch?v=9ySVRFV3aKg";
            $formats = $youtubeDL->getInfoWithFormats($url);
            dd($formats);
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