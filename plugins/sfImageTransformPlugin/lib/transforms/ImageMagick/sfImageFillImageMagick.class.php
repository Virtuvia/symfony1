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
 * sfImageFillImageMagick class.
 *
 * Fills the set area with a color or tile image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <robin@ngse.co.uk>
 * @version SVN: $Id$
 */
class sfImageFillImageMagick extends sfImageTransformAbstract
{
    /**
     * x-coordinate.
     * @var int
    */
    protected $x = 0;

    /**
     * y-coordinate
     * @var int
    */
    protected $y = 0;

    /**
     * Fill.
    */
    protected $fill = null;

    /**
     * Fuzz
     *
     * @var int
     */
    protected $fuzz = 0;

    /**
     * Border
     *
     * @var string
     */
    protected $border = null;

    /**
     * Construct an sfImageDuotone object.
     *
     * @param int
     * @param int
     * @param String/object hex color
     * @param int
     * @param String/object hex color
     */
    public function __construct($x = 0, $y = 0, $fill = '#000000', $fuzz = 0, $border = null)
    {
        $this->setX($x);
        $this->setY($y);
        $this->setFill($fill);
        $this->setFuzz($fuzz);
        $this->setBorder($border);
    }

    /**
     * Sets the X coordinate
     *
     * @param int
     * @return bool
     */
    public function setX($x)
    {
        if (is_numeric($x)) {
            $this->x = (int) $x;

            return true;
        }

        return false;
    }

    /**
     * Gets the X coordinate
     *
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Sets the Y coordinate
     *
     * @param int
     * @return bool
     */
    public function setY($y)
    {
        if (is_numeric($y)) {
            $this->y = (int) $y;

            return true;
        }

        return false;
    }

    /**
     * Gets the Y coordinate
     *
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Sets the fuzz
     *
     * @param int $fuzz
     * @return bool
     */
    public function setFuzz($fuzz)
    {
        if (is_numeric($fuzz)) {
            $this->fuzz = (int) $fuzz;

            return true;
        }

        return false;
    }

    /**
     * Gets the fuzz
     *
     * @return int
     */
    public function getFuzz()
    {
        return $this->fuzz;
    }

    /**
     * Sets the border colour.
     *
     * @param string $border
     * @return bool
     */
    public function setBorder($border)
    {
        if ($border !== null && preg_match('/#[\d\w]{6}/', $border)) {
            $this->border = $border;

            return true;
        }

        return false;
    }

    /**
     * Gets the border colour.
     *
     * @return string
     */
    public function getBorder()
    {
        return $this->border;
    }

    /**
     * Sets the fill
     *
     * @param mixed
     * @return bool
     */
    public function setFill($fill)
    {
        if (preg_match('/#[\d\w]{6}/', $fill)) {
            $this->fill = $fill;

            return true;
        }

        return false;
    }

    /**
     * Gets the fill
     *
     * @return mixed
     */
    public function getFill()
    {
        return $this->fill;
    }

    /**
     * Apply the transform to the sfImage object.
     *
     * @param sfImage
     * @return sfImage
     */
    protected function transform(sfImage $image)
    {
        $resource = $image->getAdapter()->getHolder();

        $fill = new ImagickPixel();
        $fill->setColor($this->fill);

        /*
         *  colorFloodfillImage has been depricated, use new method is available
         */
        if (method_exists($resource, 'floodFillPaintImage') && is_null($this->border)) {
            $target = $resource->getImagePixelColor($this->getX(), $this->getY());
            $resource->floodFillPaintImage($fill, $this->getFuzz(), $target, $this->getX(), $this->getY(), false);
        } else {
            $border = new ImagickPixel();
            $border->setColor($this->border);

            $resource->colorFloodfillImage($fill, $this->getFuzz(), $border, $this->getX(), $this->getY());
        }

        return $image;
    }
}
