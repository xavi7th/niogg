<script lang="ts">
  interface Article {
    title: string,
    author: string
    url: string
    publishedAt: Date
    description: string
  }

  export let appAltName = '';

  let articles: Article[] = [];

  fetch(`https://newsapi.org/v2/everything?q=nigeria&searchIn=title,description&language=en&pageSize=10&page=${Math.floor(Math.random() * 4)}&apiKey=84abb2477ec840bf8cd3073622e72058`)
    .then(res => res.json())
    .then(data => articles = data.articles.filter(x => x.author).filter(x => x.description))
    .catch(err => {
      console.log(err);
    })
</script>

{#if articles?.length}
  <section id="blogGrid" class="blog blog-grid pt-120 pb-70">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-6 offset-lg-3">
          <div class="heading text-center mb-50">
            <span class="heading__subtitle">Insight and Trends</span>
            <h2 class="heading__title">Recent Articles</h2>
            <div class="divider__line divider__theme divider__center mb-0"></div>
            <p class="heading__desc">Follow our latest news and thoughts which focuses exclusively on insight,
              industry trends, top news headlines.</p>
          </div>
        </div>
      </div>
      <div class="row">

        {#each articles as article, idx (article.title)}
          {#if article.author && idx < 6}
            <div class="col-sm-12 col-md-6 col-lg-4">
              <div class="blog-item">
                <div class="blog__img">
                  <a href="#">
                    <enhanced:img src="@publicpage-template/images/blog/grid/1.jpg?enhanced&w=350&aspect=350:230" alt="blog thumb"/>
                  </a>
                </div>
                <div class="blog__content">
                  <div class="blog__meta">
                    <div class="blog__meta-cat">
                      <a href="#/" class="truncate">{article.author || {appAltName}}</a>
                    </div>
                  </div>
                  <h4 class="blog__title"><a href="{article.url}">{article.title}</a></h4>
                  <span class="blog__meta-date">{ new Date(article.publishedAt).toDateString() }</span>
                  <p class="blog__desc">{article.description}</p>
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
