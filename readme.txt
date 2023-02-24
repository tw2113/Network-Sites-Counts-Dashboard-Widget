=== Network Sites Counts Dashboard Widget ===
Contributors:      tw2113
Tags: multisite, mu, network, post count, dashboard widget
Requires at least: 3.5.0
Tested up to:      4.7
Stable tag:        0.1.2
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Display a list of post counts for all your sites in a network.

== Description ==

After activating, this plugin adds a dashboard widget to the network admin dashboard page that displays a listing of all your sites and their published and draft posts. You can view other post-type counts by adding a `post_type` query parameter, like `http://YOUR_SITE_URL/wp-admin/network/index.php?post_type=page`.

Contribute [on Github](https://github.com/tw2113/Network-Sites-Counts-Dashboard-Widget).

== Installation ==

= Manual Installation =

1. Upload the entire `/network-sites-counts-dashboard-widget` directory to the `/wp-content/plugins/` directory.
2. Activate Network Sites Counts Dashboard Widget through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

Q: How do I view more than just counts from the `post` post type?
A: Add a GET parameter to the url, like so http://YOUR_SITE_URL/wp-admin/network/index.php?post_type=page

== Screenshots ==


== Changelog ==

= 0.1.2 =
* Ownership transfer

= 0.1.1 =
* Bug Fix: Fixed site name column empty on subdomain installs.

= 0.1.0 =
* First release

== Upgrade Notice ==

= 0.1.1 =
* Bug Fix: Fixed site name column empty on subdomain installs.

= 0.1.0 =
First Release
