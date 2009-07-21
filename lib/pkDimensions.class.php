<?php

class pkDimensions
{
  public static function constrain($originalWidth, $originalHeight, $originalFormat, $options)
  {
    if (!isset($options['width']))
    {
      throw new sfException("No width parameter in options");
    }
    $width = $options['width'];
    if (!isset($options['height']))
    {
      throw new sfException("No height parameter in options (specify false for flexHeight)");
    }
    $height = $options['height'];
    if ($height === false)
    {
      $height = ceil(($width * $originalHeight) / $originalWidth);
    }
    if (!isset($options['resizeType']))
    {
      throw new sfException("No resizeType parameter in options");
    }
    $resizeType = $options['resizeType'];
    // Never exceed original size, but don't exceed requested size on the other axis
    // as a consequence either
    if ($originalWidth < $width)
    {
      $height = ceil($height * ($originalWidth / $width));
      $width = $originalWidth;
    }
    if ($originalHeight < $height)
    {
      $width = ceil($width * ($originalHeight / $height));
      $height = $originalHeight;
    }
    if (isset($options['format']))
    {
      $format = $options['format'];
    }
    else
    {
      $format = $originalFormat;
    }
    return array("width" => $width, "height" => $height, "format" => $format, "resizeType" => $resizeType);
  }
}