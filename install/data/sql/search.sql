# --create database search;

use search;

DROP TABLE IF EXISTS search_auto_o_t;
create table search_auto_o_t
(
	url varchar(255) not null unique
)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci;

DROP TABLE IF EXISTS search_auto_t_o;
create table search_auto_t_o
(
	url varchar(255) not null unique
)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci;

DROP TABLE IF EXISTS search_url_backup;
create table search_url_backup
(
	url varchar(255) not null unique
)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci;

DROP TABLE IF EXISTS search_skip;
create table search_skip
(
	word varchar(255) not null unique
)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci;
	

#--this table for the tmp use and mainly it is the original table for classes 1代表无等级
DROP TABLE IF EXISTS search_class;
create table search_class
(
	class int unsigned not null auto_increment,
	word varchar(255),
	selected char(2) default '0',
	check(selected in ('0' or '1')),
	primary key(class)
)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci auto_increment = 0;



#--DROP TABLE IF EXISTS search_dict_tmp;
#--create table search_dict_tmp
#--(
#--	word varchar(255) unique not null
#--)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci;

DROP TABLE IF EXISTS search_index;
DROP TABLE IF EXISTS search_word;
DROP TABLE IF EXISTS search_url;

create table search_url
(
	url_id int unsigned not null auto_increment ,
	url varchar(255) not null,
	title varchar(255),
	description varchar(255) default 'No Description',
	visited int unsigned default 0,
	class int ,
	primary key(url_id)
	#--foreign key(class) references search_class(class) on delete cascade on update cascade
)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci auto_increment = 0;

#--DROP TABLE IF EXISTS search_word;
#--create table search_word
#--(
#--	word_id int unsigned auto_increment not null ,
#--	word varchar(255) not null,
#--	primary key(word_id),
#--	constraint unique (word)
#--)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci auto_increment = 0;

DROP TABLE IF EXISTS search_dict;
create table search_dict
(
	word_id int unsigned auto_increment not null,
	word varchar(255) not null,
	class int default 0,
	primary key(dict_id),
	constraint unique (word)
)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci auto_increment = 0;

#--DROP TABLE IF EXISTS search_index;
create table search_index
(
	word_id int unsigned not null,
	url_id int unsigned not null,
	primary key (word_id,url_id),
	foreign key (word_id) references search_dict(word_id),
	foreign key (url_id) references search_url (url_id)
)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci;

drop view if exists search_user_view;
create view search_user_view (url_id,url,word,class,title,description)
as
select search_url.url_id,search_url.url,search_dict.word,search_url.class,search_url.title,search_url.description
from search_url,search_word,search_index
where 
	search_index.word_id = search_dict.word_id 
and
	search_index.url_id = search_url.url_id;

DROP TABLE IF EXISTS search_log;
create table search_log
(	
	user_id int not null,
	search_time timestamp,
	search varchar(255), 
	results int unsigned default 0,#--具体的不需要知道到底返回那些结果，只要知道用户查了什么东西
	primary key(search_time,user_id)
)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci;


#--DROP TABLE IF EXISTS search_act;
#--create table search_act
#--(
#--	act_id int auto_increment not null,
#--	act varchar(255) not null,
#--	constraint unique(act),
#--	primary key(id)
#--)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci auto_increment = 0;

DROP TABLE IF EXISTS search_user;
create table search_user
(
	user_id int auto_increment not null ,
	name varchar(255) not null,
	password char(41) not null,
	email varchar(255) default null,
	telephone varchar(11) default null,
	register_time date default current_timestamp(),
	last_log timestamp default current_timestamp(),
	rights char(2) default '0', 
	is_logon char(2) default 'N', 
	check(rights in ('0' or '1')),
	check(logon in ('Y' or 'N')),
	primary key(user_id),
	constraint unique(name)
)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci auto_increment = 0;


drop trigger if exists search_user_time_update;
drop trigger if exists search_user_time_register;
delimiter //
create trigger search_user_time_update before update on search_user
for each row
begin
	set new.last_log = current_timestamp();
end 

//

create trigger search_user_time_register before insert on search_user
for each row
begin
	set new.register_time = curdate();
end 

//
delimiter ;

drop table if exists search_hi;
create table search_hi
(
	id int not null auto_increment,
	hi text not null,
	author varchar(255),
	create_time timestamp,
	creater int not null default 1,
	primary key(id),
	foreign key(creater) references search_user(user_id)
)ENGINE = InnoDB DEFAULT CHARACTER SET utf8 collate utf8_general_ci auto_increment = 0;

#-- this count gets all the powers of the database search
grant select,delete,update,insert on search.* to 'search_admin'@'localhost' identified by 'search_admin' with grant option;

#-- this count is for the spider
grant select,insert on search.search_auto_o_t to 'search_spider'@'%' identified by 'search_spider';
grant select,insert on search.search_auto_t_o to 'search_spider'@'%';
grant select,insert on search.search_dict to 'search_spider'@'%';
grant select,insert on search.search_index to 'search_spider'@'%';
grant select,insert on search.search_url to 'search_spider'@'%';
grant select on search.search_skip to 'search_spider'@'%';
grant select on search.search_class to 'search_spider'@'%';

#-- this count is just for users with limited powers
grant select on search.search_dict to 'search_user'@'%' identified by 'search_user';
grant select on search.search_user_view to 'search_user'@'%';
grant select on search.search_skip to 'search_user'@'%';
grant select on search.search_index to 'search_user'@'%';
grant select on search.search_url to 'search_user'@'%';
grant select on search.search_user to 'search_user'@'%';
grant select,insert on search.search_hi to 'search_user'@'%';
grant insert on search.search_log to 'search_user'@'%';
