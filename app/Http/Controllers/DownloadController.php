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
        try {
            $convertRequestItem = ModelConvertRequestItem::where('file_id', $fileId)->first();
            if (!empty($convertRequestItem)) {
                $headers = [];
                $sanitizedTitle = preg_replace( '/[^a-zA-Z0-9]+/', ' ', Arr::get($convertRequestItem, 'details.title') );
                $sanitizedTitle = trim($sanitizedTitle);
                
                $fileName = implode('.', [
                    $sanitizedTitle,
                    $convertRequestItem->file_type,
                ]);

                $fileFullPath = $convertRequestItem->path;
                if (!\File::exists($fileFullPath)) {
                    throw new Exception("File not found.", 1);
                }

                $convertRequestItem->update([
                    'status' => ModelConvertRequestItem::STATUS_DOWNLOADED
                ]);

                return response()->download($fileFullPath, $fileName, $headers);
            }

            throw new Exception("No data found.", 1);
        } catch (Exception $e) {
            abort(404);
        }
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
