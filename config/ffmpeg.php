<?php

return [
  /*
  |--------------------------------------------------------------------------
  | FFmpeg Binary Paths
  |--------------------------------------------------------------------------
  |
  | Paths to the ffmpeg and ffprobe binaries. On shared hosting, these
  | may need to point to a static build in your home directory.
  |
  */
  'ffmpeg.binaries' => [env('FFMPEG_BINARY', 'ffmpeg')],

  'ffprobe.binaries' => [env('FFPROBE_BINARY', 'ffprobe')],

  /*
  |--------------------------------------------------------------------------
  | FFmpeg Timeout
  |--------------------------------------------------------------------------
  |
  | Maximum execution time for FFmpeg processes in seconds.
  |
  */
  'timeout' => env('FFMPEG_TIMEOUT', 300),
];
