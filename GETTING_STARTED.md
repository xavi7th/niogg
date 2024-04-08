# Getting Started with Laravel / InertiaJS / SvelteJS Starter Template

Welcome to Laravel / InertiaJS / SvelteJS Starter Template! This guide will walk you through the steps to start using this template for your own Laravel projects.

## Installation

### Prerequisites

Before getting started, ensure that you have the following prerequisites installed on your system:

- [Composer](https://getcomposer.org/download/): Dependency manager for PHP.
- [Node.js and npm](https://nodejs.org/): JavaScript runtime and package manager.

### Clone the Repository

```bash
git clone https://github.com/xavi7th/laravel-inertia-svelte-starter-template.git
```

### Navigate to the Project Directory

```bash
cd laravel-inertia-svelte-starter-template
```

### Install PHP Dependencies

```bash
composer install
```

### Install JavaScript Dependencies

```bash
npm install
```

### Compile Assets

```bash
npm run dev
```

### Set Up Environment Variables

Copy the `.env.example` file to create a new `.env` file:

```bash
cp .env.example .env
```

Generate an application key:

```bash
php artisan key:generate
```

### Run Migrations (Optional)

If your project requires a database, run migrations to create the necessary tables:

```bash
php artisan migrate
```

### Serve the Application

```bash
php artisan serve
```

Your Laravel application should now be running at `http://localhost:8000`.

## Configuration

### Customize Environment Variables

Edit the `.env` file to configure your application settings such as database connection details, mail services, and more.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### Customize Other Settings

You can customize various aspects of the template by editing configuration files located in the `config` directory, or by modifying the stub files located in the ```stubs``` directory.

To configure the build options edit the ```vite.config.js```, ```svelte.config.js```, ```postcss.config.js```, ```tailwind.config.css```, ```vite-module-loader.js```, or the ```jsonconfig.json``` file.

To configure Docker settings (or sail) edit the ```docker-compose.yml``` file or any of the relevant files in the ```docker``` folder.

To configure code linting, edit the ```pint.json``` or the  ```phpcs.xml``` files.

## Usage

### Creating Modules

To create a new module, you can use the provided artisan commands:

```bash
php artisan make:module MyModuleName
php artisan module:enable MyModuleName
php artisan module:make-model MyModel MyModuleName
```

### Using Docker, Nwidart Modules, Inertia.js and Svelte.js

Refer to the [Laravel Sail](https://laravel.com/docs/10.x/sail), [Laravel Modules](https://laravelmodules.com/docs), [Inertia.js](https://inertiajs.com/) and [Svelte.js](https://svelte.dev/) documentation for guidance on using these technologies in your project.

## Development

### Running Tests

To run tests, use the following command:

```bash
php artisan test
```

### Linting

Lint your PHP code using:

```bash
vendor/bin/lint

vendor/bin/phpcs

vendor/bin/phpcbf -lnv
```

## Deployment

### Optimizing for Production

Before deploying your application to production, optimize assets for better performance:

```bash
npm run build
```

### Securing Your Application

Follow Laravel's [security best practices](https://laravel.com/docs/master/security) to secure your application.

## Troubleshooting

If you encounter any issues during installation or usage, refer to the [troubleshooting guide](#) or [open an issue](https://github.com/xavi7th/laravel-inertia-svelte-starter-template/issues) on GitHub for assistance.

## Community and Support

Join our community on [Discord](#) for support and discussions. You can also [follow us on Twitter](#) for updates and announcements.

## License

This project is licensed under the [MIT License](LICENSE).

## Acknowledgments

- Laravel: The PHP Framework for Web Artisans
- Inertia.js: Modern Monolithic JavaScript Framework for Laravel
- Svelte.js: Cybernetically enhanced web apps
- Nwidart Modules: Package for modularizing laravel applications
