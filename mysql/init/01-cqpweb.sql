create database cqpweb_db default charset utf8;
create user cqpweb identified by 'letmein';
grant all on cqpweb_db.* to cqpweb;
grant file on *.* to cqpweb;
