<?php

namespace Modules\Conference\Services;

use Modules\Conference\Models\PaymentTransaction;
use Modules\Conference\Models\ConferenceRegistrant;
use Modules\Conference\Exceptions\ConferenceRegistrationException;

class LaunchConferenceRegistrationService
{
  private const CONFERENCE_DESCRIPTIONS = [
    'launch_conference' => 'Launch Conference Registration',
  ];

  public function __construct(private ConferenceRegistrant $registrant)
  {
  }

  public function initialiseManualRegistration(float $amount, string $conference_tag): PaymentTransaction
  {
    return $this->registrant->payment_transactions()->create([
      'description' => $this::CONFERENCE_DESCRIPTIONS[$conference_tag] ?? 'Invalid Conference Selected',
      'amount' => $amount,
      'payment_provider' => 'MANUAL',
      'transaction_reference' => unique_random('payment_transactions', 'transaction_reference', $this->registrant->registration_id . '-', 7),
    ]);
  }

  /**
   * Initilise and unconfirmed registration.
   *
   * @throws CourseSubscriptionException
   */
  public function initialisePaystackRegistration(float $amount, string $conference_tag): array
  {
    $conference_desc = $this::CONFERENCE_DESCRIPTIONS[$conference_tag] ?? NULL;

    throw_if(is_null($conference_desc), ConferenceRegistrationException::conferenceTagNotFound());

    throw_if(
        $this->registrant->conference_tag === $conference_desc && $this->registrant->registration_processed_at,
        ConferenceRegistrationException::duplicateRegistrationAttempt($conference_desc)
    );

    $payment_transaction = $this->registrant->payment_transactions()->create([
      'description' => $conference_desc,
      'amount' => $amount,
      'payment_provider' => 'PAYSTACK',
      'transaction_reference' => unique_random('payment_transactions', 'transaction_reference', $this->registrant->registration_id . '-', 7),
    ]);

    return [
      'conference_description' => $conference_desc,
      'is_charge_free' => (float) $amount === 0.0,
      'amount' => $amount,
      'payment_transaction' => $payment_transaction,
    ];
  }

  /**
   * Confirm a pending conferenece registration payment
   */
  public function confirmPaystackRegistration(PaymentTransaction $transaction): void
  {
    $this->registrant->registration_processed_at = now();
    $this->registrant->save();

    // $this->registrant->notify(new NewCoursePurchase($transaction->description, $transaction->amount));
  }
}
