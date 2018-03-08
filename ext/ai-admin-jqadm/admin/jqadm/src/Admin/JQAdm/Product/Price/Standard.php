<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2017
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Product\Price;

sprintf( 'price' ); // for translation


/**
 * Default implementation of product price JQAdm client.
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Standard
	extends \Aimeos\Admin\JQAdm\Common\Admin\Factory\Base
	implements \Aimeos\Admin\JQAdm\Common\Admin\Factory\Iface
{
	/** admin/jqadm/product/price/standard/subparts
	 * List of JQAdm sub-clients rendered within the product price section
	 *
	 * The output of the frontend is composed of the code generated by the JQAdm
	 * clients. Each JQAdm client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain JQAdm clients themselves and therefore a
	 * hierarchical tree of JQAdm clients is composed. Each JQAdm client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the JQAdm code generated by the parent is printed, then
	 * the JQAdm code of its sub-clients. The order of the JQAdm sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  admin/jqadm/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  admin/jqadm/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural JQAdm, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2016.01
	 * @category Developer
	 */
	private $subPartPath = 'admin/jqadm/product/price/standard/subparts';
	private $subPartNames = [];


	/**
	 * Copies a resource
	 *
	 * @return string HTML output
	 */
	public function copy()
	{
		$view = $this->addViewData( $this->getView() );

		$view->priceData = $this->toArray( $view->item, true );
		$view->priceBody = '';

		foreach( $this->getSubClients() as $client ) {
			$view->priceBody .= $client->copy();
		}

		return $this->render( $view );
	}


	/**
	 * Creates a new resource
	 *
	 * @return string HTML output
	 */
	public function create()
	{
		$view = $this->addViewData( $this->getView() );
		$siteid = $this->getContext()->getLocale()->getSiteId();
		$data = $view->param( 'price', [] );

		foreach( $view->value( $data, 'product.lists.id', [] ) as $idx => $value ) {
			$data['product.lists.siteid'][$idx] = $siteid;
		}

		$view->priceData = $data;
		$view->priceBody = '';

		foreach( $this->getSubClients() as $client ) {
			$view->priceBody .= $client->create();
		}

		return $this->render( $view );
	}


	/**
	 * Deletes a resource
	 */
	public function delete()
	{
		parent::delete();

		$refIds = array_keys( $this->getView()->item->getRefItems( 'price' ) );
		\Aimeos\MShop\Factory::createManager( $this->getContext(), 'price' )->deleteItems( $refIds );
	}


	/**
	 * Returns a single resource
	 *
	 * @return string HTML output
	 */
	public function get()
	{
		$view = $this->addViewData( $this->getView() );

		$view->priceData = $this->toArray( $view->item );
		$view->priceBody = '';

		foreach( $this->getSubClients() as $client ) {
			$view->priceBody .= $client->get();
		}

		return $this->render( $view );
	}


	/**
	 * Saves the data
	 */
	public function save()
	{
		$context = $this->getContext();
		$view = $this->addViewData( $this->getView() );

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'product/lists' );
		$textManager = \Aimeos\MShop\Factory::createManager( $context, 'text' );

		$manager->begin();
		$textManager->begin();

		try
		{
			$this->fromArray( $view->item, $view->param( 'price', [] ) );
			$view->priceBody = '';

			foreach( $this->getSubClients() as $client ) {
				$view->priceBody .= $client->save();
			}

			$textManager->commit();
			$manager->commit();
			return;
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$error = array( 'product-item-price' => $context->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->errors = $view->get( 'errors', [] ) + $error;
		}
		catch( \Exception $e )
		{
			$error = array( 'product-item-price' => $e->getMessage() . ', ' . $e->getFile() . ':' . $e->getLine() );
			$view->errors = $view->get( 'errors', [] ) + $error;
		}

		$textManager->rollback();
		$manager->rollback();

		throw new \Aimeos\Admin\JQAdm\Exception();
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Admin\JQAdm\Iface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		/** admin/jqadm/product/price/decorators/excludes
		 * Excludes decorators added by the "common" option from the product JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "admin/jqadm/common/decorators/default" before they are wrapped
		 * around the JQAdm client.
		 *
		 *  admin/jqadm/product/price/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Admin\JQAdm\Common\Decorator\*") added via
		 * "admin/jqadm/common/decorators/default" to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/product/price/decorators/global
		 * @see admin/jqadm/product/price/decorators/local
		 */

		/** admin/jqadm/product/price/decorators/global
		 * Adds a list of globally available decorators only to the product JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Admin\JQAdm\Common\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/product/price/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Admin\JQAdm\Common\Decorator\Decorator1" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/product/price/decorators/excludes
		 * @see admin/jqadm/product/price/decorators/local
		 */

		/** admin/jqadm/product/price/decorators/local
		 * Adds a list of local decorators only to the product JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Admin\JQAdm\Product\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/product/price/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Admin\JQAdm\Product\Decorator\Decorator2" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/product/price/decorators/excludes
		 * @see admin/jqadm/product/price/decorators/global
		 */
		return $this->createSubClient( 'product/price/' . $type, $name );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of JQAdm client names
	 */
	protected function getSubClientNames()
	{
		return $this->getContext()->getConfig()->get( $this->subPartPath, $this->subPartNames );
	}


	/**
	 * Adds the required data used in the price template
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MW\View\Iface View object with assigned parameters
	 */
	protected function addViewData( \Aimeos\MW\View\Iface $view )
	{
		$context = $this->getContext();
		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price/type' );
		$currencyManager = \Aimeos\MShop\Factory::createManager( $context, 'locale/currency' );

		$search = $priceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'price.type.domain', 'product' ) );
		$search->setSortations( array( $search->sort( '+', 'price.type.label' ) ) );

		$currencyItems = $currencyManager->searchItems( $currencyManager->createSearch( true ) );

		if( $currencyItems === [] ) {
			throw new \Aimeos\Admin\JQAdm\Exception( 'No currencies available. Please enable at least one currency' );
		}

		$view->priceTypes = $priceManager->searchItems( $search );
		$view->priceCurrencies = $currencyItems;

		return $view;
	}


	/**
	 * Creates new and updates existing items using the data array
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item object without referenced domain items
	 * @param string[] $data Data array
	 */
	protected function fromArray( \Aimeos\MShop\Product\Item\Iface $item, array $data )
	{
		$context = $this->getContext();

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, 'product/lists' );
		$listTypeManager = \Aimeos\MShop\Factory::createManager( $context, 'product/lists/type' );

		$listIds = (array) $this->getValue( $data, 'product.lists.id', [] );
		$listItems = $manager->getItem( $item->getId(), array( 'price' ) )->getListItems( 'price' );


		$listItem = $listManager->createItem();
		$listItem->setParentId( $item->getId() );
		$listItem->setTypeId( $listTypeManager->findItem( 'default', [], 'price' )->getId() );
		$listItem->setDomain( 'price' );
		$listItem->setStatus( 1 );

		$newItem = $priceManager->createItem();
		$newItem->setDomain( 'product' );
		$newItem->setStatus( 1 );


		foreach( $listIds as $idx => $listid )
		{
			if( !isset( $listItems[$listid] ) )
			{
				$priceItem = clone $newItem;

				$litem = $listItem;
				$litem->setId( null );
			}
			else
			{
				$litem = $listItems[$listid];
				$priceItem = $litem->getRefItem();
			}

			$priceItem->setTypeId( $this->getValue( $data, 'price.typeid/' . $idx ) );
			$priceItem->setCurrencyId( $this->getValue( $data, 'price.currencyid/' . $idx ) );
			$priceItem->setQuantity( $this->getValue( $data, 'price.quantity/' . $idx, 1 ) );
			$priceItem->setValue( $this->getValue( $data, 'price.value/' . $idx, '0.00' ) );
			$priceItem->setCosts( $this->getValue( $data, 'price.costs/' . $idx, '0.00' ) );
			$priceItem->setRebate( $this->getValue( $data, 'price.rebate/' . $idx, '0.00' ) );
			$priceItem->setTaxRate( $this->getValue( $data, 'price.taxrate/' . $idx, '0.00' ) );

			$label = $priceItem->getQuantity() . ' ~ ' . $priceItem->getValue() . ' ' . $priceItem->getCurrencyId();
			$priceItem->setLabel( $item->getLabel() . ' :: ' . $label );

			$priceItem = $priceManager->saveItem( $priceItem );

			$litem->setPosition( $idx );
			$litem->setRefId( $priceItem->getId() );

			$listManager->saveItem( $litem, false );
		}


		$rmIds = [];
		$rmListIds = array_diff( array_keys( $listItems ), $listIds );

		foreach( $rmListIds as $id ) {
			$rmIds[] = $listItems[$id]->getRefId();
		}

		$listManager->deleteItems( $rmListIds  );
		$priceManager->deleteItems( $rmIds  );
	}


	/**
	 * Constructs the data array for the view from the given item
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item object including referenced domain items
	 * @param boolean $copy True if items should be copied, false if not
	 * @return string[] Multi-dimensional associative list of item data
	 */
	protected function toArray( \Aimeos\MShop\Product\Item\Iface $item, $copy = false )
	{
		$locale = $this->getContext()->getLocale();
		$siteId = $locale->getSiteId();
		$data = [];

		foreach( $item->getListItems( 'price' ) as $id => $listItem )
		{
			if( ( $refItem = $listItem->getRefItem() ) === null ) {
				continue;
			}

			$list = $listItem->toArray( true );

			if( $copy === true )
			{
				$list['product.lists.siteid'] = $siteId;
				$list['product.lists.id'] = '';
			}

			foreach( $list as $key => $value ) {
				$data[$key][] = $value;
			}

			foreach( $refItem->toArray( true ) as $key => $value ) {
				$data[$key][] = $value;
			}
		}

		return $data;
	}


	/**
	 * Returns the rendered template including the view data
	 *
	 * @param \Aimeos\MW\View\Iface $view View object with data assigned
	 * @return string HTML output
	 */
	protected function render( \Aimeos\MW\View\Iface $view )
	{
		/** admin/jqadm/product/price/template-item
		 * Relative path to the HTML body template of the price subpart for products.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in admin/jqadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the HTML code
		 * @since 2016.04
		 * @category Developer
		 */
		$tplconf = 'admin/jqadm/product/price/template-item';
		$default = 'product/item-price-default.php';

		return $view->render( $view->config( $tplconf, $default ) );
	}
}