=== Frequently Asked Questions (FAQs) ===
Contributors: husobj
Donate link: https://www.paypal.com/ncp/payment/P64UGJHU5MZVE
Tags: faq, faqs, questions, answers, knowledgebase
Requires at least: 4.7
Tested up to: 6.9
Stable tag: 2.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple management of Frequently Asked Questions (FAQ) via post type and categories.

== Description ==

This plugin is designed to be a simple solid base for managing an FAQ section on your web site.

It consists of:

* A simple 'faq' post type
* [faqs /] shortcode
* FAQ search widget
* FAQ search form template tag

== Installation ==

1. Upload the `faqz` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Why aren't there may options and features? =

This is just a first release. More to come...

= Why so many breaking changes in version 2.0? =

The main post type was changed to `faq` so the plugin is easier to migrate to/from other FAQ plugins. Front end CSS classes were changed to reflect this and follow a more predictable BEM format. Code and plugin hooks retained their `faqz` namespace. It is possible to reinstate some backward compatibility using the filters mentioned in the changelog and updating your template names and CSS files.

== Screenshots ==

1. FAQz custom post type
2. FAQ search widget

== Changelog ==

= 2.0 =
* Breaking Change: Post type `faqz` is now `faq`. Use `faqz_registered_post_type` filter for backwards compatibility.
* Breaking Change: Taxonomy `faqz-category` is now `faq-category`. Use `faqz_registered_taxonomy` filter for backwards compatibility.
* Breaking Change: Post type single and archive URL slug is now `faqs` instead of `faqz`. Use `register_faq_post_type_args` filter to change.
* Breaking Change: Category URL slug is now `faq-category` instead of `faqz-category`. Use `register_faq-category_taxonomy_args` filter to change.
* Breaking Change: `faqz` shortcode is now `faqs`.
* Breaking Change: Removed `faqz_register_post_type_args` filter. Use `register_faq_post_type_args`.
* Breaking Change: Removed `faqz_register_taxonomy_args` filter. Use `register_faq-category_taxonomy_args`.
* Breaking Change: Optional template `search-faq.php` changed to `search-faqz.php`.
* Breaking Change: Optional template `searchform-faqz.php` changed to `searchform-faq.php`.
* Breaking Change: `faqz_context` arg removed from faqz_list() display method.
* Breaking Change: CSS classes for search form and default FAQs output changed.

= 1.0 =
* Rename to "Frequently Asked Questions (FAQs).
* Fix plugin check notices.

= 0.3 =
* Make FAQz post type public by default.
* Register `faqz_category` taxonomy.
* Set rewrite slug `with_front` to false.

= 0.2 =
* Code tidy/restructure and documentation.

= 0.1 =
* First release.

== Upgrade Notice ==

= 0.1 =
First release.