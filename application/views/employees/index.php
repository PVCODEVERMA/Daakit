 <style>
        .label-title-cust{
            background-color: #e3ebf6;
            display: block;
            margin: 0;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .cust_chk.option-box-grid label:before, .cust_chk.option-box-grid label:after{
            top: 50%;
            left: 5px;
            right: inherit;
            font-size: 1.3rem;
            transform: translateY(-50%);
        }
        .cust_chk.option-box-grid input[type=checkbox]:checked+label:before, .cust_chk.option-box-grid input[type=radio]:checked+label:before{
            transform: translateY(-50%) scale(1);
        }
        .cust_chk.option-box-grid .radio-content{
            margin: 0;
            padding-left: 20px;
            padding-right: 0px;
        }
        .accrd_cus{
            border: 1px solid #e9eaee;
            border-radius: 0.25rem;
            background-color: #fff;
            box-shadow: 0 3px 8px 0 rgb(27 41 85 / 5%);
            padding: 5px;
            margin: -10px 0 10px;
            position: relative;
            z-index: 3;
        }
        .accrd_cus label{
            border: 0px solid #e9eaee;
            border-radius: 0rem;
            background-color: transparent;
            box-shadow: 0 0px 0px 0 rgb(27 41 85 / 0%);
            padding: 0px;
        }
        .accrd_cus .cust_chk.option-box-grid label:before, .accrd_cus .cust_chk.option-box-grid label:after{
            left: -2px;
            top: 55%;
        }
    </style>
<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-header border-bottom">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="m-b-0">
                            <i class="mdi mdi-account-multiple"></i> Employees
                        </h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn default btn-sm add_new_emp "  data-toggle="modal"
                                data-target=".create_new_employee"> <i class="mdi mdi-plus"></i>Add New Employee</button>
                    </div>

                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>

                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Activation date</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($employees)) {
                                        foreach ($employees as $employee) {
                                            ?>
                                            <tr>
                                                <td><?= ucwords($employee->fname); ?></td>
                                                <td><?= $employee->email; ?></td>
                                                <td><?= date("d-m-Y",strtotime($employee->modified)) ?></td>
                                                <td><button data-employee-id="<?= $employee->id; ?>" class="btn btn-sm btn-outline-info edit-employee-button edit_employee"><i class="mdi mdi-pencil"></i> Edit</button></td>
                                               

                                                <td><label class="cstm-switch">
                                    <input type="checkbox" <?= ($employee->status==1)?"checked":"" ?> onchange="change_status(<?= ($employee->status==1)?0:1; ?>,<?= $employee->id;?>)" name="option" value="0" class="cstm-switch-input">
                                    <span class="cstm-switch-indicator bg-success "></span>
                            </label>
                                                  </td>



                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No Records Found</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="modal fade create_new_employee"  tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" id="fulfillment_info">
            <form method="post" class="create_new_employee_form" id="create_new_employee_form" >
                <?php include VIEWPATH . 'employees/add_new_form.php'; ?>
            </form>
        </div>
    </div>
</div>


<script>
    function change_status(status,id){
        statusname = "inactive";
        if(status==1){
            statusname = "active";
        }
        if(!confirm("Do you want to "+statusname+" this user")){
            return false;
        }
         $.ajax({
            url: 'employees/change_status',
            type: "POST",
            data: {
                status: status,
                id :id
            },
            cache: false,
            success: function(data) {
                if (data.success){
                     location.reload();
                }else if (data.error)
                    alert(data.error);
            }
        });
    }
$(".add_new_emp").click(function(){
    $("#action_text").html('Add New');
    $('#name').val('');
    $('#email').val('');
    $('#mobile').val('');
    $('#password').val('');
    $('.ord_permission').prop('checked', true);
});
</script>

