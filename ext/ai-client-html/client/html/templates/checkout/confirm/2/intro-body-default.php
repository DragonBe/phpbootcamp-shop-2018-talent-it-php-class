<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'checkout/confirm/intro' ); ?>
<div class="checkout-confirm-intro">
	<p class="note"><?php echo nl2br( $enc->html( $this->translate( 'client', 'Unfortunately, the payment for your order was refused.
Do you wish to retry?' ), $enc::TRUST ) ); ?></p>
<?php echo $this->get( 'introBody' ); ?>
</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'checkout/confirm/intro' ); ?>
