// Ussage

1. create config files

 cp path/to/config/config.dist.php path/to/config/config.php

2. Database setting

Kontiki supports sqlite and MySQL.  Set dbtype to use database and related information.
If you want to access to multiple database, add setting which name doesn't same as 'default'.

3. Deploy files

example.

+-- index.php
+-- foo.php
+-- bar.php
+-- libs
|   +-- kontiki <- place here
+-- classes
|   +-- db.php
|   +-- view.php
+-- config
|   +-- config.php
+-- db
    +-- db.sqlite
