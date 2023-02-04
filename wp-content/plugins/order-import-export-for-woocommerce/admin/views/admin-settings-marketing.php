<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<div class="wt-iew-tab-content" data-id="<?php echo $target_id;?>">
    <div class="wt-ier-wrapper">
    <h2 class="wt-ier-page-title"><?php _e('One stop solution for all your import-export needs.');?></h2>
    <p class="wt-ier-subp"><?php _e('Upgrade to the premium version and get access to the advanced features with premium support.');?></p>
    <div class="wt-ier-row">
      <div class="wt-ier-col-12 wt-ier-col-lg-4 wt-ier-lg-4 wt-ier-mb-lg-0">
        <div class="wt-ier-p-5 wt-ier-box-wrapper wt-ier-box-highlight">
          <img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/product-ie.svg" class="wt-ier-thumbnails">
          <h3 class="wt-ier-plugin-name"><?php _e('PRODUCT IMPORT EXPORT PLUGIN FOR WOOCOMMERCE');?></h3>
          <p class="wt-ier-p"><?php _e('Imports and exports all product types and reviews. Supports both CSV and XML file formats.');?></p>
          <div class="wt-ier-d-sm-flex wt-ier-btn-group">
            <a href="https://www.webtoffee.com/product/product-import-export-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Product_Import_Export&utm_content=<?php echo WT_O_IEW_VERSION; ?>" target="_blank" class="wt-ier-primary-btn wt-ier-btn"><?php _e('Get Premium');?></a>
            <a href="" class="wt-ier-secondary-btn wt-ier-btn" data-toggle="modal" data-target="#wt-ier-comparison-modal-product"><?php _e('Compare with basic');?></a>
          </div>
        </div>
      </div>
      <div class="wt-ier-col-12 wt-ier-col-lg-4 wt-ier-lg-4 wt-ier-mb-lg-0">
        <div class="wt-ier-p-5 wt-ier-box-wrapper wt-ier-box-highlight">
          <img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/customer-ie.svg" class="wt-ier-thumbnails">
          <h3 class="wt-ier-plugin-name"><?php _e('WORDPRESS USERS & WOOCOMMERCE CUSTOMERS IMPORT EXPORT');?></h3>
          <p class="wt-ier-p"><?php _e('Import and export all your WordPress User and WooCommerce Customer data in CSV/XML file formats.');?></p>
          <div class="wt-ier-d-sm-flex wt-ier-btn-group">
            <a href="https://www.webtoffee.com/product/wordpress-users-woocommerce-customers-import-export/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=User_Import_Export&utm_content=<?php echo WT_O_IEW_VERSION; ?>" class="wt-ier-primary-btn wt-ier-btn" target="_blank"><?php _e('Get Premium');?></a>
            <a href="" class="wt-ier-secondary-btn wt-ier-btn" data-toggle="modal" data-target="#wt-ier-comparison-modal-customer"><?php _e('Compare with basic');?></a>
          </div>
        </div>
      </div>
      <div class="wt-ier-col-12 wt-ier-col-lg-4 wt-ier-lg-4 wt-ier-mb-lg-0">
        <div class="wt-ier-p-5 wt-ier-box-wrapper wt-ier-box-highlight">
          <img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/order-ie.svg" class="wt-ier-thumbnails">
          <h3 class="wt-ier-plugin-name"><?php _e('ORDER, COUPON, SUBSCRIPTION EXPORT IMPORT FOR WOOCOMMERCE');?></h3>
          <p class="wt-ier-p"><?php _e('Export or Import WooCommerce orders, Coupons and Subscriptions.');?></p>
          <div class="wt-ier-d-sm-flex wt-ier-btn-group">
            <a  href="https://www.webtoffee.com/product/order-import-export-plugin-for-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Order_Import_Export&utm_content=<?php echo WT_O_IEW_VERSION; ?>" class="wt-ier-primary-btn wt-ier-btn" target="_blank"><?php _e('Get Premium');?></a>
            <a href="" class="wt-ier-secondary-btn wt-ier-btn" data-toggle="modal" data-target="#wt-ier-comparison-modal-order"><?php _e('Compare with basic');?></a>
          </div>
        </div>
      </div>
    </div>
    <!--------product imp-exp comparison table --------->
    <div id="wt-ier-comparison-modal-product" class="wt-ier-modal">
      <div class="wt-ier-modal-content">
        <div class="wt-ier-resposive-table">
          <table class="wt-ier-table">
            <thead>
              <tr class="wt-ier-top-tr">
                <td></td>
                <td colspan="3"><span class="wt-ier-close">&times;</span></td>
              </tr>
              <tr>
                <th><?php _e('Features');?></th>
                <th><?php _e('Free');?></th>
                <th><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/pre-icon.svg" class="wt-ier-pre-icon"><?php _e('Premium');?></th>
                <th><?php _e('Import Export Suite');?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php _e('Import and export');?></td>
                <td>
					<ul>				
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Products');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Reviews');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Categories');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Tags');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Orders');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Coupons');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Subscriptions');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('WordPress Users');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('WooCommerce Customers');?></li>					  
                    </ul>
				</td>
                <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Products');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Reviews');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Categories');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Tags');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Orders');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Coupons');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Subscriptions');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('WordPress Users');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('WooCommerce Customers');?></li>					  
                    </ul>
				</td>
                  <td>
					<ul>	                						
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Products');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Reviews');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Categories');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Tags');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Orders');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Coupons');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Subscriptions');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WordPress Users');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WooCommerce Customers');?></li>					  
                    </ul>
                  </td>
                </tr>
                <tr>
                  <td><?php _e('Supported product types');?></td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Simple Products');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Grouped Products');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('External/Affiliate Products');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Variable product');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Simple subscription');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Variable subscription');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('WooCommerce Bookings');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Custom Products');?></li>
                    </ul>
				</td>
                <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Simple Products');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Grouped Products');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('External/Affiliate Products');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Variable product');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Simple subscription');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Variable subscription');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WooCommerce Bookings');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Custom Products');?></li>
                    </ul>
				</td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Simple Products');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Grouped Products');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('External/Affiliate Products');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Variable product');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Simple subscription');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Variable subscription');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WooCommerce Bookings');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Custom Products');?></li>
                    </ul>
                  </td>
                </tr>
				<tr>
                  <td><?php _e('Supported file types');?></td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('CSV');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('XML');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('CSV');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('XML');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('CSV');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('XML');?></li>
                    </ul>
				  </td>
                </tr>
				<tr>
                  <td><?php _e('Supported import methods');?></td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Local');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('FTP/SFTP');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('From URL');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('From existing files');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Local');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('FTP/SFTP');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From URL');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From existing files');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Local');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('FTP/SFTP');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From URL');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From existing files');?></li>
                    </ul>
				  </td>
                </tr>
                <tr>
                  <td><?php _e('Automatic scheduled import & export');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Import & export custom fields ( metadata )');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Import & export hidden meta');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Bulk delete products');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
                <tr>
                  <td><?php _e('Option to import products as new item during post id conflicts');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Export to FTP/SFTP');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Set CSV delimiter for export');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Export images as a separate zip');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Convert shortcodes to HTML on export');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Custom export filename');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!--------customer imp-exp comparison table --------->
      <div id="wt-ier-comparison-modal-customer" class="wt-ier-modal">
        <div class="wt-ier-modal-content">
          <div class="wt-ier-resposive-table">
            <table class="wt-ier-table">
            <thead>
              <tr class="wt-ier-top-tr">
                <td></td>
                <td colspan="3"><span class="wt-ier-close">&times;</span></td>
              </tr>
              <tr>
                <th><?php _e('Features');?></th>
                <th><?php _e('Free');?></th>
                <th><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/pre-icon.svg" class="wt-ier-pre-icon"><?php _e('Premium');?></th>
                <th><?php _e('Import Export Suite');?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php _e('Import and export');?></td>
                <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WordPress Users');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WooCommerce Customers');?></li>						
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Products');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Reviews');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Categories');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Tags');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Orders');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Coupons');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Subscriptions');?></li>                      
                    </ul>
				</td>
                <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WordPress Users');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WooCommerce Customers');?></li>						
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Products');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Reviews');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Categories');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Tags');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Orders');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Coupons');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Subscriptions');?></li>                     
                    </ul>
				</td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WordPress Users');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WooCommerce Customers');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Products');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Reviews');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Categories');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Tags');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Orders');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Coupons');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Subscriptions');?></li>                      
                    </ul>
                  </td>
                </tr>          
				<tr>
                  <td><?php _e('Supported file types');?></td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('CSV');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('XML');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('CSV');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('XML');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('CSV');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('XML');?></li>
                    </ul>
				  </td>
                </tr>
				<tr>
                  <td><?php _e('Supported import methods');?></td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Local');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('FTP/SFTP');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('From URL');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('From existing files');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Local');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('FTP/SFTP');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From URL');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From existing files');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Local');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('FTP/SFTP');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From URL');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From existing files');?></li>
                    </ul>
				  </td>
                </tr>
                <tr>
                  <td><?php _e('Automatic scheduled import & export');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Import & export custom fields ( metadata )');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Import & export hidden meta');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
                <tr>
                  <td><?php _e('Option to email new users on import');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Export to FTP/SFTP');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Set CSV delimiter for export');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Custom export filename');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!--------order imp-exp comparison table --------->
      <div id="wt-ier-comparison-modal-order" class="wt-ier-modal">
        <div class="wt-ier-modal-content">
          <div class="wt-ier-resposive-table">
            <table class="wt-ier-table">
            <thead>
              <tr class="wt-ier-top-tr">
                <td></td>
                <td colspan="3"><span class="wt-ier-close">&times;</span></td>
              </tr>
              <tr>
                <th><?php _e('Features');?></th>
                <th><?php _e('Free');?></th>
                <th><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/pre-icon.svg" class="wt-ier-pre-icon"><?php _e('Premium');?></th>
                <th><?php _e('Import Export Suite');?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php _e('Import and export');?></td>
                <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Orders');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Coupons');?></li>	
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Subscriptions');?></li>					  
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Products');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Reviews');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Categories');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Tags');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('WordPress Users');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('WooCommerce Customers');?></li>
                    </ul>
				</td>
                <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Orders');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Coupons');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Subscriptions');?></li>						
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Products');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Reviews');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Categories');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('Product Tags');?></li>                      
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('WordPress Users');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('WooCommerce Customers');?></li>
                    </ul>
				</td>
                  <td>
					<ul>
						<li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Orders');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Coupons');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Subscriptions');?></li>					 
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Products');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Reviews');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Categories');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Product Tags');?></li> 
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WordPress Users');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('WooCommerce Customers');?></li>
                    </ul>
                  </td>
                </tr>          
				<tr>
                  <td><?php _e('Supported file types');?></td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('CSV');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('XML');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('CSV');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('XML');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('CSV');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('XML');?></li>
                    </ul>
				  </td>
                </tr>
				<tr>
                  <td><?php _e('Supported import methods');?></td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Local');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('FTP/SFTP');?></li>
					  <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('From URL');?></li>
                      <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php _e('From existing files');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Local');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('FTP/SFTP');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From URL');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From existing files');?></li>
                    </ul>
				  </td>
                  <td>
					<ul>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('Local');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('FTP/SFTP');?></li>
					  <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From URL');?></li>
                      <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php _e('From existing files');?></li>
                    </ul>
				  </td>
                </tr>
                <tr>
                  <td><?php _e('Automatic scheduled import & export');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Import & export custom fields ( metadata )');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Import & export hidden meta');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
                <tr>
                  <td><?php _e('Email customers on order status update');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
                <tr>
                  <td><?php _e('Create customers on order import');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
                <tr>
                  <td><?php _e('Bulk delete orders/coupons/subcriptions');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>					
				<tr>
                  <td><?php _e('Export to FTP/SFTP');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Set CSV delimiter for export');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
				<tr>
                  <td><?php _e('Custom export filename');?></td>
                  <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                  <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!--------comparison table ends here--------->
      <div class=" wt-ier-box-wrapper wt-ier-mt-5 wt-ier-suite">
        <div class="wt-ier-row wt-ier-p-5">
          <div class="wt-ier-col-12 wt-ier-col-lg-6">
            <img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/suite.svg" class="wt-ier-thumbnails">
            <h2 class="wt-ier-page-title"><?php _e('Import Export Suite for WooCommerce');?></h2>
            <p class="wt-ier-p"><?php _e('WooCommerce Import Export Suite is an all-in-one bundle of plugins with which you can import and export WooCommerce products, product reviews, orders, customers, coupons and subscriptions.');?></p>
            <a href="https://www.webtoffee.com/product/woocommerce-import-export-suite/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Import_Export_Suite&utm_content=<?php echo WT_O_IEW_VERSION; ?>" class="wt-ier-primary-btn" target="_blank"><?php _e('Get Premium');?></a>
          </div>
        </div>
      </div>
    </div>
    <script>
    jQuery("a[data-toggle=modal]").click(function(e){
      e.preventDefault();
      var target=jQuery(this).attr('data-target');
      jQuery(target).css('display','block');
    });
    jQuery(document).click(function (e) {
      if (jQuery(e.target).is('.wt-ier-modal')) {
        jQuery('.wt-ier-modal').css('display','none');
      }

    });
    jQuery(".wt-ier-close").click(function (e) {
      jQuery(this).closest('.wt-ier-modal').css('display','none');
    });
  </script>
</div>