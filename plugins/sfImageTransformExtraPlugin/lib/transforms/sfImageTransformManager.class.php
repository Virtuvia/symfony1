<?php
/**
 * This file is part of the sfImageTransformExtraPlugin package.
 * (c) 2010 Christian Schaefer <caefer@ical.ly>>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    sfImageTransformExtraPlugin
 * @author     Christian Schaefer <caefer@ical.ly>
 * @version    SVN: $Id: sfImageTransformManager.class.php 29957 2010-06-24 08:24:23Z caefer $
 */

/**
 * Static class holding thumbnail generating and removing functionality.
 *
 * @package    sfImageTransformExtraPlugin
 * @subpackage transforms
 * @author     Christian Schaefer <caefer@ical.ly>
 */
class sfImageTransformManager
{
    /**
     * @var array $options Holder for the options as configured in your thumbnailing.yml
     */
    private $options = [];

    /**
     *
     */
    public function __construct($formats = [])
    {
        $this->options['formats'] = $formats;

        if (empty($this->options['formats'])) {
            throw new sfImageTransformExtraPluginConfigurationException('Please configure "thumbnailing_formats" in your thumbnailing.yml!');
        }
    }

    /**
     * Generates a thumbnail.
     *
     * The generation is actually done by the sfImageTransformPlugin. This method only collects the
     * options configured in the thumbnailing.yml and uses them to call sfImageTransformPlugins transformations.
     * Additionally the generated thumbnail can be cached.
     *
     * @param  string  $uri
     * @param  array   $options Thumbnail parameters taken from the thumbnail URL referencing a format and id
     * @return sfImage
     */
    public function generate($uri, $format)
    {
        if (!array_key_exists($format, $this->options['formats'])) {
            throw new sfImageTransformExtraPluginConfigurationException('Unknown format "' . $format . '" in your thumbnailing.yml!');
        }

        $sourceImage = new sfImage($uri);
        $settings    = $this->options['formats'][$format];

        if (array_key_exists('mime_type', $settings)) {
            $sourceImage->setMIMEType($settings['mime_type']);
        }

        if (is_array($settings['transformations'])) {
            foreach ($settings['transformations'] as $transformation) {
                $this->transform($sourceImage, $transformation);
            }
        }

        $sourceImage->setQuality($settings['quality']);

        return $sourceImage;
    }

    /**
     * Executes a transformation on the source image
     *
     * @param  sfImage $sourceImage    The image to transform
     * @param  array   $transformation The transformation settings
     * @return void
     */
    private function transform(sfImage $sourceImage, $transformation)
    {
        call_user_func_array([$sourceImage, $transformation['transformation']], $transformation['param']);
    }
}
