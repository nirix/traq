<style type="text/css">
body {
	background: #fff;
	color: #000;
	margin: 10px;
	padding: 0;
}
body, th, td {
	font: normal 14px verdana,arial,'Bitstream Vera Sans',helvetica,sans-serif;
}
h1, h2, h3, h4 {
	font-family: arial,verdana,'Bitstream Vera Sans',helvetica,sans-serif;
	font-weight: bold;
	letter-spacing: -0.018em;
}
h1 {
	font-size: 19px;
	margin: .15em 1em 0 0;
}
h2 {
	font-size: 16px;
}
h3 {
	font-size: 14px;
}
hr {
	border: none;
	border-top: 1px solid #ccb;
	margin: 2em 0;
}
img {
	border: none;
}
.underline {
	text-decoration: underline;
}

#wrapper {
	width: 1000px;
	margin: 0 auto;
}

/* Link styles */
a {
	text-decoration: none;
	color: #b00;
	border-bottom: 1px solid #bbb;
}
a:hover {
	
}
h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
	color: inherit;
}

.errormessage {
	border: 1px solid #C00;
	color: #C00;
	padding: 5px;
}

/* Forms */
div.form {
	width: 600px;
	margin: 0 auto;
}
div.form fieldset {
	margin-bottom: 5px;
}


input, textarea, select {
	margin: 2px;
}
input, select {
	vertical-align: middle;
}
input[type=button], input[type=submit], input[type=reset], button {
	background: #eee;
	color: #222;
	border: 1px outset #ccc;
	padding: .1em .5em;
}
input[type=button]:hover, input[type=submit]:hover, input[type=reset]:hover, button:hover {
	background: #fff;
}
input[type=button][disabled], input[type=submit][disabled],
input[type=reset][disabled] {
	background: #f6f6f6;
	border-style: solid;
	color: #999;
}
input[type=text], input[type=password], input.textwidget, textarea {
	border: 1px solid #d7d7d7;
}
input[type=text], input[type=password], input.textwidget {
	padding: .25em .5em;
}
input[type=text]:focus, input[type=password]:focus, input.textwidget:focus, textarea:focus {
	border: 1px solid #886;
}
fieldset {
	border: 1px solid #d7d7d7;
	padding: .5em;
	margin: 0;
}
fieldset.iefix {
	background: transparent;
	border: none;
	padding: 0;
	margin: 0;
}
* html fieldset.iefix {
	width: 98%;
}
fieldset.iefix p {
	margin: 0;
}
legend {
	color: #999;
	padding: 0 .25em;
	font-size: 90%;
	font-weight: bold;
}
label.disabled {
	color: #d7d7d7;
}
fieldset.error {
	border: 1px solid #C00;
	color: #C00;
}
fieldset.error input[type=text], fieldset.error input[type=password] {
	border: 1px solid #C00;
}

#header #head {
	font-size: 26px;
	font-weight: bold;
}
#header #head a {
	color: #000;
	border: 0;
}
#header #head a:hover {
	background-color: transparent;
}

#content {
	padding: 10px 0px;
}

#footer {
	border-top: 1px solid #000;
	padding: 5px 0px;
}

/* Navigation */
#metanav {
	margin-top: 5px;
}
.nav ul {
	font-size: 0px;
	list-style: none;
	margin: 0;
	padding: 0;
	text-align: left;
}
.nav li {
	border-right: 1px solid #d7d7d7;
	display: inline;
	padding: 0 .75em;
	white-space: nowrap;
	font-size: 10px;
}
.nav li.last {
	border-right: none;
}

/* Main navigation bar */
#mainnav {
	background: #f7f7f7;
	border: 1px solid #000;
	font: normal 10px verdana,'Bitstream Vera Sans',helvetica,arial,sans-serif;
	margin: .66em 0 .33em;
	padding: .2em 0;
}
#mainnav ul {
	font-size: 0px;
}
#mainnav li {
	border-right: none;
	padding: .25em 0;
	font-size: 10px;
}
#mainnav a {
	border-right: 1px solid #000;
	border-bottom: none;
	color: #000;
	padding: 2px 20px;
}
#mainnav a:hover {
	background-color: #ccc;
}
#mainnav .active a {
	background: #000;
	border-top: none;
	color: #fff;
	font-weight: bold;
}

