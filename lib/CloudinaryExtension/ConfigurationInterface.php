<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image\Transformation;

interface ConfigurationInterface
{
    /**
     * @return Cloud
     */
    public function getCloud();

    /**
     * @return Credentials
     */
    public function getCredentials();

    /**
     * @return Transformation
     */
    public function getDefaultTransformation();

    /**
     * @return boolean
     */
    public function getCdnSubdomainStatus();

    /**
     * @return string
     */
    public function getUserPlatform();

    /**
     * @return UploadConfig
     */
    public function getUploadConfig();

    /**
     * @return boolean
     */
    public function isEnabled();

    /**
     * @return array
     */
    public function getFormatsToPreserve();

    /**
     * @param  string $file
     *
     * @return string
     */
    public function getMigratedPath($file);

    /**
     * @return void
     */
    public function enable();

    /**
     * @return void
     */
    public function disable();
}
