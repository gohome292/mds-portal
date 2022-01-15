<?php
class ThumbnailHelper extends AppHelper
{
    // @param string $basename
    // @param integer $maxwidth
    // @param integer $maxheight
    // @return integer
    function getWidth($basename, $maxwidth, $maxheight)
    {
        $filename = UPLOADS . $basename;
        list($width, $height, $type, $attr) = getimagesize($filename);
        if ($width <= $maxwidth && $height <= $maxheight) return $width;
        // 倍率算出
        $magwidth = $width / $maxwidth;
        $magheight = $height / $maxheight;
        
        $maxmag = max($magwidth, $magheight);
        $width = $width / $maxmag;
        
        return intval($width);
    }
}
