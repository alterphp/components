<?php

namespace AlterPHP\Component\TwigExtensions\Extension;

use Symfony\Bundle\AsseticBundle\Twig\AsseticExtension;

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Henrik Bjornskov <hb@peytz.dk>
 * @package Twig
 * @subpackage Twig-extensions
 */
class Twig_Extensions_Extension_AssetVersion extends AsseticExtension
{

   /**
    * Returns a list of filters.
    *
    * @return array
    */
   public function getFilters()
   {
      $filters = array (
              'asset_with_version' => new Twig_Filter_Function(
                 'asset_with_version_filter', array ('needs_environment' => true)
              ),
      );

      return $filters;
   }

   /**
    * Name of this extension
    *
    * @return string
    */
   public function getName()
   {
      return 'AssetVersion';
   }

}

function asset_with_version_filter($value, $assetVersion = '20121002')
{
   $urlParts = explode('?', $value);

   return $urlParts[0] . '?' . urlencode($assetVersion);



   return str_replace("\n", $sep . "\n", $value);
}