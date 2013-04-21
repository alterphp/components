<?php

namespace AlterPHP\Tests\Component\ToolBox;

use AlterPHP\Component\ToolBox\BitTools;

/**
 * Description of BitToolsTest
 *
 * @package
 * @subpackage
 */
class BitToolsTest extends \PHPUnit_Framework_TestCase
{
   public function testHasActiveBit()
   {
      $testValue1 = 7;
      $testBitWeight1 = 1;
      $expect1 = true;
      $result1 = BitTools::hasActiveBit($testValue1, $testBitWeight1);
      $this->assertEquals($expect1, $result1);

      $testValue2 = 5;
      $testBitWeight2 = 1;
      $expect2 = false;
      $result2 = BitTools::hasActiveBit($testValue2, $testBitWeight2);
      $this->assertEquals($expect2, $result2);

      $testValue3 = 5;
      $testBitWeight3 = 3;
      $expect3 = false;
      $result3 = BitTools::hasActiveBit($testValue3, $testBitWeight3);
      $this->assertEquals($expect3, $result3);
   }

   public function testGetBitArrayFromInt()
   {
      $testValue1 = 123456;
      $returnedArray1 = BitTools::getBitArrayFromInt($testValue1);
      $expectValue1 = 0;
      foreach($returnedArray1 as $bitWeight)
      {
         $expectValue1 += pow(2, $bitWeight);
      }
      $this->assertEquals($expectValue1, $testValue1);

      $testValue2 = 444555666;
      $returnedArray2 = BitTools::getBitArrayFromInt($testValue2);
      $expectValue2 = 0;
      foreach($returnedArray2 as $bitWeight)
      {
         $expectValue2 += pow(2, $bitWeight);
      }
      $this->assertEquals($expectValue2, $testValue2);

      $testValue3 = 2048;
      $returnedArray3 = BitTools::getBitArrayFromInt($testValue3);
      $this->assertEquals(array(11), $returnedArray3);
   }
}
