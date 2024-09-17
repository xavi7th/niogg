<?php

/** @var \Illuminate\Support\Collection|\Laravel\Telescope\EntryResult[] $entries */
$data = $entries->first()->content;

$dumper = new \Symfony\Component\VarDumper\Dumper\HtmlDumper();
$varCloner = new \Symfony\Component\VarDumper\Cloner\VarCloner();

$statusCode = $data['response_status'];
if ($statusCode > 400) {
    $statusColor = 'red';
} elseif ($statusCode > 300 || ! ($data['response']['data']['page']['props']['isInertiaRequest'] ?? $data['response']['props']['isInertiaRequest'])) {
    $statusColor = 'yellow';
} else {
    $statusColor = 'green';
}
?>

@component('telescope-toolbar::item', ['name' => 'inertia', 'link' => false])

@if(isset($data['response']) && is_array($data['response']) && (isset($data['response']['data']) || $data['response']['props']) )
    @slot('icon')
        <span class="shrink-0 rounded sf-toolbar-status sf-toolbar-status-{{ $statusColor }}">INERTIA</span>
        <span class="sf-toolbar-value sf-toolbar-info-piece-additional">{{ $data['response']['data']['page']['component'] ?? $data['response']['component'] }}</span>
    @endslot

    @slot('text')
        <div class="sf-toolbar-info-group">
            <div class="sf-toolbar-info-piece">
                <b>Component</b>
                <span>{{ $data['response']['data']['page']['component'] ?? $data['response']['component'] }}</span>
            </div>

            <div class="sf-toolbar-info-piece">
                <b>Flash Messages</b>
                @forelse ($data['response']['data']['page']['props']['flash'] ?? $data['response']['props']['flash'] as $key => $msg)
                  <span>{{ $key }}: {{ $msg }}</span> <br />
                @empty
                  <span>No Flash Messages</span>
                @endforelse
            </div>

            <div class="sf-toolbar-info-piece">
                <b>Controller Action</b>
                <span>
                    {{ $data['controller_action'] }}
                </span>
            </div>

            <div class="sf-toolbar-info-piece">
              <b>Route Name</b>
              <span>
                  {{ \Route::getRoutes()->match(Request::create($data['session']['_previous']['url']))->getName() }}
              </span>
            </div>

            <div class="sf-toolbar-info-piece">
                <b>IsInertiaRequest</b>
                <span>
                    {{ ($data['response']['data']['page']['props']['isInertiaRequest'] ?? $data['response']['props']['isInertiaRequest']) ? 'YES' : 'NO' }}
                </span>
            </div>

        </div>

        @if ($data['response']['data']['page']['props']['errors'] ?? $data['response']['props']['errors'])
          <div class="sf-toolbar-info-piece">
            <b>Error Messages</b>
            <div class="sf-toolbar-dump">

                {!!  $dumper->dump($varCloner->cloneVar($data['response']['data']['page']['props']['errors'] ?? $data['response']['props']['errors'])) !!}

            </div>
          </div>
        @endif
    @endslot

  @else
      @slot('icon')
        <span class="shrink-0 rounded sf-toolbar-status sf-toolbar-status-red">INERTIA</span>
        <span class="sf-toolbar-value sf-toolbar-info-piece-additional">INVALID INERTIA REQUEST</span>
      @endslot
  @endif

@endcomponent

<style>
   .sf-toolbar-block-inertia .sf-toolbar-icon {
        display: flex;
        align-items: center;
        padding-left: 0;
        padding-right: 0;
    }
    .sf-toolbar-block-inertia .sf-toolbar-label {
        margin-left: 4px;
        margin-right: 1px;
    }
    .sf-toolbar-block-inertia .sf-toolbar-status + .sf-toolbar-request-icon {
        display: inline-flex;
        margin-left: 5px;
    }
    .sf-toolbar-block-inertia .sf-toolbar-icon .sf-toolbar-request-icon + .sf-toolbar-label {
        margin-left: 0;
    }
    .sf-toolbar-block-inertia .sf-toolbar-label + .sf-toolbar-value {
        margin-right: 5px;
    }

    .sf-toolbar-block-inertia:hover .sf-toolbar-info {
        max-width: none;
    }

    .sf-toolbar-block:not(.sf-toolbar-block-dump) .sf-toolbar-info-piece .sf-toolbar-dump span{
      color: #1299DA;
    }

    pre.sf-dump .sf-dump-key {
      color: #56DB3A !important;
    }

    pre.sf-dump .sf-dump-str {
      color: #56DB3A !important;
    }

</style>
