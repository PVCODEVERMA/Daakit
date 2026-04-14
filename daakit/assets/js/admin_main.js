$(document).ready(function () {
    $(document).ajaxStart(function () {
        //$.busyLoadFull("show");
    });
    $(document).ajaxStop(function () {
        //$.busyLoadFull("hide");
    });

    $.each($(".date-range-picker_lifetime"), function () {
        $(this).daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Life Time': ['10/01/2019', moment()]
            },
            "startDate": '10/01/2019',
            "endDate": moment(),
        }, function (start, end, label) {
            $('#date-min').val(start.format('YYYY-MM-DD'));
            $('#date-max').val(end.format('YYYY-MM-DD'));
            if (typeof dateRangeFunction == 'function') {
                dateRangeFunction();
            }
            //console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
        if ($(this).attr('data-start-date').length != 0)
            $(this).data('daterangepicker').setStartDate(moment($(this).attr('data-start-date'), 'YYYY-MM-DD'));
        if ($(this).attr('data-end-date').length != 0)
            $(this).data('daterangepicker').setEndDate(moment($(this).attr('data-end-date'), 'YYYY-MM-DD'));
    });

    $.each($(".date-range-picker"), function () {
        $(this).daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                //'Life Time': ['10/01/2019', moment()]
            },
            "startDate": '10/01/2019',
            "endDate": moment(),
        }, function (start, end, label) {
            $('#date-min').val(start.format('YYYY-MM-DD'));
            $('#date-max').val(end.format('YYYY-MM-DD'));
            if (typeof dateRangeFunction == 'function') {
                dateRangeFunction();
            }
            //console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
        if ($(this).attr('data-start-date').length != 0)
            $(this).data('daterangepicker').setStartDate(moment($(this).attr('data-start-date'), 'YYYY-MM-DD'));
        if ($(this).attr('data-end-date').length != 0)
            $(this).data('daterangepicker').setEndDate(moment($(this).attr('data-end-date'), 'YYYY-MM-DD'));
    });
    $('.show_hide_filter').on('click', function (e) {
        $("#filter_row").toggle();
        $(".show_hide_filter").toggle();
    });
    $('.shipnowbtn').on('click', function (e) {

        var order_id = $(this).attr('data-order-id');
        $.ajax({
            url: 'orders/get_delivery_info/' + order_id,
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function (data) {
                $('#fulfillment_info').html(data);
            }
        });
    });
    $('.cancel_order').on('click', function (e) {

        var order_id = $(this).attr('data-order-id');
        if (!confirm('are you sure?'))
            return;
        $.ajax({
            url: 'orders/cancel/',
            type: "POST",
            data: {
                order_id: order_id,
            },
            datatype: "JSON",
            cache: false,
            success: function (data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert(data.error);
            }
        });
    });
    $('.submit-ndr-response').on('click', function (e) {
        var ndr_id = $(this).attr('data-ndr-id');
        $("#ndr_id").val(ndr_id);
    });
    $("#ndr_submit_form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: 'ndr/action',
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function (data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert(data.error);
            }
        });
    });
    // $('.show_ndr_history').on('click', function (e) {
    //     e.preventDefault();
    //     var ndr_id = $(this).attr('data-ndr-id');
    //     if ($('.ndr_history_tr_' + ndr_id).is(":visible")) {
    //         $('.ndr_history_tr_' + ndr_id).hide();
    //     } else {
    //         $('.ndr_history_tr').hide();
    //         $.ajax({
    //             url: 'ndr/action_history',
    //             type: "POST",
    //             cache: false,
    //             data: {
    //                 ndr_id: ndr_id,
    //             },
    //             success: function (data) {
    //                 if (data.success) {
    //                     $('.ndr_history_tr_' + ndr_id).show();
    //                     $('.ndr_history_td_' + ndr_id).html(data.success);
    //                 } else if (data.error)
    //                     alert(data.error);
    //             }
    //         });
    //     }

    // });
    // $('.admin_show_ndr_history').on('click', function (e) {
    //     e.preventDefault();
    //     var ndr_id = $(this).attr('data-ndr-id');
    //     if ($('.admin_ndr_history_tr_' + ndr_id).is(":visible")) {
    //         $('.admin_ndr_history_tr_' + ndr_id).hide();
    //     } else {
    //         $('.admin_ndr_history_tr').hide();
    //         $.ajax({
    //             url: 'admin/ndr/action_history',
    //             type: "POST",
    //             cache: false,
    //             data: {
    //                 ndr_id: ndr_id,
    //             },
    //             success: function (data) {
    //                 if (data.success) {
    //                     $('.admin_ndr_history_tr_' + ndr_id).show();
    //                     $('.admin_ndr_history_td_' + ndr_id).html(data.success);
    //                 } else if (data.error)
    //                     alert(data.error);
    //             }
    //         });
    //     }

    // });
    
    $(".show_ndr_history").on("click", function (e) {
        e.preventDefault();
        var ndr_id = $(this).attr("data-ndr-id");
        $.ajax({
            url: "ndr/action_history",
            type: "POST",
            cache: false,
            data: {
                ndr_id: ndr_id,
            },
            success: function (data) {
                if (data.success) {
                    $("#show_information").html(data.success);
                } else if (data.error) alert_float(data.error);
            },
        });
    });


    $('.credit_debit_wallet_button').on('click', function (e) {
        var user_id = $(this).attr('data-user-id');
        $("#wallet_user_id").val(user_id);
    });
    $("#adjust_wallet_form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: 'admin/billing/adjust_wallet',
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function (data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert(data.error);
            }
        });
    });
    $("#select_all_orders").click(function () {
        if (this.checked) {
            $(".order_id_checkbox").attr('checked', "checked");
        } else {
            $(".order_id_checkbox").removeAttr('checked');
        }
    });
    $(".bulk-pickup-button").on('click', function (event) {
        event.preventDefault();
        var shipping_ids = [];
        $.each($("input[class='order_id_checkbox']:checked"), function () {
            shipping_ids.push($(this).val());
        });
        $.ajax({
            url: 'shipping/bulk_pickup',
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert(data.error);
            }
        });
    });
    $(".bulk-cancel-button").on('click', function (event) {
        event.preventDefault();
        if (!confirm('are you sure?'))
            return;
        var shipping_ids = [];
        $.each($("input[class='order_id_checkbox']:checked"), function () {
            shipping_ids.push($(this).val());
        });
        $.ajax({
            url: 'shipping/bulk_cancel',
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert(data.error);
            }
        });
    });
    $(".admin-bulk-cancel-button").on('click', function (event) {
        event.preventDefault();
        if (!confirm('are you sure?'))
            return;
        var shipment_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function () {
            shipment_ids.push($(this).val());
        });
        $.ajax({
            url: baseUrl+'admin/shipping/bulk_cancel_process',
            type: "POST",
            data: {
                shipping_ids: shipment_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert(data.error);
            }
        });
    });
    $('.generate-label-button').on('click', function (event) {
        event.preventDefault();
        var shipping_ids = [];
        console.log(shipping_ids);
        $.each($("input[class='order_id_checkbox']:checked"), function () {
            shipping_ids.push($(this).val());
        });
        $.ajax({
            url: 'shipping/generate_label',
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success)
                    window.open(data.success, '_blank');
                else if (data.error)
                    alert(data.error);
            }
        });
    });
    $('.bulk-ship-button').on('click', function (e) {

        $.ajax({
            url: 'orders/getBulkShipCouriers/',
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function (data) {
                $('#fulfillment_info').html(data);
            }
        });
    });
    $('.recharge_wallet_button').on('click', function (e) {
        $.ajax({
            url: 'billing/create_payment',
            type: "POST",
            data: {
                amount: $('#recharge_wallet_amount').val(),
            },
            cache: false,
            success: function (data) {
                if (data.error)
                    alert(data.error);
                if (data.success) {
                    pay_razorpay(data.success.amount, data.success.payment_id);
                }
            }
        });
    });
    $(".bulk-cancel-order-button").on('click', function (event) {
        event.preventDefault();
        if (!confirm('are you sure?'))
            return;
        var order_ids = [];
        $.each($("input[class='order_id_checkbox']:checked"), function () {
            order_ids.push($(this).val());
        });
        $.ajax({
            url: 'orders/bulk_cancel',
            type: "POST",
            data: {
                order_ids: order_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert(data.error);
            }
        });
    });
    
    
    $("#pricing_calculator_form").submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: 'admin/billing/calculate_pricing',
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                if (data.success) {
                    var price_list = data.success;
                    var content = '';
                    for (var i = 0; i < price_list.length; i++) {
                        content += '<tr>';
                        content += '<td>' + (i + 1) + '</td>';
                        content += '<td>' + price_list[i].name + '</td>';
                        content += '<td>' + price_list[i].courier_charges + '</td>';
                        content += '<td>' + price_list[i].cod_charges + '</td>';
                        content += '<td>' + price_list[i].total_price + '</td>';
                        content += '</tr>';
                    }
                    $('#calculated_price').show();
                    $('#calculated_price tbody').html(content);
                } else if (data.error)
                    alert(data.error);
            }
        });
    });


    $(".autoship_create_item").submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: 'apps/autoship/add_item',
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function (data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert(data.error);
            }
        });
    });
    $('.edit-autoship-rule-button').on('click', function (e) {
        var rule_id = $(this).attr('data-rule-id');
        $.ajax({
            url: 'apps/autoship/editRule/' + rule_id,
            type: "GET",
            cache: false,
            success: function (data) {
                $('.create_autoship_item').modal('show');
                $('#autoship_create_item_form').html(data);
            }
        });
    });
    $(".create_new_employee_form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: 'employees/add_employee',
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function (data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert(data.error);
            }
        });
    });
    $('.edit-employee-button').on('click', function (e) {
        var employee_id = $(this).attr('data-employee-id');
        $.ajax({
            url: 'employees/editEmployee/' + employee_id,
            type: "GET",
            cache: false,
            success: function (data) {
                $('.create_new_employee').modal('show');
                $('#create_new_employee_form').html(data);
            }
        });
    });
    $('.set_recharge_amount').on('click', function (e) {
        var recharge_amount = $(this).attr('data-amount');
        $('#recharge_wallet_amount').val(recharge_amount);
    });
    $('.btn_awb_list_seller_wise').on('click', function (e) {

        var seller_id = $(this).attr('data-seller-id');
        $.ajax({
            url: 'admin/remittance/seller_awb_list/' + seller_id,
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function (data) {
                $('#seller_awb_list_modal').html(data);
            }
        });
    });
    $('.btn_awb_list_expected').on('click', function (e) {

        var seller_id = $(this).attr('data-seller-id');
        $.ajax({
            url: 'admin/remittance/sellerExpectedAwbList/' + seller_id,
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function (data) {
                $('#seller_awb_list_modal').html(data);
            }
        });
    });
    $('.btn_awb_list_remittance_cycle').on('click', function (e) {

        var seller_id = $(this).attr('data-seller-id');
        $.ajax({
            url: 'admin/remittance/sellerRemittanceCycleAWBs/' + seller_id,
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function (data) {
                $('#seller_awb_list_modal').html(data);
            }
        });
    });
});

