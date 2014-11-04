<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace Traq\Helpers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * Assets helper.
 *
 * @author Jack P.
 * @since 4.0.0
 */
class Assets
{
    /**
     * Uses the manifest to search for all JavaScript assets. Not recommended
     * for use in production environments.
     *
     * @return string[]
     */
    public static function jsAssets()
    {
        $assetDir = DOCROOT . '/assets';
        $manifest = json_decode(file_get_contents("{$assetDir}/manifest.json"), true);

        $assets = [];

        foreach ($manifest['js'] as $asset) {
            // File
            if (
                file_exists("{$assetDir}/{$asset}")
                && !is_dir("{$assetDir}/{$asset}")
                && !in_array($asset, $assets)
            ) {
                $assets[] = $asset;
            }
            // Directory
            else {
                $directoryIterator = new RecursiveDirectoryIterator("{$assetDir}/{$asset}");
                $iterator          = new RecursiveIteratorIterator($directoryIterator);
                $matcher           = new RegexIterator($iterator, "(.*)");

                foreach ($matcher as $match) {
                    $file      = basename($match);
                    $extension = pathinfo($match, PATHINFO_EXTENSION);
                    $_asset    = str_replace("{$assetDir}/", '', $match);

                    if (
                        $extension == 'js'
                        && $file != 'js.js'
                        && !in_array($_asset, $assets)
                    ) {
                        $assets[] = $_asset;
                    }
                }
            }
        }

        return $assets;
    }
}
