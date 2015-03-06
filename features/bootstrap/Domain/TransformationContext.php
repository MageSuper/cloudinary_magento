<?php

namespace Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Dimensions;
use CloudinaryExtension\Image\Transformation\Quality;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use ImageProviders\TransformingImageProvider;

require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class TransformationContext implements Context
{

    private $imageProvider;
    private $image;

    private $imageUrl;

    private $transformation;

    public function __construct()
    {
        $cloud = Cloud::fromName("aCloudName");
        $credentials = new Credentials(Key::fromString("aKey"), Secret::fromString("aSecret"));

        $this->imageProvider = new TransformingImageProvider($credentials, $cloud);


    }

    /**
     * @Transform :aQuality
     */
    public function transformStringToQuality($string)
    {
        return Quality::fromString($string);
    }

    /**
     * @Transform :aDimension
     */
    public function transformStringToDimensions($string)
    {
        $dimensions = explode('x', $string);

        return Dimensions::fromWidthAndHeight($dimensions[0], $dimensions[1]);
    }

    /**
     * @Given there's an image :anImage in the image provider
     */
    public function thereSAnImageInTheImageProvider(Image $anImage)
    {
        $this->image = $anImage;
        $this->imageProvider->upload($this->image);
    }

    /**
     * @When I request the image from the image provider
     */
    public function iRequestTheImageFromTheImageProvider()
    {
        $this->imageUrl = $this->imageProvider->transformImage($this->image, Transformation::builder());
    }

    /**
     * @Then I should get an optimised image from the image provider
     */
    public function iShouldGetAnOptimisedImageFromTheImageProvider()
    {
        expect($this->urlIsOptimised())->toBe(true);
    }

    /**
     * @Given image optimisation is disabled
     */
    public function imageOptimisationIsDisabled()
    {
        $this->transformation = Transformation::builder()->withOptimisationDisabled();
    }

    /**
     * @Then I should get the original image from the image provider
     */
    public function iShouldGetTheOriginalImageFromTheImageProvider()
    {
        $this->imageUrl = $this->imageProvider->transformImage($this->image, $this->transformation);

        expect($this->urlIsOptimised())->toBe(false);
    }

    /**
     * @Then I should get an image with :aQuality percent quality from the image provider
     */
    public function iShouldGetAnImageWithPercentQualityFromTheImageProvider(Quality $aQuality)
    {
        expect($this->isPercentageQuality((string)$aQuality));
    }

    /**
     * @Given I transform the image to have :aQuality percent quality
     */
    public function iTransformTheImageToHavePercentQuality(Quality $aQuality)
    {
        $this->transformation = Transformation::builder()->withQuality($aQuality);
    }

    /**
     * @When I ask the image provider for :imageName transformed to :aDimension
     */
    public function iRequestTheImageProvideForTransformedTo($imageName, Dimensions $aDimension)
    {
        $this->receivedUrl = $this->imageProvider->transformImage(
            Image::fromPath($imageName),
            Transformation::builder()->withDimensions($aDimension)
        );
    }

    /**
     * @Then I should receive that image with the dimensions :aDimension
     */
    public function iShouldReceiveThatImageWithTheDimensions(Dimensions $aDimension)
    {
        expect($this->hasDimensions($aDimension));
    }

    private function urlIsOptimised()
    {
        return strpos($this->imageUrl, 'fetch_format=auto') !== false;
    }

    private function isPercentageQuality($percentage)
    {
        return strpos($this->imageUrl, "quality=$percentage") !== false;
    }

    private function hasDimensions(Dimensions $dimension)
    {
        $hasWidth = strpos($this->imageUrl, "width={$dimension->getWidth()}") !== false;
        $hasHeight = strpos($this->imageUrl, "height={$dimension->getHeight()}") !== false;
        return $hasWidth && $hasHeight;
    }
}