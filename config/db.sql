-- phpMyAdmin SQL Dump
-- version 4.4.15.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2018-01-08 11:59:54
-- 服务器版本： 5.5.48-log
-- PHP Version: 5.6.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wxLaxsjc`
--

-- --------------------------------------------------------

--
-- 表的结构 `day_stat`
--

CREATE TABLE IF NOT EXISTS `day_stat` (
  `id` int(11) unsigned NOT NULL,
  `day` int(11) unsigned NOT NULL DEFAULT '0',
  `keyword` varchar(128) NOT NULL DEFAULT '' COMMENT '关键字',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '今日出现次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(32) NOT NULL DEFAULT '' COMMENT 'openid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='天统计表';

-- --------------------------------------------------------

--
-- 表的结构 `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '按钮名字',
  `url` varchar(128) NOT NULL DEFAULT '' COMMENT '链接地址',
  `par_btn` int(11) NOT NULL DEFAULT '0' COMMENT '父级按钮',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='天统计表';

-- --------------------------------------------------------

--
-- 表的结构 `picmsg`
--

CREATE TABLE IF NOT EXISTS `picmsg` (
  `id` int(11) unsigned NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `author` varchar(128) NOT NULL DEFAULT '' COMMENT '作者',
  `source_url` varchar(1024) NOT NULL DEFAULT '' COMMENT '阅读更多链接',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `source_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '素材表ID',
  `content` varchar(2048) NOT NULL DEFAULT '' COMMENT '推着文章内容',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '正在推送',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `send_time` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `show_cover_pic` tinyint(1) NOT NULL DEFAULT '0' COMMENT '缩略图是否作封面显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图片推送消息';

-- --------------------------------------------------------

--
-- 表的结构 `push_info`
--

CREATE TABLE IF NOT EXISTS `push_info` (
  `push_id` int(11) unsigned NOT NULL,
  `show_cover_picmsg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '默认封面文章ID',
  `push_detail` varchar(2048) NOT NULL DEFAULT '' COMMENT '推送详情',
  `media_id` varchar(255) NOT NULL DEFAULT '' COMMENT '微信端id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '图文素材类型,0:news',
  `msg_id` varchar(64) NOT NULL DEFAULT '' COMMENT '消息发送任务的ID',
  `msg_data_id` varchar(64) NOT NULL DEFAULT '' COMMENT '消息的数据ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `push_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推送时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态,0保存未推送,1已推送',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推送表';

-- --------------------------------------------------------

--
-- 表的结构 `re_kw_info`
--

CREATE TABLE IF NOT EXISTS `re_kw_info` (
  `id` int(11) unsigned NOT NULL COMMENT 'ID',
  `keyword` varchar(128) NOT NULL DEFAULT '' COMMENT '关键字',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '回复关键词的标题',
  `url` varchar(256) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `description` varchar(256) NOT NULL DEFAULT '' COMMENT '回复关键词的描述',
  `imgurl` varchar(256) NOT NULL DEFAULT '' COMMENT '图片链接',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关键词回复';

-- --------------------------------------------------------

--
-- 表的结构 `source`
--

CREATE TABLE IF NOT EXISTS `source` (
  `id` int(11) unsigned NOT NULL,
  `file_name` varchar(128) NOT NULL DEFAULT '' COMMENT '素材存储路径',
  `media_id` varchar(255) NOT NULL DEFAULT '' COMMENT '微信端id',
  `url` varchar(2048) NOT NULL DEFAULT '' COMMENT '存储链接',
  `file_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '资源类型,0图片',
  `file_size` int(11) NOT NULL DEFAULT '0' COMMENT '资源大小',
  `media_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '素材类型,0临时1永久',
  `upload_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上传素材时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='素材数据表';

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) unsigned NOT NULL,
  `open_id` char(28) NOT NULL DEFAULT '' COMMENT 'open_id',
  `nick_name` varchar(255) NOT NULL DEFAULT '' COMMENT '昵称',
  `remark_name` varchar(255) NOT NULL DEFAULT '' COMMENT '备注名称',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `language` varchar(128) NOT NULL DEFAULT '' COMMENT '语言',
  `country` varchar(128) NOT NULL DEFAULT '' COMMENT '所在国家',
  `province` varchar(128) NOT NULL DEFAULT '' COMMENT '所在省',
  `city` varchar(128) NOT NULL DEFAULT '' COMMENT '所在城市',
  `headimgurl` varchar(1024) NOT NULL DEFAULT '' COMMENT '头像图片链接',
  `subscribe_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订阅时间',
  `groupid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '组id',
  `tagid_list` varchar(128) NOT NULL DEFAULT '' COMMENT '标签ID, 格式:100,101',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户状态，默认0正在关注，1取消关注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关注用户信息表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `day_stat`
--
ALTER TABLE `day_stat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `picmsg`
--
ALTER TABLE `picmsg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `push_info`
--
ALTER TABLE `push_info`
  ADD PRIMARY KEY (`push_id`);

--
-- Indexes for table `re_kw_info`
--
ALTER TABLE `re_kw_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `keyword` (`keyword`);

--
-- Indexes for table `source`
--
ALTER TABLE `source`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_id` (`media_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `open_id` (`open_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `day_stat`
--
ALTER TABLE `day_stat`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `picmsg`
--
ALTER TABLE `picmsg`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT for table `push_info`
--
ALTER TABLE `push_info`
  MODIFY `push_id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `re_kw_info`
--
ALTER TABLE `re_kw_info`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT for table `source`
--
ALTER TABLE `source`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `picmsg` add `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除';
