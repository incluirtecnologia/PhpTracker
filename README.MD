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
    session_id varchar(100) not null,
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
    session_values varchar(300) default null,
    reg_date timestamp not null default CURRENT_TIMESTAMP
);

CREATE TABLE adwords (
	id int auto_increment primary key,
	client_info_id varchar(100) not null,
  campaignid varchar(255) default null,
  adgroupid varchar(255) default null,
  feeditemid varchar(255) default null,
  targetid varchar(255) default null,
  loc_physical_ms varchar(255) default null,
  matchtype varchar(255) default null,
  network varchar(255) default null,
  device varchar(255) default null,
  devicemodel varchar(255) default null,
  keyword varchar(255) default null,
  placement varchar(255) default null,
  adposition varchar(255) default null,
	reg_date timestamp not null default CURRENT_TIMESTAMP
);

create table mouse_move (
	id int auto_increment primary key,
	client_info_id varchar(100) not null,
	x int not null,
	y int not null,
	element varchar(300) not null,
  screen varchar(100) default null,
  height int default null,
  width int default null
);

```

## Adicionar o middleware no *EntryPoint* do projeto. Ex:

```.php

// index.php
// Rastreio de ações do usuário
SimpleRouter::setDefaultMiddlewares([function($request) {
    TrackerMiddleware::userTracker($request);
}]);
```

## (Para rastreio do mouse) Adicionar uma rota para o controller TrackerController
```.php
// routes.php
// Rastreio do mouse do usuário
[
    'pattern' => '/tracker/mouse',
    'callback' => function($request) {
        \Intec\Tracker\Controller\TrackerController::mouseMoveTracker($request);
    }
]
```
