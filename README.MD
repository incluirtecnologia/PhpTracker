Biblioteca em php para rastreio de navegação de usuários.


# Como utilizar

## Definir variáveis de ambiente

```.php
<?php
putenv('MODE=dev');

putenv('DB_TRACKER_HOST=localhost');
putenv('DB_TRACKER_NAME=tracker_db');
putenv('DB_TRACKER_USER=root');
putenv('DB_TRACKER_PASS=root');
putenv('DB_TRACKER_CHARSET=utf8mb4');

```
## Criar tabelas

```.sql
CREATE TABLE client_info (
    id int auto_increment primary key,
    ip varchar(30) default null,
    server_name varchar(100) default null,
    server_port int default null,
    server_request_uri varchar(300) default null,
    server_software varchar(100) default null,
    request_method varchar(20) default null,
    http_cookie varchar(200) default null,
    user_agent varchar(200) default null,
    remote_addr varchar(100) default null,
    remote_port varchar(100) default null,
    request_time int default null
);

```
