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
 * sfImageArcGD class.
 *
 * Draws an arc.
 *
 * Draws an arc on an GD image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Stuart Lowes <stuart.lowes@gmail.com>
 * @version SVN: $Id$
 */
class sfImageArcGD extends sfImageTransformAbstract
{
    /**
     * X-coordinate of the center.
     * @var int
    */
    protected $x = 0;

    /**
     * Y-coordinate of the center.
     * @var int
    */
    protected $y = 0;

    /**
     * The arc width
     * @var int
    */
    protected $width = 0;

    /**
     * The arc height
     * @var int
    */
    protected $height = 0;

    /**
     * Line thickness
     * @var int
    */
    protected $thickness = 0;

    /**
     * The arc start angle, in degrees.
     * @var int
    */
    protected $start_angle = 0;

    /**
     * The arc end angle, in degrees.
     * @var int
    */
    protected $end_angle = 90;

    /**
     * Line color.
     * @var string hex
    */
    protected $color = '#000000';

    /**
     * Fill.
     * @var string/sfImage hex color or sfImage
    */
    protected $fill = null;

    /**
     * Line style.
     * @var int
    */
    protected $style = null;

    /**
     * Construct an sfImageArc object.
     *
     * @param int $x x coordinate
     * @param int $y y coordinate
     * @param int $width width of arc
     * @param int $height height of arc
     * @param int $start_angle angle in degrees
     * @param int $end_angle angle in degrees
     * @param int $thickness line thickness
     * @param string  $color hex color of line
     * @param string/object $fill string color or fill object
     * @param int $style fill style, only applicable if using a fill object
     */
    public function __construct($x, $y, $width, $height, $start_angle, $end_angle, $thickness = 1, $color = '#000000', $fill = null, $style = null)
    {
        $this->setX($x);
        $this->setY($y);
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setStartAngle($start_angle);
        $this->setEndAngle($end_angle);
        $this->setThickness($thickness);
        $this->setColor($color);
        $this->setFill($fill);
        $this->setStyle($style);
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
     * Sets the width
     *
     * @param int
     * @return bool
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
     * Gets the Width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Sets the height
     *
     * @param int
     * @return bool
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
     * Gets the height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Sets the start angel
     *
     * @param int
     * @return bool
     */
    public function setStartAngle($start_angle)
    {
        if (is_numeric($start_angle)) {
            $this->start_angle = (int) $start_angle;

            return true;
        }

        return false;
    }

    /**
     * Gets the start angel
     *
     * @return int
     */
    public function getStartAngle()
    {
        return $this->start_angle;
    }

    /**
     * Sets the end angel
     *
     * @param int
     * @return bool
     */
    public function setEndAngle($end_angle)
    {
        if (is_numeric($end_angle)) {
            $this->end_angle = (int) $end_angle;

            return true;
        }

        return false;
    }

    /**
     * Gets the end angel
     *
     * @return int
     */
    public function getEndAngle()
    {
        return $this->end_angle;
    }

    /**
     * Sets the thickness
     *
     * @param int
     * @return bool
     */
    public function setThickness($thickness)
    {
        if (is_numeric($thickness)) {
            $this->thickness = (int) $thickness;
            return true;
        }

        return false;
    }

    /**
     * Gets the thickness
     *
     * @return int
     */
    public function getThickness()
    {
        return $this->thickness;
    }

    /**
     * Sets the color
     *
     * @param string
     * @return bool
     */
    public function setColor($color)
    {
        if (preg_match('/#[\d\w]{6}/', $color)) {
            $this->color = $color;
            return true;
        }
        return false;
    }

    /**
     * Gets the color
     *
     * @return int
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Sets the fill
     *
     * @param mixed
     * @return bool
     */
    public function setFill($fill)
    {
        if (preg_match('/#[\d\w]{6}/', $fill) || (is_object($fill) && class_name($fill) === 'sfImage')) {
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
     * Sets the style
     *
     * @param int
     * @return bool
     */
    public function setStyle($style)
    {
        if (is_numeric($style)) {
            $this->style = (int) $style;

            return true;
        }

        return false;
    }

    /**
     * Gets the style
     *
     * @return int
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Apply the transform to the sfImage object.
     *
     * @param object
     * @return object
     */
    protected function transform(sfImage $image)
    {
        $resource = $image->getAdapter()->getHolder();

        imagesetthickness($resource, $this->thickness);

        if (!is_null($this->fill)) {
            if (!is_object($this->fill)) {
                imagefilledarc($resource, $this->x, $this->y, $this->width, $this->height, $this->start_angle, $this->end_angle, $image->getAdapter()->getColorByHex($resource, $this->fill), $this->style);
            }

            if ($this->color !== "" && $this->fill !== $this->color) {
                imagearc($resource, $this->x, $this->y, $this->width, $this->height, $this->start_angle, $this->end_angle, $image->getAdapter()->getColorByHex($resource, $this->color));
            }

        } else {

            imagearc($resource, $this->x, $this->y, $this->width, $this->height, $this->start_angle, $this->end_angle, $image->getAdapter()->getColorByHex($resource, $this->color));
        }

        return $image;
    }
}
