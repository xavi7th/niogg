<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;

</script>

<script>
  import { page } from "@inertiajs/svelte";
  import PageTitle from '@publicpage-partials/PageTitle.svelte';

  $: ({ app } = $page.props);

  let articles = [];

  fetch(`https://newsapi.org/v2/everything?q=nigeria&searchIn=title,description&language=en&pageSize=15&page=${Math.floor(Math.random() * 3)}&apiKey=84abb2477ec840bf8cd3073622e72058`)
    .then(res => res.json())
    .then(data => articles = data.articles.filter(x => x.author).filter(x => x.description))
    .catch(err => {
      console.log(err);
    })
</script>

<PageTitle appName={app.name} pageTitle='Latest Articles'>
  <li class="breadcrumb-item active" aria-current="page">Latest Articles</li>
</PageTitle>


{#if articles?.length}
  <section id="blogGrid" class="blog blog-grid pt-0 pb-70">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-6 offset-lg-3">
          <div class="heading text-center mb-50">
            <span class="heading__subtitle">Insight and Trends</span>
            <h2 class="heading__title">Trending events from {app.alt_name}</h2>
            <div class="divider__line divider__theme divider__center mb-0"></div>
            <p class="heading__desc">
              Follow our latest news and thoughts which focuses exclusively on insight, industry trends, top news headlines.
            </p>
          </div>
        </div>
      </div>
      <div class="row">

        {#each articles as article, idx (article.title)}
          {#if article.author && idx < 9}
            <div class="col-sm-12 col-md-6 col-lg-4">
              <div class="blog-item">
                <div class="blog__img">
                  <a href="#">
                    <enhanced:img src="@publicpage-assets/images/src/placeholders/7.jpg?enhanced&w=350&aspect=350:230" alt="blog thumb"/>
                  </a>
                </div>
                <div class="blog__content">
                  <div class="blog__meta">
                    <div class="blog__meta-cat">
                      <a href="#/" class="truncate">{ article.author || app.alt_name }</a>
                    </div>
                  </div>
                  <h4 class="blog__title"><a href="{article.url}">{article.title}</a></h4>
                  <span class="blog__meta-date">{ new Date(article.publishedAt).toDateString() }</span>
                  <p class="blog__desc line-clamp-4">{article.description}</p>
                  <a href="{article.url}" class="btn btn__secondary btn__link">
                    <span>Read More</span>
                    <i class="icon-arrow-right2"></i>
                  </a>
                </div>
              </div>
            </div>
          {/if}
        {/each}
      </div>
    </div>
  </section>
{/if}


<style lang="scss">
  .blog__meta{
    max-width: 80%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
</style>
