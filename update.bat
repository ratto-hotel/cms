git fetch --all

git reset --hard origin/master

git pull origin main

composer install

php artisan migrate

npm install

npm run build:atom
