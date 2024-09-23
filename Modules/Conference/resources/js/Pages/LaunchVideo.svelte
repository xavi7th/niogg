<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { onDestroy, onMount} from "svelte";
  import { getImgUrl, shuffle } from '@/helpers';

  let player = undefined;

  onMount(() => {
    player = window.videojs('launch-video-player', {
      controls: true,
      autoplay: false,
      preload: 'auto',
      crossOrigin: "anonymous",
      sources: {
        src: '/build/img/niogg-launch-conference-teaser-' + shuffle([1,2])[0] + '.mp4',
        type: 'video/mp4'
      }
    });

    jQuery('.popup-video').magnificPopup({
      mainClass: 'mfp-fade',
      removalDelay: 0,
      preloader: false,
      fixedContentPos: false,
      preferFullWindow: true,
      type: 'inline',
      callbacks: {
        beforeClose: function() {
          player.pause();
        }
      }
    });
  })

  onDestroy(() => {
    player && player.dispose()
  })
</script>

<div class="video-1 bg-overlay mb-25 ring-2 ring-offset-4 ring-slate-200 hover:ring-slate-300 hover:ring-offset-8 scale-95 hover:scale-100 transition-all duration-700 ease-in-out">
  <div class="bg-img"><img src="{getImgUrl('Modules/PublicPage/resources/images/src/video/conference-flyer.jpg')}" alt="background"></div>
  <div class="video__btn text-center">
    <a class="popup-video" href="#launch-video">
      <span class="video__player-animation"></span>
      <span class="video__player-animation video__player-animation-2"></span>
      <span class="video__player-animation video__player-animation-3"></span>
      <div class="video__player">
        <i class="fa fa-play"></i>
      </div>
    </a>
    <div id="launch-video" class="relative w-fit mx-auto p-2 lg:p-4 mfp-hide">
      <video-js id="launch-video-player" poster="{ getImgUrl('Modules/PublicPage/resources/images/src/video/niogg-launch-poster.jpg') }">
        <p class="vjs-no-js">
          To view this video please enable JavaScript, and consider upgrading to a
          web browser that
          <a href="https://videojs.com/html5-video-support/" target="_blank">
            supports HTML5 video
          </a>
        </p>
      </video-js>
    </div>
  </div>
</div>

<style lang="scss">
  :global{
    .video-1.bg-overlay{
      margin: auto;
      border-radius: 1rem;

      @media (min-width: 768px) {
        width: 700px;
        height: 700px;
      }

      &::before{
        border-radius: 1rem;
        transition: background-color ease-out 500ms;
      }

      &:hover{
        &::before{
          background-color: rgba(27, 26, 26, 0.85);
        }
      }

      .video__btn{
        top: calc(50% - 37.5px);
      }
    }

    #launch-video .mfp-close {
      top: -44px;
      color: #FFF;
      text-align: right;
      right: 1px;
    }

    .launch-video-player-dimensions{
      @media (max-width:767px) {
        width: 94vw;
        height: auto;
        aspect-ratio: 1.8;
      }
    }
  }
</style>
