<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
 * Copyright (C) 2012-2015 Traq.io
 * https://github.com/nirix
 * https://traq.io
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

namespace Traq\Translations;

use Traq\Traq;
use Avalon\Language;

/**
 * Brazilian Portuguese translation.
 *
 * @author Jack P., Ramiro V.
 * @copyright (C) Jack P.
 * @package Traq\Translations
 */
$enAU = new Language(function ($t) {
    $t->name   = "Português (Brasil)";
    $t->locale = "ptBR";

    $t->strings = array(
        'copyright' => "Powered by Traq " . Traq::version() . " &copy; 2009-" . date("Y") . " Traq.io",

        // --------------------------------------------------------------------
        // AdminCP
        'admincp'     => "AdminCP",
        'dashboard'   => "Painel",
        'latest_news' => "Últimas Notícias",

        // --------------------------------------------------------------------
        // Changelog
        'changelog' => "Changelog",

        // --------------------------------------------------------------------
        // Components
        'components'     => "Componentes",
        'new_component'  => "Novo Componente",
        'edit_component' => "Editar Componente",

        // --------------------------------------------------------------------
        // Custom Fields
        'custom_fields' => "Campos Personalizados",

        // --------------------------------------------------------------------
        // Errors
        'errors.404.title'                    => "Página não encontrada...",
        'errors.404.message'                  => "Parece que a página '{1}' não existe.",
        'errors.404.modal.title'              => "Oh, é um popup!",
        'errors.no_permission.title'          => "Você não deve passar!",
        'errors.no_permission.message'        => "Você não tem permissão para acessar esta página.",
        'errors.invalid_username_or_password' => "Usuário e/ou senha inválido(s).",
        'errors.account.activation_required'  => "Você deve ativar sua conta primeiro.",
        'errors.correct_the_following'        => "Por favor corrija os seguintes itens",

        // --------------------------------------------------------------------
        // Filters
        'filter_events' => "Filtros Eventos",
        'apply_filters' => "Aplicar Filtros",

        // --------------------------------------------------------------------
        // Groups
        'groups'     => "Grupos",
        'new_group'  => "Novo Grupo",
        'edit_group' => "Editar Grupo",
        'is_admin'   => "É Admin",

        // --------------------------------------------------------------------
        // Issues
        'issues'            => "Problemas",
        'create_issue'      => "Adicionar Problema",
        'open'              => "Abertos",
        'closed'            => "Fechados",
        'total'             => "Total",
        'issue.page-title'  => "#{1} - {2}",
        'issue.page-header' => "#{1} - {2}",

        // Issue properties
        'id'          => "ID",
        'ticket_id'   => "ID",
        'summary'     => "Sumário",
        'status'      => "Status",
        'owner'       => "Dono",
        'type'        => "Tipo",
        'component'   => "Componente",
        'milestone'   => "Milestone",
        'assigned_to' => "Atribuído para",
        'priority'    => "Prioridade",
        'severity'    => "Gravidade",
        'created_at'  => "Criado",
        'updated_at'  => "Atualizado",
        'votes'       => "Votos",
        'created'     => "Criado",
        'updated'     => "Atualizado",

        // --------------------------------------------------------------------
        // Notifications
        'notifications.hello_x' => "Olá {name}",

        // Account Activation
        'notifications.account_activation.subject'  => "{title} Ativação da Conta",
        'notifications.account_activation.body.txt' => "Alguém criou uma conta recentemente em {title} com este " .
                                                        "endereço de email, se não foi você, basta ignorar este " .
                                                        "email, caso contrário para ativar sua conta visite a " .
                                                        "URL abaixo.",

        // --------------------------------------------------------------------
        // Settings
        'settings'                          => "Configurações",
        'traq_settings'                     => "Configurações do Traq",
        'settings.title'                    => "Título do Traq",
        'settings.default_language'         => "Idioma Padrão",
        'settings.theme'                    => "Tema",
        'settings.site'                     => "Configurações do Site",
        'settings.site.name'                => "Nome do Site",
        'settings.site.url'                 => "URL do Site",
        'settings.users.allow_registration' => "Permitir Registro",
        'settings.users.email_validation'   => "Validar Email",
        'settings.date_and_time'            => "Data e Hora",
        'settings.date_time_format'         => "Formato de Data e Hora",
        'settings.date_format'              => "Formato de Data",
        'settings.timeline.day_format'      => "Formato do Dia na Linha do Tempo",
        'settings.timeline.time_format'     => "Formato da Hora na Linha do Tempo",
        'settings.notifications.from_email' => "Email De",
        'settings.issues.history_sorting'   => "Ordenação de Histórico",
        'settings.issues.creation_delay'    => "Atraso na Criação",

        // --------------------------------------------------------------------
        // Ticket listing
        'filters' => "Filtros",
        'columns' => "Colunas",
        'update'  => "Atualizar",

        // --------------------------------------------------------------------
        // Milestones
        'milestones'       => "Milestones",
        'new_milestone'    => "Nova Milestone",
        'edit_milestone'   => "Editar Milestone",
        'delete_milestone' => "Remover Milestone",
        'due_date'         => "Data de Entrega",

        // --------------------------------------------------------------------
        // Misc
        'add'                     => "Adicionar",
        'ascending'               => "Crescente",
        'descending'              => "Decrescente",
        'x_by_x'                  => "{1} por {2}",
        'information'             => "Informação",
        'oldest_first'            => "Antigos Primeiro",
        'newest_first'            => "Recentes Primeiro",
        'notifications'           => "Notificações",
        'leave_blank_for_current' => "Deixe em branco para usar o atual",

        // --------------------------------------------------------------------
        // Plugins
        'plugins'   => "Plugins",
        'authors'   => "Autores",
        'version'   => "Versão",
        'install'   => "Instalar",
        'uninstall' => "Desinstalar",
        'enable'    => "Habilitar",
        'disable'   => "Desabilitar",

        // --------------------------------------------------------------------
        // Priorities
        'priorities'    => "Prioridades",
        'new_priority'  => "Nova Prioridade",
        'edit_priority' => "Editar Prioridade",

        // --------------------------------------------------------------------
        // Projects
        'projects'               => "Projetos",
        'project'                => "Projeto",
        'new_project'            => "Novo Projeto",
        'edit_project'           => "Editar Projeto",
        'name'                   => "Nome",
        'slug'                   => "Slug",
        'codename'               => "Codinome",
        'description'            => "Descrição",
        'enable_wiki'            => "Habilitar Wiki",
        'display_order'          => "Ordem de Exibição",
        'default_ticket_type'    => "Tipo padrão do ticket",
        'default_ticket_sorting' => "Ordenação padrão do ticket",

        // Project Settings
        'project_settings' => "Configurações do Projeto",
        'members'          => "Membros",

        // --------------------------------------------------------------------
        // Project Roles
        'role'       => "Papel",
        'roles'      => "Papéis",
        'new_role'   => "Novo Papel",
        'edit_role'  => "Editar Papel",
        'assignable' => "Atribuível",

        // --------------------------------------------------------------------
        // Roadmap
        'roadmap'   => "Cronograma",
        'all'       => "Todos",
        'active'    => "Ativos",
        'completed' => "Finalizados",
        'cancelled' => "Cancelados",
        'x_open'    => "{1} abertos",
        'x_started' => "{1} iniciados",
        'x_closed'  => "{1} fechados",

        // --------------------------------------------------------------------
        // Severities
        'severities'    => "Gravidades",
        'new_severity'  => "Nova Gravidade",
        'edit_severity' => "Editar Gravidade",

        // --------------------------------------------------------------------
        // Statuses
        'statuses'          => "Status",
        'status.type.0'     => "Fechado",
        'status.type.1'     => "Aberto",
        'status.type.2'     => "Iniciado",
        'new_status'        => "Novo Status",
        'edit_status'       => "Editar Status",
        'show_on_changelog' => "Exibir no Changelog",

        // --------------------------------------------------------------------
        // Timeline
        'timeline'                     => "Linha do Tempo",
        'activity'                     => "Atividade",
        'metrics'                      => "Métricas",
        'timeline.ticket_created'      => "Criado {type} #{id}: {summary}",
        'timeline.ticket_closed'       => "Fechado {type} #{id} como {status}: {summary}",
        'timeline.ticket_reopened'     => "Reaberto {type} #{id} como {status}: {summary}",
        'timeline.ticket_updated'      => "Atualizado {type} #{id}: {summary}",
        'timeline.ticket_comment'      => "Comentado em {link}",
        'timeline.milestone_completed' => "Milestone {name} finalizado",
        'timeline.milestone_cancelled' => "Milestone {name} cancelado",
        'timeline.ticket_moved_from'   => "Problema ({issue}) movido de {project}",
        'timeline.ticket_moved_to'     => "Problema ({issue}) movido para {project}",
        'timeline.wiki_page_created'   => "Criada {title} página wiki",
        'timeline.wiki_page_edited'    => "Editada {title} página wiki",
        'timeline.by_x'                => "por {1}",

        // --------------------------------------------------------------------
        // Timeline filters
        'timeline.filters.new_tickets'           => "Novos tickets",
        'timeline.filters.tickets_opened_closed' => "Tickets abertos/fechados",
        'timeline.filters.ticket_updates'        => "Atualizações de Ticket",
        'timeline.filters.ticket_comments'       => "Comentários de Ticket",
        'timeline.filters.ticket_moves'          => "Migrações de Ticket",
        'timeline.filters.milestones'            => "Milestones",
        'timeline.filters.wiki_pages'            => "Páginas Wiki",

        // --------------------------------------------------------------------
        // Types
        'types'     => "Tipos",
        'new_type'  => "Novo Tipo",
        'edit_type' => "Editar Tipo",
        'bullet'    => "Bullet",
        'template'  => "Modelo",

        // --------------------------------------------------------------------
        // Users
        'users'          => "Usuários",
        'new_user'       => "Novo Usuário",
        'edit_user'      => "Editar Usuário",
        'newest'         => "Recentes",
        'profile'        => "Perfil",
        'usercp'         => "UsuárioCP",
        'register'       => "Cadastrar",
        'login'          => "Login",
        'logout'         => "Logout",
        'username'       => "Nome do Usuário",
        'password'       => "Senha",
        'email'          => "Email",
        'create_account' => "Criar Conta",

        // UserCP
        'options'          => "Opções",
        'api_key'          => "Chave da API",
        'current_password' => "Senha Atual",
        'language'         => "Idioma",
        'subscriptions'    => "Assinaturas",


        // --------------------------------------------------------------------
        // Wiki
        'wiki'        => "Wiki",
        'home'        => "Home",
        'pages'       => "Páginas",
        'new_page'    => "Nova Página",
        'edit_page'   => "Editar Página",
        'delete_page' => "Remover Página",
        'revisions'   => "Revisões",
        'revision_x'  => "Revisão {1}",
        'title'       => "Título",
        'content'     => "Conteúdo"
    );
});
