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
    $tesimonials = [
      [
        'name' => 'Nelson Mandela',
        'country' => 'South Africa',
        'testimonial' => 'A fundamental concern for others in our individual and community lives would go a long way in making the world the better place we so passionately dreamt of.',
        'img_url' => 'Modules/PublicPage/resources/template/assets/images/testimonials/thumbs/nelson-mandela.jpg',
      ],
      [
        'name' => 'Chinua Achebe',
        'country' => 'Nigeria',
        'testimonial' => 'Democracy is the worst form of government except for all those other forms that have been tried from time to time.',
        'img_url' => 'Modules/PublicPage/resources/template/assets/images/testimonials/thumbs/china-achuebe.jpg',
      ],
      [
        'name' => 'Dr. Akinwumi Adesina',
        'country' => 'Nigeria',
        'testimonial' => 'Proper management of our natural resources is vital for Nigeria\'s development. It ensures sustainable growth, environmental balance, and the well-being of future generations',
        'img_url' => 'Modules/PublicPage/resources/template/assets/images/testimonials/thumbs/adesina-a-akinwumi.jpg',
      ],
      [
        'name' => 'Peter Drucker',
        'country' => 'Austria',
        'testimonial' => 'The best way to predict the future is to create it. Good governance lays the foundation for a sustainable future.',
        'img_url' => 'Modules/PublicPage/resources/template/assets/images/testimonials/thumbs/peter-drucker.jpg',
      ],
      [
        'name' => 'Ban Ki-moon',
        'country' => 'South Korea',
        'testimonial' => 'Corruption erodes trust, destroys institutions, and stifles progress, making it one of the greatest threats to development and democracy.',
        'img_url' => 'Modules/PublicPage/resources/template/assets/images/testimonials/thumbs/ban-ki-moon.webp',
      ],
    ];

    return Inertia::render('PublicPage::Index', [
      'pageTitle' => 'Welcome to ' . config('app.name'),
      'testimonials' => $tesimonials,
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
        'desc' => 'Chief (Sir) Toranmah Victor Ukin (KSM) is from Alaka Quarters in Effurun, Uvwie L. G. A. He attended St. Mary Rrivate Schools, Lagos, Lagos State. Government College Ughell, Delta
            State and Anambra State Polytechnic Oko, Anambra State. He has served in the public domain for over 30 years as an architect, builder, project and design supervisor.
            He is happily married to his loving wife and they are blessed with children. He is a member of the Uvwe traditional council as a titled chief of the kingdom.
            He is an ambassador of the International Association of World Peace Advocate.',
      ],
      [
        'name' => 'Barrister Okiemute Akpofure Esq.',
        'position' => 'Legal Adviser',
        'imgUrl' => 'Modules/PublicPage/resources/template/assets/images/team/2.jpg',
        'desc' => '',
      ],
      [
        'name' => 'Ebenezer Emunarhine',
        'position' => 'P.R.O.',
        'imgUrl' => 'Modules/PublicPage/resources/template/assets/images/team/woman.jpeg',
        'desc' => '',
      ],
      [
        'name' => 'Ognenevwegba Merit',
        'position' => 'Treasurer',
        'imgUrl' => 'Modules/PublicPage/resources/template/assets/images/team/2.jpg',
        'desc' => '',
      ],
      [
        'name' => 'Musa khairat',
        'position' => 'Secretary / Admin',
        'imgUrl' => 'Modules/PublicPage/resources/template/assets/images/team/woman.jpeg',
        'desc' => '',
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
