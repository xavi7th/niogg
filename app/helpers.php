<?php

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

if ( ! function_exists('unique_random')) {
  /**
   * Generate a unique random string of characters
   * uses str_random() helper for generating the random string
   *
   * @param  string  $table  - name of the table
   * @param  string  $col  - name of the column that needs to be tested
   * @param  string  $prefix  Any prefix you want to add to generated string
   * @param  int  $chars  - length of the random string
   * @param  bool  $numeric  Whether or not the generated characters should be numeric
   * @return string
   */
  function unique_random($table, $col, $prefix = NULL, $chars = NULL, $numeric = FALSE)
  {
    $unique = FALSE;

    // Store tested results in array to not test them again
    $tested = [];

    do {
      // Generate random string of characters

      if ($chars === NULL) {
        if ($numeric) {
          $random = $prefix . rand(100001, 999999999);
        } else {
          $random = $prefix . Str::uuid();
        }
      } else {
        if ($numeric) {
          $random = $prefix . rand(mb_substr(100000001, 1, ($chars)), mb_substr(9999999999, -($chars)));
        } else {
          $random = $prefix . Str::random($chars);
        }
      }

      // Check if it's already testing
      // If so, don't query the database again
      if (in_array($random, $tested)) {
        continue;
      }

      // Check if it is unique in the database
      $count = DB::table($table)->where($col, '=', $random)->count();

      // Store the random character in the tested array
      // To keep track which ones are already tested
      $tested[] = $random;

      // String appears to be unique
      if ($count === 0) {
        // Set unique to true to break the loop
        $unique = TRUE;
      }

      // If unique is still false at this point
      // it will just repeat all the steps until
      // it has generated a random string of characters
    } while ( ! $unique);

    return $random;
  }
}

if ( ! function_exists('unique_random2')) {
  /**
   * Generate a unique random string of characters
   * uses str_random() helper for generating the random string
   *
   * @param  string  $prefix  Any prefix you want to add to generated string
   * @param  int  $chars  - length of the random string
   * @param  bool  $numeric  Whether or not the generated characters should be numeric
   */
  function unique_random2(?string $prefix = NULL, int $chars = 32, bool $numeric = FALSE, $salt_length = 3): string
  {
    $seed = hash('sha256', now()->timestamp);
    $count_chars = mb_strlen($seed);

    if ($numeric) {
      $seed = Str::replaceMatches(pattern: '/[^0-9]++/', replace: '', subject: $seed);
      $count_chars = mb_strlen($seed);

      if ($count_chars < $chars) {
        return $prefix . $seed . rand(str()->repeat(1, $chars - $count_chars), str()->repeat(9, $chars - $count_chars));
      }

      //Randomize the tail of the generated string with 3 characters to prevent micro-second collisions possibilities
      return $prefix . mb_substr($seed, 0, $chars - $salt_length) . rand(str()->repeat(1, $salt_length), str()->repeat(9, $salt_length));
    }

    if ($count_chars < $chars) {
      return $prefix . $seed . rand(str()->repeat(1, $chars - $count_chars), str()->repeat(9, $chars - $count_chars));
    }

    //Randomize the tail of the generated string with $salt_length characters to prevent micro-second collisions possibilities
    return $prefix . mb_substr($seed, 0, $chars - $salt_length) . Str::random($salt_length);
  }
}

if ( ! function_exists('str_obfuscate')) {
  function str_obfuscate(?string $val, $decrypt = FALSE, $chunks = 2, $padding = 2, $salt_limiter = '-'): ?string
  {
    if (is_null($val)) {
      return NULL;
    }

    if ($decrypt) {
      $unhashed = $salt = '';
      try {
        list($chunks, $padding) = explode($salt_limiter, Str::after($val, $salt_limiter));
      } catch (ErrorException $th) {
        if ($th->getMessage() === 'Undefined array key 1') {
          throw new Exception('You cannot decrypt an unencrypted string', 422);
        }
        throw $th;
      }

      // if we pass and unbfuscated string without a padding and a chunk factor, just return the string
      if ( ! collect([$chunks, $padding])->every(fn ($v) => is_numeric($v))) {
        return $val;
      }

      // Remove the chunk and padding key and the trim out the last padded characters (to prevent characters overflowing into our desired result)
      $val = mb_substr(Str::before($val, $salt_limiter), $padding);

      $val = mb_str_split($val);

      //Retrieve the original string
      while (count($val) > 0) {
        for ($i = 0; $i < $chunks; $i++) {
          $unhashed .= array_pop($val);
        }
        for ($i = 0; $i < $padding; $i++) {
          $salt .= array_pop($val);
        }
      }

      return $unhashed;
    }

    $hash = '';
    $salter = md5($val); //use md5 to get the same salt for the same value

    foreach (mb_str_split($val, $chunks) as $key => $value) {
      $hash .= $value . Str::substr($salter, $key * $chunks, $padding);
    }

    //Reverse the string to make unhashing it more efficient using array_pop instead of array_shift and then save the chunk and padding factor so that we can unhash the result without remembering them
    return strrev($hash) . $salt_limiter . $chunks . $salt_limiter . $padding;
  }
}
