<?php

namespace Modules\PublicPage\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\PublicPage\DTOs\ContactFormMessageDTO;
use Modules\PublicPage\Emails\NewContactFormMessage;

class PublicPageController extends Controller
{
  public function index()
  {
    return Inertia::render('PublicPage::Index', [
      'pageTitle' => 'Welcome to ' . config('app.name'),
    ])->withViewData([
      'pageTitle' => 'Welcome to ' . config('app.name'),
      'metaDesc' => config('app.alt_name') . ' promotes good governance in Nigeria by empowering youth through leadership training, advocating for transparency and accountability in
            public institutions, enhancing the electoral system, and educating citizens on their roles in governance to encourage active participation and societal development.',
      'ogUrl' => route('app.index'),
      'canonical' => route('app.index'),
    ]);
  }

  public function about()
  {
    $teams = [
      [
        'name' => 'Comrade Activist Asuke Robinson',
        'position' => 'C.E.O. / Founder',
        'imgUrl' => 'Modules/PublicPage/resources/template/assets/images/team/2.jpg',
        'desc' => 'Comrade auke Robinson,hails from isoko north LGA ozoro delta state university,abraka delta state is a versatile personality with great idea and business accumen.
                  He is a visionary writer, an activist , a human resources development personnel, a motivational speaker, multiple award winning, a real estate and property
                  manager a mentor whose examplary behavior is outstanding.above all he is a philanthropist.',
      ],
      [
        'name' => 'Chief Victor Ukiri',
        'position' => 'Chairman',
        'imgUrl' => 'Modules/PublicPage/resources/template/assets/images/team/woman.jpeg',
        'desc' => 'Aliquet adipiscing lectus praesent cras sed quis lectus egestas erat. Bibendum curabitur eget habitant feugiat nec faucibus eu lorem suscipit. Vitae vitae tempor enim eget.',
      ],
      [
        'name' => 'Barrister Okiemute Akpofure Esq.',
        'position' => 'Legal Adviser',
        'imgUrl' => 'Modules/PublicPage/resources/template/assets/images/team/2.jpg',
        'desc' => 'Ultricies massa malesuada viverra cras lobortis. Tempor orci hac ligula dapibus mauris sit ut eu. Eget turpis urna maecenas cras. Nisl dictum.',
      ],
      [
        'name' => 'Ebenezer Emunarhine',
        'position' => 'P.R.O.',
        'imgUrl' => 'Modules/PublicPage/resources/template/assets/images/team/woman.jpeg',
        'desc' => 'Aliquet adipiscing lectus praesent cras sed quis lectus egestas erat. Bibendum curabitur eget habitant feugiat nec faucibus eu lorem suscipit. Vitae vitae tempor enim eget.',
      ],
      [
        'name' => 'Ognenevwegba Merit',
        'position' => 'Treasurer',
        'imgUrl' => 'Modules/PublicPage/resources/template/assets/images/team/2.jpg',
        'desc' => 'Ultricies massa malesuada viverra cras lobortis. Tempor orci hac ligula dapibus mauris sit ut eu. Eget turpis urna maecenas cras. Nisl dictum.',
      ],
      [
        'name' => 'Musa khairat',
        'position' => 'Secretary / Admin',
        'imgUrl' => 'Modules/PublicPage/resources/template/assets/images/team/woman.jpeg',
        'desc' => 'Aliquet adipiscing lectus praesent cras sed quis lectus egestas erat. Bibendum curabitur eget habitant feugiat nec faucibus eu lorem suscipit. Vitae vitae tempor enim eget.',
      ],
    ];

    return Inertia::render('PublicPage::About', [
      'pageTitle' => 'About ' . config('app.name'),
      'teams' => $teams,
    ])->withViewData([
      'pageTitle' => 'About ' . config('app.name'),
      'metaDesc' => 'At ' . config('app.name') . ' we envision a society where the government is transparent and accountable to her citizens.',
      'ogUrl' => route('app.about'),
      'canonical' => route('app.about'),
    ]);
  }

  public function visionAndValues()
  {
    return Inertia::render('PublicPage::VisionAndValues', [
      'pageTitle' => 'Vision and Values of ' . config('app.name'),
    ])->withViewData([
      'pageTitle' => 'Vision and Values of ' . config('app.name'),
      'metaDesc' => config('app.alt_name') . '\'s core values—Transparency, Respect, Accountability, and Purpose—guide everything we do.',
      'ogUrl' => route('app.vision-and-values'),
      'canonical' => route('app.vision-and-values'),
    ]);
  }

  public function careers()
  {
    return Inertia::render('PublicPage::Careers', [
      'pageTitle' => 'Career opportunities available at ' . config('app.name'),
    ])->withViewData([
      'pageTitle' => 'Career opportunities available at ' . config('app.name'),
      'metaDesc' => config('app.alt_name') . ' is an equal opportunity employer. ' . config('app.alt_name') . ' does not discriminate on the basis of race,
            religion, colour, sex, age, non-disqualifying physical or mental disability, state of origin, or  any other basis covered by appropriate law. ',
      'ogUrl' => route('app.careers'),
      'canonical' => route('app.careers'),
    ]);
  }

  public function awards()
  {
    return Inertia::render('PublicPage::Awards', [
      'pageTitle' => 'Career opportunities available at ' . config('app.name'),
    ])->withViewData([
      'pageTitle' => 'Career opportunities available at ' . config('app.name'),
      'metaDesc' => config('app.alt_name') . ' is an equal opportunity employer. ' . config('app.alt_name') . ' does not discriminate on the basis of race,
            religion, colour, sex, age, non-disqualifying physical or mental disability, state of origin, or  any other basis covered by appropriate law. ',
      'ogUrl' => route('app.careers'),
      'canonical' => route('app.careers'),
    ]);
  }

  public function gallery()
  {
    return Inertia::render('PublicPage::Gallery', [
      'pageTitle' => 'Images speeaks thousand words',
    ])->withViewData([
      'pageTitle' => 'Images speeaks thousand words',
      'metaDesc' => config('app.alt_name') . ' is an equal opportunity employer. ' . config('app.alt_name') . ' does not discriminate on the basis of race,
            religion, colour, sex, age, non-disqualifying physical or mental disability, state of origin, or  any other basis covered by appropriate law. ',
      'ogUrl' => route('app.careers'),
      'canonical' => route('app.careers'),
    ]);
  }

  public function contact()
  {
    return Inertia::render('PublicPage::ContactUs', [
      'pageTitle' => 'Career opportunities available at ' . config('app.name'),
    ])->withViewData([
      'pageTitle' => 'Career opportunities available at ' . config('app.name'),
      'metaDesc' => config('app.alt_name') . ' is an equal opportunity employer. ' . config('app.alt_name') . ' does not discriminate on the basis of race,
            religion, colour, sex, age, non-disqualifying physical or mental disability, state of origin, or  any other basis covered by appropriate law. ',
      'ogUrl' => route('app.careers'),
      'canonical' => route('app.careers'),
    ]);
  }

  public function contactUs(Request $request)
  {
    $message = ContactFormMessageDTO::fromRequest($request);

    Mail::to([config('app.name') => config('app.email')])->send(new NewContactFormMessage($message));

    return back()->withFlash(['success' => 'Thank you for reaching out to us. We will get back to you shortly.']);
  }
}
