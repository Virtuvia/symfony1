<?php
/*
 * This file is part of the sfImageTransform package.
 * (c) 2007 Stuart Lowes <stuart.lowes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * sfImageCropImageMagick class.
 *
 * Crops image.
 *
 * This class crops a image to a set size.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageCropImageMagick extends sfImageTransformAbstract
{
    /**
     * Left coordinate.
    */
    protected $left = 0;

    /**
     * Top coordinate
    */
    protected $top = 0;

    /**
     * Cropped area width.
    */
    protected $width;

    /**
     * Cropped area height
    */
    protected $height;

    /**
     * Construct an sfImageCrop object.
     *
     * @param int
     * @param int
     * @param int
     * @param int
     */
    public function __construct($left, $top, $width, $height)
    {
        $this->setLeft($left);
        $this->setTop($top);
        $this->setWidth($width);
        $this->setHeight($height);
    }

    /**
     * Sets the left coordinate
     *
     * @param int
     */
    public function setLeft($left)
    {
        if (is_numeric($left)) {
            $this->left = (int) $left;

            return true;
        }

        return false;
    }

    /**
     * returns the left coordinate
     *
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * set the top coordinate.
     *
     * @param int
     */
    public function setTop($top)
    {
        if (is_numeric($top)) {
            $this->top = (int) $top;

            return true;
        }

        return false;
    }

    /**
     * returns the top coordinate
     *
     * @return int
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * set the width.
     *
     * @param int
     */
    public function setWidth($width)
    {
        if (is_numeric($width)) {
            $this->width = (int) $width;

            return true;
        }

        return false;
    }

    /**
     * returns the width of the thumbnail
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * set the height.
     *
     * @param int
     */
    public function setHeight($height)
    {
        if (is_numeric($height)) {
            $this->height = (int) $height;

            return true;
        }

        return false;
    }

    /**
     * returns the height of the thumbnail
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Apply the transform to the sfImage object.
     *
     * @access protected
     * @param sfImage
     * @return sfImage
     */
    protected function transform(sfImage $image)
    {
        $resource = $image->getAdapter()->getHolder();
        $resource->cropImage($this->getWidth(), $this->getHeight(), $this->getLeft(), $this->getTop());
        $resource->setImagePage($this->getWidth(), $this->getHeight(), 0, 0);

        return $image;
    }
}
