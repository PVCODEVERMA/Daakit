<?php
$courier_list = array();
$courier_dropdown = array();
$courier_names = array(
    'cheapest_air' => 'Cheapest (Air)',
    'cheapest_surface' => 'Cheapest (Surface)',
    'cheapest_any' => 'Cheapest (Any)',
);

if (!empty($couriers)) {
    foreach ($couriers as $courier) {
        $courier_list[$courier->id] = $courier;
        $courier_dropdown[$courier->id] = $courier->name;
        $courier_names[$courier->id] = $courier->name;
    }
}
if (!empty($plan_list)) {
    foreach ($plan_list as $key=>$courier) {
        $courier_list[$key] = $courier;
        $courier_dropdown[$key] = $courier;
        $courier_names[$key] = $courier;
    }
}
//pr($courier_names,1);
?>

<?php
$filter_fields = array(
    '' => 'Select',
    'delivery_pincode' => 'Delivery Pincode',
    'order_amount' => 'Order Amount',
    'payment_type' => 'Payment Mode',
    'pickup_pincode' => 'Pickup Pincode',
    'product_name' => 'Product Name',
    'product_sku' => 'Product SKU',
    'weight' => 'Weight',
    'zone' => 'Zone',
);

$filter_conditions = array(
    '' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
        'starts_with' => 'Starts with',
        'greater_than' => 'Greater than',
        'less_than' => 'Less than',
    ),
    'default' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
        'starts_with' => 'Starts with',
        'greater_than' => 'Greater than',
        'less_than' => 'Less than',
    ),

    'payment_type' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
    ),
    'state' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
    ),
    'zone' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
    ),
    'order_amount' => array(
        'greater_than' => 'Greater than',
        'less_than' => 'Less than',
    ),
    'weight' => array(
        'greater_than' => 'Greater than',
        'less_than' => 'Less than',
    ),
    'pickup_pincode' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
        'any_of' => 'Any of (Comma Separated)',
    ),
    'delivery_pincode' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
        'starts_with' => 'Starts with',
        'any_of' => 'Any of (Comma Separated)',
    ),
    'product_name' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
        'starts_with' => 'Starts with',
        'contain' => 'Contain word',
        'any_of' => 'Any of (Comma Separated)',
    ),
    'product_sku' => array(
        'is' => 'Is',
        'is_not' => 'Is not',
        'starts_with' => 'Starts with',
        'contain' => 'Contain word',
        'any_of' => 'Any of (Comma Separated)',
    ),
);

$filter_values = array(
    'payment_type' => array(
        '' => 'Select',
        'cod' => 'COD',
        'prepaid' => 'Prepaid',
        'reverse' => 'Reverse',
    ),
    'zone' => array(
        '' => 'Select',
        'z1' => 'Z1',
        'z2' => 'Z2',
        'z3' => 'Z3',
        'z4' => 'Z4',
        'z5' => 'Z5',
    )
);
?>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Rule allocation</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">						
        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#lgscrollmodal" class="btn btn-info btn-sm pull-left display-block mright5"> Create Rule </a>
		</li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">
