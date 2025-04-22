<?php

namespace Modules\Conference\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Conference\Models\PaymentTransaction;
use Modules\Miscellaneous\Services\PaymentService;
use Modules\Conference\Models\ConferenceRegistrant;
use Modules\Conference\Exceptions\ConferenceRegistrationException;
use Modules\Conference\Services\LaunchConferenceRegistrationService;
use Modules\Conference\Http\Requests\LaunchConferenceRegistrationRequest;

class Award2024ConferenceRegistrationController extends Controller
{
  public function index(): Response
  {
    return Inertia::render('Conference::Award2024Conference/AwardRegistrations', [
      'title' => 'Conference Registrations',
      'registrants' => ConferenceRegistrant::with('payment_transactions')->select('id', 'registration_id', 'full_name', 'email', 'phone')->latest()->paginate(),
      'payment_transactions' => PaymentTransaction::with('purchased_item:id,registration_id,full_name,email,phone,created_at')
          ->select('id', 'amount', 'description', 'transaction_reference', 'payment_provider', 'processed_at', 'purchased_item_id', 'purchased_item_type')->paginate(),
    ])->withViewData([
      'pageTitle' => 'Conference Registrations',
      'metaDesc' => 'This is where you can manage the conference registrations.',
      'ogUrl' => route('auth.login'),
      'canonical' => route('auth.login'),
    ]);
  }

  public function store(LaunchConferenceRegistrationRequest $request)
  {
    $registrant = $request->registrant();

    if ($request->is_manual_reg) {
      (new LaunchConferenceRegistrationService($registrant))->initialiseManualRegistration($request->amount, $request->conference_tag);

      return back();
    }

    list(
      'is_charge_free' => $is_charge_free,
      'amount' => $amount,
      'conference_description' => $conference_description,
      'payment_transaction' => $payment_transaction,
    ) = (new LaunchConferenceRegistrationService($registrant))->initialisePaystackRegistration($request->amount, $request->conference_tag);

    if ($is_charge_free) {
      (new LaunchConferenceRegistrationService($registrant))->confirmPaystackRegistration($payment_transaction);

      return back()->withFlash([
        'success' => '<b>Congratulations!!</b> <br /> <br />You have successfully registered to the <b><i>' . $conference_description .
            '</i></b>. An email has been sent to the address supplied containing your registration details. We look forward to seeing you there',
        'qr_code' => NULL,
      ]);
    }

    //redirect to paystack so that they can pay
    $subscriptionData = [
      'label' => $registrant->full_name . ' ' . $conference_description,
      'email' => $registrant->email,
      'amount' => $amount * 100,
      'customer_name' => $registrant->full_name,
      'callback_url' => route('app.conferences.launch.payment.verify'), //NOTE: This is the url where we are supposed verify the transaction.
      'metadata' => [
        'user_name' => $registrant->full_name,
        'user_email' => $registrant->email,
        'user_phone' => $registrant->phone,
        'cancel_action' => url()->previous() . '#become-a-sponsor',
        'purchased_item_id' => $payment_transaction->purchased_item_id,
        'purchased_item_type' => $payment_transaction->purchased_item_type,
        'amount' => $amount,
        'description' => 'Payment for the registration to ' . $conference_description . '.',
      ],
    ];

    return PaymentService::initializePaystackTransaction($registrant, $subscriptionData, $payment_transaction);
  }

  public function update(Request $request, ConferenceRegistrant $reg)
  {
    $rsp = PaymentService::verifyPaystackTransaction($request->trxref, returnResponse: TRUE);

    throw_unless($rsp['status'], ConferenceRegistrationException::paymentError($rsp['description']));

    (new LaunchConferenceRegistrationService($rsp['registrant']))->confirmPaystackRegistration($rsp['payment_transaction']);

    return redirect($rsp['redirect_url'])->withFlash([
      'success' => '<b>Congratulations!!</b> <br /> <br /> Your registration was successful. An email has been sent to the address supplied containing your registration details. We look forward to seeing you there.',
      'qr_code' => NULL,
    ]);
  }
}
