<?php

/**
 * ProcessWire FieldtypeWebmentions > WebmentionItem
 *
 * @author Gregor Morrill, http://gregorlove.com
 * @see http://indiewebcamp.com/webmention
 */

class WebmentionItem extends WireData
{

	/**
	 * Action: process
	 */
	const actionProcess = 1;


	/**
	 * Action: none
	 */
	const actionNone = 0;


	/**
	 * Action: delete
	 */
	const actionDelete = -1;


	/**
	 * Status for Webmention that has errors
	 */
	const statusError = -1;


	/**
	 * Status for Webmention pending review
	 */
	const statusPending = 0;


	/**
	 * Status for Webmention that's been approved
	 */
	const statusApproved = 1;


	/**
	 * Visibility for Webmention that's private
	 */
	const visibilityPrivate = 0;


	/**
	 * Visibility for Webmention that's public
	 */
	const visibilityPublic = 1;


	/**
	 * Previous Webmention status, when it has been changed
	 */
	protected $prevStatus;


	/**
	 * Construct a WebmentionItem and set defaults
	 * @access public
	 */
	public function __construct()
	{
		$this->set('id', 0);
		$this->set('md5key', '');
		$this->set('source_url', '');
		$this->set('target_url', '');
		$this->set('vouch_url', '');
		$this->set('type', 'mention');
		$this->set('is_like', 0);
		$this->set('is_repost', 0);
		$this->set('is_rsvp', 0);
		$this->set('content', '');
		$this->set('url', '');
		$this->set('name', '');
		$this->set('author_name', '');
		$this->set('author_photo', '');
		$this->set('author_logo', '');
		$this->set('author_url', '');
		$this->set('published', NULL);
		$this->set('published_offset', 0);
		$this->set('updated', NULL);
		$this->set('updated_offset', 0);
		$this->set('status', self::statusPending);
		$this->set('visibility', self::visibilityPublic);
		$this->set('sort', 0);
		$this->set('microformats', '');
		$this->set('created', NULL);
		$this->prevStatus = self::statusPending;
	} # end method __construct()


	/**
	 * Set a webmention key
	 * @param string $key
	 * @param string $value
	 * @access public
	 */
	public function set($key, $value)
	{

		switch ( $key )
		{
			case 'id':
			case 'status':
			case 'pages_id':
			case 'is_like':
			case 'is_repost':
			case 'is_rsvp':
				$value = (int) $value;
			break;

			case 'source_url':
			case 'target_url':
			case 'vouch_url':
			case 'url':
			case 'author_photo':
			case 'author_logo':
			case 'author_url':
				$value = wire('sanitizer')->url($value, array('allowRelative' => FALSE));
			break;
		}

		if ( $key == 'status' )
		{
			$this->prevStatus = $this->status;
		}

		return parent::set($key, $value);
	} # end method set()


	/**
	 * Set the Webmention page
	 * @param Page $page
	 * @access public
	 */
	public function setPage(Page $page)
	{
		$this->page = $page;
	} # end method setPage()


	/**
	 * Set the Webmention field
	 * @param Field $field
	 * @access public
	 */
	public function setField(Field $field)
	{
		$this->field = $field;
	} # end method setField()


	/**
	 * Get the Webmention page
	 * @access public
	 */
	public function getPage()
	{
		return $this->page;
	} # end method getPage()


	/**
	 * Get the Webmention field
	 * @access public
	 */
	public function getField()
	{
		return $this->field;
	} # end method getField()


	/**
	 * This method returns the item key
	 * @access public
	 * @return int
	 */
	public function getItemKey()
	{
		return $this->id;
	} # end method getItemKey()


	/**
	 * String value of a WebmentionItem is its database ID
	 * @access public
	 * @return string
	 */
	public function __toString()
	{
		return "{$this->id}";
	} # end method __toString()


	/**
	 * Returns true if the webmention is approved and thus appearing on the site
	 * @access public
	 * @return bool
	 */
	public function isApproved()
	{
		return $this->status >= self::statusApproved;
	} # end method isApproved()

}
