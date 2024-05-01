# Laravel / InertiaJS / SvelteJS Starter Template

This is a starter template for building applications using Laravel, InertiaJS, SvelteJS, and Docker (or Laravel Sail).

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## About InertiaJS

Inertia is a new approach to building classic server-driven web apps. We call it the modern monolith. InertiaJS allows you to create fully client-side rendered, single-page apps, without the complexity that comes with modern SPAs. It does this by leveraging existing server-side patterns that you already love.

Inertia has no client-side routing, nor does it require an API. Simply build controllers and page views like you've always done! Inertia works great with any backend framework, but it's fine-tuned for Laravel.

You can learn more at their [home page](https://inertiajs.com/)

## About SvelteJS

Svelte is a front-end, open-source JavaScript framework for making interactive webpages. The general concept behind Svelte is similar to pre-existing frameworks like React and Vue in that it enables developers to make web apps. However, Svelte brings several features to the table that provides developers with a unique experience, such as:

  - Less code
  - No virtual DOM
  - Truly Reactive

Svelte provides a different approach to building web apps than some of the other frameworks covered in this module. While frameworks like React and Vue do the bulk of their work in the user's browser while the app is running, Svelte shifts that work into a compile step that happens only when you build your app, producing highly optimized vanilla JavaScript.

The outcome of this approach is not only smaller application bundles and better performance, but also a developer experience that is more approachable for people that have limited experience of the modern tooling ecosystem.

You can read more at [their website](https://svelte.dev) or give it a spin at their [playground](https://learn.svelte.dev/tutorial/welcome-to-svelte)


## Features of this template

This template has the following integrated already:

- Laravel 10
- Vite asset bundling
- Tailwind CSS
- Hot Module reloading using vite
- Authentication scafolded using Laravel Breeze, InertiaJS and SvelteJS components
- Modules Scafolded using the popular Laravel [Nwidart Modules](https://laravelmodules.com/). generate a module using ```php artisan module:create [ModuleName}``` and you are good to go
- An opinionated code linting standard using Laravel Pint and PHPCS configurable by editing the included ```pint.json``` and ```phpcs.xml``` files. To lint run the ```vendor/bin/pint```, ```vendor/bin/phpcs``` and ```vendor/bin/phpcbf``` commands from your terminal at the project root.
- Docker (Laravel Sail) setup and configured. Run ```sail up``` to use docker
- Docker Sync for faster docker development. (Uncomment the relevant line in the docker-compose file and then run ```make start_dev``` at project root to use docker-sync).
  - See [Docker Sync Website](http://docker-sync.io/) and [this article by Rohit Lingayat](https://betterprogramming.pub/improve-performance-of-docker-on-macos-by-using-docker-sync-4f46edbde570)
- Pre commit linting (Run ```npm run dev``` to set this up). You can edit the configiration for this in package.json's predev script.
- Sample package.json script to push production build to git remote server. Checkout package.json's push script.
- Laravel Telescope and Laravel Telescope toolar for local development. This can be disabled or enabled via env option ```TELESCOPE_ENABLED=true```

## Getting Started

To get started, view the [Getting Started guide](GETTING_STARTED.md)

## Contributing

Thank you for considering contributing to this template! The contribution guide can be found in the [here](CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [xavi7th@gmail.com](mailto:xavi7th@gmail.com). All security vulnerabilities will be promptly addressed.

## License

This template is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
