<?php namespace ProcessWire;
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
 * @author Gregor Morrill, https://gregorlove.com
 * @see https://webmention.net/
 */

use DateTime;


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
	 * @access protected
	 */
	protected $webmentions = null;


	/**
	 * Default options that may be overridden from constructor
	 * @access protected
	 */
	protected $options = array(
		'headline' => '',
		'encoding' => 'UTF-8',
		'admin' => false,
	);


	/**
	 * Construct the WebmentionList
	 * @param WebmentionArray $webmentions
	 * @param array $options Options that may override those provided with the class (see WebmentionList::$options)
	 * @access public
	 */
	public function __construct(WebmentionArray $webmentions, $options = array())
	{
		$h3 = $this->_('h3');
		$this->options['headline'] = "<$h3>" . $this->_('Comments') . "</$h3>";

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
		$output = '';

		foreach ( $this->webmentions as $webmention )
		{

			if ( !$this->options['admin'] )
			{
				$is_not_approved = ($webmention->status != WebmentionItem::statusApproved);
				$is_not_public = ($webmention->visibility != WebmentionItem::visibilityPublic);

				if ( $is_not_approved || $is_not_public )
				{
					continue;
				}

			}

			$output .= $this->renderItem($webmention);
		}

		if ( $output )
		{
			$output = sprintf('%s <ul class="WebmentionList"> %s </ul>', $this->options['headline'], $output);
		}

		return $output;
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

		$author_link = sprintf('<a href="%s">%s</a>',
			$this->sanitizer->url($webmention->author_url),
			htmlspecialchars($webmention->author_name)
		);

		# if: webmention is a 'like'
		if ( $webmention->is_like )
		{
			$webmention_content = __('likes this');
		}
		# else: show webmention content
		else
		{
			$webmention_content = ( $webmention->content_plain ) ? htmlspecialchars($webmention->content_plain) : htmlspecialchars($webmention->name);
		} # end if

		$published = new DateTime($webmention->updated);
		$via = parse_url($webmention->url, PHP_URL_HOST);

		$output = <<< END
<li id="Webmention{$webmention->id}" class="mention u-comment h-cite">

	<div class="avatar"> {$h_card} </div>

	<div class="note">
		<p class="reply-context"> <strong>{$author_link}</strong> – <time class="dt-published" datetime="{$published->format('c')}" title="{$published->format('F j, Y g:ia T')} via {$via}"><a href="{$webmention->url}" class="u-url">{$published->format('F j, Y')}</a></time> </p>

		<p class="p-content p-name"> {$webmention_content} </p>
	</div>

</li>
END;

		return $output;
	}


	/**
	 * This method returns the h-card for a webmention
	 * @param WebmentionItem $webmention
	 * @access public
	 * @return
	 */
	public function getHCard(WebmentionItem $webmention)
	{
		$h_card = '';

		$h_card = sprintf('<a href="%s" class="p-author h-card"><img src="%s" alt="%s" title="%3$s" class="u-photo" /></a> ',
			$this->sanitizer->url($webmention->author_url),
			$this->sanitizer->url($webmention->author_photo),
			strip_tags($webmention->author_name)
		);

		return $h_card;
	} # end method getHCard()

}

