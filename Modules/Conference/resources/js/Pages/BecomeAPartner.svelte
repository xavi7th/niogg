<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
  import { Icons } from "@/Components/icons";
</script>

<script>
  import { page } from "@inertiajs/svelte";

  $: ({ app } = $page.props);
</script>

<h2 class="text-4xl text-gray-800">Become a Sponsor</h2>
<p class="text__block-desc">
  We are seeking financial support to cover essential costs such as venue rental, speaker fees, marketing and promotion, entertainment, decoration, catering and refreshment, Awards and prizes, event security, event logistics and post-event activities. The sponsorship levels outlined above offer a range of opportunities your Company/Brand to contribute meaningfully while receiving substantial value in return.
</p>
<div id="accordion" class="mb-60 testimonial-wrap-panel !p-4 md:!p-16">
  <div class="accordion-item opened">
    <div class="accordion__item-heade" data-toggle="collapse" data-target="#collapse1">
      <a class="accordion__item-title text-xl font-semibold uppercase" href="#">Diamond Sponsor - Ten Million Naira (₦10,000,000)</a>
    </div>
    <div id="collapse1" class="collapse show" data-parent="#accordion">
      <div class="accordion__item-body">
        <p class="!mb-4 text-gray-500 text-lg">Exclusive branding as the “Diamond Sponsor” of the event entails</p>
        <ul>
          <li><span class="tick">{@html Icons.adFlash}</span> Prominent logo placement on all event materials, including banners, brochures, and the event website.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Opportunity to deliver a keynote address on the topic of the event.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Acknowledgment in over 300 press releases and media coverage.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Three (3) complimentary tickets to the event.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Company/Brand to be displayed on screen throughout the event</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Presentation of Awards</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Booth design and layout for business exhibition </li>
          <li><span class="tick">{@html Icons.adFlash}</span> Seat at the front row          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="accordion-item">
    <div class="accordion__item-heade" data-toggle="collapse" data-target="#collapse2">
      <a class="accordion__item-title text-xl font-semibold uppercase" href="#">Platinum Sponsor - Eight Million Naira (₦8,000,000)</a>
    </div>
    <div id="collapse2" class="collapse" data-parent="#accordion">
      <div class="accordion__item-body">
        <p class="!mb-4 text-gray-500 text-lg">Exclusive branding as the “Platinum Sponsor” of the event entails</p>
        <ul>
          <li><span class="tick">{@html Icons.adFlash}</span> Prominent logo placement on all event materials, including banners, brochures, and the event website.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Opportunity to deliver a keynote address on the topic of the event.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Acknowledgment in over 200 press releases and media coverage.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Two (2) complimentary tickets to the event.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Company/Brand to be displayed on screen throughout the event</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Presentation of Awards</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Booth design and layout for business exhibition </li>
          <li><span class="tick">{@html Icons.adFlash}</span> Seat at the front row</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="accordion-item">
    <div class="accordion__item-heade" data-toggle="collapse" data-target="#collapse3">
      <a class="accordion__item-title text-xl font-semibold uppercase" href="#">Gold Sponsor - Five Million Naira (₦5,000,000)</a>
    </div>
    <div id="collapse3" class="collapse" data-parent="#accordion">
      <div class="accordion__item-body">
        <p class="!mb-4 text-gray-500 text-lg">Exclusive branding as the “Gold Sponsor” of the event entails</p>
        <ul>
          <li><span class="tick">{@html Icons.adFlash}</span> Prominent logo placement on all event materials, including banners, brochures, and the event website.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Opportunity to deliver a keynote address on the topic of the event.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Acknowledgment in over 100 press releases and media coverage.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Two (2) complimentary tickets to the event.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Company/Brand to be displayed on screen throughout the event </li>
          <li><span class="tick">{@html Icons.adFlash}</span> Presentation of Awards</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Booth design and layout for business exhibition </li>
          <li><span class="tick">{@html Icons.adFlash}</span> Seat at the front row</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="accordion-item">
    <div class="accordion__item-heade" data-toggle="collapse" data-target="#collapse4">
      <a class="accordion__item-title text-xl font-semibold uppercase" href="#">Silver Sponsor - Three Million Naira (₦3,000,000)</a>
    </div>
    <div id="collapse4" class="collapse" data-parent="#accordion">
      <div class="accordion__item-body">
        <p class="!mb-4 text-gray-500 text-lg">Exclusive branding as the “Silver Sponsor” of the event entails</p>
        <ul>
          <li><span class="tick">{@html Icons.adFlash}</span> Opportunity to deliver a keynote address on the topic of the event.</li>
          <li><span class="tick">{@html Icons.adFlash}</span> One (1) complimentary tickets to the event. </li>
          <li><span class="tick">{@html Icons.adFlash}</span> Presentation of Awards</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="accordion-item">
    <div class="accordion__item-heade" data-toggle="collapse" data-target="#collapse4">
      <a class="accordion__item-title text-xl font-semibold uppercase" href="#">Bronze Sponsor - One Million Naira (₦1,000,000)</a>
    </div>
    <div id="collapse4" class="collapse" data-parent="#accordion">
      <div class="accordion__item-body">
        <p class="!mb-4 text-gray-500 text-lg">Exclusive branding as the “Bronze Sponsor” of the event entails</p>
        <ul>
          <li><span class="tick">{@html Icons.adFlash}</span> Presentation of Awards</li>
          <li><span class="tick">{@html Icons.adFlash}</span> 1 complimentary tickets to the event</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="accordion-item">
    <div class="accordion__item-heade" data-toggle="collapse" data-target="#collapse6">
      <a class="accordion__item-title text-xl font-semibold uppercase" href="#">Delegate Package - Two Hundred Thousand Naira (₦200,000)</a>
    </div>
    <div id="collapse6" class="collapse" data-parent="#accordion">
      <div class="accordion__item-body">
        <p class="!mb-4 text-gray-500 text-lg">Exclusive branding as a “Delegate” of the event entails</p>
        <ul>
          <li><span class="tick">{@html Icons.adFlash}</span> Tickets for both morning and evening</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Entitled to twice meal both morning and evening</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Access to any individual in the hall</li>
          <li><span class="tick">{@html Icons.adFlash}</span> Entitled to a customized souvenir  </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<style lang="scss">
  li{
    display: flex;
    margin-bottom: 10px;
    align-items: center;

    .tick{
      height: 24px;
      width: 24px;
      margin-right: 10px;

      :global{
        svg{
          stroke-width: 1;
          stroke: #b48374;
        }
      }
    }
  }
</style>