/* Styles for the milestone and project view */
ul.milestones, ul.projects {
	margin: 2em 0 0;
	padding: 0
}
li.milestone, li.project {
	list-style: none;
	margin-bottom: 2em;
}
.milestone .info, .project .info {
	white-space: nowrap;
}
.milestone .info h2, .project .info h2 {
	background: #f7f7f7;
	border-bottom: 1px solid #d7d7d7;
	margin: 0;
}
.milestone .info h2 a, .project .info h2 a {
	color: #000;
	display: block;
	border-bottom: none;
}
.milestone .info h2 a:hover,
.project .info h2 a:hover {
	color: #000;
	background-color: #eee;
}
.milestone .info h2 em, .project .info h2 em {
	color: #b00;
	font-style: normal;
}
.milestone .info .date, .msdate {
	color: #888;
	font-size: 11px;
	font-style: italic;
	margin: 0;
}
.milestone .info .progress {
	margin: 1em 1em 0;
	width: 40em;
	max-width: 70%;
}
.milestone .info dl, .project .info dl {
	font-size: 10px;
	font-style: italic;
	margin: 0 1em 2em;
	white-space: nowrap;
}
.milestone .info dt, .project .info dt {
	display: inline;
	margin-left: .5em;
}
.milestone .info dd, .project .info dd {
	display: inline;
	margin: 0 1em 0 .5em;
}
.milestone .description, .project .description {
	margin-left: 1em;
}
.milestone .date {
	color: #888;
	font-style: italic;
	margin: 0;
}
.milestone .description, .project .description {
	margin: 1em 0 2em;
}

/* General styles for the progress bars */
table.progress {
	border: 1px solid #d7d7d7;
	border-collapse: collapse;
	border-spacing: 0;
	float: left;
	margin: 0;
	padding: 0;
	empty-cells: show;
}
table.progress a, table.progress a:hover {
	border: none;
	display: block;
	width: 100%;
	height: 1.2em;
	padding: 0;
	margin: 0;
	text-decoration: none
}
table.progress td {
	background: #dd847e;
	padding: 0px;
}
table.progress td.closed {
	background: #bae0ba;
}
table.progress td :hover {
	background: none;
}
p.percent {
	font-size: 10px;
	line-height: 2.4em;
	margin: 0.9em 0 0;
}


/* Styles for tabe listings. */
table.listing {
	clear: both;
	border-bottom: 1px solid #d7d7d7;
	border-collapse: collapse;
	border-spacing: 0;
	margin-top: 1em;
	width: 100%;
}
table.listing th {
	text-align: left;
	padding: 0 1em .1em 0;
	font-size: 12px;
}
table.listing thead {
	background: #f7f7f0;
}
table.listing thead th {
	border: 1px solid #d7d7d7;
	border-bottom-color: #999;
	font-size: 11px;
	font-weight: bold;
	padding: 2px .5em;
	vertical-align: bottom;
}
table.listing thead th a, table.listing thead th a:hover {
 	background-color: transparent;
}
table.listing thead th a {
	border: none;
	padding-right: 12px;
}
table.listing th.asc a, table.listing th.desc a {
	font-weight: bold;
}
table.listing th.asc a, table.listing th.desc a {
	background-position: 100% 50%;
	background-repeat: no-repeat;
}
table.listing tbody td, table.listing tbody th {
	border: 1px dotted #ddd;
	padding: .33em .5em;
	vertical-align: top;
}
table.listing tbody td a:hover, table.listing tbody th a:hover {
	background-color: transparent;
}
table.listing tbody tr {
	border-top: 1px solid #ddd;
}
table.listing tbody tr.even {
	background-color: #fcfcfc;
}
table.listing tbody tr.odd {
	background-color: #f7f7f7;
}
table.listing tbody tr:hover {
	background: #eed !important;
}

/* Styles for the ticket list. */
.tickets thead th.id {
	width: 50px;
}
.reports tbody td a, .tickets tbody td a {
	display: block;
}
.tickets {
	border-bottom: none;
}
.tickets thead th {
	text-transform: capitalize;
	white-space: nowrap;
}
.tickets tbody td, .reports tbody td {
	padding: .1em .5em !important;
}
.tickets tbody td a {
	border-bottom: none;
}
.tickets tbody td.id a {
	font-weight: bold;
}
.tickets tbody tr:hover {
	background: #eed;
	color: #000;
}

