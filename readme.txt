=== Whale-Kit ===
Contributors: stur, Yuri Stepanov
Donate link: http://www.wp.od.ua/en/
Tags: widgets, categories, taxonomies, posts, pages, shortcodes, get_terms, WP_Query, get_pages
Requires at least: 3.0.1
Tested up to: 4.1.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Three alternative to standard widget Categories, Recent Posts and Pages. These widgets can be used as short tags.
== Description ==

Three alternative to standard widget Categories, Recent Posts and Pages. These widgets can be used as short tags:

1.  WK_trem working with categories, post_tag or any taxonomies. Settings from function get_terms().
2.  WK_posts works with posts, pages and any other type of records. Settings from class WP_Query.
3.  WK_pages working pages, posts, and any other type of records. The data received through the function get_pages(). Unlike WK_posts working with tree hierarchical data.

Collapse - hide inactive branches of a tree - can significantly reduce the list categories or pages, Javascript is not used - hide html code.

Plagin supported hierarchical structure, this has its own Walker.

To construct the resulting html code You can used micro-templates.


Description and examples eXtra optsy see plugin page:

[wk_terms](http://www.wp.od.ua/en/?page_id=333 "categories, tags and other taxonomic")  - categories, tags and other taxonomic

[wk_posts adn wk_pages](http://www.wp.od.ua/en/?page_id=366 "posts, page and  any type of records")  - posts, page

== Installation ==

1. Unzip and upload folder `whale-kit` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to `Apperance->Widgets` add a widget in the sidebar and configure it.
4. You can use short tags [wk_posts ...] or [wk_terms ...] or [wk_pages ...] in the text post or page.

== Frequently Asked Questions ==
= Show child categories from the category My_Category id:34 =
`[wk_terms child_of=34]`

= Show all categories and empty too =
`[wk_terms hide_empty=0]`

= Exclude a category 32 and all childs =
`[wk_terms exclude_tree=array(32)]`

= Sort categories by count of records =
`[wk_terms orderby=count order=ASC]`
for the widget:
orderby=count&order=ASC

= Collapse categories =
`[wk_terms collapse=1 hierarchical=1]`
The collapse of the inactive branches of the tree of categories.

= Display tags and specify the number of records =
`[wk_terms taxonomy=post_tag show_count=1]`

= Show category and set the font size depending on the number of entries in the category =
`[wk_terms show_count=1 size_of_count=1 smallest=8 largest=22 unit=px]`

= Show 5 records out of category id:56, exclude category id:23 =
`[wk_posts cat=56,-23 posts_per_page=5]`
for the widget:
`cat=56,-23&posts_per_page=5`


= Show entries with thumbnail =
`[wk_posts meta_key=_thumbnail_id show_thumbnail=60?60 /]`
for the widget:
`meta_key=_thumbnail_id&show_thumbnail=60?60`
*none_thumbnai - plug, if the record does not have a thumbnail, then specify the id attachment

= Custom Field Query =
for the widget write all in one line:
`meta_key=color&meta_value=blue&meta_compare=<=&posts_per_page=5`
or per line of name = value pairs:
`
meta_key=color
meta_value=blue
meta_compare=<=
posts_per_page=5
`
= Multiple Custom Field Handling =
`
[wk_posts
tax_query='array(
    "relation"=>"AND",
     array(
       "taxonomy" => "category",
       "field" => "id",
       "terms" => array(16)
     ),
     array(
        "taxonomy" => "post_tag",
        "field" => "slug",
        "terms" => array("test_wk")
     )
)'
/]
`
for the widget write all in one line !newline is not allowed here:
`tax_query=array(  "relation"=>"AND",  array(  "taxonomy" => "category",  "field" => "id",  "terms" => array(16)  ),  array(  "taxonomy" => "post_tag",   "field" => "slug",   "terms" => array("test_wk") ) )`


= Show child pages to 567 pages =
`[wk_pages child_of=567]`


= Collapse and sorting pages =
`[wk_pages collapse=1 sort_column=menu_order sort_order=ASC]`
for the widget:
`
collapse=1
sort_column=menu_order
sort_order=ASC
`


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
= 1.1.0 =
 WK_Pages added to work with hierarchical structures. Added ability to display thumbnails of records.
