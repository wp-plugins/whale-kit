=== Whale-Kit ===
Contributors: stur, Yuri Stepanov
Donate link: http://www.wp.od.ua/en/
Tags: widgets, categories, taxonomies, posts, pages, shortcodes, get_terms, WP_Query
Requires at least: 3.0.1
Tested up to: 3.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Two advanced widgets: WK_trem working with categories, post_tag or any taxonomies;  WK_posts works with posts, pages and any other type of records.

== Description ==

Two widgets and two shortcodes, alternative for standart widgets Categories and Pages:

1.  WK_terms works with categories, tags (post_tag) and other taxonomies. Initial data, we obtain the function get_terms(), all options this function - available for change.
2.  WK_posts - working with posts, pages, and any other type of records. For data used WP_Query, a huge number of input parameters available to change.

`Collapse` – hide inactive branches of a tree – can significantly reduce the list categories or pages, Javascript is not used - hide html code.
Plagin supported hierarchical structure, this has its own Walker.
To construct the resulting html code You can used `micro-templates`.


Description and examples eXtra optsy see plugin page:

[WK_terms](http://wp.od.ua/en/?p=76 "categories, tags and other taxonomic")  - categories, tags and other taxonomic

[WK_posts](http://wp.od.ua/en/?p=80 "posts, page and  any type of records")  - posts, page
== Installation ==

1. Unzip and upload folder `whale-kit` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to `Apperance->Widgets` add a widget in the sidebar and configure it.
4. You can use short tags [wk_posts ...] and [wk_terms ...]  in the text post or page.

== Screenshots ==

1. Add a widget to the sidebar

2. Categories tree

3. Pages tree

4. Use as a shortcod


== Changelog ==

= 1.0 =
 * Start Projects
= 1.0.1 =
 * Fixed bug `division by zero`  in the calculation of the font size.