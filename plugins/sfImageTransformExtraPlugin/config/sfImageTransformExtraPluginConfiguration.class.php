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
 * @version    SVN: $Id: sfImageTransformExtraPluginConfiguration.class.php 30357 2010-07-22 05:31:34Z caefer $
 */

/**
 * sfImageTransformExtraPlugin configuration.
 *
 * @package     sfImageTransformExtraPlugin
 * @subpackage  config
 * @author      Christian Schaefer <caefer@ical.ly>
 * @version     SVN: $Id: sfImageTransformExtraPluginConfiguration.class.php 30357 2010-07-22 05:31:34Z caefer $
 */
class sfImageTransformExtraPluginConfiguration extends sfPluginConfiguration
{
  const VERSION = '1.0.12';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    if($this->configuration instanceof sfApplicationConfiguration)
    {
      require_once($this->configuration->getConfigCache()->checkConfig('config/thumbnailing.yml'));
    }
  }
}
