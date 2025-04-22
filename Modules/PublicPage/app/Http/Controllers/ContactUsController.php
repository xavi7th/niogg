<?php

namespace Modules\PublicPage\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\PublicPage\DTOs\ContactFormMessageDTO;
use Modules\PublicPage\Emails\NewContactFormMessage;

class ContactUsController extends Controller
{
  public function index()
  {
    return Inertia::render('PublicPage::ContactUs', [
      'pageTitle' => 'Contact us for further information about any of our conferences or upcoming events',
    ])->withViewData([
      'pageTitle' => 'Contact us for further information about any of our conferences or upcoming events',
      'metaDesc' => 'Contact us for further information about any of our conferences or upcoming events',
      'canonical' => route('app.contact'),
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:155',
      'message' => 'required|string',
      'phone' => 'required',
      'how_did_you_hear_about_us' => 'required',
    ]);

    $message = ContactFormMessageDTO::fromRequest($request);

    Mail::to([config('app.name') => config('app.email')])->send(new NewContactFormMessage($message));

    return back()->withFlash(['success' => 'Thank you for reaching out to us. We will get back to you shortly.']);
  }
}
