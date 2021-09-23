<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertRequest as ModelConvertRequest;
use App\Http\Requests\ConvertRequest;
use App\Services\YoutubeDL\Interfaces\YoutubeDLInterface;
use App\Http\Abilities\MapDetailsAbility;
use Exception;

class ConvertController extends Controller
{
    use MapDetailsAbility;

    public function index(Request $request)
    {
        return view('pages.convert.index', []);
    }
    
    public function convertNow(ConvertRequest $request)
    {
        $youtubeDL = app(YoutubeDLInterface::class);
        $convertType = $request->get('convert_type');
        try {
            switch($convertType) {
                case ModelConvertRequest::TYPE_YOUTUBE:
                    $url = $request->get('url');
                    // check if url already requested -- check if not older than 30 days
                    $convertRequest = ModelConvertRequest::where('url', $url)->first();

                    if (empty($convertRequest)) {
                        $jsonData = $youtubeDL->getInfoByJson($url);

                        if (empty($jsonData)) throw new Exception("Url not found.");
                        $formats = $youtubeDL->getInfoWithFormats($url);
                        $jsonData['available_download_options'] = $formats;

                        $convertRequest = ModelConvertRequest::create([
                            'url' => $url,
                            'type' => $convertType,
                            'method' => ModelConvertRequest::METHOD_CONVERSION,
                            'details' => $jsonData
                        ]);
                    }
                    
                    $methodName = $convertType."Map";
                    $convertRequest['mapped_details'] = method_exists($this, $methodName) ? $this->{$methodName}($convertRequest) : null;

                    return response()->json($convertRequest, 200);
                break;
            }

            throw new Exception("Unable to process");

        } catch (Exception $e) {

            return response()->json([
                "message" => "An error occured.",
                "errors" => [
                    "url" => [$e->getMessage()],
                ]
            ], 422);
        }
    }
}
