<?php

use ProductSystem\Core\Database\PDO;
use ProductSystem\Core\Model\Product\ComponentChildType;
use ProductSystem\Core\Model\Product\Product;
use ProductSystem\Core\Model\Product\Set;
use ProductSystem\Core\Model\User\Admin;
use ProductSystem\Core\Model\User\Manager;
use ProductSystem\Core\Model\User\User;
use ProductSystem\Core\Model\User\UserType;

require_once(__DIR__.'/../../vendor/autoload.php');

$pdo = PDO::getInstance();

$sql = 'create table if not exists '.UserType::TABLE_NAME.'
(
	'.UserType::ID.' int auto_increment, '
    .UserType::NAME.' varchar(200) not null, '
    .UserType::AT_CREATED.' datetime default NOW() not null, '
    .UserType::AT_DELETED.' datetime default null null,
	constraint '.UserType::TABLE_NAME.'_pk
		primary key ('.UserType::ID.')
);';
$pdo->query($sql);

$sql = 'INSERT INTO '.UserType::TABLE_NAME.' ('.UserType::ID.', '.UserType::NAME.') VALUES ('.Admin::USER_TYPE_ID.', \'Admin\')';
$pdo->query($sql);
$sql = 'INSERT INTO '.UserType::TABLE_NAME.' ('.UserType::ID.', '.UserType::NAME.') VALUES ('.Manager::USER_TYPE_ID.', \'Manager\')';
$pdo->query($sql);

$sql = 'create table if not exists '.User::TABLE_NAME.'
(
	'.User::ID.' int auto_increment, '
    .User::TYPE.' int not null, '
    .User::NAME.' varchar(200) not null, '
    .User::SURNAME.' varchar(200) not null, '
    .User::PATRONYMIC.' varchar(200) not null, '
    .User::EMAIL.' varchar(200) not null, '
    .User::PASSWORD_HASH.' varchar(200) not null, '
    .User::AT_CREATED.' datetime default NOW() not null, '
    .User::AT_DELETED.' datetime default null null,
	constraint '.User::TABLE_NAME.'_pk
		primary key ('.User::ID.')
);';
$pdo->query($sql);

$sql = 'create table if not exists '.Product::TABLE_NAME.'
(
	'.Product::ID.' int auto_increment, '
	.Product::NAME.' varchar(200) not null, '
	.Product::PRICE.' decimal(10,2) not null, '
	.Product::AT_CREATED.' datetime default NOW() not null, '
	.Product::AT_DELETED.' datetime default null null,
	constraint '.Product::TABLE_NAME.'_pk
		primary key ('.Product::ID.')
);';
$pdo->query($sql);

$sql = 'create table if not exists `'.Set::TABLE_NAME.'` 
(
	'.Set::ID.' int auto_increment, '
    .Set::NAME.' varchar(200) not null, '
    .Set::AT_CREATED.' datetime default NOW() not null, '
    .Set::AT_DELETED.' datetime default null null,
	constraint '.Set::TABLE_NAME.'_pk
		primary key ('.Set::ID.')
);';
$pdo->query($sql);

$sql = 'create table if not exists `ProductSetRef`
(
	setId int not null ,
	childId int not null,
	childType int not null,
	constraint ProductSetRef_pk primary key (setId, childId, childType)
);';
$pdo->query($sql);

$sql = 'create table if not exists `'.ComponentChildType::TABLE_NAME.'`
(
	id int auto_increment,
	name varchar(200) not null,'
	.ComponentChildType::AT_CREATED.' datetime default NOW() not null, '
    .ComponentChildType::AT_DELETED.' datetime default null null,
	constraint SetChildType_pk primary key (id)
);';
$pdo->query($sql);

$sql = 'INSERT INTO '.ComponentChildType::TABLE_NAME.' ('.ComponentChildType::NAME.') VALUES (\''.Product::TABLE_NAME.'\')';
$pdo->query($sql);
$sql = 'INSERT INTO '.ComponentChildType::TABLE_NAME.' ('.ComponentChildType::NAME.') VALUES (\''.Set::TABLE_NAME.'\')';
$pdo->query($sql);
