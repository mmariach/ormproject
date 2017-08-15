ormproject
==========

A Symfony project created on August 1, 2017, 11:21 pm.

A quick Setup-Instruction to this Project: 

You might want to install Symfony at your System:
http://symfony.com/doc/current/setup.html

As usual you can run this project: 
$ php bin/console server:run

ToDo:

You need to create a DataBase(e.g. test_project) for Symfony:
https://symfony.com/doc/current/doctrine.html

and to create DB-dependencies:
$ php bin/console doctrine:schema:update --force


You have to adjust the setting for DB-Access and Swift-Mailer:
/app/config/config.yml

If you want to use the Swift-Mailer for registration edit:
/src/AppBundle/Controller/SecurityController.php



