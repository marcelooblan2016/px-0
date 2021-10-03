<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertRequest as ModelConvertRequest;
use App\Models\ConvertRequestItem as ModelConvertRequestItem;
use App\Jobs\YoutubeDL;
use App\Http\Requests\ConvertRequest;
use App\Services\YoutubeDL\Interfaces\YoutubeDLInterface;
use App\Http\Abilities\MapDetailsAbility;
use App\Http\Abilities\SanitizeAbility;
use Illuminate\Support\Arr;
use ChrisUllyott\FileSize;
use Exception;

class ConvertController extends Controller
{
    use MapDetailsAbility;
    use SanitizeAbility;

    public function index(Request $request)
    {
        return view('pages.convert.index', []);
    }

    public function showConvertRequest(Request $request, $convertRequestExternalId, ModelConvertRequestItem $convertRequestItem = null)
    {
        try {
            $convertRequest = ModelConvertRequest::whereExternalId($convertRequestExternalId)->first();
            if (empty($convertRequest)) throw new Exception("ConvertRequest not found.", 1);
            
            $convertType = $convertRequest->type;
            $methodName = $convertType."Map";
            $convertRequest['mapped_details'] = method_exists($this, $methodName) ? $this->{$methodName}($convertRequest) : null;
            
            if (!empty($convertRequestItem)) {
                $convertRequestItem = $convertRequestItem->load('convertRequest');
                if ($convertRequestItem->convertRequest->id != $convertRequest->id) throw new Exception("ConvertRequest and convertRequestItem mismatched.", 1);
            }

            return view('pages.convert.index', compact('convertRequest', 'convertRequestItem'));

        } catch (Exception $e) {
            abort(404);
        }
    }
    
    public function convertNow(ConvertRequest $request)
    {
        $youtubeDL = app(YoutubeDLInterface::class);
        $convertType = $request->get('convert_type');
        try {
            switch($convertType) {
                case ModelConvertRequest::TYPE_INSTAGRAM:
                    $url = ($request->get('url'));
                    
                    if (empty($url)) throw new Exception("url not found: ". $request->get('url'), 1);

                    // check if url already requested
                    $convertRequest = ModelConvertRequest::where('url', $url)->first();

                    if (empty($convertRequest)) {
                        $jsonData = $youtubeDL->getInfoByJson($url, $convertType);

                        if (empty($jsonData)) throw new Exception("Url not found.");

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
                case ModelConvertRequest::TYPE_YOUTUBE:
                    $url = $this->youtubeSanitizeUrl($request->get('url'));
                    
                    if (empty($url)) throw new Exception("url not found: ". $request->get('url'), 1);
                    
                    // check if url already requested
                    $convertRequest = ModelConvertRequest::where('url', $url)->first();

                    if (empty($convertRequest)) {
                        $jsonData = $youtubeDL->getInfoByJson($url, $convertType);

                        if (empty($jsonData)) throw new Exception("Url not found.");

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
                case ModelConvertRequest::TYPE_FACEBOOK:
                    $url = ($request->get('url'));
                    
                    if (empty($url)) throw new Exception("url not found: ". $request->get('url'), 1);
                    // check if url already requested
                    $convertRequest = ModelConvertRequest::where('url', $url)->first();

                    if (empty($convertRequest)) {
                        $jsonData = $youtubeDL->getInfoByJson($url, $convertType);

                        if (empty($jsonData)) throw new Exception("Url not found.");

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

    public function convertItemNow(ModelConvertRequest $convertRequest, $formatId, Request $request)
    {
        try {
            $availableDownloadOptions = Arr::get($convertRequest, 'details.available_download_options');

            $foundDownload = collect($availableDownloadOptions)
                ->filter( function ($download) use ($formatId) {

                    return $download['id'] == $formatId;
                })
                ->first();

            if (empty($foundDownload)) throw new Exception("formatID not found.", 1);
            
            $formatIds = [$formatId];

            if ($foundDownload['type'] == 'video') {
                $m4aDownload = collect($availableDownloadOptions)
                    ->filter( function ($download) {
                        
                        return $download['file_type'] == 'm4a';
                    })
                    ->first();

                $formatIds[] = $m4aDownload['id'];
            }
            $formatIdString = implode('+', $formatIds);
            $convertRequestItem = ModelConvertRequestItem::where('convert_request_id', $convertRequest->id)
                ->where('format_id', $formatIdString)
                ->first();
            
            $pathDirDownloads = public_path('storage/downloads');

            if (!\File::exists($pathDirDownloads)) {
                \File::makeDirectory($pathDirDownloads, $mode = 0777, true, true);
            }

            $isDispatchedJob = false;
            $fileType = $foundDownload['file_type'];
            $quality = $foundDownload['quality'] ?? 'best';
            if (empty($convertRequestItem)) {
                $isDispatchedJob = true;
                switch($fileType) {
                    case 'm4a': 
                        $fileType = 'mp3';
                        $quality = 'best';
                    break;
                }

                // create convert item
                $convertRequestItem = ModelConvertRequestItem::create([
                    'convert_request_id' => $convertRequest->id,
                    'file_type' => $fileType,
                    'quality' => $quality,
                    'format_id' => $formatIdString,
                    'status' => ModelConvertRequestItem::STATUS_PROCESSING,
                    'details' => [],
                ]);

            }
            else {
                // check if status is converted
                    // check if file_exist
                    // if not, redownload file
                $updateParams = null;
                switch($convertRequestItem->status) {
                    case ModelConvertRequestItem::STATUS_DOWNLOADED:
                    case ModelConvertRequestItem::STATUS_CONVERTED:
                        if (!\File::exists($convertRequestItem->path)) {
                            $isDispatchedJob = true;
                            $updateParams['status'] = ModelConvertRequestItem::STATUS_PROCESSING;
                        }
                        else $isDispatchedJob = false;
                        break;
                    default:
                        $isDispatchedJob = true;
                        $updateParams['status'] = ModelConvertRequestItem::STATUS_PROCESSING;
                        break;
                }

                if (!empty($updateParams)) {
                    $convertRequestItem->update($updateParams);
                    $convertRequestItem = ModelConvertRequestItem::whereId($convertRequestItem->id)->first();
                }
            }

            if ($isDispatchedJob === true) {
                // dispatch jobs
                $saveInPath = 
                    vsprintf(
                        '%s/%s.%s', [
                        $pathDirDownloads,
                        $convertRequestItem->file_id,
                        $fileType
                    ]
                );

                $methodName = $convertRequest->type."Map";
                $mappedDetails = method_exists($this, $methodName) ? $this->{$methodName}($convertRequest) : null;

                $details = [
                    'title' => Arr::get($convertRequest, 'details.title'),
                    'download_details' => $foundDownload,
                    'mapped_details' => $mappedDetails
                ];

                $convertRequestItem->update([
                    'url' => route('download._file_id_.post.download-now', ['fileId' => $convertRequestItem['file_id']]),
                    'path' => $saveInPath,
                    'details' => $details
                ]);

                YoutubeDL::dispatch($convertRequestItem, $saveInPath, $formatIdString);
            }
            
            return response()->json($convertRequestItem, 200);
                
        } catch (Exception $e) {

            return response()->json([
                "message" => "An error occured.",
                "errors" => [
                    "convert_item" => [$e->getMessage()],
                ]
            ], 422);
        }
    }
}
