# Develop the SnowTricks community website from A to Z

Project number six completed as part of my OpenClassrooms training.

[![SymfonyInsight](https://insight.symfony.com/projects/d75197a6-ec64-4e80-82bc-cab190e3f047/big.svg)](https://insight.symfony.com/projects/d75197a6-ec64-4e80-82bc-cab190e3f047)

### Requirements

 * PHP 8.1
 * Symfony CLI
 * Composer 2.3
 
## Install

1. In your terminal, execute the following command to clone the project into the "blog" directory.
```shell
git clone https://github.com/damien-jandard/develop-from-a-to-z-the-snowtricks-community-website.git snowtricks
```

2. Access the "snowtricks" directory.
```shell
cd snowtricks
```

3. Duplicate and rename the .env file to .env.local, and modify the necessary information (APP_ENV, APP_SECRET, DATABASE_URL, MAILER_DSN, JWT_SECRET).
```shell
cp .env .env.local
```

4. Install the composer dependencies.
```shell
composer install
```

5. Run the migrations.
```shell
symfony console doctrine:migration:migrate --no-interaction
```

6. Adding default fixtures.
```shell
symfony console doctrine:fixtures:load --no-interaction
```

7. Start the local server.
```shell
symfony server:start -d
```

8. You can test the website using the following credentials.

- Administrator Account:
	- Username: admin@snowtricks.com
	- Password: Admin1234*

- User Account:
	- Username: user1@snowtricks.com
	- Password: User1234*

9. To enable local email sending, you can install [MailHog](https://github.com/mailhog/MailHog).
