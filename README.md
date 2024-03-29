#P8 - ToDo & Co

[![Maintainability](https://api.codeclimate.com/v1/badges/a0c3fa2b406fa3b2ff45/maintainability)](https://codeclimate.com/github/Kakahuette400/project_08/maintainability)

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/306d5f1d7538499e8f052ac5c196ba82)](https://www.codacy.com/gh/Kakahuette400/project_08/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Kakahuette400/project_08&amp;utm_campaign=Badge_Grade)

## Installation
- PHP 8.0.13
- MySql 5.7.36
- Apache 2.*
- Symfony 6

## Requirements
- Localhost 
For this project i used WAMPSERVER avaible here : https://www.wampserver.com/ (include PHP/SQL/APACHE)

## Installing the project:
Step 1: Clone the Repository on server from the root via the command: **git clone https://github.com/Kakahuette400/project_08.git**

Step 2: Install composer on your project if it's not already the case: https://getcomposer.org/
- Modify your "DATABASE_URL=" in .env file
- Modify your "MAILER_DSN=" in .env file
- Install APCu : http://pecl.php.net/package/APCu/5.1.21/windows (install this in the ext folder in your PHP file version)
- Install all dependances avaible on : https://packagist.org/ whit "composer install"
- Read the documentation to customize your installation

Step 3: To create a database follow this instructions :

`Place this command in your terminal `
  
    1 - php bin/console doctrine:database:create
    2 - php bin/console doctrine:migrations:migrate
    3 - php bin/console doctrine:fixtures:load

Step 4: Run the application : 

`Place this command in your terminal `
  
    php -S localhost:8000 -t public  

Step 5: Go to http://localhost:8000/login :

`You have 2 choices : `
  
    Admin - admin-test@gmail.com
    User  - user-test@gmail.com
    Password : password
	

## Make tests

Step 1: Make sure your .env.test have a DATABASE_URL different of .env (database creation and datas fixtures are include in tests methods)

Step 2 : let tests !

`Place this command in your terminal to make all tests `
  
     - php bin/phpunit


`Place this command in your terminal to make specify test `
  
     - php bin/phpunit --filter=<methodNameOrClassName>


`Place this command in your terminal to make all tests with coverage result in Documents folder `
  
     - php bin/phpunit --coverage-html Documents
   
   