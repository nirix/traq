<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
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

namespace traq\locale;

/**
 * ruRU localization class.
 *
 * @author Menelion Elensúle
 * @copyright (C) Menelion Elensúle
 * @package Traq
 * @subpackage Locale
 */
class ruRU extends \traq\libraries\Locale
{
    protected static $info = array(
        'name'    => "Русский (Russian)",
        'author'  => "Menelion Elensúle",
        'version' => "3.0",

        // Locale information
        'language'       => "Russian",
        'language_short' => "ru",
        'locale'         => "RU"
    );

    public static function locale()
    {
        return array(
            'traq'             => "Traq",
            'copyright'        => "Работает под управлением Traq " . TRAQ_VER . " &copy; 2009-" . date("Y") . " Traq.io",
            'projects'         => "Проекты",
            'project_info'     => "Информация о проекте",
            'tickets'          => "Задачи",
            'roadmap'          => "план работ",
            'settings'         => "Настройки",
            'information'      => "Информация",
            'milestones'       => "Этапы работ",
            'components'       => "Компоненты",
            'project_settings' => "Настройки проекта",
            'name'             => "Название",
            'slug'             => "Адрес",
            'codename'         => "Имя кода",
            'open'             => "Открыто",
            'closed'           => "Закрыто",
            'cancel'           => "Отмена",
            'new'              => "Создать",
            'wiki'             => "Вики",
            'x_open'           => "{1} открыто",
            'x_closed'         => "{1} закрыто",
            'yes'              => "Да",
            'no'               => "Нет",
            'created'          => "Создано",
            'updated'          => "Обновлено",
            'project'          => "Проект",
            'never'            => "Никогда",
            'votes'            => "Голоса",
            'update_ticket'    => "Обновить задачу",
            'comment'          => "Комментарий",
            'update'           => "Обновить",
            'x_by_x'           => "{1} автора {2}",
            'submit'           => "Отправить",
            'see_all'          => "Посмотреть все",
            'close'            => "Закрыть",
            'all'              => "Все",
            'active'           => "Активно",
            'completed'        => "Завершено",
            'cancelled'        => "Отменено",
            'due_x'            => "Ожидаемые {1}",
            'members'          => "Члены",
            'none'             => "Ничего",
            'member_since'     => "Является членом с",
            'unknown'          => "Неизвестно",
            'changelog'        => "Журнал изменений",
            'or'               => "или",
            'language'         => "Язык",
            'filters'          => "Фильтры",
            'is'               => "является",
            'is_not'           => "не является",
            'contains'         => "содержит",
            'does_not_contain' => "не содержит",
            'subscribe'        => "Подписаться",
            'unsubscribe'      => "Отписаться",
            'for'              => "для",
            'search'           => "Поиск",
            'renew'            => "Возобновить",
            'preview'          => "Предварительный просмотр",
            'continue'         => "Продолжить",
            'filter_events'    => "Фильтр событий",
            'done'             => "Готово",

            // AdminCP
            'admin.theme_select_option' => "{1} (v{2} автора {3})",
            'dashboard'            => "Панель управления",
            'traq_settings'        => "Настройки Traq",
            'users'                => "Пользователи",
            'groups'               => "Группы",
            'new_project'          => "Новый проект",
            'plugins'              => "Надстройки",
            'enabled_plugins'      => "Включённые надстройки",
            'disabled_plugins'     => "Отключённые надстройки",
            'author'               => "Автор",
            'version'              => "Версия",
            'enable'               => "Включить",
            'disable'              => "Отключить",
            'new_user'             => "Новый пользователь",
            'edit_user'            => "Редактировать пользователя",
            'group'                => "Группа",
            'new_group'            => "Новая группа",
            'edit_group'           => "Редактировать группу",
            'types'                => "Типы",
            'statuses'             => "Статусы",
            'new_type'             => "Новый тип",
            'edit_type'            => "Редактировать тип",
            'bullet'               => "Маркер",
            'show_on_changelog'    => "Показывать в журнале изменений",
            'template'             => "Шаблон",
            'new_status'           => "Новый статус",
            'edit_status'          => "Редактировать статус",
            'traq_title'           => "Заголовок Traq",
            'default_language'     => "Язык по умолчанию",
            'theme'                => "Тема оформления",
            'allow_registration'   => "Разрешить регистрацию",
            'date_and_time'        => "Дата и время",
            'date_time_format'     => "Формат даты и времени",
            'date_format'          => "Формат даты",
            'timeline_day_format'  => "Формат отображения дня в ленте",
            'timeline_time_format' => "Формат времени в ленте",
            'install'              => "Установить",
            'uninstall'            => "Удалить",
            'roles'                => "Роли",
            'new_role'             => "Новая роль",
            'edit_role'            => "Редактировать роль",
            'due'                  => "Ожидаемое",
            'role'                 => "Роль",
            'add'                  => "Добавить",
            'enable_wiki'          => "Включить вики",
            'priorities'           => "Приоритеты",
            'new_priority'         => "Новый приоритет",
            'edit_priority'        => "Редактировать приоритет",
            'severities'           => "Уровни серьёзности",
            'new_severity'         => "Новый уровень серьёзности",
            'edit_severity'        => "Редактировать уровень серьёзности",
            'notifications'        => "Уведомления",
            'total'                => "Всего",
            'newest'               => "Самые новые",
            'email_validation'     => "Проверка адреса электронной почты",
            'notifications_from_email' => "Отправлять с адреса",

            // Project settings
            'new_milestone'    => "Новый этап",
            'delete_milestone' => "Удалить этап",
            'edit_milestone'   => "Редактировать этап",
            'new_component'    => "Новый компонент",
            'edit_component'   => "Редактировать компонент",
            'display_order'    => "Порядок показа",

            // Tickets
            'id'                    => "Идентификатор",
            'ticket'                => "Задача",
            'new_ticket'            => "Новая задача",
            'summary'               => "Краткое описание",
            'status'                => "Статус",
            'owner'                 => "Владелец",
            'type'                  => "Тип",
            'component'             => "Компонент",
            'milestone'             => "Этап завершения",
            'description'           => "Описание",
            'updates'               => "Обновления",
            'severity'              => "Серьёзность",
            'assigned_to'           => "Назначено",
            'reported'              => "Сообщено",
            'priority'              => "Приоритет",
            'edit_ticket'           => "Редактировать задачу",
            'no_votes'              => "Нет голосов",
            'attachment'            => "Вложение",
            'attachments'           => "Вложения",
            'edit_ticket_history'   => "Редактировать историю задачи",
            'x_uploaded_by_x_x_ago' => "{1} загружено пользователем {2}, {3}",
            'move'                  => "Переместить",
            'move_ticket'           => "Переместить задачу",
            'mass_actions'          => "Групповые действия",
            'people_who_have_voted_on_this_ticket' => "Пользователи, проголосовавшие за эту задачу ({1})",

            // Ticket columns
            'columns'    => "Столбцы",
            'ticket_id'  => "Номер задачи",
            'created_at' => "Дата создания",
            'updated_at' => "Дата обновления",

            // Ticket tasks
            'tasks'        => "Задания",
            'manage'       => "Настроить",
            'manage_tasks' => "Управление заданиями",
            'add_task'     => "Добавить задание",

            // Custom fields
            'text'          => "Текст",
            'select'        => "Выберите",
            'integer'       => "Целое число",
            'custom_fields' => "Пользовательские поля",
            'new_field'     => "Новое поле",
            'edit_field'    => "Редактировать поле",
            'required'      => "Обязательно",
            'min_length'    => "Минимальная длина",
            'max_length'    => "Максимальная длина",
            'regex'         => "Регулярное выражение",
            'default_value' => "Значение по умолчанию",
            'values'        => "Значения",
            'multiple'      => "Со множественным выбором",

            // Users
            'login'                => "Вход",
            'logout'               => "Выход",
            'usercp'               => "Ваша панель управления",
            'admincp'              => "Панель администратора",
            'register'             => "Регистрация",
            'username'             => "Имя пользователя",
            'password'             => "Пароль",
            'old_password'         => "Старый пароль",
            'new_password'         => "Новый пароль",
            'confirm_password'     => "Подтверждение пароля",
            'email'                => "Адрес электронной почты",
            'xs_profile'           => "Профиль {1}",
            'assigned_tickets'     => "Назначенные задачи",
            'tickets_created'      => "Создано задач",
            'ticket_updates'       => "Обновление задач",
            'information'          => "Информация",
            'options'              => "Параметры",
            'watch_my_new_tickets' => "Отслеживать мои новые задачи",
            'subscriptions'        => "Подписки",
            'forgot_password'      => "Я забыл(а) пароль",
            'reset'                => "Сброс",
            'api_key'              => "Ключ API",
            'account_validated'    => "Ваша учётная запись проверена",
            'please_validate_your_account' => "Ваша учётная запись создана. Пожалуйста, проверьте электронную почту на предмет письма со ссылкой для активации.",

            // Password reset
            'password_reset.success' => "Your password has been reset, your new password is '{1}', it is recommended that you change it immediately",
            'password_reset.email_sent' => "We have sent an email to the the address for the account with instructions on how to reset your password.",

            // Wiki
            'home'         => "Home",
            'pages'        => "Pages",
            'new_page'     => "New Page",
            'edit_page'    => "Edit Page",
            'delete_page'  => "Delete Page",
            'page_title'   => "Page Title",
            'page_content' => "Page Content",

            // Pagination
            'previous' => "Previous",
            'next'     => "Next",

            // Other
            'actions' => "Actions",
            'create'  => "Create",
            'save'    => "Save",
            'edit'    => "Edit",
            'delete'  => "Delete",

            // Permissions
            'group_permissions' => "Group Permissions",
            'role_permissions'  => "Role Permissions",
            'ticket_properties' => "Ticket Properties",
            'action'            => "Action",
            'defaults'          => "Defaults",
            'allow'             => "Allow",
            'deny'              => "Deny",
            'permissions' => array(
                // Projects
                'view'                   => "View",
                'project_settings'       => "Project Settings",
                'delete_timeline_events' => "Delete timeline events",

                // Tickets
                'tickets' => array(
                    'create_tickets'            => "Create",
                    'update_tickets'            => "Update",
                    'delete_tickets'            => "Delete",
                    'move_tickets'              => "Move tickets",
                    'vote_on_tickets'           => "Vote",
                    'comment_on_tickets'        => "Comment",
                    'edit_ticket_description'   => "Edit description",
                    'add_attachments'           => "Add attachments",
                    'view_attachments'          => "View attachments",
                    'delete_attachments'        => "Delete attachments",
                    'perform_mass_actions'      => "Perform mass actions",

                    // Ticket History
                    'edit_ticket_history'   => "Edit history",
                    'delete_ticket_history' => "Delete history",
                ),

                // Ticket properties
                'ticket_properties' => array(
                    'ticket_properties_change_type'        => "Change Type",
                    'ticket_properties_change_summary'     => "Change Summary",

                    'ticket_properties_set_assigned_to'    => "Set Assigned to",
                    'ticket_properties_change_assigned_to' => "Change Assigned to",

                    'ticket_properties_set_milestone'      => "Set Milestone",
                    'ticket_properties_change_milestone'   => "Change Milestone",

                    'ticket_properties_set_version'        => "Set Version",
                    'ticket_properties_change_version'     => "Change Version",

                    'ticket_properties_set_component'      => "Set Component",
                    'ticket_properties_change_component'   => "Change Component",

                    'ticket_properties_set_severity'       => "Set Severity",
                    'ticket_properties_change_severity'    => "Change Severity",

                    'ticket_properties_set_priority'       => "Set Priority",
                    'ticket_properties_change_priority'    => "Change Priority",

                    'ticket_properties_set_status'         => "Set Status",
                    'ticket_properties_change_status'      => "Change Status",

                    'ticket_properties_set_tasks'         => "Set Tasks",
                    'ticket_properties_change_tasks'      => "Change Tasks",
                    'ticket_properties_complete_tasks'    => "Complete Tasks",
                ),

                // Wiki
                'wiki' => array(
                    'create_wiki_page' => "Create page",
                    'edit_wiki_page'   => "Edit page",
                    'delete_wiki_page' => "Delete page"
                )
            ),

            // Time
            'time'          => "Time",
            'time.ago'      => "{1} ago",
            'time.from_now' => "{1} from now",
            'time.x_and_x'  => "{1} and {2}",
            'time.x_second' => "{1} {plural:{1}, {second|seconds}}",
            'time.x_minute' => "{1} {plural:{1}, {minute|minutes}}",
            'time.x_hour'   => "{1} {plural:{1}, {hour|hours}}",
            'time.x_day'    => "{1} {plural:{1}, {day|days}}",
            'time.x_week'   => "{1} {plural:{1}, {week|weeks}}",
            'time.x_month'  => "{1} {plural:{1}, {month|months}}",
            'time.x_year'   => "{1} {plural:{1}, {year|years}}",

            // Timeline
            'timeline'                     => "Timeline",
            'timeline.ticket_created'      => "{ticket_type_name} #{ticket_id} ({ticket_summary}) created",
            'timeline.ticket_closed'       => "{ticket_type_name} #{ticket_id} ({ticket_summary}) closed as {ticket_status_name}",
            'timeline.ticket_reopened'     => "{ticket_type_name} #{ticket_id} ({ticket_summary}) reopened as {ticket_status_name}",
            'timeline.ticket_updated'      => "{ticket_type_name} #{ticket_id} ({ticket_summary}) updated",
            'timeline.ticket_comment'      => "Commented on ticket {link}",
            'timeline.milestone_completed' => "Milestone {milestone_name} completed",
            'timeline.milestone_cancelled' => "Milestone {milestone_name} cancelled",
            'timeline.ticket_moved_from'   => "Moved ticket ({ticket}) from {project}",
            'timeline.ticket_moved_to'     => "Moved ticket ({ticket}) to {project}",
            'timeline.wiki_page_created'   => "Created {title} wiki page",
            'timeline.wiki_page_edited'    => "Edited {title} wiki page",
            'timeline.by_x'                => "by {1}",

            // Timeline filters
            'timeline.filters.new_tickets'           => "New tickets",
            'timeline.filters.tickets_opened_closed' => "Tickets open/closed",
            'timeline.filters.ticket_updates'        => "Ticket updates",
            'timeline.filters.ticket_comments'       => "Ticket comments",
            'timeline.filters.ticket_moves'          => "Ticket migrations",
            'timeline.filters.milestones'            => "Milestones",
            'timeline.filters.wiki_pages'            => "Wiki pages",

            // Help
            'help.slug'                     => "A lower case alpha-numerical string with the exception of dashes, underscores and periods to be used in the URL.",
            'help.ticket_type_bullet'       => "The bullet style used on the changelog list.",
            'help.custom_fields.regex'      => "Regular expression to match user value against.",
            'help.custom_fields.min_length' => "Minimum value length, blank for none.",
            'help.custom_fields.max_length' => "Maximum value length, blank for none.",
            'help.custom_fields.values'     => "Options for select, one per line.",
            'help.custom_fields.multiple'   => "Allows multiple options to be selected.",

            // Ticket property hints
            'help.milestone' => "The version in which the ticket should be completed for.",
            'help.version'   => "The version in which the defect was introduced or the version being used.",
            'help.component' => "The part of the project the ticket is related to.",
            'help.severity' => "How severe the ticket is.",

            // Confirmations
            'confirm.delete'   => "Are you sure you want to delete that?",
            'confirm.delete_x' => "Are you sure you want to delete '{1}' ?",
            'confirm.remove_x' => "Are you sure you want to remove '{1}' ?",

            // Feeds
            'x_timeline_feed'  => "{1} Timeline Feed",
            'x_ticket_feed'    => "{1} Ticket Feed",
            'x_x_history_feed' => "{1} / '{2}' History Feed",
            'x_changelog_feed' => "{1} Changelog Feed",
            'update_x'         => "Update #{1}",

            // Editor
            'editor' => array(
                // Intentionally left empty to use the default
                // strings from the editor.
                //
                // Enter your localisation strings here.
                // example:
                // 'h2' => "My custom string",
                // 'h3' => "Another custom string",
                // and so on...
            ),

            // Ticket history
            'ticket_history' => array(
                'Ticket History',

                // Most fields
                'x_from_x_to_x'    => "Changed {1} from {2} to {3}",
                'x_from_null_to_x' => "Set {1} to {3}",
                'x_from_x_to_null' => "Cleared {1}, was {2}",

                // Assignee field
                'assignee_from_x_to_x'    => "Reassigned ticket from {2} to {3}",
                'assignee_from_null_to_x' => "Assigned ticket to {3}",
                'assignee_from_x_to_null' => "Unassigned ticket from {2}",

                // Actions
                'close'          => "Closed ticket as {2}",
                'reopen'         => "Reopened ticket as {2}",
                'add_attachment' => "Added attachment {2}",
            ),

            // Warnings
            'warnings' => array(
                'delete_milestone' => "Select which milestone to move tickets to."
            ),

            // Errors
            'errors' => array(
                'invalid_username_or_password' => "Invalid Username or Password.",
                'invalid_username'             => "Invalid Username",
                'name_blank'                   => "Name cannot be blank",
                'slug_blank'                   => "Slug cannot be blank",
                'slug_in_use'                  => "That slug is already in use",
                'page_title_blank'             => "Page Title cannot be blank",
                'already_voted'                => "You have already voted.",
                'must_be_logged_in'            => "You must be logged in to do that.",
                'type_blank'                   => "You must choose a type",
                'regex_blank'                  => "You need to enter a regex value",
                'values_blank'                 => "You need to enter some values",
                'email_validation_required'    => "You need to validate your email, check your inbox",

                // Custom fields
                'custom_fields' => array(
                    'x_required'     => "{1} is required",
                    'x_is_not_valid' => "{1} is not valid"
                ),

                // 404 error page
                '404' => array(
                    'title'   => "He's dead, Jim!",
                    'message' => "The requested page '{1}' couldn't be found."
                ),

                // No Permission page
                'no_permission' => array(
                    'title'   => "Move along, move along",
                    'message' => "You don't have permission to access this page."
                ),

                // Tickets
                'tickets' => array(
                    'summary_blank'     => "Summary cannot be blank",
                    'description_blank' => "Description cannot be blank"
                ),

                // Ticket types
                'ticket_type.bullet_blank' => "Bullet cannot be blank",

                // User errors
                'users' => array(
                    'username_blank'           => "Username cannot be blank",
                    'name_blank'               => "Name cannot be blank",
                    'username_in_use'          => "That username is already registered",
                    'password_blank'           => "Password cannot be blank",
                    'new_password_blank'       => "Your new password cannot be blank",
                    'confirm_password_blank'   => "You must confirm your password",
                    'invalid_confirm_password' => "Your confirmation password doesn't match your new password",
                    'invalid_password'         => "Invalid password",
                    'email_invalid'            => "Invalid email address",
                    'doesnt_exist'             => "User doesn't exist",
                    'already_a_project_member' => "User is already a project member",
                    'password_same'            => "Your new password cannot be the same as your current password",
                    'username_too_long'        => "Username cannot be longer than 25 characters"
                ),

                // Traq Settings errors
                'settings' => array(
                    'title_blank'              => "Traq Title cannot be blank",
                    'locale_blank'             => "You must select a default language",
                    'theme_blank'              => "You must select a theme",
                    'allow_registration_blank' => "Allow Registration must be set"
                )
            ),

            // ----------------------------------------------------------------------------------------------------
            // Security Questions
            'security_question'  => "Security Question",
            'security_questions' => "Security Questions",
            'question'           => 'Question',
            'answer'             => "Answer",
            'answers'            => "Answers",
            'add_question'       => "Add Question",

            'errors.security_questions.fill_in_fields_marked_red' => "Please fill in the fields marked in red",
            'errors.security_questions.answer_is_wrong'           => "The security answer you provided is incorrect",
            'help.security_questions.answers'                     => "Accepted answers sperated by a vertical bar: <code>|</code>",

            // ----------------------------------------------------------------------------------------------------
            // Custom tabs
            'custom_tabs' => "Custom Tabs",
            'new_tab'     => "New Tab",
            'edit_tab'    => "Edit Tab",
            'label'       => "Label",
            'url'         => "URL",

            'errors.label_blank' => "Label cannot be blank",
            'errors.url_empty'   => "URL cannot be empty",

            // ----------------------------------------------------------------------------------------------------
            // Notifications

            // Ticket assigned
            'notifications.ticket_assigned.subject' => "Ticket #{2} on project {4} has been assigned to you",
            'notifications.ticket_assigned.message' => "{2},<br /><br />".
                                                       "Ticket #{3} (<a href=\"{8}\">{4}</a>) on project {6} has been assigned to you.<br /><br />".
                                                       "----------------------------------------------------------------<br />".
                                                       "{5}".
                                                       "----------------------------------------------------------------",

            // Ticket created
            'notifications.ticket_created.subject' => "New ticket #{2} ({3}) on project {4}",
            'notifications.ticket_created.message' => "{2},<br /><br />".
                                                      "Ticket #{3} (<a href=\"{8}\">{4}</a>) has been created on project {6}.<br /><br />".
                                                      "----------------------------------------------------------------<br />".
                                                      "{5}".
                                                      "----------------------------------------------------------------",

            // Ticket updated
            'notifications.ticket_updated.subject' => "Ticket #{2} ({3}) updated on project {4}",
            'notifications.ticket_updated.message' => "{2},<br /><br />".
                                                      "Ticket #{3} (<a href=\"{8}\">{4}</a>) has been updated on project {6}.",

            // Ticket closed
            'notifications.ticket_closed.subject' => "Ticket #{2} ({3}) closed on project {4}",
            'notifications.ticket_closed.message' => "{2},<br /><br />".
                                                     "Ticket #{3} (<a href=\"{8}\">{4}</a>) has been closed on project {6}.",

            // Ticket reopened
            'notifications.ticket_reopened.subject' => "Ticket #{2} ({3}) reopened on project {4}",
            'notifications.ticket_reopened.message' => "{2},<br /><br />".
                                                       "Ticket #{3} (<a href=\"{8}\">{4}</a>) has been reopened on project {6}.",

            // Password reset
            'notifications.password_reset.subject' => "Password reset request",
            'notifications.password_reset.message' => "{2},<br /><br />".
                                                      "You are receiving this email because you or someone has requested a password reset<br />".
                                                      "for your account '{3}' at {1}. If you did not request a password reset, ignore this email.<br /><br />".
                                                      "If you did request a password reset, you can <a href=\"{4}\">continue by clicking here</a>.<br /><br />".
                                                      "This reset request was done by someone with the IP of: {5}",

            // Email validation
            'notifications.email_validation.subject' => "Account validation",
            'notifications.email_validation.message' => "{name},<br /><br />".
                                                        "To validate your account click the link below:<br />".
                                                        "{link}",

            // ----------------------------------------------------------------------------------------------------

            'traq_update_available' => "<strong>Update available: <a href=\"{4}\">{1}</a> [<a href=\"{3}\">Download</a>]",

            // Testing purposes only...
            'test' => array(
                'plurals' => "There {plural:{1}, {is {1} bottle|are {1} bottles}} of scotch on the shelf."
            )
        );
    }
}
