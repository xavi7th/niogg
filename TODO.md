# FEATURES TO IMPLEMENT

- Implement API Auth using [Laravel Sanctum](https://laravel.com/docs/migrations).
- Implemnet Permissions using [Spatie Permissions](https://laravel.com/docs/queues).
- Update stubs to copy folders to public folder. Sample Enski
- Implement floating labels for text inputs @see https://www.youtube.com/watch?v=nJzKi6oIvBA


1. Update composer.json
2. update app.js
3. update app.scss
4. update vite.config.js
5. update app.blade to reflect vite.config.js
6. Update ModuleServiceProvider
7. Update ModuleRouteServiceProvider
8. Update all ModuleService Providers and stubs to change casing of directory structures
   1. Database
   2. Migrations
   3. Resources
   4. Config
   5. Routes (web and api)
9. Update docker to use docker-sync
10. Update build scripts to auto add public on build
    ```json
    "predev": "cp pre-commit .git/hooks/ && chmod +x .git/hooks/pre-commit && echo 'hook copied'"
    "prepackage": "sed -i.bak '/public\\\/[build\\\\|vendor\\\\|conference]/d' ./.gitignore",
    "package": "node -e \"require('child_process').execSync('npm run build && git add . && git commit -m \\\" build \\\"')\""
    "prepush": "if [[ ${npm_config_server} ]]; then echo \"\\033[30mbuilding production output...\\033[0m\" && node -e \"require('child_process').execSync('npm run package')\"; else echo \"\\033[32mskipping package step\\033[0m\"; fi;",
    "push": "if [[ ${npm_config_server} ]]; then args=--force && server=${npm_config_server}; fi; node -e \"require('child_process').execSync('git push $server $args')\"",
    "postpush": "if [[ ${npm_config_server} ]]; then echo \"\\033[31mrunning git reset\\033[0m\" && node -e \"require('child_process').execSync('git reset --hard HEAD~')\"; else echo \"\\033[33mskipping git reset...\\033[0m\"; fi;"
    ```
11. Update Vite config to ignore external url refs during build. This prevents these warnings ```[url] not resolved at build time, it will remain unchanged to be resolved at runtime```
    1.  ```js
        return defineConfig( {
          build: {
            rollupOptions: {
              external: [
                /^\/build\/vendor/,
                /^\/assets\/inertiajs/,
                /^\/sales-funnel/,
                /^\/conferences/,
                /^\/img\//,
                /^\/beyond-four-walls-conference/,
                /^\/accelerator-programme/,
                /^\/images\/slider\//,
                /^\/fonts\/slick/,
                /^\/img\//
              ]
            }
          },
          // ...
            })
        ```
12. iukjk
