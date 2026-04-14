<?php
$filter_fields = array(
    '' => 'Select',
    'shipping_fname' => 'First Name',
    'shipping_lname' => 'Last Name',
    'phone' => 'Phone',
    'address' => 'Address',
    'address_2' => 'Address 2',
    'pincode' => 'Pincode',
    'order_amount' => 'Order Amount',
    'payment_type' => 'Payment Type',
    'order_status' => 'Order Status',
    'weight' => 'Weight',
    'product_name' => 'Product Name',
    'product_sku' => 'Product SKU',
    'product_qty' => 'Product Qty',
);
$placehoder='';
if($filter_fields['weight']=='Weight'){
    $placehoder='Weight In gram';
}
$filter_conditions = array(
    '' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
        'starts_with' => 'Starts With',
        'contain' => 'Has any of these words',
        'does_not_contain' => 'Has none of these words',
        'words_gt' => 'Words count greater than',
        'words_lt' => 'Words count less than',
    ),
    'default' => array(
        'contain' => 'Has any of these words',
        'does_not_contain' => 'Has none of these words',
        'words_gt' => 'Words count greater than',
        'words_lt' => 'Words count less than',
    ),
    'phone' => array(
      
        'words_gt' => 'Words count greater than',
        'words_lt' => 'Words count less than',
    ),
    'pincode' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
        'starts_with' => 'Starts with',
        'any_of' => 'Any of (Comma Separated)',
        'words_gt' => 'Words count greater than',
        'words_lt' => 'Words count less than',
     
    ),
    'payment_type' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
    ),
    'order_status' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
    ),
    'order_amount' => array(
        'greater_than' => 'Greater than',
        'less_than' => 'Less than',
    ),
    'ordr_amount' => array(
        'greater_than' => 'Greater than',
        'less_than' => 'Less than',
    ),
    'weight' => array(
        'greater_than' => 'Greater than',
        'less_than' => 'Less than',
    ),
    'product_name' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
        'starts_with' => 'Starts with',
        'contain' => 'Has any of these words',
        'does_not_contain' => 'Has none of these words',
    ),
    'product_sku' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
        'starts_with' => 'Starts with',
        'contain' => 'Has any of these words',
        'does_not_contain' => 'Has none of these words',
    ),
    'product_qty' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
        'greater_than' => 'Greater than',
        'less_than' => 'Less than',
    ),


);

$filter_conditions['address'] = $filter_conditions['default'];
$filter_conditions['address_2'] = $filter_conditions['default'];
$filter_conditions['shipping_fname'] = $filter_conditions['default'];
$filter_conditions['shipping_lname'] = $filter_conditions['default'];

$filter_values = array(
    'payment_type' => array(
        '' => 'Select',
        'cod' => 'COD',
        'prepaid' => 'Prepaid',
        'reverse' => 'Reverse',
    ),
    'order_status' => array(
        '' => 'Select',
        'new' => 'New',
        'booked' => 'Booked',
        'cancelled' => 'Cancelled',
    ),

);



?>

