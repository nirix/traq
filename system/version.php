<?php
/*
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

// Version
define("TRAQ_VER", "3.0-dev"); // Pretty obvious...

// Version code
// 1.2.3   -> 10203
// 1.3(.0) -> 10300
// 1.12.1  -> 11201
// 1.2.11  -> 10211
define("TRAQ_VER_CODE", "30000"); // Used to check for new versions

// Database revision
define("TRAQ_DB_REV", 21); // Used by the updater