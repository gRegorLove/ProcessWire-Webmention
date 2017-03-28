# ProcessWire Webmention Module

[ProcessWire](http://processwire.com) module to send and receive webmentions.

[Webmention](http://webmention.net) is a simple way to notify any URL when you link to it on your site. From the receiver’s perspective, it’s a way to request notifications when other sites link to it.

Version 1.1.2 is a stable release that covers webmention sending, receiving, parsing, and display. It includes an admin interface for browsing and managing all webmentions. It also includes support for the Webmention Vouch extension.

## Features
* Webmention endpoint discovery
* Automatically send webmentions asynchronously
* Automatically receive webmentions
* Automatically process received webmentions to extract [microformats](http://microformats.org)
* Automatically logging referring URLs for use with Webmention Vouch extension
* Sending, receiving, and validation of Webmention Vouch parameter
* Automatically monitor a blogroll or whitelist, adding new links to the list of approved vouch domains.

## Requirements
* [php-mf2](https://github.com/indieweb/php-mf2) and [php-mf2-cleaner](https://github.com/barnabywalters/php-mf-cleaner) libraries; bundled with this package and may optionally be updated using Composer.
* This module hooks into the [LazyCron](http://modules.processwire.com/modules/lazy-cron/) module.

## Installation
Installing the core module named “Webmention” will automatically install the Fieldtype and Inputfield modules included in this package.

This module will attempt to add a template and page named “Webmention Endpoint” if the template does not exist already. The default location of this endpoint is http://example.com/webmention-endpoint

After installing the module, create a new field of type “Webmentions” and add it to the template(s) you want to be able to support webmentions.

## Sending Webmentions
When creating or editing a page that has a Webmentions field, a checkbox “Send Webmentions” will appear at the bottom. Check this box and any URLs linked in the page body will be queued up for sending webmentions. Note: you should only check the “Send Webmentions” box if the page status is “published."

## Receiving Webmentions
This module enables receiving webmentions on any pages that have have a Webmentions field, by adding the webmention endpoint as an HTTP Link header. If you would like to specify a custom webmention endpoint URL, you can do so in the admin area, Modules > Webmention.

## Processing Webmentions
To automatically process received webmentions asynchronously, specify the frequency in the admin area, Modules > Webmention.

You can manually process received webmentions by editing an individual page, or use the Webmention Manager, Setup > Webmentions. There is a dropdown for “Action” and “Visibility” beside each webmention. Select “Process” to parse the webmention for microformats.

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


## Logs
This module writes two logs: webmentions-sent and webmentions-received.

## Vouch (beta)
The [Vouch](https://indieweb.org/Vouch) is a beta anti-spam extension to Webmention. This plugin will always attempt to send vouch URLs with webmentions, if possible. You can optionally require received webmentions include a vouch URL. In the admin area, Modules > Webmention, check the box to “Require webmention with vouch” and add “Approved vouch domains,” one domain per line.

As of version 1.1.1, there is a new config field “Vouch whitelist URL.” You can enter the URL of your blogroll or other whitelist. It will be monitored periodically and new domains will be added to the list of approved vouch domains. Links must use the [h-card microformat](http://microformats.org/wiki/h-card). No domains will be removed from the approved vouch domains.

## IndieWeb
The IndieWeb movement is about owning your data. It encourages you to create and publish on your own site and optionally syndicate to third-party sites. Webmention is one of the core building blocks of this movement.

Learn more and get involved by visiting <https://indieweb.org>.

## Changelog
### v1.1.3 2017-03-27
- Fixed fatal error on install
- Improved validation of source, target, and vouch parameters
- Enabled sending webmentions to links that have been removed from a post
- Added hookable methods for image caching
- Fixed handling of HTTP 410 Gone responses

### v1.1.2 2016-04-11
- Important bug fix when parsing author name.

### v1.1.1 2016-04-10
- Updated packaged php-mf2 library to version 0.3.0
- Added config option to automatically monitor a page for approved vouch domains.
- Better authorship algorithm support.
- Fixed issues #2, #3

### v1.1.0 2016-02-25
Stable release