function loader(show_hide = 'show') {
    alert(show_hide);
    if (show_hide == 'show')
        $('.load_screen').show();
    else
        $('.load_screen').hide();
}



var dttable = $('.datatable').DataTable({
    //DataTable Options
    "pageLength": 5000,
    //"searching": false,
    "bLengthChange": false,
});

$('#searchDatatable').on('keyup click', function () {
    dttable.search($('#searchDatatable').val()).draw();
});


$('.expand_daily_order_summary').on('click', function (e) {
    e.preventDefault();
    var expand_date = $(this).attr('data-expand-date');
    if ($('.expanded_daily_order_summary_tr_' + expand_date).is(":visible")) {
        $('.expanded_daily_order_summary_tr_' + expand_date).hide();
    } else {
        $('.expanded_daily_order_summary_tr').hide();
        $.ajax({
            url: 'admin/reports/daily_order_summary_seller',
            type: "POST",
            cache: false,
            data: {
                expand_date: expand_date,
            },
            success: function (data) {
                if (data.success) {
                    $('.expanded_daily_order_summary_tr_' + expand_date).show();
                    $('.expanded_daily_order_summary_td_' + expand_date).html(data.success);
                } else if (data.error)
                    alert(data.error);
            }
        });
    }

});

$('.expand_state_order_summary').on('click', function (e) {
    e.preventDefault();
    var expand_state = $(this).attr('data-expand-state');
    var start_date = $(this).attr('data-start-date');
    var end_date = $(this).attr('data-end-date');
    if ($('.expanded_state_order_summary_tr_' + expand_state).is(":visible")) {
        $('.expanded_state_order_summary_tr_' + expand_state).hide();
    } else {
        $('.expanded_state_order_summary_tr').hide();
        $.ajax({
            url: 'admin/reports/state_order_summary_couriers',
            type: "POST",
            cache: false,
            data: {
                expand_state: expand_state,
                start_date: start_date,
                end_date: end_date,
            },
            success: function (data) {
                if (data.success) {
                    $('.expanded_state_order_summary_tr_' + expand_state).show();
                    $('.expanded_state_order_summary_td_' + expand_state).html(data.success);
                } else if (data.error)
                    alert(data.error);
            }
        });
    }

});

