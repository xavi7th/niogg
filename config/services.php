<?php

return [

  /*
   |--------------------------------------------------------------------------
   | Third Party Services
   |--------------------------------------------------------------------------
   |
   | This file is for storing the credentials for third party services such
   | as Mailgun, Postmark, AWS and more. This file provides the de facto
   | location for this type of information, allowing packages to have
   | a conventional file to locate the various service credentials.
   |
   */

  'mailgun' => [
    'domain' => env('MAILGUN_DOMAIN'),
    'secret' => env('MAILGUN_SECRET'),
    'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    'scheme' => 'https',
  ],

  'postmark' => [
    'token' => env('POSTMARK_TOKEN'),
  ],

  'ses' => [
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
  ],
  'paystack' => [
    'card_key' => env('PAYSTACK_SECRET_KEY'),
    'secret_key' => env('PAYSTACK_SECRET_KEY'),
    'nuban_preferred_bank' => env('PAYSTACK_NUBAN_PREFERRED_BANK', 'test-bank'),

    'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
    'bvn_match_url' => env('PAYSTACK_BVN_URL', 'https://api.paystack.co/bvn/match'),
    'create_customer_url' => env('PAYSTACK_CREATE_CUSTOMER_URL', 'https://api.paystack.co/customer'),
    'create_nuban_url' => env('PAYSTACK_CREATE_NUBAN_URL', 'https://api.paystack.co/dedicated_account'),
    'verification_url' => env('PAYSTACK_VERIFICATION_URL', 'https://api.paystack.co/transaction/verify/'),
    'initialisation_url' => env('PAYSTACK_INITIALISATION_URL', 'https://api.paystack.co/transaction/initialize'),
    'charge_authorization_url' => env('PAYSTACK_CHARGE_AUTHORIZATION_URL', 'https://api.paystack.co/transaction/charge_authorization'),
  ],
];
