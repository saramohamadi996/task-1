<?php

namespace App\Http\Controllers;

use App\Services\ServiceBase;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    protected int $statusCode = 200;

    protected string $message =  'Articles added successfully';

    protected array|object $data = [];


    /**
     * @param array|object|bool $data
     * @param string            $message
     * @param int               $statusCode
     * @return JsonResponse
     */
    protected function apiResponse(
        array|object|bool $data,
        string $message = 'Success',
        int $statusCode = 200
    ) : JsonResponse
    {
        return response()
            ->json([
            'data' => $data,
            'message' => $message],
            $statusCode
            );
    }


    /**
     *
     * @param UploadedFile $file
     * @param string       $modelName
     * @return mixed
     * @throws \Exception
     */
    protected function getJsonFile( UploadedFile $file, string $modelName) : array
    {
        $jsonData = file_get_contents($file);
        return json_decode($jsonData, true)[$modelName]
            ?? throw new \Exception("error");

    }



}
