alter table client_info add request_params varchar(2000) default null after request_method;
alter table client_info add uploaded_files varchar(8000) default null after session_values;
