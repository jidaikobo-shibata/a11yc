Ussage

1. create config files

 cp path/to/config/kontiki.dist.php path/to/config/kontiki.php
 cp path/to/config/users.dist.php path/to/config/users.php

2. Database setting

Kontiki supports sqlite and MySQL.  Set dbtype to use database and related information.
If you want to access to multiple database, add setting which name doesn't same as 'default'.

3. User setting

Set users to config/users.php.

4. Deploy files

example.

+-- main.php
+-- index.php
+-- foo.php
+-- bar.php
+-- libs
|   +-- kontiki <- place here
+-- classes
|   +-- db.php
|   +-- view.php
+-- config
|   +-- kontiki.php
|   +-- users.php
+-- db
    +-- db.sqlite
