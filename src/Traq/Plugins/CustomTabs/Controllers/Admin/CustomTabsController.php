<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
 * https://github.com/nirix
 * http://traq.io
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

namespace CustomTabs\Controllers\Admin;

use Avalon\Http\Request;
use Avalon\Http\Response;

use CustomTabs\Models\CustomTab;

/**
 * Custom tabs controller.
 *
 * @author Jack P.
 * @since 3.0.7
 * @package CustomTabs
 * @subpackage Controllers
 */
class CustomTabsController extends \Traq\Controllers\Admin\AppController
{
    /**
     * Tab listing page.
     * Nothing to do here as the tabs are already sent to the view.
     */
    public function index(): Response
    {
        return $this->render('admin/custom_tabs/index.phtml', [
            'custom_tabs' => CustomTab::fetchAll(),
        ]);
    }

    /**
     * New tab.
     */
    public function new(): Response
    {
        $tab = new CustomTab;

        // Check if the form has been submitted.
        if (Request::method() == 'POST') {
            $tab->set(array(
                'label'         => Request::get('label'),
                'url'           => Request::get('url'),
                'groups'        => implode(',', Request::get('groups', \Traq\Models\Group::all_group_ids())),
                'display_order' => Request::get('display_order', 0),
                'project_id'    => Request::get('project_id', 0)
            ));

            // Save and redirect
            if ($tab->save()) {
                return $this->redirectTo('/admin/custom-tabs');
            }
        }

        if (Request::get('overlay') == 'true') {
            return $this->render('admin/custom_tabs/new.overlay.phtml', compact('tab'));
        }

        return $this->render('admin/custom_tabs/new.phtml', compact('tab'));
    }

    /**
     * Edit tab.
     *
     * @param integer $id Tab ID
     */
    public function edit(int $id): Response
    {
        $tab = CustomTab::find($id);

        // Check if the form has been submitted.
        if (Request::method() == 'POST') {
            $tab->set(array(
                'label'         => Request::get('label', $tab->label),
                'url'           => Request::get('url', $tab->url),
                'groups'        => implode(',', Request::get('groups', explode(',', $tab->groups))),
                'display_order' => Request::get('display_order', $tab->display_order),
                'project_id'    => Request::get('project_id', $tab->project_id)
            ));

            // Save and redirect
            if ($tab->save()) {
                return $this->redirectTo('/admin/custom-tabs');
            }
        }

        if (Request::get('overlay') == 'true') {
            return $this->render('admin/custom_tabs/edit.overlay.phtml', compact('tab'));
        }

        return $this->render('admin/custom_tabs/edit.phtml', compact('tab'));
    }

    /**
     * Delete tab.
     *
     * @param integer $id Tab ID
     */
    public function delete(int $id): Response
    {
        CustomTab::find($id)->delete();
        return $this->redirectTo('/admin/custom-tabs');
    }
}
