<?php

namespace Cloudinary\Cloudinary\Plugin;

use CloudinaryExtension\Image;
use CloudinaryExtension\CloudinaryImageManager;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Uploader;

class FileUploader
{
    /**
     * @var CloudinaryImageManager
     */
    private $cloudinaryImageManager;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @param CloudinaryImageManager $cloudinaryImageManager
     * @param DirectoryList $directoryList
     */
    public function __construct(
        CloudinaryImageManager $cloudinaryImageManager,
        DirectoryList $directoryList
    ) {
        $this->cloudinaryImageManager = $cloudinaryImageManager;
        $this->directoryList = $directoryList;
    }

    /**
     * @param Uploader $uploader
     * @param array $result
     * @return array
     */
    public function afterSave(Uploader $uploader, $result)
    {
        $filepath = $this->absolutePath($result);

        if (!$this->isTemporaryPath($filepath)) {

            $this->cloudinaryImageManager->uploadAndSynchronise(
                Image::fromPath($filepath, $this->mediaRelativePath($filepath))
            );

        }

        return $result;
    }

    /**
     * @param string $filepath
     * @return bool
     */
    protected function isTemporaryPath($filepath)
    {
        return strpos($filepath, sprintf('%s/tmp', $this->directoryList->getPath('media'))) === 0;
    }

    /**
     * @param array $result
     * @return string
     */
    protected function absolutePath(array $result)
    {
        return sprintf('%s%s%s', $result['path'], DIRECTORY_SEPARATOR, $result['file']);
    }

    /**
     * @param string $filepath
     * @return string
     */
    protected function mediaRelativePath($filepath)
    {
        $mediaPath = $this->directoryList->getPath('media') . DIRECTORY_SEPARATOR;
        return (strpos($filepath, $mediaPath) === 0) ? str_replace($mediaPath, '', $filepath) : $filepath;
    }
}
