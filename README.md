# ProcessWire Webmention Module

[ProcessWire](https://processwire.com) module to send and receive webmentions.

[Webmention](https://webmention.net) is a simple way to notify any URL when you link to it on your site. From the receiver’s perspective, it’s a way to request notifications when other sites link to it.

This module is for ProcessWire 2.x. If you are using ProcessWire 3.x, please use [the modules directory release](https://modules.processwire.com/modules/webmention/) instead.

Starting with version 1.1.2, this is a stable release that covers webmention sending, receiving, parsing, and display. It includes an admin interface for browsing and managing all webmentions. It also includes support for the Webmention Vouch extension.

## Features
* Webmention endpoint discovery
* Send webmentions asynchronously
* Receive webmentions
* Process received webmentions to extract [microformats](http://microformats.org)
* Log referring URLs for use with Webmention Vouch extension
* Send, receive, and validate Webmention Vouch parameter
* Monitor a blogroll or whitelist, adding new links to the list of approved Vouch domains

## Requirements
* [php-mf2](https://github.com/indieweb/php-mf2) and [php-mf2-cleaner](https://github.com/barnabywalters/php-mf-cleaner) libraries; bundled with this package and may optionally be updated using Composer.
* This module hooks into the [LazyCron](http://modules.processwire.com/modules/lazy-cron/) module.

## Installation
Installing the core module named “Webmention” will automatically install the Fieldtype, Inputfield, and Webmentions Manager modules included in this package.

This module will attempt to add a template and page named “Webmention Endpoint” if the template does not exist already. The default location of this endpoint is http://example.com/webmention-endpoint

After installing the modules:
* Create a new field of type “Webmentions” and add it to the template(s) you want to be able to support webmentions
* In the admin area, Modules > Webmention > Settings, select the frequency to automatically process received webmentions (optional but recommended)

## Sending Webmentions
When creating or editing a page that has a Webmentions field, a checkbox “Send Webmentions” will appear at the bottom. Check this box and any URLs linked in the page body will be queued up for sending webmentions. Webmentions will only be sent if the page status is “published."

## Receiving Webmentions
Any page that has a `Webmentions` field will be able to receive webmentions. If you would like to customize the webmention endpoint your site uses, refer to the __Settings__ section below.

## Processing Webmentions
You can review and manually process received webmentions using: Setup > Webmentions Manager, or by editing an individual page that has a `Webmentions` field. There is a dropdown for “Action” and “Visibility” beside each webmention. Select “Process” to parse the webmention for microformats.

To automatically process received webmentions asynchronously, refer to the __Settings__ section below.

## Displaying Webmentions
Within your template file, you can use `$page->Webmentions->render()` [where “Webmentions” is the name you used creating the field] to display a list of approved webmentions. As with the Comments Fieldtype, you can also [generate your own output](https://processwire.com/api/fieldtypes/comments/).

You can add this sample CSS if using the built-in render() method:

```
.WebmentionList ul,
.WebmentionList li
{
	list-style: none;
}

.WebmentionList .avatar
{
	display: inline-block;
}

	.avatar img
	{
		max-width: 50px;
		border-radius: 5px;
	}

.WebmentionList .note
{
	display: inline-block;
	padding-left: 0.5em;
	vertical-align: top;
}

	.note .reply-context
	{
		margin-top: 0;
	}
```

### Custom Display (Advanced)

If you would like more control over the display of webmentions, you can use individual properties of the `WebmentionItem` in your template. Properties include:

- source_url
- target_url
- vouch_url
- type
- is_like
- is_repost
- is_rsvp
- content
- url
- name
- author_name
- author_photo
- author_logo
- author_url
- published
- published_offset
- updated
- updated_offset
- status
- visibility

For more information, refer to the WebmentionItem.php `__construct()` method and the WebmentionList.php `render()` method.

## Vouch (beta)
The [Vouch](https://indieweb.org/Vouch) is a beta anti-spam extension to Webmention. This plugin will always attempt to send vouch URLs with webmentions, if possible. You can optionally require received webmentions include a vouch URL. Since Vouch is in beta and not many webmention implementations support sending the vouch parameter yet, it is not recommended for general usage yet.

For more information, refer to the __Settings__ section below.

## Settings
In the admin area, Modules > Webmention > Settings are several settings you can customize:

**Frequency to automatically process received webmentions**

(Recommended) When enabled, your site will use LazyCron to process received webmentions. Note that valid webmentions are automatically approved and will appear on your public site if you have set up __Displaying Webmentions__.

If disabled, you will need to manually process webmentions through the admin, Setup > Webmentions Manager.

**Fields to parse for URLs**

When sending webmentions, these fields in the template will be parsed for URLs. Defaults to `body`.

**Custom webmention endpoint URL**

If your webmention endpoint is at a different URL, enter it here.

**Treat http and https as the same**

(Recommended) If your site supports `http` and `https` and the same content is delivered for both (or `http` redirects to `https`), enable this option. This option is on by default.

**Enable verbose logging**

(Recommended) Verbose logging includes webmention endpoint discovery, sending, and receiving. Regular logging will only include errors. Verbose logging can help with debugging and is on by default.

**Process referer URLs for potential vouches**

(Recommended) Referer URLs will be logged and processed to find URLs that link back to you. These URLs can be used as vouches when sending webmentions. This option is on by default.

Note that this module attempts to send the `vouch` parameter with webmentions regardless of whether you have __Require webmention with vouch__ selected.

**Require webmention with vouch**

(Beta) If enabled, your site will require a valid `vouch` parameter with each incoming webmention. Vouch is in beta and not many webmention implementations support sending the vouch parameter yet, so this is not recommended for general usage yet.

**Vouch whitelist URL**

(Beta) You can enter the URL of your blogroll or other whitelist. This URL will be monitored daily and new domains will be added to the list of approved vouch domains. No domains will be removed from the approved vouch domains.

The links on this page must use the [h-card microformat](http://microformats.org/wiki/h-card).

**Approved vouch domains**

(Beta) You can manually enter domains that you will accept as vouches.

## Logs
This module writes a few logs: webmentions-sent, webmentions-received, and webmentions-referers.

## IndieWeb
The IndieWeb movement is about owning your data. It encourages you to create and publish on your own site and optionally syndicate to third-party sites. Webmention is one of the core building blocks of this movement.

Learn more and get involved by visiting <https://indieweb.org>.

## Changelog
### v2.0.0 2018-05-06
- Update to support ProcessWire 3.x
- Update php-mf2 library to version 0.4.x
- Improve verification of source linking to target
- Fix delete webmention bug
- Fix webmention author display in admin
- Fix WebmentionList render() method

### v1.1.3 2017-03-27
- Fixed fatal error on install
- Improved validation of source, target, and vouch parameters
- Enabled sending webmentions to links that have been removed from a post
- Added hookable methods for image caching
- Fixed handling of HTTP 410 Gone responses

### v1.1.2 2016-04-11
- Stable release
- Important bug fix when parsing author name.

### v1.1.1 2016-04-10
- Updated packaged php-mf2 library to version 0.3.0
- Added config option to automatically monitor a page for approved vouch domains.
- Better authorship algorithm support.
- Fixed issues #2, #3

### v1.1.0 2016-02-25
Stable release

