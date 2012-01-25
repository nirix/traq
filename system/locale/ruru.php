<?php
/*
 * Traq
 * Copyright (C) 2009-2012 Jack Polgar
 * 
 * This file is part of Traq.
 * 
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * ruRU localization information.
 *
 * @package Traq
 * @subpackage Locale
 *
 * @return array
 */
function ruru_info()
{
	return array(
		'name' => 'Russian',
		'author' => 'Dmitry V. Alexeev',
		'version' => '3.0'
	);
}

/**
 * ruRU localization strings.
 *
 * @package Traq
 * @subpackage Locale
 *
 * @return array
 */
function ruru_locale()
{
	return array(
		'copyright' => "Работает на Traq " . TRAQ_VER . " &copy; 2009-" . date("Y"),
		'projects' => "Проекты",
		'project_info' => "Данные проекта",
		'tickets' => "Заявки",
		'roadmap' => "История",
		'timeline' => "Прогресс",
		'settings' => "Настройки",
		'managers' => "Менеджеры",
		'information' => "Информация",
		'milestones' => "Этапы",
		'components' => "Компоненты",
		'project_settings' => "Настройки проекта",
		'name' => "Имя",
		'slug' => "Псевдоним",
		'codename' => "Кодовое имя",
		'new_milestone' => "Новый этап",
		'edit_milestone' => "Редактировать этап",
		'new_component' => "Новый компонент",
		'edit_component' => "Редактировать компонент",
		
		// AdminCP
		'users' => "Пользователи",
		'groups' => "Группы",
		'new_project' => "Новый проект",
		'plugins' => "Плагины",
		'enabled_plugins' => "Активные плагины",
		'disabled_plugins' => "Неактивные плагины",
		'author' => "Автор",
		'version' => "Версия",
		'enable' => "Включить",
		'disable' => "Выключить",
		'new_user' => "Новый пользователь",
		'edit_user' => "Редактировать пользователя",
		'group' => "Группа",
		'new_group' => "Новая группа",
		'edit_group' => "Редактировать группу",
		
		// Tickets
		'summary' => "Аннотация",
		'status' => "Статус",
		'owner' => "Владелец",
		'type' => "Тип",
		'component' => "Компонент",
		'milestone' => "Этап",
		'description' => "Описание",
		
		// User stuff
		'login' => "Вход",
		'logout' => "Выход",
		'usercp' => "UserCP",
		'admincp' => "AdminCP",
		'register' => "Регистрация",
		'username' => "Имя пользователя",
		'password' => "Пароль",
		'email' => "Email",
		
		// Other
		'actions' => "Действия",
		'create' => "Создать",
		'save' => "Сохранить",
		'edit' => "Изменить",
		'delete' => "Удалить",
		
		// Help
		'help:slug' => "Буквенно-цифровая строка в нижнем регистре за исключением тире и подчеркивания, которая будет использована в URL.",
		
		// Confirmations
		'confirm:delete_x' => "Вы уверены что хотите удалить {1}?",
		
		// Errors
		'error:404_title' => "Упс",
		'error:404_message' => "Запрошенная страница '{1}' не найдена.",
		'error:invalid_username_or_password' => "Неверное имя пользователя или пароль.",
		'error:name_blank' => "Имя не может быть пустым",
		'error:slug_blank' => "Псевдоним",
		'error:slug_in_use' => "Псевдоним не может быть пуст",
		
		// User errors
		'error:user:username_blank' => "Имя пользователя не может быть пустым",
		'error:user:username_in_use' => "Таке имя пользователя уже занято",
		'error:user:password_blank' => "Пароль не может быть пустым",
		'error:user:email_invalid' => "Неверный почтовый адрес",
	);
}