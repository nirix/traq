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

$(document).ready(function(){
	var news_container = $("#traq_news ul");

	var strip = function(text) {
		return text.replace(/</g, "&lt;");
	};

	if (window.location.protocol == "http:") {
		$.getJSON('http://traq.io/news.json?callback=?').done(function(data){
			$.each(data, function(i, data){
				var item = $("<li>").addClass('box');

				var summary = $("<h4>");
				if (data.url) {
					summary.append($("<a>").attr('href', data.url).append(data.summary));
				} else {
					summary.append(data.summary);
				}

				var created_at = $("<span>").append(data.created_at_relative).attr('title', data.created_at);

				// Format the Markdown text.
				var content = $("<div>").load(traq.base + '_misc/format_text', { data: data.content });

				item.append(summary);
				item.append(created_at);
				item.append(content);
				news_container.append(item);
			});
		});
	} else {
		$("#traq_news .secure_alert").show();
	}
});
