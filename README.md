# ProcessWire Webmention Module

[ProcessWire](http://processwire.com) module to send and receive webmentions.

[Webmention](http://webmention.org) is a simple way to automatically notify any URL when you link to it on your site. From the receiver's perpective, it is a way to request notification when other sites link to it.

Version 1.0.0 is a stable beta that covers webmention sending, receiving, parsing, and display. An easy admin interface for received webmentions is under development, as well as support for the Webmention Vouch extension.

## Features
* Webmention endpoint discovery
* Automatically send webmentions asynchronously
* Automatically receive webmentions
* Process webmentions to extract microformats

## Requirements
* php-mf2 and php-mf2-cleaner libraries; bundled with this package and may optionally be updated using Composer.
* This module hooks into the LazyCron module.

## Installation
Installing the core module named "Webmention" will automatically install the Fieldtype and Inputfield modules included in this package.

This module will attempt to add a template and page named "Webmention Endpoint" if the template does not exist already. The default location of this endpoint is http://example.com/webmention-endpoint

After installing the module, create a new field of type "Webmentions" and add it to the template(s) you want to be able to support webmentions.

## Sending Webmentions
When creating or editing a page that has the Webmentions field, a checkbox "Send Webmentions" will appear at the bottom. Check this box and any URLs linked in the page body will be queued up for sending webmentions. Note: you should only check the "Send Webmentions" box if the page status is "published."

## Receiving Webmentions
This module enables receiving webmentions on any pages that have have "Webmentions" field, by adding the webmention endpoint as an HTTP Link header. If you would like to specify a custom webmention endpoint URL, you can do so in the admin area, Modules > Webmention.

## Processing Webmentions (beta)
Currently no webmentions are automatically processed. You will need to browse to the page in the backend, click "Edit," and scroll to the Webmentions field. There is a dropdown for "Visibility" and "Action" beside each webmention. Select "Process" to parse the webmention for microformats.

A better interface for viewing/processing all received webmentions in one place is under development.

## Displaying Webmentions (beta)
Within your template file, you can use `$page->Webmentions->render()` [where "Webmentions" is the name you used creating the field] to display a list of approved webmentions. As with the Comments Fieldtype, you can also [generate your own output](https://processwire.com/api/fieldtypes/comments/).

The display functionality is also under development.

## Logs
This module writes two logs: webmentions-sent and webmentions-received.

## Vouch
The [Vouch](http://indiewebcamp.com/Vouch) anti-spam extension is still under development.

## IndieWeb
The IndieWeb movement is about owning your data. It encourages you to create and publish on your own site and optionally syndicate to third-party sites. Webmention is one of the core building blocks of this movement.

Learn more and get involved by visiting <http://indiewebcamp.com>.
