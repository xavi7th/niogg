<?php

namespace Modules\AppUser\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\AppUser\Models\AppUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class AppUserController extends Controller
{
  public function index()
  {
    // $this->authorize('index', AppUser::class);

    return Inertia::render('AppUser::Index', [
      'phpVersion' => PHP_VERSION,
    ])->withViewData([
      'title' => 'Page title',
      'metaDesc' => 'Description of this page',
      'ogUrl' => route('app.index'),
      'canonical' => route('app.index'),
    ]);
  }

  public function create()
  {
    // $this->authorize('create', AppUser::class);

    return view('appuser::create');
  }

  public function store(Request $request): RedirectResponse
  {
    // $this->authorize('store', AppUser::class);

    return back()->withFlash(['success' => 'Succesful!']);
  }

  public function show($id)
  {
    // $this->authorize('show', AppUser::class);

    return view('appuser::show');
  }

  public function edit($id)
  {
    // $this->authorize('edit', AppUser::class);

    return view('appuser::edit');
  }

  public function update(Request $request, $id): RedirectResponse
  {
    // $this->authorize('update', AppUser::class);

    return back()->withFlash(['success' => 'Succesful!']);
  }

  public function destroy($id): void
  {
    // $this->authorize('destroy', AppUser::class);
  }
}
