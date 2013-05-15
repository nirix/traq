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

    /**
     * Determines which replacement to use for plurals.
     * NOTE: you have always to specify three forms for Russian.
     *
     * @param integer $numeral
     *
     * @return integer
     */
    public function calculate_numeral($numeral)
    {
        return $numeral % 10 == 1 && $numeral % 100 != 11 ? 0 : ($numeral % 10 >= 2 && $numeral % 10 <= 4 && ($numeral % 100 < 10 || $numeral % 100 >= 20) ? 1 : 2);
    }

    public static function locale()
    {
        return array(
            'traq'             => "Traq",
            'copyright'        => "Работает под управлением Traq " . TRAQ_VER . " &copy; 2009-" . date("Y") . " Traq.io",
            'projects'         => "Проекты",
            'project_info'     => "Информация о проекте",
            'tickets'          => "Инциденты",
            'roadmap'          => "план работ",
            'settings'         => "Настройки",
            'information'      => "Информация",
            'milestones'       => "Этапы",
            'components'       => "Компоненты",
            'project_settings' => "Настройки проекта",
            'name'             => "Имя",
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
            'update_ticket'    => "Обновить инцидент",
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
            'members'          => "Участники",
            'none'             => "Нет",
            'member_since'     => "Участник с",
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
            'default_ticket_type' => "Тип инцидента по умолчанию",

            // Tickets
            'id'                    => "Номер",
            'ticket'                => "Инцидент",
            'new_ticket'            => "Новый инцидент",
            'summary'               => "Краткое описание",
            'status'                => "Статус",
            'owner'                 => "Владелец",
            'type'                  => "Тип",
            'component'             => "Компонент",
            'milestone'             => "Этап завершения",
            'description'           => "Описание",
            'updates'               => "Обновления",
            'severity'              => "Серьёзность",
            'assigned_to'           => "Назначен",
            'reported'              => "Добавлен",
            'priority'              => "Приоритет",
            'edit_ticket'           => "Редактировать инцидент",
            'no_votes'              => "Нет голосов",
            'attachment'            => "Вложение",
            'attachments'           => "Вложения",
            'edit_ticket_history'   => "Редактировать историю инцидента",
            'x_uploaded_by_x_x_ago' => "{1} загружено пользователем {2}, {3}",
            'move'                  => "Переместить",
            'move_ticket'           => "Переместить инцидент",
            'mass_actions'          => "Групповые действия",
            'people_who_have_voted_on_this_ticket' => "Пользователи, проголосовавшие за этот инцидент ({1})",

            // Ticket columns
            'columns'    => "Столбцы",
            'ticket_id'  => "Номер инцидента",
            'created_at' => "Дата создания",
            'updated_at' => "Дата обновления",

            // Ticket tasks
            'tasks'        => "Задания",
            'manage'       => "Настроить",
            'manage_tasks' => "Управление заданиями",
            'add_task'     => "Добавить задание",

            // Custom fields
            'text'          => "Текст",
            'select'        => "Комбинированный список",
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
            'username'             => "Логин",
            'password'             => "Пароль",
            'old_password'         => "Старый пароль",
            'new_password'         => "Новый пароль",
            'confirm_password'     => "Подтверждение пароля",
            'email'                => "Адрес электронной почты",
            'xs_profile'           => "Профиль {1}",
            'assigned_tickets'     => "Назначенные инциденты",
            'tickets_created'      => "Созданные инциденты",
            'ticket_updates'       => "Обновление инцидентов",
            'information'          => "Информация",
            'options'              => "Параметры",
            'watch_my_new_tickets' => "Отслеживать мои новые инциденты",
            'subscriptions'        => "Подписки",
            'forgot_password'      => "Я забыл(а) пароль",
            'reset'                => "Сброс",
            'api_key'              => "Ключ API",
            'account_validated'    => "Ваша учётная запись подтверждена",
            'please_validate_your_account' => "Ваша учётная запись создана. Пожалуйста, проверьте электронную почту на предмет письма со ссылкой для активации.",

            // Password reset
            'password_reset.success' => "Ваш пароль сброшен, ваш новый пароль: {1}. Мы советуем вам сразу же сменить его",
            'password_reset.email_sent' => "Мы отправили вам письмо с инструкциями по смене пароля.",

            // Wiki
            'home'         => "Главная страница",
            'pages'        => "Страницы",
            'new_page'     => "Создать страницу",
            'edit_page'    => "Редактировать страницу",
            'delete_page'  => "Удалить страницу",
            'page_title'   => "Заголовок страницы",
            'page_content' => "Содержимое страницы",

            // Pagination
            'previous' => "Предыдущая",
            'next'     => "Следующая",

            // Other
            'actions' => "Действия",
            'create'  => "Создать",
            'save'    => "Сохранить",
            'edit'    => "Редактировать",
            'delete'  => "Удалить",

            // Permissions
            'group_permissions' => "Права группы",
            'role_permissions'  => "Права роли",
            'ticket_properties' => "Свойства инцидента",
            'action'            => "Действие",
            'defaults'          => "По умолчанию",
            'allow'             => "Разрешить",
            'deny'              => "Запретить",
            'permissions' => array(
                // Projects
                'view'                   => "Просматривать",
                'project_settings'       => "Настройки проекта",
                'delete_timeline_events' => "Удалять события ленты",

                // Tickets
                'tickets' => array(
                    'create_tickets'            => "Создавать",
                    'update_tickets'            => "Обновлять",
                    'delete_tickets'            => "Удалять",
                    'move_tickets'              => "Перемещать инциденты",
                    'vote_on_tickets'           => "Голосовать",
                    'comment_on_tickets'        => "Комментировать",
                    'edit_ticket_description'   => "Редактировать описание",
                    'add_attachments'           => "Добавлять вложения",
                    'view_attachments'          => "Просматривать вложения",
                    'delete_attachments'        => "Удалять вложения",
                    'perform_mass_actions'      => "Выполнять групповые действия",

                    // Ticket History
                    'edit_ticket_history'   => "Редактировать историю",
                    'delete_ticket_history' => "Удалять историю",
                ),

                // Ticket properties
                'ticket_properties' => array(
                    'ticket_properties_change_type'        => "Изменять тип",
                    'ticket_properties_change_summary'     => "Изменять краткое описание",

                    'ticket_properties_set_assigned_to'    => "Назначать разработчикам",
                    'ticket_properties_change_assigned_to' => "Изменять назначенных разработчиков",

                    'ticket_properties_set_milestone'      => "Устанавливать этап завершения",
                    'ticket_properties_change_milestone'   => "Изменять этап завершения",

                    'ticket_properties_set_version'        => "Устанавливать версию",
                    'ticket_properties_change_version'     => "Изменять версию",

                    'ticket_properties_set_component'      => "Устанавливать компонент",
                    'ticket_properties_change_component'   => "Изменять компонент",

                    'ticket_properties_set_severity'       => "Устанавливать серьёзность",
                    'ticket_properties_change_severity'    => "Изменять серьёзность",

                    'ticket_properties_set_priority'       => "Устанавливать приоритет",
                    'ticket_properties_change_priority'    => "Изменять приоритет",

                    'ticket_properties_set_status'         => "Устанавливать статус",
                    'ticket_properties_change_status'      => "Изменять статус",

                    'ticket_properties_set_tasks'         => "Устанавливать задания",
                    'ticket_properties_change_tasks'      => "Изменять задания",
                    'ticket_properties_complete_tasks'    => "Завершать задания",
                ),

                // Wiki
                'wiki' => array(
                    'create_wiki_page' => "Создавать страницы",
                    'edit_wiki_page'   => "Редактировать страницы",
                    'delete_wiki_page' => "Удалять страницы"
                )
            ),

            // Time
            'time'          => "Время",
            'time.ago'      => "{1} назад",
            'time.from_now' => "Через {1}",
            'time.x_and_x'  => "{1} {2}",
            'time.x_second' => "{1} {plural:{1}, {секунду|секунды|секунд}}",
            'time.x_minute' => "{1} {plural:{1}, {минуту|минуты|минут}}",
            'time.x_hour'   => "{1} {plural:{1}, {час|часа|часов}}",
            'time.x_day'    => "{1} {plural:{1}, {день|дня|дней}}",
            'time.x_week'   => "{1} {plural:{1}, {неделю|недели|недель}}",
            'time.x_month'  => "{1} {plural:{1}, {месяц|месяца|месяцев}}",
            'time.x_year'   => "{1} {plural:{1}, {год|года|лет}}",

            // Timeline
            'timeline'                     => "Лента",
            'timeline.ticket_created'      => "{ticket_type_name} #{ticket_id} ({ticket_summary}) создано",
            'timeline.ticket_closed'       => "{ticket_type_name} #{ticket_id} ({ticket_summary}) закрыта как {ticket_status_name}",
            'timeline.ticket_reopened'     => "{ticket_type_name} #{ticket_id} ({ticket_summary}) снова открыта как {ticket_status_name}",
            'timeline.ticket_updated'      => "{ticket_type_name} #{ticket_id} ({ticket_summary}) обновлена",
            'timeline.ticket_comment'      => "Оставлен комментарий к инциденту {link}",
            'timeline.milestone_completed' => "Этап {milestone_name} завершён",
            'timeline.milestone_cancelled' => "Этап {milestone_name} отменён",
            'timeline.ticket_moved_from'   => "Инцидент ({ticket}) перемещён из {project}",
            'timeline.ticket_moved_to'     => "Инцидент ({ticket}) перемещён в {project}",
            'timeline.wiki_page_created'   => "Создана вики-страница {title}",
            'timeline.wiki_page_edited'    => "Изменена вики-страница {title}",
            'timeline.by_x'                => "пользователем {1}",

            // Timeline filters
            'timeline.filters.new_tickets'           => "Новые инциденты",
            'timeline.filters.tickets_opened_closed' => "Открытые/закрытые инциденты",
            'timeline.filters.ticket_updates'        => "Обновления инцидентов",
            'timeline.filters.ticket_comments'       => "Комментарии к инцидентам",
            'timeline.filters.ticket_moves'          => "Перемещения инцидентов",
            'timeline.filters.milestones'            => "Этапы",
            'timeline.filters.wiki_pages'            => "Вики-страницы",

            // Help
            'help.slug'                     => "Строка, состоящая из малых латинских букв, цифр, точек, тире и знаков подчёркивания, использующаяся в URL-адресе.",
            'help.ticket_type_bullet'       => "Маркер, используемый в журнале изменений.",
            'help.custom_fields.regex'      => "Регулярное выражение, с которым сравнивается пользовательское значение.",
            'help.custom_fields.min_length' => "Минимальная длина значения. Если не нужно, оставьте пустым.",
            'help.custom_fields.max_length' => "Максимальная длина значения. Если не нужно, оставьте пустым.",
            'help.custom_fields.values'     => "Варианты выбора, по одному на строке.",
            'help.custom_fields.multiple'   => "Разрешает выбрать несколько вариантов.",

            // Ticket property hints
            'help.milestone' => "Версия, в которой инцидент должен быть завершён.",
            'help.version'   => "Версия, в которой появилась проблема или используемая в данный момент.",
            'help.component' => "Часть проекта, к которой относится инцидент.",
            'help.severity' => "Насколько серьёзен инцидент.",

            // Confirmations
            'confirm.delete'   => "Вы действительно хотите удалить это?",
            'confirm.delete_x' => "Вы действительно хотите удалить '{1}' ?",
            'confirm.remove_x' => "Вы действительно хотите удалить '{1}' ?",

            // Feeds
            'x_timeline_feed'  => "Поток ленты {1}",
            'x_ticket_feed'    => "Поток инцидента {1}",
            'x_x_history_feed' => "Поток истории {1} / '{2}'",
            'x_changelog_feed' => "Поток журнала изменений {1}",
            'update_x'         => "Обновление #{1}",

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
                'История инцидента',

                // Most fields
                'x_from_x_to_x'    => "{1} изменено с {2} на {3}",
                'x_from_null_to_x' => "{1} установлено в {3}",
                'x_from_x_to_null' => "{1} очищено, было {2}",

                // Assignee field
                'assignee_from_x_to_x'    => "Назначенный разработчик сменён с {2} на {3}",
                'assignee_from_null_to_x' => "Задача назначена {3}",
                'assignee_from_x_to_null' => "Назначение задачи убрано с {2}",

                // Actions
                'close'          => "Инцидент закрыт как {2}",
                'reopen'         => "Инцидент снова открыт как {2}",
                'add_attachment' => "Добавлено вложение {2}",
            ),

            // Warnings
            'warnings' => array(
                'delete_milestone' => "Выберите этап, на который следует переместить инциденты."
            ),

            // Errors
            'errors' => array(
                'invalid_username_or_password' => "Неверный логин или пароль.",
                'invalid_username'             => "Недопустимый логин",
                'name_blank'                   => "Имя не может быть пустым",
                'slug_blank'                   => "Адрес не может быть пустым",
                'slug_in_use'                  => "Этот адрес уже используется",
                'page_title_blank'             => "Заголовок страницы не может быть пустым",
                'already_voted'                => "Вы уже проголосовали.",
                'must_be_logged_in'            => "Чтобы выполнить это действие, вам необходимо войти.",
                'type_blank'                   => "Вам необходимо выбрать тип",
                'regex_blank'                  => "Вам необходимо ввести регулярное выражение",
                'values_blank'                 => "Вам необходимо ввести несколько значений",
                'email_validation_required'    => "Вам необходимо подтвердить электронную почту, проверьте входящие",

                // Custom fields
                'custom_fields' => array(
                    'x_required'     => "{1} обязательно к заполнению",
                    'x_is_not_valid' => "{1} недопустиом"
                ),

                // 404 error page
                '404' => array(
                    'title'   => "Похоже, вы попали не туда.",
                    'message' => "The Запрашиваемая страница '{1}' не найдена."
                ),

                // No Permission page
                'no_permission' => array(
                    'title'   => "Проходите, не задерживайтесь",
                    'message' => "У вас нет прав доступа к этой странице."
                ),

                // Tickets
                'tickets' => array(
                    'summary_blank'     => "Краткое описание не может быть пустым",
                    'description_blank' => "Описание не может быть пустым"
                ),

                // Ticket types
                'ticket_type.bullet_blank' => "Маркер не может быть пустым",

                // User errors
                'users' => array(
                    'username_blank'           => "Логин не может быть пустым",
                    'name_blank'               => "Имя не может быть пустым",
                    'username_in_use'          => "Этот логин уже занят",
                    'password_blank'           => "Пароль не может быть пустым",
                    'new_password_blank'       => "Новый пароль не может быть пустым",
                    'confirm_password_blank'   => "Вам необходимо подтвердить пароль",
                    'invalid_confirm_password' => "Подтверждение не совпадает с новым паролем",
                    'invalid_password'         => "Неверный пароль",
                    'email_invalid'            => "Недопустимый адрес электронной почты",
                    'doesnt_exist'             => "Пользователь не существует",
                    'already_a_project_member' => "Пользователь уже в команде проекта",
                    'password_same'            => "Новый пароль не может совпадать с текущим",
                    'username_too_long'        => "Логин не может быть длиннее 25 символов"
                ),

                // Traq Settings errors
                'settings' => array(
                    'title_blank'              => "Заголовок Traq не может быть пустым",
                    'locale_blank'             => "Вам необходимо выбрать язык по умолчанию",
                    'theme_blank'              => "Вам необходимо выбрать тему",
                    'allow_registration_blank' => "Вам необходимо установить право регистрации"
                )
            ),

            // ----------------------------------------------------------------------------------------------------
            // Security Questions
            'security_question'  => "Проверочный вопрос",
            'security_questions' => "Проверочные вопросы",
            'question'           => 'Вопрос',
            'answer'             => "Ответ",
            'answers'            => "Ответы",
            'add_question'       => "Добавить вопрос",

            'errors.security_questions.fill_in_fields_marked_red' => "Пожалуйста, заполните поля, отмеченные красным",
            'errors.security_questions.answer_is_wrong'           => "Вы дали неверный ответ на проверочный вопрос",
            'help.security_questions.answers'                     => "Правильные ответы, разделённые вертикальной чертой: <code>|</code>",

            // ----------------------------------------------------------------------------------------------------
            // Custom tabs
            'custom_tabs' => "Пользовательские вкладки",
            'new_tab'     => "Новая вкладка",
            'edit_tab'    => "Редактировать вкладку",
            'label'       => "Метка",
            'url'         => "Адрес",

            'errors.label_blank' => "Метка не может быть пустой",
            'errors.url_empty'   => "Адрес не может быть пустым",

            // ----------------------------------------------------------------------------------------------------
            // Notifications

            // Ticket assigned
            'notifications.ticket_assigned.subject' => "Вам назначен инцидент #{2} в проекте {4}",
            'notifications.ticket_assigned.message' => "{2},<br /><br />".
                                                       "Вам назначен инцидент #{3} (<a href=\"{8}\">{4}</a>) в проекте {6}.<br /><br />".
                                                       "----------------------------------------------------------------<br />".
                                                       "{5}".
                                                       "----------------------------------------------------------------",

            // Ticket created
            'notifications.ticket_created.subject' => "Создан инцидент #{2} ({3}) в проекте {4}",
            'notifications.ticket_created.message' => "{2},<br /><br />".
                                                      "Был создан инцидент #{3} (<a href=\"{8}\">{4}</a>) в проекте {6}.<br /><br />".
                                                      "----------------------------------------------------------------<br />".
                                                      "{5}".
                                                      "----------------------------------------------------------------",

            // Ticket updated
            'notifications.ticket_updated.subject' => "Обновлён инцидент #{2} ({3}) в проекте {4}",
            'notifications.ticket_updated.message' => "{2},<br /><br />".
                                                      "Обновлён инцидент #{3} (<a href=\"{8}\">{4}</a>) в проекте {6}.",

            // Ticket closed
            'notifications.ticket_closed.subject' => "Закрыт инцидент #{2} ({3}) в проекте {4}",
            'notifications.ticket_closed.message' => "{2},<br /><br />".
                                                     "Закрыт инцидент #{3} (<a href=\"{8}\">{4}</a>) в проекте {6}.",

            // Ticket reopened
            'notifications.ticket_reopened.subject' => "Снова открыт инцидент #{2} ({3}) в проекте {4}",
            'notifications.ticket_reopened.message' => "{2},<br /><br />".
                                                       "Снова открыт инцидент #{3} (<a href=\"{8}\">{4}</a>) в проекте {6}.",

            // Password reset
            'notifications.password_reset.subject' => "Запрос на сброс пароля",
            'notifications.password_reset.message' => "{2},<br /><br />".
                                                      "Вы получили это письмо, поскольку вы или кто-то другой запросили сброс пароля<br />".
                                                      "для вашей учётной записи {3} на сайте {1}. Если вы не запрашивали сброс пароля, проигнорируйте это письмо.<br /><br />".
                                                      "Если вы запросили сброс пароля, вы можете <a href=\"{4}\">продолжить, щёлкнув сюда</a>.<br /><br />".
                                                      "Этот запрос на сброс пароля был сделан пользователем с IP-адресом: {5}",

            // Email validation
            'notifications.email_validation.subject' => "Проверка учётной записи",
            'notifications.email_validation.message' => "{name},<br /><br />".
                                                        "Чтобы подтвердить вашу учётную запись, щёлкните по ссылке ниже:<br />".
                                                        "{link}",

            // ----------------------------------------------------------------------------------------------------

            'traq_update_available' => "<strong>Доступно обновление: <a href=\"{4}\">{1}</a> [<a href=\"{3}\">Скачать</a>]",

            // Testing purposes only...
            'test' => array(
                'plurals' => "На полке {plural:{1}, {{1} бутылка|{1} бутылки|{1} бутылок}} виски."
            )
        );
    }
}