<style>
    .modal_scroll {

        max-height: 350px;
        overflow-y: scroll;
        /* Add the ability to scroll */
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    .modal_scroll::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE and Edge */
    .modal_scroll {
        -ms-overflow-style: none;
    }
</style>

<form method="post" class="create_new_order_filter_form" id="create_new_order_filter_form">
    <div class="modal-header bg-gray-300">
        <h5 class="modal-title " id="mySmallModalLabel"><?= (!empty($edit_data)) ? 'Edit' : 'Add new'; ?> order segment</h5>

    </div>
    <div class="model-body modal_scroll">

        <input type="hidden" name="filter_id" value="<?= (!empty($edit_data)) ? $edit_data->id : '' ?>">
        <div class="card">

            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-sm-6">

                        <label for="inputPassword" class="col-form-label">Segment Name</label>
                        <input type="text" class="form-control" required="" name="name" value="<?php if (!empty($edit_data->filter_name)) echo ucwords($edit_data->filter_name); ?>">
                    </div>

                </div>

                <div class="form-row p-l-30 border-top p-t-20">
                    <div class="custom-control custom-radio col-sm-6">
                        <input type="radio" required="" id="customRadio1" name="filter_type" class="custom-control-input courier_priority" <?php echo set_radio('filter_type', 'or', ((empty($edit_data->filter_type) || $edit_data->filter_type == 'or') ? TRUE : FALSE)); ?> value="or">
                        <label class="custom-control-label" for="customRadio1">Match Any of the Below</label>
                    </div>
                    <div class="custom-control custom-radio col-sm-6">
                        <input type="radio" required="" id="customRadio2" name="filter_type" class="custom-control-input courier_priority" <?php echo set_radio('filter_type', 'and', ((!empty($edit_data->filter_type) && $edit_data->filter_type == 'and') ? TRUE : FALSE)); ?> value="and">
                        <label class="custom-control-label" for="customRadio2">Match All of the Below</label>
                    </div>
                </div>

                <div class="card-body  filter-body">
                    <?php
                    $edit_rules_count = '1';
                    if (!empty($edit_data->conditions)) {
                        $edit_conditions = $edit_data->conditions;
                        $edit_rules_count = count($edit_conditions);
                    }
                    ?>
                    <?php for ($i = 0; $i < $edit_rules_count; $i++) { ?>
                        <div class="row bg-gray-300 p-t-20 m-b-10 <?php if ($i > 0) echo 'border-top border-light '; ?>" id="filter_number_<?= $i; ?>">
                            <div class="col-sm-6">
                                <div class="form-group">

                                    <?php
                                    $js = 'class="custom-select" required onchange="on_field_change(' . $i . ',this.value)" ';
                                    echo form_dropdown("filter[{$i}][field]", $filter_fields, (!empty($edit_conditions[$i]->field)) ? $edit_conditions[$i]->field : '', $js);
                                    ?>

                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">

                                    <?php
                                    $js = 'class="custom-select" required id="filter_' . $i . '_conditions"';
                                    echo form_dropdown("filter[{$i}][condition]", (!empty($edit_conditions[$i]->field)) ? $filter_conditions[$edit_conditions[$i]->field] : $filter_conditions[''], (!empty($edit_conditions[$i]->condition)) ? $edit_conditions[$i]->condition : '', $js);
                                    ?>

                                </div>
                            </div>
                            <div class="col-sm-10">
                                <div class="form-group" id="filter_<?= $i; ?>_value">
                                    <?php if ((!empty($edit_conditions[$i]->field))  && !empty($filter_values[$edit_conditions[$i]->field])) { ?>

                                        <?php
                                        $js = 'class="custom-select" required';
                                        echo form_dropdown("filter[{$i}][value]", $filter_values[$edit_conditions[$i]->field], (!empty($edit_conditions[$i]->value)) ? $edit_conditions[$i]->value : '', $js);
                                        ?>

                                    <?php } else { ?>

                                        <textarea class="form-control" rows="1" required="" name="filter[<?= $i; ?>][value]" placeholder="<?=$placehoder?>"><?= (!empty($edit_conditions[$i]->value)) ? $edit_conditions[$i]->value : '' ?></textarea>
                                        <small class="form-text text-muted">Comma(,) separated values</small>

                                    <?php }  ?>
                                </div>
                            </div>
                            <?php if ($i > 0) { ?>
                                <div class="col-sm-2 text-right">
                                    <button type="button" class="btn btn-link btn-sm" onclick="deleteFilterRow('<?= $i; ?>');"><i class="mdi mdi-delete-circle mdi-18px"></i></button>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                </div>
                <div class="form-row border-bottom">
                    <div class="form-group col-sm-12 text-center">
                        <button type="button" class="btn-outline-info btn-rounded-circle btn-sm btn add_new_condition"><i class="mdi mdi-plus"></i></button>
                    </div>
                </div>

            </div>

        </div>



    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <?php if (!empty($edit_data)) { ?>
            <button type="button" class="btn btn-outline-danger delete_order_filter" data-filter-id="<?= $edit_data->id; ?>"><i class="mdi mdi-delete-circle"></i></button>
        <?php } ?>
    </div>
</form>

<script>
    $(".create_new_order_filter_form").submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: 'orders/add_segment',
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                if (data.success)
                    location.reload();
                else if (data.error) {
                    alert(data.error);
                }

            }
        });
    });
    $(".delete_order_filter").click(function(event) {
        event.preventDefault();
        if (!confirm('are you sure?'))
            return;
        var filter_id = $(this).attr('data-filter-id');
        $.ajax({
            url: 'orders/delete_segment',
            type: "POST",
            data: {
                filter_id: filter_id,
            },
            cache: false,
            success: function(data) {
                if (data.success)
                    window.location.href = 'orders/all';
                else if (data.error)
                    alert(data.error);
            }
        });
    });

    var x = <?= $i; ?>;
    $('.add_new_condition').click(function() {
        var fieldHTML = '<div class="row bg-gray-300 p-t-20 m-b-10 border-top border-light" id="filter_number_' + x + '"><div class="col-sm-6"><div class="form-group"><select name="filter[' + x + '][field]" onchange="on_field_change(' + x + ',this.value)" required class="custom-select"><?php
                                                                                                                                                                                                                                                                                                foreach ($filter_fields as $f_key => $ff) {
                                                                                                                                                                                                                                                                                                    echo '<option value="' . $f_key . '">' . $ff . '</option>';
                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                ?>\
                            </select>\
                        </div>\
                    </div>\
                    <div class="col-sm-6">\
                        <div class="form-group">\
                          <select name="filter[' + x + '][condition]" id="filter_' + x + '_conditions" required class="custom-select">\
                            </select>\
                        </div>\
                    </div>\
                    <div class="col-sm-10">\
                        <div class="form-group" id="filter_' + x + '_value">\
                            <textarea class="form-control" rows="1" required="" name="filter[' + x + '][value]" placeholder=""></textarea>\
                            <small class="form-text text-muted">Comma(,) separated values</small>\
                        </div>\
                    </div>\
                    <div class="col-sm-2 text-right">\
                        <button type="button" class="btn btn-link btn-sm" onclick="deleteFilterRow(' + x + ');"><i class="mdi mdi-delete-circle mdi-18px"></i></button>\
                    </div></div></div>';
        x++;
        $('.filter-body').append(fieldHTML);
    });

    function deleteFilterRow(id) {
        var element = document.getElementById("filter_number_" + id);
        element.parentNode.removeChild(element);
    }

    function on_field_change(row = false, value = false) {
        var options = '';
        var placehold='';
        if(value=='weight'){
            placehold='Weight In gram';
        }
       
        var values_options = '<textarea class="form-control" rows="1" required="" name="filter[' + row + '][value]" placeholder="'+placehold+'"></textarea><small class="form-text text-muted">Comma(,) separated values</small>';
        document.getElementById("filter_" + row + "_value").innerHTML = values_options;

        switch (value) {
            case 'payment_type':
                options = '<?php
                            foreach ($filter_conditions['payment_type'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                var values_options = '<select name="filter[' + row + '][value]" class="custom-select" required=""><?php
                                                                                                                    foreach ($filter_values['payment_type'] as $c_key => $fc) {
                                                                                                                        echo '<option value="' . $c_key . '">' . $fc . '</option>';
                                                                                                                    }
                                                                                                                    ?></select>';
                document.getElementById("filter_" + row + "_value").innerHTML = values_options;
                break;
            case 'order_status':
                options = '<?php
                            foreach ($filter_conditions['order_status'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                var values_options = '<select name="filter[' + row + '][value]" class="custom-select" required=""><?php
                                                                                                                    foreach ($filter_values['order_status'] as $c_key => $fc) {
                                                                                                                        echo '<option value="' . $c_key . '">' . $fc . '</option>';
                                                                                                                    }
                                                                                                                    ?></select>';
                document.getElementById("filter_" + row + "_value").innerHTML = values_options;
                break;

            case 'order_amount':
                options = '<?php
                            foreach ($filter_conditions['order_amount'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                break;
            case 'pincode':
                options = '<?php
                            foreach ($filter_conditions['pincode'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                break;
            case 'phone':
                options = '<?php
                            foreach ($filter_conditions['phone'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                break;

            case 'weight':
                options = '<?php
                            foreach ($filter_conditions['weight'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
             
                
                break;
            case 'product_name':
                options = '<?php
                            foreach ($filter_conditions['product_name'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                break;
            case 'product_sku':
                options = '<?php
                            foreach ($filter_conditions['product_sku'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                break;
            case 'product_qty':
                options = '<?php
                            foreach ($filter_conditions['product_qty'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                break;
            default:
                options = '<?php
                            foreach ($filter_conditions['default'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
        }

        document.getElementById("filter_" + row + "_conditions").innerHTML = options;
    }
</script>