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

namespace CustomTabs\controllers\admin;

use avalon\http\Request;
use avalon\output\View;

use CustomTabs\models\CustomTab;

/**
 * Custom tabs controller.
 *
 * @author Jack P.
 * @since 3.0.7
 * @package CustomTabs
 * @subpackage Controllers
 */
class CustomTabs extends \traq\controllers\admin\AppController
{
    /**
     * Tab listing page.
     * Nothing to do here as the tabs are already sent to the view.
     */
    public function action_index() {}

    /**
     * New tab.
     */
    public function action_new()
    {
        $tab = new CustomTab;

        // Check if the form has been submitted.
        if (Request::method() == 'post') {
            $tab->set(array(
                'label'         => Request::post('label'),
                'url'           => Request::post('url'),
                'groups'        => implode(',', Request::post('groups', \traq\models\Group::all_group_ids())),
                'display_order' => Request::post('display_order', 0),
                'project_id'    => Request::post('project_id', 0)
            ));

            // Save and reidrect
            if ($tab->save()) {
                Request::redirectTo('/admin/custom_tabs');
            }
        }

        View::set(compact('tab'));
    }

    /**
     * Edit tab.
     *
     * @param integer $id Tab ID
     */
    public function action_edit($id)
    {
        $tab = CustomTab::find($id);

        // Check if the form has been submitted.
        if (Request::method() == 'post') {
            $tab->set(array(
                'label'         => Request::post('label', $tab->label),
                'url'           => Request::post('url', $tab->url),
                'groups'        => implode(',', Request::post('groups', explode(',', $tab->groups))),
                'display_order' => Request::post('display_order', $tab->display_order),
                'project_id'    => Request::post('project_id', $tab->project_id)
            ));

            // Save and redirect
            if ($tab->save()) {
                Request::redirectTo('/admin/custom_tabs');
            }
        }

        View::set(compact('tab'));
    }

    /**
     * Delete tab.
     *
     * @param integer $id Tab ID
     */
    public function action_delete($id)
    {
        CustomTab::find($id)->delete();
        Request::redirectTo('/admin/custom_tabs');
    }
}
