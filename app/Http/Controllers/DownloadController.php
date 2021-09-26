<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertRequest as ModelConvertRequest;
use App\Models\ConvertRequestItem as ModelConvertRequestItem;
use Illuminate\Support\Arr;
use Exception;

class DownloadController extends Controller
{
    public function downloadNow($fileId)
    {
        // download
    }

    public function checkAvailability($fileId)
    {
        try {
            $convertRequestItem = ModelConvertRequestItem::where('file_id', $fileId)->first();
            if (!empty($convertRequestItem)) {
                return response()->json($convertRequestItem, 200);
            }

            throw new Exception("No data found.", 1);
        } catch (Exception $e) {
            return response()->json("An error occured: ".$e->getMessage(), 404);
        }
    }
}
