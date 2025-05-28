<?php

namespace BookStack\Settings\Plugins;

use Illuminate\Http\Request;
use BookStack\Http\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use BookStack\Plugins\MermaidProvider;

class MermaidController extends Controller
{
    public function store(Request $request)
    {
        $version = $request->input('version');

        if (!$version || $version == 'disabled') {
            return response()->json([
                'success' => false,
                'message' => 'No version specified.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $remoteUrl = sprintf(MermaidProvider::MERMAID_CDN, $version);
        $localDirectory = public_path('mermaid');
        $localFilename = "mermaid.min.js";
        $localPath = $localDirectory . '/' . $localFilename;

        if (!File::exists($localDirectory)) {
            File::makeDirectory($localDirectory, 0755, true);
        }

        try {
            $response = Http::get($remoteUrl);

            if ($response->successful()) {
                File::put($localPath, $response->body());

                return response()->json([
                    'success' => true,
                    'path' => asset("mermaid/$localFilename")
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Remote file not found.'
                ], Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
