DROP TABLE IF EXISTS `room`;
CREATE TABLE `room` ( 
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,  
	`create_aid` int(11) UNSIGNED DEFAULT NULL COMMENT '创建人' ,  
	`name` varchar(30) NOT NULL DEFAULT '' COMMENT '会议室名称',  
	`address` varchar(100) NOT NULL DEFAULT '' COMMENT '会议室地址',   
	`remark` varchar(200) DEFAULT '' COMMENT '备注', 
	`status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 0-禁用 1-启用 2-删除',  
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会议室表';