$('.expand_courier_hub').on('click', function (e) {
    e.preventDefault();
    var expand_pincode = $(this).attr('data-expand-pincode');
    if ($('.expanded_courier_hub_tr_' + expand_pincode).is(":visible")) {
        $('.expanded_courier_hub_tr_' + expand_pincode).hide();
    }
    else {
        $('.expanded_courier_hub_tr').hide();
        $.ajax({
            url: 'admin/warehouse_hub/couriers_hub_list',
            type: "POST",
            cache: false,
            data: {
                expand_pincode: expand_pincode,
            },
            success: function (data) {
                if (data.success) {
                    $('.expanded_courier_hub_tr_' + expand_pincode).show();
                    $('.expanded_courier_hub_td_' + expand_pincode).html(data.success);
                } else if (data.error)
                    alert(data.error);
            }
        });
    }

});

$("#select_all_checkboxes").click(function () {
    if (this.checked) {
        $('.multiple_checkboxes').each(function () {
            $(this).prop('checked', true).trigger('change');
        });

    } else {
        $('.multiple_checkboxes').each(function () {
            $(this).prop('checked', false).trigger('change');
        });
    }
});

$('.multiple_checkboxes').change(function () {
    var selected_chkbx = [];
    $.each($("input[class='multiple_checkboxes']:checked"), function () {
        selected_chkbx.push($(this).val());
    });

    if (selected_chkbx.length > 0) {
        $('.multiple_select_count').html(selected_chkbx.length);
        $('.action_row_selected').show();
        $('.action_row_default').hide();
    } else {
        $('.action_row_selected').hide();
        $('.action_row_default').show();
    }
});


