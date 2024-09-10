<?php

namespace Modules\Miscellaneous\Services;

use Throwable;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Modules\Conference\Models\PaymentTransaction;
use Modules\Conference\Models\ConferenceRegistrant;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentService
{
  /**
   * This initialises a paystack transaction
   *
   * @return Response A redirect response either to Paystack servers on success or a back response to where we came from on failure
   */
  public static function initializePaystackTransaction(User $registrant, array $subscriptionData, ?PaymentTransaction $transaction = NULL, bool $isJson = TRUE): Response|RedirectResponse
  {
    $url = config('services.paystack.initialisation_url');
    $key = config('services.paystack.secret_key');

    $trxrf = $transaction?->transaction_reference ?? unique_random('payment_transactions', 'transaction_reference', 'TRF-', 21);

    $subscriptionData['reference'] = $trxrf;
    $subscriptionData['metadata']['transaction_reference'] = $trxrf;
    $purchased_item_id = $subscriptionData['metadata']['purchased_item_id'];
    $purchased_item_type = $subscriptionData['metadata']['purchased_item_type'];

    try {
      /**
       * --  SAMPLE RESPONSE FROM ENDPOINT --
       *
       * {
       *   "status": true,
       *   "message": "Authorization URL created",
       *   "data": {
       *     "authorization_url": "https://checkout.paystack.com/079zqhmgaplexpz",
       *     "access_code": "079zqhmgaplexpz",
       *     "reference": "TRF-A2TDAYjXd4ZTfJ7v4HpGl"
       *   }
       * }
       */
      $response = Http::withToken($key)->post($url, $subscriptionData);

      $paystackRsp = $response->json();

      if ($response->failed()) {
        self::reportTransactionIssue('Paystack Initialisation Failed for ' . $registrant->email, ['paystackRsp' => $paystackRsp, 'requestData' => $subscriptionData, 'affectedUser' => $registrant]);

        PaymentTransaction::updateOrCreate(
            [
              'transaction_reference' => $trxrf,
            ],
            [
              'amount' => $subscriptionData['metadata']['amount'],
              'description' => 'Failed Initialisation for ' . $registrant->email . '\'s ' . $subscriptionData['metadata']['description'],
              'purchased_item_id' => $purchased_item_id,
              'purchased_item_type' => $purchased_item_type,
              'payment_provider' => 'PAYSTACK',
              'api_response' => collect(['rsp' => json_decode($response->body())])->merge(['subscriptionData' => $subscriptionData]), //NOTE: Indicates issues from paystack's end generating the payment url.
            ],
        );

        return back()->withFlash(['error' => 'An error occured while trying to initialise the payment transaction. Please reload the page and try again.']);
      }

      if ($paystackRsp['status']) {
        PaymentTransaction::updateOrCreate(
            [
              'transaction_reference' => $trxrf,
            ],
            [
              'amount' => $subscriptionData['metadata']['amount'],
              'description' => $subscriptionData['metadata']['description'],
              'purchased_item_id' => $purchased_item_id,
              'purchased_item_type' => $purchased_item_type,
              'payment_provider' => 'PAYSTACK',
              'api_response' => collect(['rsp' => json_decode($response->body())])->merge(['subscriptionData' => $subscriptionData]), //NOTE: Indicates success from paystack's end generating the payment url.
            ],
        );

        if ($isJson) {
          return response('', 409)->header('X-Inertia-Location', $paystackRsp['data']['authorization_url']);
        }

        return redirect($paystackRsp['data']['authorization_url']);
      }

      return back()->withFlash(['error' => 'An unknown error occured while trying to initialise the payment transaction. Please reload the page and try again.']);
    } catch (Throwable $th) {
      self::reportTransactionIssue('Paystack Initialisation Failed for ' . $registrant->email, ['trxrf' => $trxrf, 'requestData' => $subscriptionData, 'affectedUser' => $registrant], $th);

      return back()->withFlash(['error' => 'An unknown error occured while trying to initialise the payment transaction. Please reload the page and try again.']);
    }
  }

  /**
   * This verifies a paystack transaction
   *
   * @param  bool  $returnResponse  Specify if the paystack response should be returned
   */
  public static function verifyPaystackTransaction(string $trxrf, ?ConferenceRegistrant $registrant = NULL, bool $returnResponse = FALSE): array
  {
    $url = config('services.paystack.verification_url') . $trxrf;
    $key = config('services.paystack.secret_key');

    /**
     * --  SAMPLE SUCCESS RESPONSE FROM ENDPOINT --
     *
     * {
     *   "status": true, //NOTE: This is NOT the status of the transaction BUT of the verification request. The STATUS of the verification is in the data key
     *   "message": "Verification successful",
     *   "data": {
     *     "id": 2690307081,
     *     "domain": "test",
     *     "status": "success", //SETTING: This is the status of the TRANSACTION VERIFICATION.
     *     "reference": "TRF-NtuCH2gXDpfMPAoTdUhdQ",
     *     "amount": 500000, //- Amount in Kobo
     *     "message": "test-3ds",
     *     "gateway_response": "[Test] Approved",
     *     "paid_at": "2023-04-04T05:39:55.000Z",
     *     "created_at": "2023-04-04T05:39:38.000Z",
     *     "channel": "card",
     *     "currency": "NGN",
     *     "ip_address": "41.217.35.93",
     *     "metadata": {
     *       "user_name": "Buffy Ballard",
     *       "user_phone": "+1 (216) 685-8382",
     *       "cancel_action": "http://enskiedutech.test/course/korrect-gadgets-instructional-videos",
     *       "course_id": "ScD5nX3Sw3-3-9",
     *       "course_package_id": "cPQ3sPnx31-3-8",
     *       "purchased_item_id": "52",
     *       "purchased_item_type": "RegistrantCourseSubscription",
     *       "amount": "5000",
     *       "is_upgrade": FALSE,
     *       "description": "Payment for subscription to Basic price tier of the Korrect Gadgets Instructional Videos course.",
     *       "transaction_reference": "TRF-NtuCH2gXDpfMPAoTdUhdQ"
     *     },
     *     "log": {
     *       "start_time": 1680586784,
     *       "time_spent": 12,
     *       "attempts": 1,
     *       "authentication": "3DS",
     *       "errors": 0,
     *       "success": true,
     *       "mobile": false,
     *       "input": [],
     *       "history": [
     *         {
     *           "type": "action",
     *           "message": "Attempted to pay with card",
     *           "time": 7
     *         },
     *         {
     *           "type": "auth",
     *           "message": "Authentication Required: 3DS",
     *           "time": 8
     *         },
     *         {
     *           "type": "action",
     *           "message": "Third-party authentication window opened",
     *           "time": 10
     *         },
     *         {
     *           "type": "success",
     *           "message": "Successfully paid with card",
     *           "time": 11
     *         },
     *         {
     *           "type": "action",
     *           "message": "Third-party authentication window closed",
     *           "time": 12
     *         }
     *       ]
     *     },
     *     "fees": 17500,
     *     "fees_split": null,
     *     "authorization": {
     *       "authorization_code": "AUTH_2hpoeyfpbh", //Reference: This can be saved and used to charge the user for subsequent transaction.
     *       "bin": "408408", //ADDED: This and the last4 can be combined 408408****0409
     *       "last4": "0409", //ADDED: to do "Card ending with 0409"
     *       "exp_month": "01",
     *       "exp_year": "2030",
     *       "channel": "card",
     *       "card_type": "visa ",
     *       "bank": "TEST BANK",
     *       "country_code": "NG",
     *       "brand": "visa",
     *       "reusable": true,
     *       "signature": "SIG_15NPi0w9k57g8dxXccFF",
     *       "account_name": null,
     *       "receiver_bank_account_number": null,
     *       "receiver_bank": null
     *     },
     *     "customer": {
     *       "id": 118314922,
     *       "first_name": null,
     *       "last_name": null,
     *       "email": "cohyvijan@mailinator.com",
     *       "customer_code": "CUS_cgd8cdof7zxn32o",
     *       "phone": null,
     *       "metadata": null,
     *       "risk_action": "default",
     *       "international_format_phone": null
     *     },
     *     "plan": null,
     *     "split": {},
     *     "order_id": null,
     *     "paidAt": "2023-04-04T05:39:55.000Z",
     *     "createdAt": "2023-04-04T05:39:38.000Z",
     *     "requested_amount": 500000, //- Amount in Kobo
     *     "pos_transaction_data": null,
     *     "source": null,
     *     "fees_breakdown": null,
     *     "transaction_date": "2023-04-04T05:39:38.000Z",
     *     "plan_object": {},
     *     "subaccount": {}
     *   }
     * }
     */
    $response = Http::withToken($key)->get($url);

    $paystackRsp = $response->json();
    $subscriptionData = $paystackRsp['data']['metadata'];

    $suspiciousTransaction = FALSE;

    /**
     * Search for a saved transaction of this reference
     * ! If not found and the verification is successful,
     * ! then proceed but note it that this was a request
     * ! that had not been presaved
     */
    try {
      $savedTransaction = PaymentTransaction::getByRef($trxrf);
    } catch (ModelNotFoundException $th) {
      $suspiciousTransaction = TRUE;

      self::reportTransactionIssue('Suspicious attempt to verify a non existing transaction with reference: ' . $trxrf, ['paystackRsp' => $paystackRsp, 'transactionRef' => $trxrf, 'affectedUser' => $registrant]);
    } catch (MultipleRecordsFoundException $th) {
      $suspiciousTransaction = TRUE;

      self::reportTransactionIssue('Suspicious attempt to verify a non existing transaction with reference: ' . $trxrf, ['paystackRsp' => $paystackRsp, 'transactionRef' => $trxrf, 'affectedUser' => $registrant]);
      $savedTransaction = PaymentTransaction::where('transaction_reference', $trxrf)->latest()->first();

      $savedTransaction->description = 'Suspicious transaction with duplicate trans reference: ' . $savedTransaction->description;
      $savedTransaction->save();
    }

    if (is_null($registrant)) { // This is probably an unknown user conference registration (therefore no $request->user() available)
      $registrant = $savedTransaction->purchased_item?->registrant ?? $savedTransaction->purchased_item; //NOTE: The purchased_item is most likely the registrant itself.
    }

    if ($response->failed()) {
      self::reportTransactionIssue('Paystack Verification Failed for ' . $registrant->email, ['paystackRsp' => $paystackRsp, 'transactionRef' => $trxrf, 'affectedUser' => $registrant]);

      return self::failedResponse($savedTransaction, $subscriptionData, $paystackRsp, TRUE);
    }

    if ( ! $paystackRsp['status']) {
      self::reportTransactionIssue('Paystack Verification Failed for ' . $registrant->email, ['paystackRsp' => $paystackRsp, 'transactionRef' => $trxrf, 'affectedUser' => $registrant]);

      //Peradventure Paystack will attempt to notify us again later so we are not deleting the subscription reference yet. We may set a number of tries thingy to delete after certain tries
      return self::failedResponse($savedTransaction, $subscriptionData, $paystackRsp, FALSE);
    }

    if ($suspiciousTransaction) {
      return self::failedResponse($savedTransaction, $subscriptionData, $paystackRsp, FALSE);
    }

    if ($savedTransaction->is_processed) {
      self::reportTransactionIssue('Suspicious attempt to reverify transaction with reference: ' . $trxrf, ['paystackRsp' => $paystackRsp, 'transactionRef' => $trxrf, 'affectedUser' => $registrant]);

      return self::failedResponse($savedTransaction, $subscriptionData, $paystackRsp, FALSE);
    }

    $savedTransaction->api_response = $paystackRsp; //INFO: Overwrite the previous Initialisation response with this new successful transaction response.
    $savedTransaction->processed_at = now();
    $savedTransaction->save();

    return [
      'status' => TRUE,
      'amount' => $paystackRsp['data']['amount'] / 100,
      'description' => $savedTransaction->description,
      'redirect_url' => $paystackRsp['data']['metadata']['cancel_action'],
      'payment_transaction' => $savedTransaction,
      'registrant' => $registrant,
    ] + ($returnResponse ? ['paystackRsp' => $paystackRsp] : []);
  }

  private static function failedResponse(PaymentTransaction $savedTransaction, array $subscriptionData, array $paystackRsp, bool $deleteRecords = FALSE)
  {
    if ($deleteRecords) {
      $savedTransaction->delete();
    } else {
      $savedTransaction->api_response = $paystackRsp;
      $savedTransaction->save();
    }

    return [
      'status' => FALSE,
      'cancel_action' => $paystackRsp['data']['metadata']['cancel_action'],
    ];
  }

  private static function reportTransactionIssue(string $msg, array $context, ?Throwable $th = NULL): void
  {
    logger()->critical('PAYSTACK TRANSACTION ISSUE: ' . $msg, $context);

    if ($th) {
      logger()->critical($th);
    }
  }
}
