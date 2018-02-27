DROP TABLE IF EXISTS `meeting`;
CREATE TABLE `meeting` ( 
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,  
	`create_aid` int(11) unsigned DEFAULT 0 COMMENT '创建人' ,  
	`title` varchar(100) NOT NULL DEFAULT '' COMMENT '会议名称',  
	`room` int(11) NOT NULL DEFAULT '0' COMMENT '会议室编号',  
	`start_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '开始时间',  
	`end_time` int(10) unsigned NOT NULL DEFAULT  0 COMMENT '结束时间',  
	`remark` varchar(200) DEFAULT '' COMMENT '备注',  
	`create_time` int(10) unsigned NOT NULL DEFAULT  0  COMMENT '创建时间',  
	`update_time` int(10) unsigned NOT NULL DEFAULT 0  COMMENT '更新时间',  
	`status` int(1) NOT NULL DEFAULT 1 COMMENT '状态 0-已过期-禁用 1-启用 2-删除',  
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会议表';