<?php

/**
 * Description of BitTools
 *
 * @package    AlterPHP.Component
 * @subpackage ToolBox
 * @author     pcb <pc.bertineau@alterphp.com>
 */

namespace AlterPHP\Component\ToolBox;

class BitTools
{

   /**
    * Return an array of active bits in the binary representation of the given integer
    * @param integer $int
    * @return array
    */
   public static function getBitArrayFromInt($int)
   {
      $binstr = (string) decbin($int);
      $binarr = array_reverse(str_split($binstr));
      $bitarr = array_keys($binarr, '1', true);

      return $bitarr;
   }

}
