<?php
/*!
 * Traq
 * Copyright (C) 2009-2024 Jack P.
 * Copyright (C) 2012-2024 Traq.io
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

/**
 * Returns the HTML to include a UI asset package.
 *
 * @since 3.8
 *
 * @return string
 */
function ui_package($entry)
{
  static $imported = [];

  $manifestPath = dirname(dirname(dirname(__DIR__))) . '/assets/ui/manifest.json';

  try {
    $manifest = json_decode(file_get_contents($manifestPath), true);
  } catch (ErrorException $e) {
    return "Unable to open file {$manifestPath}";
  }

  // Direct match or no?
  if (isset($manifest[$entry])) {
    $index = $entry;
  } else {
    $index = "traq-ui/{$entry}.ts";
  }

  if (in_array($index, $imported)) {
    return;
  }

  $imported[] = $index;

  if (isset($manifest[$index])) {
    $info = $manifest[$index];
    $file = $info['file'];

    $html = [];

    // CSS files
    if (isset($info['css'])) {
      foreach ($info['css'] as $cssFile) {
        $html[] = '<link rel="stylesheet" href="' . Request::base("assets/ui/{$cssFile}") . '" media="screen" />';
      }
    }

    // Modules/chunks
    if (isset($info['imports'])) {
      foreach ($info['imports'] as $importFile) {
        $html[] = ui_package($importFile);
      }
    }

    // Main file
    $html[] = '<script type="module" src="' . Request::base("assets/ui/{$file}") . '" type="text/javascript"></script>';

    return implode(PHP_EOL, $html);
  }
}
