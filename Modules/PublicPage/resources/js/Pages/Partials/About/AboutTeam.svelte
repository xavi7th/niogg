<script>
  import { getImgUrl } from '@/helpers';

  export let teams = [], appAltName = '';
</script>

<section id="team" class="projects-carousel-3 pt-120 pb-90">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="heading heading-3 text-center mb-50">
          <!-- <span class="heading__subtitle">Explore Recent Works</span> -->
          <h2 class="heading__title">Meet the Team</h2>
          <div class="divider__line divider__theme divider__center"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="py-16 lg:py-24 min-h-64">
          <div class="grid grid-cols-1 xl:grid-cols-5 gap-y-20 xl:gap-x-8 px-8 max-w-7xl mx-auto">
            <div class="col-span-2 max-w-md xs:text-center md:text-left">
              <h2 class="tracking-tighter text-gray-900 font-bold text-3xl leading-9">About the Team</h2>
              <p class="text-gray-600 leading-8 text-lg mt-6">
                { appAltName } is made up of a diverse team of academics, advisors and experts in their respective professions, who are dedicated to seeing the benefits of
                Democracy flourish in our nation Nigeria. Meet them all here.
              </p>
            </div>
            <ul class="xl:col-span-3 -mt-12 team-list">
              {#each teams as team}
                <li class="d-flex pt-12 flex-col sm:flex-row gap-10">
                  <img class="object-cover rounded-2xl flex-none w-52 aspect-[4/5] self-center md:self-auto" alt="" src="{ getImgUrl(team.imgUrl) }">
                  <div class="flex-auto max-w-xl">
                    <h3 class="text-gray-900 tracking-tight font-semibold text-lg mb-0">
                      { team.name }
                    </h3>
                    <p class="text-gray-600 text-base leading-7">
                      { team.position }
                    </p>
                    <p class="text-gray-600 text-base leading-7 mt-6">
                      { team.desc }
                    </p>
                    {#if teams.socials}
                      <ul role="list" class="gap-x-6 d-flex mt-6">
                        <li>
                          <a href="#" class="text-gray-400">
                            <span class="soc_media_name">X</span>
                            <svg fill="currentColor" viewBox="0 0 20 20" aria-hidden="true" class="w-5 h-5"><path d="M11.4678 8.77491L17.2961 2H15.915L10.8543 7.88256L6.81232 2H2.15039L8.26263 10.8955L2.15039 18H3.53159L8.87581 11.7878L13.1444 18H17.8063L11.4675 8.77491H11.4678ZM9.57608 10.9738L8.95678 10.0881L4.02925 3.03974H6.15068L10.1273 8.72795L10.7466 9.61374L15.9156 17.0075H13.7942L9.57608 10.9742V10.9738Z"></path></svg>
                          </a>
                        </li>
                        <li>
                          <a href="#" class="text-gray-400">
                            <span class="soc_media_name">LinkedIn</span>
                            <svg fill="currentColor" viewBox="0 0 20 20" aria-hidden="true" class="w-5 h-5"><path d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                            </svg>
                          </a>
                        </li>
                      </ul>
                    {/if}
                  </div>
                </li>
              {/each}


              <li class="d-flex pt-12 flex-col sm:flex-row gap-10">
                <img class="object-cover rounded-2xl flex-none w-52 aspect-[4/5]" alt="" src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=1024&h=1024&q=80">
                <div class="flex-auto max-w-xl">
                  <h3 class="text-gray-900 tracking-tight font-semibold text-lg mb-0">
                    Dries Vincent
                  </h3>
                  <p class="text-gray-600 text-base leading-7">
                    Business Relations
                  </p>
                  <p class="text-gray-600 text-base leading-7 mt-6">
                    Aliquet adipiscing lectus praesent cras sed quis lectus egestas erat. Bibendum curabitur eget habitant feugiat nec faucibus eu lorem suscipit. Vitae vitae tempor enim eget lacus nulla leo.
                  </p>
                  <ul role="list" class="gap-x-6 d-flex mt-6">
                    <li>
                      <a href="#" class="text-gray-400">
                        <span class="soc_media_name">X</span>
                        <svg fill="currentColor" viewBox="0 0 20 20" aria-hidden="true" class="w-5 h-5"><path d="M11.4678 8.77491L17.2961 2H15.915L10.8543 7.88256L6.81232 2H2.15039L8.26263 10.8955L2.15039 18H3.53159L8.87581 11.7878L13.1444 18H17.8063L11.4675 8.77491H11.4678ZM9.57608 10.9738L8.95678 10.0881L4.02925 3.03974H6.15068L10.1273 8.72795L10.7466 9.61374L15.9156 17.0075H13.7942L9.57608 10.9742V10.9738Z"></path></svg>
                      </a>
                    </li>
                    <li>
                      <a href="#" class="text-gray-400">
                        <span class="soc_media_name">LinkedIn</span>
                        <svg fill="currentColor" viewBox="0 0 20 20" aria-hidden="true" class="w-5 h-5"><path d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                        </svg>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  .soc_media_name{
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
  }

  .team-list{

    > :not([hidden]) {

      & ~ :not([hidden]){
        --tw-divide-opacity: 1;
        --tw-divide-y-reverse: 0;
        --tw-space-y-reverse: 0;

        border-color: rgb(229 231 235 / var(--tw-divide-opacity));
        border-top-width: calc(1px* calc(1 - var(--tw-divide-y-reverse)));
        border-bottom-width: calc(1px* var(--tw-divide-y-reverse));
        margin-top: calc(3rem* calc(1 - var(--tw-space-y-reverse)));
        margin-bottom: calc(3rem* var(--tw-space-y-reverse));
      }
    }
  }
</style>
