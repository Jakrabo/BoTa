<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Service;

\defined('_JEXEC') or die;

final class BadgeUploadService
{
    private const MAX_SIZE = 3 * 1024 * 1024;
    private const MAX_DIMENSION = 2048;
    private const RELATIVE_DIRECTORY = 'images/jugendtraining/badges';

    public function store(?array $upload): ?string
    {
        if (
            !$upload
            || empty($upload['tmp_name'])
            || (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }

        if ((int) ($upload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('COM_JUGENDTRAINING_ERROR_BADGE_UPLOAD');
        }

        if ((int) ($upload['size'] ?? 0) > self::MAX_SIZE) {
            throw new \RuntimeException('COM_JUGENDTRAINING_ERROR_BADGE_TOO_LARGE');
        }

        $temporaryFile = (string) $upload['tmp_name'];
        $image = @getimagesize($temporaryFile);

        if (!$image || ($image['mime'] ?? '') !== 'image/png') {
            throw new \RuntimeException('COM_JUGENDTRAINING_ERROR_BADGE_PNG_ONLY');
        }
        if((int)($image[0]??0)<=0||(int)($image[1]??0)<=0||(int)$image[0]>self::MAX_DIMENSION||(int)$image[1]>self::MAX_DIMENSION){
            throw new \RuntimeException('COM_JUGENDTRAINING_ERROR_BADGE_TOO_LARGE');
        }
        $signature=@file_get_contents($temporaryFile,false,null,0,8);
        if($signature!=="\x89PNG\r\n\x1a\n"){
            throw new \RuntimeException('COM_JUGENDTRAINING_ERROR_BADGE_PNG_ONLY');
        }

        $targetDirectory = JPATH_ROOT . '/' . self::RELATIVE_DIRECTORY;

        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0755, true) && !is_dir($targetDirectory)) {
            throw new \RuntimeException('COM_JUGENDTRAINING_ERROR_BADGE_UPLOAD');
        }

        $originalName = pathinfo((string) ($upload['name'] ?? 'badge'), PATHINFO_FILENAME);
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($originalName));
        $slug = trim((string) $slug, '-') ?: 'badge';
        $filename = $slug . '-' . bin2hex(random_bytes(4)) . '.png';
        $destination = $targetDirectory . '/' . $filename;

        $moved = is_uploaded_file($temporaryFile)
            ? move_uploaded_file($temporaryFile, $destination)
            : rename($temporaryFile, $destination);

        if (!$moved) {
            throw new \RuntimeException('COM_JUGENDTRAINING_ERROR_BADGE_UPLOAD');
        }

        // Re-encode PNG where GD is available. This strips ancillary metadata and
        // prevents appended/polyglot payloads from being retained in the public file.
        if(function_exists('imagecreatefrompng')&&function_exists('imagepng')){
            $resource=@imagecreatefrompng($destination);
            if(!$resource||!imagepng($resource,$destination,9)){
                if($resource)imagedestroy($resource);
                @unlink($destination);
                throw new \RuntimeException('COM_JUGENDTRAINING_ERROR_BADGE_UPLOAD');
            }
            imagedestroy($resource);
        }
        @chmod($destination, 0644);

        return self::RELATIVE_DIRECTORY . '/' . $filename;
    }

    public function listAvailable(): array
    {
        $targetDirectory = JPATH_ROOT . '/' . self::RELATIVE_DIRECTORY;

        if (!is_dir($targetDirectory)) {
            return [];
        }

        $files = glob($targetDirectory . '/*.png') ?: [];
        natcasesort($files);

        $result = [];

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $relative = self::RELATIVE_DIRECTORY . '/' . basename($file);
            $result[] = [
                'value' => $relative,
                'text' => pathinfo($file, PATHINFO_FILENAME),
            ];
        }

        return array_values($result);
    }
}
