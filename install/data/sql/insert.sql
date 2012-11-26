insert into search_user (name,password,rights) values("admin","d033e22ae348aeb5660fc2140aec35850c4da997",1);

insert into search_act (act) 
values
('查找')，('添加url'),('删除url'), ('添加停止词'),('删除停止词'),('添加字典'),('删除字典项'),('添加分类'),('删除分类'),('删除倒排序'),('清空结果')；



class int unsigned auto_increment not null,
	word varchar(255),
	primary key(class)


insert into search_class (word)
values
(' ')('计算机'),('游戏'),('机械'),('建筑'),('石油'),('农业'),('人文'),
('政治'),('生活'),('地名'),('医学'),('艺术'),('体育'),('自然');