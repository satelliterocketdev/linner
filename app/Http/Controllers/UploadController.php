<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\MediaFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use App\Services\MediaFileService;

class UploadController extends Controller
{
    /**
     * MediaFIle ロジック.
     */
    protected $mediaFileService;

    /**
     * Create a new controller instance.
     *
     * @param  MediaFileService  $mediaFileService
     * @return void
     */
    public function __construct(MediaFileService $mediaFileService)
    {
        $this->mediaFileService = $mediaFileService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json(['hello world'], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $file = $request->file('file');
            $media = $this->mediaFileService->saveUploadFile($file);
            if (\App::environment() == 'local') {
                $media->featured_url = str_replace('minio', "localhost", $media->featured_url);
            }
            return response()->json($media, Response::HTTP_OK);
        } catch (FileNotFoundException $e) {
            \Log::error($e);
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            // dump($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $path = $request->input('path');
            $this->mediaFileService->deleteImageMedia($path);
            return response()->json(null, Response::HTTP_OK);
        } catch (FileNotFoundException $e) {
            \Log::error($e);
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            // dump($e->getMessage());
        }
    }

    public function lists($type)
    {
        $media = MediaFile::where('type', $type)->get();
        return response()->json($media, Response::HTTP_OK);
    }
}
