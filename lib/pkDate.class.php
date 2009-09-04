<?php

class pkDate
{
  // All date formatters here accept both Unix timestamps and MySQL date or datetime values.
  // These methods output only the date, not the time (see pkTime). Use these methods
  // for consistency within and across our applications.
  
  // Most compact: Sep 3 (2-digit year follows but only if not current year)
  
  static public function pretty($date)
  {
    $date = self::normalize($date);
    $month = date('F', $date);
    $day = date('j', $date);
    $month = substr($month, 0, 3);
    $year = date('Y', $date);
    $yearNow = date('Y');
    $result = "$month $day";
    if ($year != $yearNow)
    {
      // Switch to 2 digit year for compactness. TBB
      $result .= " '" . substr($year, 2);
    }
    return $result;
  }
  
  // Saturday, 14 January 2009
  
  static public function long($date)
  {
    $date = self::normalize($date);
    return date('l, j F Y', $date);
  }

  // Sat, 14 Jan 2009

  static public function medium($date)
  {
    $date = self::normalize($date);
    return date('D, j M Y', $date);
  }

  // 9/4/09 4PM

  static public function short($date)
  {
    $date = self::normalize($date);
    return date('n/j/y', $date);
  }
  
  static public function date($date, $format)
  {
    if (!in_array($format, array('pretty', 'short', 'medium', 'long')))
    {
      throw new Exception("Unknown or missing date format: $format\n");
    }
    return self::$format($date);
  }
  
  // IN: date as timestamp OR the following formats:
  // YYYY-MM-DD 
  // YYYY-MM-DD hh:mm:ss
  // hh:mm:ss
  // hh:mm:ss by itself is interpreted relative to the current day.
  //
  // OUT: timestamp
  static public function normalize($date)
  {  
    if (preg_match("/^(\d\d\d\d)-(\d\d)-(\d\d)( (\d\d):(\d\d):(\d\d))?$/", $date, $matches))
    {
      if (count($matches) == 4)
      {
        list($dummy1, $year, $month, $day) = $matches;
        $hour = 0;
        $min = 0;
        $sec = 0;
      }
      else
      {
        list($dummy1, $year, $month, $day, $dummy2, $hour, $min, $sec) = $matches;
      }
      $date = mktime($hour, $min, $sec, $month, $day, $year);
    }  
    elseif (preg_match("/^(\d\d):(\d\d):(\d\d)?$/", $date, $matches))
    {
      $now = time();
      $year = date('Y', $now);
      $month = date('n', $now);
      $day = date('j', $now);
      list($dummy1, $hour, $min, $sec) = $matches;
      $date = mktime($hour, $min, $sec, $month, $day, $year);
    }
    return $date;
  }
  
  // We have only one preferred time format
  
  static public function time($date)
  {
    $date = self::normalize($date);
    $hour = date('g', $date);
    $min = date('i', $date);
    $s = $hour;
    if ($min != 0)
    {
      $s .= ":$min";
    }
    $s .= date('A', $date);
    return $s;
  }
}
