how to set

<!--set laravel-->

1. composer create-project laravel/laravel laravel_vue_demo
2. edit >> .env
3. php artisan migrate

<!--set vue-->

4. npm install
5. npm install vue @vitejs/plugin-vue
6. edit >> vite.config.js
7. edit >> app.js
8. create App.vue

<!-- set laravel <=> vue -->

9. create app.blade.php
10. edit web.php

<!-- set model, controller, policy-->

11. php artisan make:model Post -m
12. php artisan make:controller AuthController
13. php artisan make:controller PostController
14. php artisan make:policy PostPolicy --model=Post

---

how to run

php artisan serve<br>
npm run dev
