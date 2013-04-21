<?php

namespace AlterPHP\Component\ToolBox;

/**
 * Description of BitTools
 *
 * @package    AlterPHP.Component
 * @subpackage ToolBox
 * @author     pcb <pc.bertineau@alterphp.com>
 */
class BitTools
{

   public static function hasActiveBit($value, $bitWeight)
   {
      return ($value & pow(2, $bitWeight)) > 0;
   }

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
