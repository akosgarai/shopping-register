<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Exception;

class ImageService
{
    private const RAW_IMAGE_PATH = 'tmp';
    private const FINAL_IMAGE_PATH = 'receipts';

    // Save the image to the user's temp folder.
    public function saveTempImageToUserFolder(&$image, $authenticatedUserId): string
    {
        return $image->store(self::RAW_IMAGE_PATH . '/' . $authenticatedUserId, 'private');
    }
    public function updateTempImageOfUser($imageName, $authenticatedUserId, $imageContent)
    {
        $image = str_replace('data:image/png;base64,', '', $imageContent);
        $image = str_replace(' ', '+', $image);

        Storage::disk('private')->put(self::RAW_IMAGE_PATH . '/' . $authenticatedUserId . '/' . $imageName, base64_decode($image));
    }

    // View the image from the user's temp folder.
    public function viewTempImageFromUserFolder($filename, $authenticatedUserId): string
    {
        $path = storage_path('app/private/' . self::RAW_IMAGE_PATH . '/' . $authenticatedUserId . '/' . $filename);

        return $this->getFile($this->tempFilePath($filename, $authenticatedUserId));
    }

    public function tempFilePath($filename, $authenticatedUserId): string
    {
        return storage_path('app/private/' . self::RAW_IMAGE_PATH . '/' . $authenticatedUserId . '/' . $filename);
    }

    // List all the images from the user's temp folder.
    public function listTempImageeFromUserFolder($authenticatedUserId): array
    {
        $filePaths = Storage::files('private/' . self::RAW_IMAGE_PATH . '/' . $authenticatedUserId);
        $files = [];
        foreach ($filePaths as $filePath) {
            $files[] = basename($filePath);
        }

        return $files;
    }

    // View the image from the user's receipts folder.
    public function viewReceiptImageFromUserFolder($filename, $authenticatedUserId): string
    {
        $path = storage_path('app/private/' . self::FINAL_IMAGE_PATH . '/' . $authenticatedUserId . '/' . $filename);

        return $this->getFile($path);
    }

    private function getFile($path)
    {
        // if the file doesn't exist, throw an exception
        if (!file_exists($path)) {
            throw new Exception('File not found');
        }
        return file_get_contents($path);
    }
}