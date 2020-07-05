# afeka_face

## Environment setup
You should use a Linux OS, WSL-Ubuntu-18.04 is recommended.

Run the following commands to install the Apache2 and PHP 7.2.24:
```
sudo apt-get install apache2
sudo apt-get install php libapache2-mod-php
sudo /etc/init.d/apache2 start
```

Configure the path to the root directory of the *afeka_face* project on your machine instead of the */var/www/html* in the following 2 files:
```
sudo vim /etc/apache2/sites-available/000-default.conf
sudo vim /etc/apache2/apache2.conf
```

Restart the Apache2 server:
```
sudo /etc/init.d/apache2 restart
```

Run the following commands to install the MySQL 5.7.30:
```
sudo apt-get install mysql-server
sudo service mysql start
```

Right now MySQL is running with a default user and a default database.
You should delete them both and setup a root user with the database for *afeka_face*.
The following command will assist you in doing so.
You will be asked questions several times, answer "No" for the first one and "Y" for the rest.
The password that you setup for the root user should be inserted into the config file of the afeka_face.
```
sudo mysql_secure_installation
```

Create the database for the *afeka_face*:
```
mysql -u root
create database afeka_face
```

Install the *Composer* into the root directory of *afeka_face*.
The detailed steps are here: https://getcomposer.org/download/.

Install the dependecies for our project via the *Composer*:
```
php composer.phar install
```

## Useful commands
- Set the dev server
```
php -S localhost:8000
```