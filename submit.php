 <form action="diff.php" method="post" style="display:inline" onSubmit="document.forms['form_2'].submit();"> 
                  <input type="hidden" name="cmd" value="_xclick" /> 
                  <input name="return" type="hidden" id="return" value="/cart/ordercompleted.php" /> 
                  <input type="hidden" name="business" value="sales@x.co.uk" /> 
                  <input type="hidden" name="item_name" value="Order #<?php echo $orderid; ?> " /> 
                  <input type="hidden" name="amount" value="<?php echo $total_price; ?>" /> 
                  <input type="hidden" name="currency_code" value="GBP" /> 
      <input type="hidden" name="shipping" value="0.00"> 
      <input type="hidden" name="no_shipping" value="2"> 
                  <input type="image" name="submit" src="/images/checkout.png" alt="Pay Now" /> 
                </form>
 <form id="form_2" action="builder.php" method="post"> 
                  <input type="hidden" name="cmd" value="_xclick" /> 
                  <input name="return" type="hidden" id="return" value="/cart/ordercompleted.php" /> 
                  <input type="hidden" name="business" value="sales@x.co.uk" /> 
                  <input type="hidden" name="item_name" value="Order #<?php echo $orderid; ?> " /> 
                  <input type="hidden" name="amount" value="<?php echo $total_price; ?>" /> 
                  <input type="hidden" name="currency_code" value="GBP" /> 
      <input type="hidden" name="shipping" value="0.00"> 
      <input type="hidden" name="no_shipping" value="2"> 
                  <input type="image" name="submit" src="/images/checkout.png" alt="Pay Now" /> 
                </form>