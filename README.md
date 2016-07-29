Tasks Manager
==========

##Requirements:
- PHP 5 or higher
- Mysql
- Gulp
- [Requirements for Running Symfony 2.8](http://symfony.com/doc/2.8/reference/requirements.html)

##Setup:

Using GIT and Composer:
```
$ git clone https://github.com/rokst/Task-Manager
$ cd Task-Manager
$ composer install 
$ npm install
$ bower install
$ gulp
$ php app/console doctrine:database:create
$ php app/console doctrine:schema:update --force
```
##Usage:
```
$ cd Task-Manager/
$ php app/console server:run
```
This command will start a web application which you can access at http://localhost:8000/. In order to stop web server press ```Ctrl + C``` in your terminal window.

Set your specified user as an admin:
```
$ php app/console fos:user:promote

Please choose a username: 
$ [your user name]

Please choose a role: 
$ ROLE_ADMIN
```

- - - - - - -  
###### &copy; 2016 [Rokas Stašys](https://github.com/rokst)