table.tickets tbody tr.priority6 {
	background: #f0f0f0;
	border-color: #ddd;
}
table.tickets tbody tr.even.priority6 {
	background: #f7f7f7;
}
table.tickets tbody tr.priority5 {
	background: #fdc;
	border-color: #e88;
}
table.tickets tbody tr.even.priority5 {
	background: #fed;
	border-color: #e99;
}
table.tickets tbody tr.priority4 {
	background: #ffb;
	border-color: #eea;
}
table.tickets tbody tr.even.priority4 {
	background: #ffd;
	border-color: #dd8;
}
table.tickets tbody tr.priority3 {
	background: #e7ffff;
	border-color: #cee;
}
table.tickets tbody tr.even.priority3 {
	background: #dff;
	border-color: #bee;
}
table.tickets tbody tr.priority2 {
	background: #e7eeff;
	border-color: #cde;
}
table.tickets tbody tr.even.priority2 {
	background: #dde7ff;
}
table.tickets tbody tr.priority1 {
	background: #fbfbfb;
	border-color: #ddd;
}
table.tickets tbody tr.even.priority1 {
	background: #f6f6f6;
	border-color: #ccc;
}

form#newticket {
	width: 700px;
	margin: 0 auto;
}
form#newticket #body {
	
	width: 100%;
}
#newticket fieldset {
	width: 100%;
}

#ticket {
	background: #ffd;
	border: 1px outset #996;

	padding: .5em 1em;
	position: relative;
}

#ticket_sidebar {
	width: 200px;
	margin-left: 5px;
}

#history, #comments {
	border: 1px outset #9e9e9e;
	margin-top: 1em;
	padding: .5em 1em;
	position: relative;
}
#history h3, #comments h3 {
	border-bottom: 1px solid #000;
	color: #000;
	font-size: 100%;
	font-weight: normal;
	margin: 0px;
}

#comments textarea {
	width: 500px;
	height: 100px;
}

table.comments th {
	text-align: left;
	vertical-align: top;
	padding-right: 2px;
}
table.comments td {
	vertical-align: top;
	padding-left: 2px;
}

h1 .status {
	color: #444;
	text-transform: lowercase;
}
#ticket h1.summary {
	margin: 0 0 8px 0;
}
#ticket h2.summary {
	margin: 0 0 .8em 0;
}
#ticket .date {
	color: #996;
	float: right;
	font-size: 85%;
	position: relative;
}
#ticket .date p {
	margin: 0;
}

#ticket table.properties {
	border-top: 1px solid #dd9;
	border-collapse: collapse;
	table-layout: fixed;
	width: 100%;
}
#ticket table.properties tr {
	border-bottom: 1px dotted #eed;
}
#ticket table.properties td, #ticket table.properties th {
	font-size: 80%;
	padding: .5em 1em;
	vertical-align: top;
}
#ticket table.properties th {
	color: #663;
	font-weight: normal;
	text-align: left;
	width: 20%;
}
#ticket table.properties td {
	width: 30%;
}
#ticket table.properties .description {
	border-top: 1px solid #dd9;
}


#ticket .description h3 {
	border-bottom: 1px solid #dd9;
	color: #663;
	font-size: 100%;
	font-weight: normal;
}
#ticket .inlinebuttons { 
	float: right;
	position: relative;
	bottom: 0.3em;
}

#properties {
	white-space: nowrap;
	line-height: 160%;
	padding: .5em;
}
#properties table {
	border-spacing: 0;
	width: 100%;
}
#properties table th {
	padding: .4em;
	text-align: right;
	width: 20%;
	vertical-align: top;
}
#properties table th.col2 {
	border-left: 1px dotted #d7d7d7;
}
#properties table td {
	vertical-align: middle;
	width: 30%;
}
#properties table td.fullrow {
	vertical-align: middle;
	width: 80%;
}

fieldset.radio {
	border: none;
	margin: 0;
	padding: 0;
}
fieldset.radio legend {
	color: #000;
	float: left;
	font-size: 100%;
	font-weight: normal;
	padding: 0 1em 0 0;
}
fieldset.radio label {
	padding-right: 1em;
}
</style>