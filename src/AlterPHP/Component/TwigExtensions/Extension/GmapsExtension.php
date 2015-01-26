<?php

namespace AlterPHP\Component\TwigExtensions\Extension;

use AlterPHP\Component\ToolBox\PolylineEncoder;

class GmapsExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('gmaps_circle', array($this, 'circle')),
        ];
    }

    public function circle($lat, $lng, $rad, $detail = 8)
    {
        $R    = 6371;

        $pi   = pi();

        $lat  = ($lat * $pi) / 180;
        $lng  = ($lng * $pi) / 180;
        $d    = $rad / $R;

        $points = array();
        $i = 0;

        for($i = 0; $i <= 360; $i+=$detail) {
            $brng = $i * $pi / 180;

            $pLat = asin(sin($lat)*cos($d) + cos($lat)*sin($d)*cos($brng));
            $pLng = (($lng + atan2(sin($brng)*sin($d)*cos($lat), cos($d)-sin($lat)*sin($pLat))) * 180) / $pi;
            $pLat = ($pLat * 180) /$pi;

            $points[] = array($pLat,$pLng);
        }

        $polyEnc   = new PolylineEncoder($points);
        $encString = $polyEnc->dpEncode();

        return $encString['Points'];
    }

    public function getName()
    {
        return 'gmaps_polyline_extension';
    }
}