/*--Admin Seller View Page Ajax start here--*/
$(".edit_personal_form").submit(function (event)
{
    event.preventDefault();
    $.ajax({
        url: 'admin/users/sellerpersonaldetailsedit',
        type: "POST",
        data: $(this).serialize(),
        cache: false,
        success: function (data)
        {
            if(data.success){
                alert(data.success);
                location.reload();
            }
            else{
                alert(data.error);
            }
        }
    });
});

$(".seller_plan_form").submit(function (event)
{
    event.preventDefault();
    $.ajax({
        url: 'admin/users/sellerplan',
        type: "POST",
        data: $(this).serialize(),
        cache: false,
        success: function (data) {
            if(data.success){
                alert(data.success);
                location.reload();
            }
            else{
                alert(data.error);
            }
        }
    });
});

$("#bankcustomFile").on("change", function() {
    var file_data = $(this).prop('files')[0];
    var form_data = new FormData();
    form_data.append('chequeimage', file_data);
    $.ajax({
        url: 'admin/users/upload_cheque_img',
        type: "POST",
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.error)
                alert(data.error);
            if (data.success) {
                $("#logo_preview").attr('src', data.success);
                $("#logo_field").val(data.success);
            }
        }
    });
});

$(".edit_seller_bankdetails").submit(function (event)
{
    event.preventDefault();
    $.ajax({
        url: 'admin/users/bankdetailsedit',
        type: "POST",
        data: $(this).serialize(),
        cache: false,
        success: function (data)
        {
            if(data.success){
                alert(data.success);
                location.reload();
            }
            else{
                alert(data.error);
            }
                
        }
    });
});


$("#soledocFile").on("change", function(){
    var file_data = $(this).prop('files')[0];
    var form_data = new FormData();
    form_data.append('soleimage', file_data);
    $.ajax({
        url: 'admin/users/upload_soledoc_img',
        type: "POST",
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.error)
                alert(data.error);
            if (data.success) {
                $("#soledoc_preview").attr('src', data.success);
                $("#soledoc_field").val(data.success);
            }
        }
    });
});

