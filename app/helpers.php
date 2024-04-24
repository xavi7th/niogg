<?php

use Illuminate\Support\Collection;

if ( ! function_exists('slug_to_string')) {
  function slug_to_string($data)
  {
    return str_replace('-', ' ', $data);
  }
}

if ( ! function_exists('str_ordinal')) {
  /**
   * Append an ordinal indicator to a numeric value.
   *
   * @param  string|int  $value
   * @param  bool  $superscript
   * @return string
   */
  function str_ordinal($value, $superscript = FALSE)
  {
    $number = abs($value);

    if (class_exists('NumberFormatter')) {
      $nf = new NumberFormatter('en_US', NumberFormatter::ORDINAL);
      $ordinalized = $superscript ? number_format($number) . '<sup>' . mb_substr($nf->format($number), -2) . '</sup>' : $nf->format($number);

      return $ordinalized;
    }

    $indicators = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

    $suffix = $superscript ? '<sup>' . $indicators[$number % 10] . '</sup>' : $indicators[$number % 10];
    if ($number % 100 >= 11 && $number % 100 <= 13) {
      $suffix = $superscript ? '<sup>th</sup>' : 'th';
    }

    return number_format($number) . $suffix;
  }
}

if ( ! function_exists('uses_trait')) {
  function uses_trait($trait, $class): bool
  {
    return in_array($trait, class_uses_recursive($class));
  }
}

if ( ! function_exists('array_to_object')) {
  function array_to_object($array): object
  {
    return is_array($array) && ! empty($array)
      ? (object) array_map(fn ($v) => (object) $v, $array)
      : (gettype($array) === 'object' && empty((array) $array) ? NULL : $array);
  }
}

if ( ! function_exists('parse_size')) {
  /**
   * Parses a size string like 512M 1G and returns the byte float value
   *
   * @param  string  $size
   */
  function parse_size($size): float
  {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.

    if ($unit) {
      // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
      return round($size * pow(1024, mb_stripos('bkmgtpezy', $unit[0])));
    }

    return round($size);
  }
}

if ( ! function_exists('is_identical')) {
  function is_identical(mixed $v1, mixed $v2): bool
  {
    $type1 = gettype($v1);
    $type2 = gettype($v2);

    if ($v1 instanceof Collection) {
      $v1 = $v1->values();
    }
    if ($v2 instanceof Collection) {
      $v2 = $v2->values();
    }

    switch (TRUE) {
      case $type1 !== $type2:
        return FALSE;

      case $v1 !== $v2: // Loose comparison
        return FALSE;

      case in_array($type1, ['boolean', 'integer', 'double', 'string']):
        if ($v1 !== $v2) {
          return FALSE;
        } //Strict comparison here.
        break;

      case $type1 === 'array':
        $count = count($v1);
        if (count($v2) !== $count) {
          return FALSE;
        }

        //Check same keys.
        $arrKeysInCommon = array_intersect_key($v1, $v2);
        if (count($arrKeysInCommon) !== $count) {
          return FALSE;
        }

        //Require that their keys be in the same order.
        $arrKeys1 = array_keys($v1);
        $arrKeys2 = array_keys($v2);
        foreach ($arrKeys1 as $key => $val) {
          if ($arrKeys1[$key] !== $arrKeys2[$key]) {
            return FALSE;
          }
        }

        //Check same keys in same order.
        foreach ($v1 as $key => $val) {
          if (is_identical($v1[$key], $v2[$key]) === FALSE) {
            return FALSE;
          }
        }
        break;

      case $type1 === 'object':
        if ($v1 !== $v2) {
          return FALSE;
        } //See if loose comparison fails.

        //Now do strict(er) comparison.
        $objReflection1 = new ReflectionObject($v1);
        $objReflection2 = new ReflectionObject($v2);

        $arrProperties1 = $objReflection1->getProperties(ReflectionProperty::IS_PUBLIC);
        $arrProperties2 = $objReflection2->getProperties(ReflectionProperty::IS_PUBLIC);

        if (is_identical($arrProperties1, $arrProperties2) === FALSE) {
          return FALSE;
        }

        foreach ($arrProperties1 as $key => $propName) {
          if (is_identical($v1->{$propName}, $v2->{$propName}) === FALSE) {
            return FALSE;
          }
        }
        break;

      //Since both types are same, consider their "values" equal.
      case $type1 === 'NULL':
      case $type1 === 'resource':
      case $type1 === 'unknown type':
        break;
    }

    return TRUE; //All tests passed.
  }
}
