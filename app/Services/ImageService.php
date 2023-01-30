<?php

namespace App\Services;

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

    // View the image from the user's temp folder.
    public function viewTempImageFromUserFolder($filename, $authenticatedUserId): string
    {
        $path = storage_path('app/private/' . self::RAW_IMAGE_PATH . '/' . $authenticatedUserId . '/' . $filename);

        return $this->getFile($path);
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
