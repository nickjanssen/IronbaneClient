Ironbane Client
==============

The client for Ironbane, the open source MMO.
Play the game at <http://www.ironbane.com/>.

## Requirements

* A browser with WebGL support
* A local webserver (XAMPP, WAMP, etc)
* MySQL 5.0 or later
* A MySQL client (I recommend [SQLyog Community Edition](https://code.google.com/p/sqlyog/downloads/list) but you can also use phpMyAdmin which should come pre-installed with your webserver)
* PHP 5.3.8 or later

## Getting started

* Clone this repository inside your webserver.
 
```
    git clone https://github.com/ironbane/IronbaneClient.git
```

* Copy ```config_example.php``` from the ```/install/``` directory to the root directory

* Rename your copied ```config_example.php``` to ```config.php```

* Open ```config.php``` and edit the variables to match your system.

* Open up your MySQL client and make a new database ```ironbane```

* Import ```install/install.sql``` to database ```ironbane```

* [Install the Ironbane server on the same machine](https://github.com/ironbane/IronbaneServer/)

* Run the Ironbane server

* Open up ```http://localhost/IronbaneClient/game.php```

## Note

A lot of code in this repository is somewhat ancient and majority of it needs to be improved/rewritten.
I have learned more about better software development since I started this project, and would do it differently if I had to start over.
That being said, it works! If you find stuff you think you can improve, by all means go for it and make a pull request!
