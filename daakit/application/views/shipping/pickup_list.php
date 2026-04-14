<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

/* tr:nth-child(even) {
  background-color: #dddddd;
} */
.background {
  background-color: #dddddd;
}
</style>

   
    <h3 style="text-align:center;">PICK LIST</h3>
    <h4 style="text-align:left;">Selected Orders:<?php echo count($totalselectedorders);?> </h4></small>
    <table id="customers"> 
        <thead>
            <tr>
                <td class="background" style="text-align:left;">SKU</td>
                <td class="background" style="text-align:left;">Description</td>
                <td class="background" style="text-align:left;">Shelf</td>
                <td class="background" style="text-align:left;">Quantity</td>
                <td class="background" style="text-align:left;">Picked</td>
            </tr>
            <?php 

   foreach($picklist as $data){
   ?>
              
            <tr>
                <td style="text-align:left;"><?= $data->product_sku;?></td>
                <td style="text-align:left;"><?= $data->product_name; ?></td>
                <td style="text-align:left;"></td>
                <td style="text-align:left;"><?= $data->product_qty; ?></td>
                <td style="text-align:left;"></td>
            </tr>
          
         
        </thead>
      
        <?php }?>
    </table>
    <footer>
   <p 
   style="text-align:right;">Generated At: <?php echo date('d-m-Y H:i:s'); ?></p>
   
</footer>