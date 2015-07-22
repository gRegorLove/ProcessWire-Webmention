<?php

/**
 * ProcessWire WebmentionListInterface and WebmentionList
 *
 * WebmentionListInterface defines an interface for WebmentionLists.
 * WebmentionList provides the default implementation of this interface.
 *
 * Use of these is not required. These are just here to provide output for a FieldtypeWebmentions field.
 * Typically you would iterate through the field and generate your own output. But if you just need
 * something simple, or are testing, then this may fit your needs.
 *
 * @author Gregor Morrill, http://gregorlove.com
 * @see http://indiewebcamp.com/webmention
 */

/**
 * WebmentionListInterface defines an interface for WebmentionLists.
 */
interface WebmentionListInterface
{
	public function __construct(WebmentionArray $webmentions, $options = array());

	public function render();

	public function renderItem(WebmentionItem $webmention);
}

/**
 * WebmentionList provides the default implementation of the WebmentionListInterface interface.
 */
class WebmentionList extends Wire implements WebmentionListInterface
{

	/**
	 * Reference to WebmentionArray provided in constructor
	 */
	protected $webmentions = null;


	/**
	 * Default options that may be overridden from constructor
	 */
	protected $options = array(
		'headline' => '', 	// '<h3>Comments</h3>',
		'encoding' => 'UTF-8',
		'admin' => FALSE, // shows unapproved webmentions if true
	);


	/**
	 * Construct the WebmentionList
	 * @param WebmentionArray $webmentions
	 * @param array $options Options that may override those provided with the class (see WebmentionList::$options)
	 * @access public
	 */
	public function __construct(WebmentionArray $webmentions, $options = array())
	{
		$h3 = $this->_('h3'); // Headline tag
		$this->options['headline'] = "<$h3>" . $this->_('Comments') . "</$h3>"; // Header text

		$this->webmentions = $webmentions;
		$this->options = array_merge($this->options, $options);
	} # end method __construct()


	/**
	 * Rendering of webmentions for API demonstration and testing purposes (or feel free to use for production if suitable)
	 * @see WebmentionItem::render()
	 * @return string or blank if no webmentions
	 * @access public
	 */
	public function render()
	{
		$out = '';

		foreach ( $this->webmentions as $webmention )
		{

			if ( !$this->options['admin'] )
			{

				if ( $webmention->status != WebmentionItem::statusApproved )
				{
					continue;
				}

			}

			$out .= $this->renderItem($webmention);
		}

		if ( $out )
		{
			$out =
				"\n" . $this->options['headline'] .
				"\n<ul class='WebmentionList'>$out\n</ul><!--/WebmentionList-->";
		}

		return $out;
	} # end method render()


	/**
	 * Render the webmention
	 *
	 * This is the default rendering for development/testing/demonstration purposes
	 *
	 * It may be used for production, but only if it meets your needs already. Typically you'll want to render the webmentions
	 * using your own code in your templates.
	 *
	 * @see WebmentionArray::render()
	 * @param WebmentionItem $webmention
	 * @access public
	 * @return string
	 */
	public function renderItem(WebmentionItem $webmention)
	{
		$h_card = $this->getHCard($webmention);

		$text = htmlentities(trim($webmention->name), ENT_QUOTES, $this->options['encoding']);
		$text = str_replace("\n\n", "</p><p>", $text);
		$text = str_replace("\n", "<br />", $text);

		$via = parse_url($webmention->source_url, PHP_URL_HOST);
		$published = new DateTime($webmention->published);

		$output = <<< END
<li id="Webmention{$webmention->id}" class="mention p-comment h-cite">

	<div class="avatar"> {$h_card} </div>

	<div class="note">
		<p class="reply-context"> <strong><a href="{$webmention->author_url}">{$webmention->author_name}</a></strong> – <time class="dt-published" datetime="{$published->format('c')}" title="{$published->format('F j, Y g:ia T')} via {$via}"><a href="{$webmention->url}" class="u-url">{$published->format('F j, Y')}</a></time> </p>

		<p class="p-content p-name"> {$text} </p>
	</div>

</li>
END;

		return $output;
	}


	/**
	 * This method returns the h-card for a webmention
	 * @param array
	 * @access public
	 * @return
	 */
	public function getHCard(WebmentionItem $webmention)
	{
		$h_card = '';

		if ( $webmention->author_photo )
		{
			$h_card = sprintf('<a href="%s" class="p-author h-card"><img src="%s" alt="%s" title="%3$s" class="u-photo"></a>',
				$webmention->author_url,
				$webmention->author_photo,
				$webmention->author_name
			);
		}

		return $h_card;
	} # end method getHCard()

}
