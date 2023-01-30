<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\ImageService;

class ImageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the image after the authentication is successful.
     */
    public function viewTemp($filename, ImageService $imageService)
    {
        $authenticatedUserId = auth()->user()->id;
        $image = null;
        try {
            $image = $imageService->viewTempImageFromUserFolder($filename, $authenticatedUserId);
        } catch (Exception $e) {
            abort(404);
        }

        return response($image)->header('Content-Type', 'image/png');
    }
    public function viewReceipt($filename, ImageService $imageService)
    {
        $authenticatedUserId = auth()->user()->id;
        $image = null;
        try {
            $image = $imageService->viewReceiptImageFromUserFolder($filename, $authenticatedUserId);
        } catch (Exception $e) {
            abort(404);
        }

        return response($image)->header('Content-Type', 'image/png');
    }
}
