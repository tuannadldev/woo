<?php
if (!defined('ABSPATH')) {
    exit('Direct\'s not allowed');
}
/*
 * Support functions
 */
 function get_order_log($order_id){
     global $wpdb;
     $check_logs = $wpdb->get_results("SELECT * FROM checkout_log WHERE order_id = ".$order_id, ARRAY_A);

     foreach($check_logs as $key => $check_log){
 ?>
         <div>
            <?php if($check_log['env'] == 'ax'){?>
                <div class="ax-log">
                    <h4>AX</h4>
                    <p>Date:  <?php echo $check_log['create_date']?></p>
                    <p>Body</p>
                    <textarea style="width: 800px; height: 450px;">
                        <?php echo $check_log['body']?>
                    </textarea>
                    <p>Response</p>
                    <textarea style="width: 800px; height: 450px;">
                        <?php echo $check_log['log']?>
                    </textarea>
                </div>
            <?php }?>
             <?php if($check_log['env'] == 'oms'){?>
                 <div class="ax-log">
                     <h4>OMS</h4>
                     <p>Date:  <?php echo $check_log['create_date']?></p>
                     <p>Body</p>
                    <textarea style="width: 800px; height: 450px;">
                        <?php echo $check_log['body']?>
                    </textarea>
                     <p>Response</p>
                    <textarea style="width: 800px; height: 450px;">
                        <?php echo $check_log['log']?>
                    </textarea>
                 </div>
             <?php }?>
         </div>
     <?php }?>
<?php }?>