$("#partenrdocFile1").on("change", function()
{
    var file_data = $(this).prop('files')[0];
    var form_data = new FormData();
    form_data.append('documentinfo1', file_data);
    $.ajax({
        url: 'admin/users/upload_doc1',
        type: "POST",
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.error)
                alert(data.error);
            if (data.success) {
                $("#doc_preview1").attr('src', 'assets/kyc_document_panimage/' + data.success);
                $("#doc_field1").val(data.success);
            }
        }
    });
});


$("#partenrdocFile2").on("change", function()
{
    var file_data = $(this).prop('files')[0];
    var form_data = new FormData();
    form_data.append('documentinfo2', file_data);
    $.ajax({
        url: 'admin/users/upload_doc2',
        type: "POST",
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.error)
                alert(data.error);
            if (data.success) {
                $("#doc_preview2").attr('src', 'assets/kyc_document/' + data.success);
                $("#doc_field2").val(data.success);
            }
        }
    });
});

$(".edit_seller_kycdetails").submit(function (event)
{
    event.preventDefault();
    $.ajax({
        url: 'admin/users/editsellerkycdetails',
        type: "POST",
        data: $(this).serialize(),
        cache: false,
        success: function (data)
        {
            if(data.success){
                alert(data.success);
                location.reload();
            }
            else{
                alert(data.error);
            }
                
        }
    });
});
 $(document).on("click", "#user_seting_form", function(event) { 
    var dataString = $("#FormId").serialize();
     event.preventDefault();
     var url="admin/users/settinguser"
     $.ajax({
     type:"POST",
     url:url,
     data:dataString,
     success:function (data) {
        if(data.success){
            alert(data.success);
            location.reload();
        }
        else{
            alert(data.error);
        }
     }
     });  
}); 
$(document).on("click", "#user_support_form", function(event) { 
  var dataString = $("#FormId_support_category").serialize();
     event.preventDefault();
     var url="admin/users/supportcategory"
     $.ajax({
     type:"POST",
     url:url,
     data:dataString,
     success:function (data) {
        if(data.success){
            alert(data.success);
            location.reload();
        }
        else{
            alert(data.error);
        }
     }
     });   
});

$(".edit_notesdetails").submit(function (event){
    event.preventDefault();
    $.ajax({
        url: 'admin/users/notesdetailsedit',
        type: "POST",
        data: $(this).serialize(),
        cache: false,
        success: function (data)
        {   
            if(data.success){
                alert(data.success);
                location.reload();
            }
            else{
                alert(data.error);
            }
                
        }
    });
});



$("form#edit_ndrcall").submit(function (event)
{	
    event.preventDefault();
    $.ajax({
        url: 'admin/users/ndrcalledit',
        type: "POST",
        data: $(this).serialize(),
        cache: false,
        success: function (data)
        {   
            if(data.success){
                alert(data.success);
                location.reload();
            }
            else{
                alert(data.error);
            }
                
        }
    }); 
});

$('.getUserlist').select2({
    minimumInputLength: 3,
    placeholder: 'Select Seller',
    ajax: {
        url: baseUrl+'admin/analytics/getUserAjax',
        dataType: 'json',
        delay: 250,
        data: function (data) {
            return {
                searchTerm: data.term // search term
            };
        },
        processResults: function (response) {
            return {
                results:response
            };
        },
        cache: true
    },
});

$(".user_add_remove_tags_button").on("click", function (e) {
    e.preventDefault();
    var action = $(this).attr("data-tag-action");
   
    $.ajax({
        url: "tags/" + action,
        type: "GET",
        cache: false,
        success: function (data) {
            $("#user_add_remove_tags").html(data);
        },
    });
});

$(".edit_legal_entity_modal").submit(function (event)
{
    event.preventDefault();
    $.ajax({
        url: 'admin/users/legalentityupdate',
        type: "POST",
        data: $(this).serialize(),
        cache: false,
        success: function (data)
        {
            if(data.success){
                alert(data.success);
                location.reload();
            }
            else{
                alert(data.error);
            }
                
        }
    });
});
/*--Admin Seller View Page Ajax stop here--*/