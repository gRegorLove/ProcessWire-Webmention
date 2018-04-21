<?php namespace ProcessWire;

/**
 * ProcessWire FieldtypeWebmentions > WebmentionArray
 *
 * @author Gregor Morrill, https://gregorlove.com
 * @see https://webmention.net/
 */

class WebmentionArray extends PaginatedArray implements WirePaginatable
{
	/**
	 * Page that owns these webmentions, required to use the renderForm() or getWebmentionForm() methods.
	 */
	protected $page = NULL;


	/**
	 * Field object associated with this WebmentionArray
	 */
	protected $field = NULL;


	/**
	 * This method handles making a blank WebmentionItem
	 * @access public
	 * @return WebmentionItem
	 */
	public function makeBlankItem()
	{
		return new WebmentionItem();
	} # end method makeBlankItem()


	/**
	 * Per the WireArray interface, is the item a WebmentionItem
	 * @param $item
	 * @access public
	 */
	public function isValidItem($item)
	{

		if ( $item instanceof WebmentionItem )
		{

			if ( $this->page )
			{
				$item->setPage($this->page);
			}

			if ( $this->field )
			{
				$item->setField($this->field);
			}

			return TRUE;
		}
		else
		{
			return FALSE;
		}

	} # end method isValidItem()


	/**
	 * Provides the default rendering of a webmention list, which may or may not be what you want
	 * @param array $options
	 * @access public
	 * @see WebmentionList class and override it to serve your needs
	 */
	public function render(array $options = array())
	{
		$defaultOptions = array();
		$options = array_merge($defaultOptions, $options);
		$webmentionList = $this->getWebmentionList($options);

		return $webmentionList->render();
	} # end method render()


	/**
	 * Provides the default rendering of a webmention form, which may or may not be what you want
	 * @param array $options
	 * @access public
	 * @see WebmentionForm class and override it to serve your needs
	 */
	public function renderForm(array $options = array())
	{
		$form = $this->getWebmentionForm($options);
		return $form->render();
	} # end method renderForm()


	/**
	 * Return instance of WebmentionList object
	 * @param array $options
	 * @access public
	 */
	public function getWebmentionList(array $options = array())
	{
		return new WebmentionList($this, $options);
	} # end method getWebmentionList()


	/**
	 * Return instance of WebmentionForm object
	 * @param array $options
	 * @access public
	 */
	public function getWebmentionForm(array $options = array())
	{

		if ( !$this->page )
		{
			throw new WireException('You must set a page to this WebmentionArray before using it i.e. $ca->setPage($page)');
		}

		return new WebmentionForm($this->page, $this, $options);
	} # end method getWebmentionForm()


	/**
	 * Set the page that these webmentions are on
	 * @param Page $page
	 * @access public
	 */
	public function setPage(Page $page)
	{
		$this->page = $page;
	} # end method setPage()


	/**
	 * Set the Field that these webmentions are on
	 * @param Field $field
	 * @access public
	 */
	public function setField(Field $field)
	{
		$this->field = $field;
	} # end method setField()


	/**
	 * Get the page that these comments are on
	 * @access public
	 */
	public function getPage()
	{
		return $this->page;
	}


	/**
	 * Get the Field that these comments are on
	 * @access public
	 */
	public function getField()
	{
		return $this->field;
	}

}

