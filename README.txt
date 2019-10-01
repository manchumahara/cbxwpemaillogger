=== CBX Email SMTP & Logger ===
Contributors: codeboxr, manchumahara
Tags: wordpress smtp, wordpress email log, smtp
Requires at least: 3.9
Tested up to: 5.2.3
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin helps to send email using SMTP and other methods as well as logs email and displays in admin panel and more.

== Description ==

This plugin helps to log any email sent from wordpress.

For support  [please contact](https://codeboxr.com/contact-us/)

**Email Log Features:**

* Default enabled on plugin activation
* Logs every email sent
* Logs email send success or fail(Bullet proof way to detect email send or not)
* Delete all email logs or single
* View Email Log
* View Email Preview
* ReSend email from the list window
* Delete X Days old logs from Log listing
* Auto delete X Days old logs using wordpress native event schedule
* Custom Setting panel
* Delete custom options created by this plugin and email logs on uninstall(it's not deactivate, uninstall means delete plugin)
* Save email attachments if enabled, default disabled

**Email SMTP Features:**

* Default disabled on plugin activation
* Enable/disable override from Name
* Enable/disable override from Email
* Override wordpress default email to send via SMTP
* Full SMTP feature implementations

For documentation and pro features [please visit](https://codeboxr.com/product/cbx-email-logger-for-wordpress/)

== Installation ==

This section describes how to install the plugin and get it working.

> this plugins add an extra header to email to tracking email sent success or not. The custom header added in email is in format
  'x-cbxwpemaillogger-id: $log_id'

e.g.

1. Upload `cbxwpemaillogger` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You can search from wordpress plugin manager by keyword "cbxwpemaillogger" and install from live

== Frequently Asked Questions ==


== Screenshots ==

1. Email Log List
2. Setting - General settings
3. Setting - Tools settings
4. Email Log List Toolbar - View, Delete, Template, ReSend
5. Email Single Log View with Email Preview
6. Email Template Preview from Lost list
7. Setting - SMTP configuration

== Changelog ==

= 1.0.3 =

* [New] Custom SMTP
* [New] Email attachment store/save
* [Fix] Email resend now maintain same email content type

= 1.0.2 =

* Added option panel
* Delete X Days old logs from Log listing
* Auto delete X Days old logs using wordpress native event schedule
* Custom Setting panel
* Delete custom options created by this plugin and email logs on uninstall(it's not deactivate, uninstall means delete plugin)

= 1.0.1 =

* View Email Log
* View Email Template in Popup
* View Email log template in single view display
* Single click resend email

= 1.0.0 =

* First public release