<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Allocation</h3>
			</div>
			<div class="card-body">
            <div class="table-responsive">
                <table class="table card-table table-bordered table-vcenter text-dark table-outline" data-order-col="2" data-order-type="desc">                    
                    <thead>
                        <tr>
                            <th><span class="bold">Rule Name</span></th>
                            <th><span class="bold">Priority</span></th>
                            <th><span class="bold">Rule Matched</span></th>
                            <th><span class="bold">Courier</span></th>
                            <th><span class="bold">Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                          if (!empty($filters)) {
                            $c = 1;
                            foreach ($filters as $filter) {
                                if($user_plan_flag!=$filter->user_plan)
                                    continue;

                                $filter_rules = $filter->conditions;
                                $conditions = array();
                                foreach ($filter_rules as $f_rl) {
                                    $conditions[] = ucwords((array_key_exists($f_rl->field, $filter_fields) ? $filter_fields[$f_rl->field] : '')
                                        . ' ' . (array_key_exists($f_rl->condition, $filter_conditions[$f_rl->field]) ? $filter_conditions[$f_rl->field][$f_rl->condition] : '')
                                        . ' ' . $f_rl->value);
                                }
                            ?>
                                    <tr>
                                        <td><?= $c; ?>. <?= ucwords($filter->filter_name); ?></td>
                                        <td> #<?= $filter->priority; ?></td>
                                        <td><?= implode(" <b>" . strtoupper($filter->filter_type) . "</b> ", $conditions) ?></td>
                                        <td>
                                            <?php for ($j = 1; $j <= 8; $j++) {
                                                echo "<div class='col-sm-12'>";
                                                echo "<b>Priority {$j}</b>: ";
                                                $p = 'courier_priority_' . $j;
                                                echo (array_key_exists($filter->{$p}, $courier_names)) ? $courier_names[$filter->{$p}] : 'No Set';
                                                echo '</div>';
                                            } ?>
                                        </td>
                                        <td>
                                            <div class="material-switch float-start" style="margin-top: 6px;">
                                                <input id="ruleOptionSuccess<?= $filter->id; ?>" data-rule-id="<?= $filter->id; ?>" value="<?= $filter->status ?>" class="cstm-switch-input" <?php if ($filter->status == '1') {echo 'checked';} ?> name="someSwitchOption<?= $filter->id; ?>" type="checkbox">
                                                <label for="ruleOptionSuccess<?= $filter->id; ?>" class="label-success"></label>
                                            </div>
                                            <button type="button" style="margin-left: 5px;" class="btn btn-sm btn-outline-primary edit_order_filter" data-filter-id="<?= $filter->id; ?>"><a href="<?php echo base_url('allocation/v/edit_rule')?>/<?= $filter->id; ?>" style="color: inherit;"><i class="fa fa-edit" aria-hidden="true"></a></i> </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete_order_filter" data-filter-id="<?= $filter->id; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                <?php
                                    $c++;
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="4" style="text-align:center">No entries found.</td>
                                </tr>
                            <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
		</div>
	</div>
</div>
<!-- END ROW-1 -->
</div>
<div class="modal fade bs-modal-lg" id="lgscrollmodal" tabindex="-1" role="dialog" aria-labelledby="slideRightModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-dialog-centered modal-lg" >
        <div class="modal-content" style="height:auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="slideRightModalLabel">Add/Edit Rule</h5>
                <!-- <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                    <div class="modal-content" id="show_information">
                        <form  method="post" class="create_new_order_filter_form" id="create_new_order_filter_form" accept-charset="utf-8">
                            <div class="col-sm-12">
                                    <input type="hidden" name="filter_id" value="<?= (!empty($edit_id)) ? $edit_id : '' ?>">
                                    <input type="hidden" name="user_plan" value="<?= (!empty($user_plan) && $user_plan['plan_type']=='smart') ? '1':'0' ?>">
                                    <!-- <div class="card-header bg-gray-300 ">
                                        <h5><?= (!empty($edit_id)) ? 'Edit' : 'Add New'; ?> Shipping Rule</h5>
                                    </div> -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label for="inputPassword" class="col-form-label">Rule</label>
                                                <input type="text" class="form-control" required="" name="name" value="<?php if (!empty($edit_data->filter_name)) echo $edit_data->filter_name; ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="inputPassword" class="col-form-label">Priority</label>
                                                <input type="number" class="form-control" required="" name="priority" value="<?php if (!empty($edit_data->priority)) echo $edit_data->priority; ?>">
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top:20px">
                                            <div class="custom-control custom-radio col-sm-6">
                                                <input type="radio" required="" id="customRadio1" name="filter_type" class="form-check-input courier_priority" <?php echo set_radio('filter_type', 'or', ((empty($edit_data->filter_type) || $edit_data->filter_type == 'or') ? TRUE : FALSE)); ?> value="or">
                                                <label class="custom-control" for="customRadio1">Match Any</label>
                                            </div>
                                            <div class="custom-control custom-radio col-sm-6">
                                                <input type="radio" required="" id="customRadio2" name="filter_type" class="form-check-input courier_priority" <?php echo set_radio('filter_type', 'and', ((!empty($edit_data->filter_type) && $edit_data->filter_type == 'and') ? TRUE : FALSE)); ?> value="and">
                                                <label class="custom-control" for="customRadio2">Match All</label>
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
                                                            $js = 'class="form-control" required onchange="on_field_change(' . $i . ',this.value)" ';
                                                            echo form_dropdown("filter[{$i}][field]", $filter_fields, (!empty($edit_conditions[$i]->field)) ? $edit_conditions[$i]->field : '', $js);
                                                            ?>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">

                                                            <?php
                                                            $js = 'class="form-control" required id="filter_' . $i . '_conditions"';
                                                            echo form_dropdown("filter[{$i}][condition]", (!empty($edit_conditions[$i]->field)) ? $filter_conditions[$edit_conditions[$i]->field] : $filter_conditions[''], (!empty($edit_conditions[$i]->condition)) ? $edit_conditions[$i]->condition : '', $js);
                                                            ?>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <div class="form-group" id="filter_<?= $i; ?>_value">
                                                            <?php if ((!empty($edit_conditions[$i]->field))  && !empty($filter_values[$edit_conditions[$i]->field])) { ?>

                                                                <?php
                                                                $js = 'class="form-control" required';
                                                                echo form_dropdown("filter[{$i}][value]", $filter_values[$edit_conditions[$i]->field], (!empty($edit_conditions[$i]->value)) ? $edit_conditions[$i]->value : '', $js);
                                                                ?>

                                                            <?php } else { ?>

                                                                <textarea class="form-control" rows="5" required="" name="filter[<?= $i; ?>][value]" placeholder=""><?= (!empty($edit_conditions[$i]->value)) ? $edit_conditions[$i]->value : '' ?></textarea>

                                                            <?php }  ?>
                                                        </div>
                                                    </div>
                                                    <?php if ($i > 0) { ?>
                                                        <div class="col-sm-2 text-right">
                                                            <button type="button" class="btn btn-warning" onclick="deleteFilterRow('<?= $i; ?>');"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>

                                        </div>
                                        <div class="form-row border-bottom">
                                            <div class="form-group col-sm-12 text-right">
                                                <button type="button" class="btn btn-sm btn-success add_new_condition"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="row m-t-1">
                                                <div class="col-sm-12" style="margin-bottom: -50px;">
                                                    <p class="bold p_style">Courier Priority</p>
                                                    <hr class="hr_style">
                                                </div>
                                            </div>
                                            <div class=" row bg-gray-300  m-b-10">
                                                <div class="form-group col-sm-6">
                                                    <label for="inputPassword" class="col-form-label">P1</label>
                                                    <select name="courier_priority_1" required class="form-control">
                                                        <option value="">Select</option>
                                                        <?php if(!empty($user_plan) && $user_plan['plan_type']=='smart'){foreach ($plan_list as $cd_key => $cd) { ?>
                                                            <option value="<?= $cd_key; ?>" <?php echo set_select('courier_priority_1', $cd_key, ((!empty($edit_data->courier_priority_1) && $edit_data->courier_priority_1 == $cd_key) ? TRUE : FALSE)); ?>><?= $cd; ?></option>
                                                        <?php }} else{ foreach ($courier_dropdown as $cd_key => $cd) { ?><option value="<?= $cd_key; ?>" <?php echo set_select('courier_priority_1', $cd_key, ((!empty($edit_data->courier_priority_1) && $edit_data->courier_priority_1 == $cd_key) ? TRUE : FALSE)); ?>><?= $cd; ?></option>
                                                        <?php } }?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label for="inputPassword" class="col-form-label">P2</label>
                                                    <select name="courier_priority_2" required class="form-control">
                                                        <option value="">Select</option>
                                                        <?php if(!empty($user_plan) && $user_plan['plan_type']=='smart'){foreach ($plan_list as $cd_key => $cd) { ?>
                                                            <option value="<?= $cd_key; ?>" <?php echo set_select('courier_priority_2', $cd_key, ((!empty($edit_data->courier_priority_2) && $edit_data->courier_priority_2 == $cd_key) ? TRUE : FALSE)); ?>><?= $cd; ?></option>
                                                        <?php }} else{ foreach ($courier_dropdown as $cd_key => $cd) { ?>
                                                            <option value="<?= $cd_key; ?>" <?php echo set_select('courier_priority_2', $cd_key, ((!empty($edit_data->courier_priority_2) && $edit_data->courier_priority_2 == $cd_key) ? TRUE : FALSE)); ?>><?= $cd; ?></option>
                                                        <?php }} ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label for="inputPassword" class="col-form-label">P3</label>
                                                    <select name="courier_priority_3" required class="form-control">
                                                        <option value="">Select</option>
                                                        <?php if(!empty($user_plan) && $user_plan['plan_type']=='smart'){foreach ($plan_list as $cd_key => $cd) { ?>
                                                            <option value="<?= $cd_key; ?>" <?php echo set_select('courier_priority_3', $cd_key, ((!empty($edit_data->courier_priority_3) && $edit_data->courier_priority_3 == $cd_key) ? TRUE : FALSE)); ?>><?= $cd; ?></option>
                                                        <?php }} else{ foreach ($courier_dropdown as $cd_key => $cd) { ?>
                                                            <option value="<?= $cd_key; ?>" <?php echo set_select('courier_priority_3', $cd_key, ((!empty($edit_data->courier_priority_3) && $edit_data->courier_priority_3 == $cd_key) ? TRUE : FALSE)); ?>><?= $cd; ?></option>
                                                        <?php }} ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label for="inputPassword" class="col-form-label">P4</label>
                                                    <select name="courier_priority_4" required class="form-control">
                                                        <option value="">Select</option>
                                                        <?php if(!empty($user_plan) && $user_plan['plan_type']=='smart'){foreach ($plan_list as $cd_key => $cd) { ?>
                                                            <option value="<?= $cd_key; ?>" <?php echo set_select('courier_priority_4', $cd_key, ((!empty($edit_data->courier_priority_4) && $edit_data->courier_priority_4 == $cd_key) ? TRUE : FALSE)); ?>><?= $cd; ?></option>
                                                        <?php }} else{ foreach ($courier_dropdown as $cd_key => $cd) { ?>
                                                            <option value="<?= $cd_key; ?>" <?php echo set_select('courier_priority_4', $cd_key, ((!empty($edit_data->courier_priority_4) && $edit_data->courier_priority_4 == $cd_key) ? TRUE : FALSE)); ?>><?= $cd; ?></option>
                                                        <?php }} ?>
                                                    </select>
                                                </div>
                                                <!-- Placeholder for more priority dropdowns -->
<div class="row bg-gray-300" id="more-priority-boxes"></div>

<div class="form-group col-sm-12 text-right">
  <button type="button" class="btn btn-sm btn-success" onclick="addMorePriority()">
    <i class="fa fa-plus"></i> Add Priority
  </button>
</div>

                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-footer text-right">
                                        <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                                        <a href="<?php echo base_url('allocation/v/rules');?>" class="btn btn-secondary">Cancel</a>
                                    </div>
                            </div>  
                        </form>                                          
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let priorityCount = 4;
const maxPriority = 8;
const courierOptions = `<?php
  $options = '';
  if(!empty($user_plan) && $user_plan['plan_type']=='smart'){
      foreach ($plan_list as $cd_key => $cd) {
          $options .= "<option value='{$cd_key}'>{$cd}</option>";
      }
  } else {
      foreach ($courier_dropdown as $cd_key => $cd) {
          $options .= "<option value='{$cd_key}'>{$cd}</option>";
      }
  }
  echo addslashes($options);
?>`;

function addMorePriority() {
  if (priorityCount >= maxPriority) return;

  priorityCount++;
  const newBox = `
    <div class="form-group col-sm-6">
      <label for="courier_priority_${priorityCount}" class="col-form-label">P${priorityCount}</label>
      <select name="courier_priority_${priorityCount}" class="form-control">
        <option value="">Select</option>
        ${courierOptions}
      </select>
    </div>
  `;
  document.getElementById('more-priority-boxes').insertAdjacentHTML('beforeend', newBox);
}
</script>

<script>
    $(document).ready(function() {
        $(document).on('submit', '.create_new_order_filter_form', function(event) {
            event.preventDefault();
            var button = document.getElementById('save-btn');
            button.disabled = true;
            button.textContent = 'Loading...';
            $.ajax({
                url: '<?php echo base_url('allocation/add_filter');?>',
                type: "POST",
                data: $(this).serialize(),
                cache: false,
                success: function(data) {
                    if (data.success) {
                        alert_float(data.success, 'notice');
                        setInterval(function() {
                            window.location.href = '<?php echo base_url('allocation');?>';
                        }, 1000);
                    } else if (data.error) {
                        var button = document.getElementById('save-btn');
                        button.disabled = false;
                        button.textContent = 'Save';
                        alert_float(data.error, 'danger');
                    }
                }
            });
        });
        <?php 
        if(!empty($edit_id))
        {
            ?>
                $('#lgscrollmodal').modal('show'); // This opens the modal
                $('#lgscrollmodal').data('bs.modal').options.backdrop = 'static';
                $('#lgscrollmodal').data('bs.modal').options.backdrop = 'static'; // Set backdrop to static
            <?php
        }
        ?>
    });

    $(".delete_order_filter").click(function(event) {
        event.preventDefault();
        if (!confirm('are you sure?'))
            return;
        var filter_id = $(this).attr('data-filter-id');
        $.ajax({
            url: '<?php echo base_url('allocation/delete_rule');?>',
            type: "POST",
            data: {
                filter_id: filter_id,
            },
            cache: false,
            success: function(data) {
                if (data.success)
                    window.location.href = 'allocation';
                else if (data.error)
                    alert_float(data.error,'danger');
            }
        });
    });
    var x = <?= $i; ?>;
    $('.add_new_condition').click(function() {
        var fieldHTML = '<div class="row bg-gray-300 p-t-20 m-b-10 border-top border-light" id="filter_number_' + x + '"><div class="col-sm-6"><div class="form-group"><select name="filter[' + x + '][field]" onchange="on_field_change(' + x + ',this.value)" required class="form-control"><?php
                                                                                                                                                                                                                                                                                                foreach ($filter_fields as $f_key => $ff) {
                                                                                                                                                                                                                                                                                                    echo '<option value="' . $f_key . '">' . $ff . '</option>';
                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                ?>\
                            </select>\
                        </div>\
                    </div>\
                    <div class="col-sm-6">\
                        <div class="form-group">\
                          <select name="filter[' + x + '][condition]" id="filter_' + x + '_conditions" required class="form-control">\
                            </select>\
                        </div>\
                    </div>\
                    <div class="col-sm-10">\
                        <div class="form-group" id="filter_' + x + '_value">\
                            <textarea class="form-control" rows="5" required="" name="filter[' + x + '][value]" placeholder=""></textarea>\
                                <small  id="filter_' + x + '_help_text" class="form-text text-muted"></small>\
                        </div>\
                    </div>\
                    <div class="col-sm-2 text-right">\
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteFilterRow(' + x + ');"><i class="fa fa-minus"></i></button>\
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

        var values_options = '<textarea class="form-control" rows="5" required="" name="filter[' + row + '][value]" placeholder=""></textarea>';
        document.getElementById("filter_" + row + "_value").innerHTML = values_options;

        switch (value) {
            case 'payment_type':
                options = '<?php
                            foreach ($filter_conditions['payment_type'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                var values_options = '<select name="filter[' + row + '][value]" class="form-control" required=""><?php
                                                                                                                    foreach ($filter_values['payment_type'] as $c_key => $fc) {
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
            case 'pickup_pincode':
                options = '<?php
                            foreach ($filter_conditions['pickup_pincode'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                break;
            case 'delivery_pincode':
                options = '<?php
                            foreach ($filter_conditions['delivery_pincode'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                break;
            case 'state':
                options = '<?php
                            foreach ($filter_conditions['state'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                break;
            case 'zone':
                options = '<?php
                            foreach ($filter_conditions['zone'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
                var values_options = '<select name="filter[' + row + '][value]" class="form-control" required=""><?php
                                                                                                                    foreach ($filter_values['zone'] as $c_key => $fc) {
                                                                                                                        echo '<option value="' . $c_key . '">' . $fc . '</option>';
                                                                                                                    }
                                                                                                                    ?></select>';
                document.getElementById("filter_" + row + "_value").innerHTML = values_options;

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
            default:
                options = '<?php
                            foreach ($filter_conditions['default'] as $c_key => $fc) {
                                echo '<option value="' . $c_key . '">' . $fc . '</option>';
                            }
                            ?>';
        }

        document.getElementById("filter_" + row + "_conditions").innerHTML = options;
    }

    $('.cstm-switch-input').change(function() {
        var rule_id = $(this).attr('data-rule-id');
        var status = 0;
        if ($(this).is(':checked'))
            var status = 1;

        $.ajax({
            url: '<?php echo base_url('allocation/change_status');?>',
            type: "POST",
            data: {
                rule_id: rule_id,
                status: status,
            },
            cache: false,
            success: function(data) {
                if (data.error) {
                   alert_float(data.error,'danger');
                }
                else{
                    alert_float(data.success,'notice');
                }
            }
        });
    });
</script>