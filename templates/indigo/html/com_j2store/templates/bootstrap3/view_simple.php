<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 *
 * Bootstrap 2 layout of product detail
 */
// No direct access
defined('_JEXEC') or die;

// get menu id
$input = JFactory::getApplication()->input;
$item_id = $input->get('Itemid', NULL, 'INT');

// Generate Product URL
$product_url = JRoute::_('index.php?option=com_j2store&view=products&task=view&id='.$this->product->j2store_product_id);

// Get Host and generate final URL
$root = JURI::base();
$root = new JURI($root);
$product_url = $root->getScheme() . '://' . $root->getHost() . $product_url;

?>
<div itemscope itemtype="https://schema.org/Product" class="product-<?php echo $this->product->j2store_product_id; ?> <?php echo $this->product->product_type; ?>-product">
	<div class="row">
		<div class="col-sm-6">
			<?php $images = $this->loadTemplate('images');
			J2Store::plugin()->event('BeforeDisplayImages', array(&$images, $this, 'com_j2store.products.view.bootstrap'));
			echo $images;
			?>
		</div>

		<div class="col-sm-6">
			<?php echo $this->loadTemplate('title'); ?>
			<?php if(isset($this->product->source->event->afterDisplayTitle)) : ?>
				<?php echo $this->product->source->event->afterDisplayTitle; ?>
			<?php endif;?>

			<div class="price-sku-brand-container row">
				<div class="col-sm-6">
				<?php echo $this->loadTemplate('price'); ?>
				</div>

				<div class="col-sm-6">
				<?php if(isset($this->product->source->event->beforeDisplayContent)) : ?>
					<?php echo $this->product->source->event->beforeDisplayContent; ?>
				<?php endif;?>
					
					<?php echo $this->loadTemplate('brand'); ?>
					<?php if($this->params->get('item_show_product_stock', 1) && J2Store::product()->managing_stock($this->product->variant)) : ?>
						<?php echo $this->loadTemplate('stock'); ?>
					<?php endif; ?>
				</div>
			</div>

			<?php if( J2Store::product()->canShowCart($this->params) ): ?>
			<form action="<?php echo $this->product->cart_form_action; ?>"
					method="post" class="j2store-addtocart-form"
					id="j2store-addtocart-form-<?php echo $this->product->j2store_product_id; ?>"
					name="j2store-addtocart-form-<?php echo $this->product->j2store_product_id; ?>"
					data-product_id="<?php echo $this->product->j2store_product_id; ?>"
					data-product_type="<?php echo $this->product->product_type; ?>"
					enctype="multipart/form-data">

				<?php echo $this->loadTemplate('options'); ?>
				<?php echo $this->loadTemplate('cart'); ?>

			</form>
			<?php endif; ?>

			<?php if($this->params->get('item_use_tabs', 1)): ?>
				<div class="j2store-product-details-tab">
					<?php echo $this->loadTemplate('tabs'); ?>
				</div>
			<?php endif; ?>

			<div class="row j2store-product-details-footer">
				<div class="col-sm-6">
					<?php echo $this->loadTemplate('sku'); ?>
					<?php echo $this->loadTemplate('brand'); ?>
				</div>
				<div class="col-sm-6">
					<div class="j2-store-social-share">
						<span class="share-title"><?php echo JText::_('J2STORE_PRODUCT_DETAILS_TAB_SHARE');?></span>
						<ul>
							<li>
								<a class="facebook" onClick="window.open('http://www.facebook.com/sharer.php?u=<?php echo $product_url; ?>','Facebook','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="http://www.facebook.com/sharer.php?u=<?php echo $product_url; ?>" title="<?php echo JText::_('HELIX_ULTIMATE_SHARE_FACEBOOK'); ?>">
									<span class="fa fa-facebook"></span>
								</a>
							</li>
							<li>
								<a class="twitter" title="<?php echo JText::_('HELIX_ULTIMATE_SHARE_TWITTER'); ?>" onClick="window.open('http://twitter.com/share?url=<?php echo $product_url; ?>&amp;text=<?php echo str_replace(" ", "%20", $displayData->title); ?>','Twitter share','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="http://twitter.com/share?url=<?php echo $product_url; ?>&amp;text=<?php echo str_replace(" ", "%20", $displayData->title); ?>">
									<span class="fa fa-twitter"></span>
								</a>
							</li>
							<li>
								<a class="linkedin" title="<?php echo JText::_('HELIX_ULTIMATE_SHARE_LINKEDIN'); ?>" onClick="window.open('http://www.linkedin.com/shareArticle?mini=true&url=<?php echo $product_url; ?>','Linkedin','width=585,height=666,left='+(screen.availWidth/2-292)+',top='+(screen.availHeight/2-333)+''); return false;" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo $product_url; ?>" >
									<span class="fa fa-linkedin-square"></span>
								</a>
							</li>
						</ul>
					</div> <!-- /.j2-store-social-share -->
				</div> <!--  /. col-sm-6 -->
			</div> <!-- /. lj2store-product-details-footer -->
		</div>
	</div>
	
	<?php if(!$this->params->get('item_use_tabs')): ?>
		<?php echo $this->loadTemplate('notabs'); ?>
	<?php endif; ?>

	<?php if(isset($this->product->source->event->afterDisplayContent)) : ?>
		<?php echo $this->product->source->event->afterDisplayContent; ?>
	<?php endif;?>
</div>
 
<?php if($this->product->product_long_desc) { ?>
	<div class="j2store-details-ldesc">
		<h4><?php echo JText::_('J2STORE_PRODUCT_LARGE_DESCRIPTION');?></h4>
		<?php echo $this->loadTemplate('ldesc'); ?>
	</div>
<?php } ?>

<?php if($this->params->get('item_show_product_upsells', 0) && count($this->up_sells)): ?>
	<?php echo $this->loadTemplate('upsells'); ?>
<?php endif;?>

<?php if($this->params->get('item_show_product_cross_sells', 0) && count($this->cross_sells)): ?>
	<?php echo $this->loadTemplate('crosssells'); ?>
<?php endif;?>

