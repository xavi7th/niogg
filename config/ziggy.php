<?php

return [
  // 'whitelist' => ['home', 'api.*'],
  /**
   * !Blacklist hidden routes from the general @routes call
   * ? Access the blacklisted routes using group routes
   *
   * * @routes('admin'), @routes(['admin', 'superadmin]), @routes('*')
   */
  'blacklist' => ['debugbar.*', 'horizon.*', 'ignition.*'],
  'groups' => [
    'public' => [
      'app.*',
    ],
    'user' => [
      'appuser.*',
    ],
    'auth' => [
      'auth.login',
      'auth.logout',
      'auth.password.*',
      'auth.register',
      'auth.verification.notice',
    ],
  ],
];
