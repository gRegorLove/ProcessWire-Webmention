<?php
/**
 * Webmention endpoint template file
 *
 * This provides a user-friendly form in case someone comes across the webmention
 * endpoint in a browser. The contents of this endpoint template can be anything
 * (including blank), as long as it does not return HTTP 404.
 * @author Gregor Morrill, https://gregorlove.com
 */

	# if: not a POST
	if ( !count($input->post) )
	{
?>

		<h1> Send a Webmention </h1>

		<p> Webmention is a way to notify me that you’ve linked to one of my pages. </p>

		<form method="post" action="<?=$page->httpUrl;?>">

			<p> <label for="i_source">Your URL:</label> <input type="url" name="source" id="i_source" placeholder="http://" required /> </p>

			<p> <label for="i_target">Links to my URL:</label> <input type="url" name="target" id="i_target" placeholder="http://" required /> </p>

			<p> <input type="submit" value="Send" /> </p>

		</form>

		<p> Read more about <a href="https://webmention.net">Webmention</a> and the <a href="https://indieweb.org">IndieWeb</a>. </p>

<?php
	}

