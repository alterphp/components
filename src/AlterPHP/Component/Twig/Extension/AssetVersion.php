<?php

namespace AlterPHP\Component\Twig\Extension;

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
class AssetVersion extends \Twig_Extension
{

   /**
    * Returns a list of filters.
    *
    * @return array
    */
   public function getFilters()
   {
      $filters = array (
              'asset_with_version' => new \Twig_Filter_Method($this, 'asset_with_version_filter'),
      );

      return $filters;
   }

   public function asset_with_version_filter($value, $assetVersion = '20121002')
   {
      $urlParts = explode('?', $value);

       if ($assetVersion instanceof \DateTime)
       {
          $cacheBuster = $assetVersion->format('YmdHis');
       }
       else
       {
          $cacheBuster = urlencode($assetVersion);
       }

      return $urlParts[0] . '?' . $cacheBuster;
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