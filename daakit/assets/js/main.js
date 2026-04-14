//curent updated code after all changes :
$(document).ready(function () {
    $(document).ajaxStart(function () {
        document.getElementById("global-loader").style.display = "";
    });
    $(document).ajaxStop(function () {
        document.getElementById("global-loader").style.display = "none";
    });
    
    $.each($(".date-range-picker"), function () {
        $(this).daterangepicker({
            opens: 'left',
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(30, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
            },
            startDate: "10/01/2019",
            endDate: moment(),
        },
            function (start, end, label) {
                $("#date-min").val(start.format("YYYY-MM-DD"));
                $("#date-max").val(end.format("YYYY-MM-DD"));
                if (typeof dateRangeFunction == "function") {
                    dateRangeFunction();
                }
                //console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            }
        );
        if ($(this).attr("data-start-date").length != 0)
            $(this)
                .data("daterangepicker")
                .setStartDate(moment($(this).attr("data-start-date"), "YYYY-MM-DD"));
        if ($(this).attr("data-end-date").length != 0)
            $(this)
                .data("daterangepicker")
                .setEndDate(moment($(this).attr("data-end-date"), "YYYY-MM-DD"));
    });

    $(".show_hide_filter").on("click", function (e) {
        $("#filter_row").toggle();
        $(".show_hide_filter").toggle();
    });

    $(".shipnowbtn").on("click", function (e) {
        var order_id = $(this).attr("data-order-id");
        $.ajax({
            url: baseUrl+"orders/get_delivery_info/" + order_id,
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function (data) {
                $("#show_information").html(data);
            },
        });
    });

    $(".cancel_order").on("click", function (e) {
        var order_id = $(this).attr("data-order-id");

        if (!confirm("are you sure?")) return;

        $.ajax({
            url: baseUrl+"orders/cancel/",
            type: "POST",
            data: {
                order_id: order_id,
            },
            datatype: "JSON",
            cache: false,
            success: function (data) {
                if (data.success) location.reload();
                else if (data.error) alert_float(data.error);
            },
        });
    });

    $(".submit-ndr-response").on("click", function (e) {
        var ndr_id = $(this).attr("data-ndr-id");
        $("#ndr_id").val(ndr_id);
        if ($(".ndr_action_change option[value='change address']").length <= 0)
            $(".ndr_action_change").append(
                '<option value="change address">Change Address</option>'
            );

        if ($(".ndr_action_change option[value='change phone']").length <= 0)
            $(".ndr_action_change").append(
                '<option value="change phone">Change Phone Number</option>'
            );
    });

    $("#ndr_submit_form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: "ndr/action",
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function (data) {
                if (data.success) location.reload();
                else if (data.error) alert_float(data.error);
            },
        });
    });

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

    $(".credit_debit_wallet_button").on("click", function (e) {
        var user_id = $(this).attr("data-user-id");
        $("#wallet_user_id").val(user_id);
    });

    $("#adjust_wallet_form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: "admin/adjust_wallet",
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function (data) {
                if (data.success) location.reload();
                else if (data.error) alert_float(data.error);
            },
        });
    });

    $("#select_all_checkboxes").click(function () {
        if (this.checked) {
            $(".multiple_checkboxes").each(function () {
                $(this).prop("checked", true).trigger("change");
            });
        } else {
            $(".multiple_checkboxes").each(function () {
                $(this).prop("checked", false).trigger("change");
            });
        }
    });

    $(".bulk-pickup-button").on("click", function (event) {
        event.preventDefault();
        var shipping_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function () {
            shipping_ids.push($(this).val());
        });

        $.ajax({
            url: "bulk_pickup",
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success) location.reload();
                else if (data.error) alert_float(data.error);
            },
        });
    });


    $(".cargo-bulk-pickup-button").on("click", function (event) {
        event.preventDefault();
        var shipping_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function () {
            shipping_ids.push($(this).val());
        });

        $.ajax({
            url: "cargo_bulk_pickup",
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success) location.reload();
                else if (data.error) alert_float(data.error);
            },
        });
    });

    $(".bulk-list-button").on("click", function (event) {
        event.preventDefault();
        var shipping_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function () {
            shipping_ids.push($(this).val());
           
        });

        $.ajax({
            url: "pickup_list",
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success) window.open(data.success, "_blank");
                else if (data.error) alert_float(data.error);
            },
        });
    });

    $(".bulk-cancel-button").on("click", function (event) {
        event.preventDefault();
        if (!confirm("are you sure?")) return;
        var shipping_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function () {
            shipping_ids.push($(this).val());
        });

        $.ajax({
            url: baseUrl+"shipping/bulk_cancel",
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success) location.reload();
                else if (data.error) alert_float(data.error);
            },
        });
    });

    $(".admin-bulk-cancel-button").on("click", function (event) {
        event.preventDefault();
        if (!confirm("are you sure?")) return;
        var shipping_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function () {
            shipping_ids.push($(this).val());
        });

        $.ajax({
            url: "admin/bulk_cancel_process",
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success) location.reload();
                else if (data.error) alert_float(data.error);
            },
        });
    });

    $(".generate-label-button").on("click", function (event) {
        event.preventDefault();

        var shipping_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function () {
            shipping_ids.push($(this).val());
        });

        $.ajax({
            url: "generate_label",
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success) window.open(data.success, "_blank");
                else if (data.error) alert_float(data.error);
            },
        });
    });

    $(".bulk-ship-button").on("click", function (e) {
        $.ajax({
            url: baseUrl+"orders/getBulkShipCouriers/",
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function (data) {
                $("#show_information").html(data);
            },
        });
    });

    $(".recharge_wallet_button").on("click", function (e) {
            $(".recharge_wallet_button").html('Please wait...');
            payment_recharge();
    });
    
    

    $("#adjust_wallet_from_remittance_form").submit(function (e) {
        e.preventDefault();
        if (!confirm("Are you sure?")) return;
        $.ajax({
            url: "recharge_from_remittance",
            type: "POST",
            data: {
                amount: $("#remittance_recharge_amount").val(),
            },
            cache: false,
            success: function (data) {
                if (data.error) alert_float(data.error);
                if (data.success) {
                    alert_float("Wallet recharge successfull.");
                    location.reload();
                }
            },
        });
    });

    $(".bulk-cancel-order-button").on("click", function (event) {
        event.preventDefault();
        if (!confirm("are you sure?")) return;
        var order_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function () {
            order_ids.push($(this).val());
        });

        $.ajax({
            url: baseUrl+"orders/bulk_cancel",
            type: "POST",
            data: {
                order_ids: order_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success) location.reload();
                else if (data.error) alert_float(data.error);
            },
        });
    });

    $("#pricing_calculator_form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: baseUrl+"billing/calculate_pricing",
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function (data) {
                if (data.success) {
                    var price_list = data.success;
                    var content = "";
                    for (var i = 0; i < price_list.length; i++) {
                        content += "<tr>";
                        content += "<td>" + (i + 1) + "</td>";
                        content += "<td>" + price_list[i].name + "</td>";
                        content += "<td>" + price_list[i].courier_charges + "</td>";
                        content += "<td>" + price_list[i].cod_charges + "</td>";
                        content += "<td>" + price_list[i].total_price + "</td>";
                        content += "</tr>";
                    }
                    $("#calculated_price").show();
                    $("#calculated_price tbody").html(content);
                } else if (data.error) alert_float(data.error);
            },
        });
    });

    $(".create_new_employee_form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: "employees/add_employee",
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function (data) {
                if (data.success) location.reload();
                else if (data.error) alert_float(data.error);
            },
        });
    });

    $(".edit-employee-button").on("click", function (e) {
        var employee_id = $(this).attr("data-employee-id");
        $.ajax({
            url: "employees/editEmployee/" + employee_id,
            type: "GET",
            cache: false,
            success: function (data) {
                $(".create_new_employee").modal("show");
               
                $("#create_new_employee_form").html(data);
                $("#action_text").html('');
                $("#action_text").html('Edit');
            },
        });
    });

    $("#recharge_amount").on("change", function () {
        var recharge_amount = $(this).val(); // Use 'this' to refer to the changed select element
        $("#recharge_wallet_amount").val(recharge_amount);
    });

    $(".btn_awb_list_seller_wise").on("click", function (e) {
        var seller_id = $(this).attr("data-seller-id");

        $.ajax({
            url: "admin/remittance/seller_awb_list/" + seller_id,
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function (data) {
                $("#seller_awb_list_modal").html(data);
            },
        });
    });

    $(".btn_awb_list_expected").on("click", function (e) {
        var seller_id = $(this).attr("data-seller-id");

        $.ajax({
            url: "admin/remittance/sellerExpectedAwbList/" + seller_id,
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function (data) {
                $("#seller_awb_list_modal").html(data);
            },
        });
    });

    $(".expand_daily_order_summary").on("click", function (e) {
        e.preventDefault();
        var expand_date = $(this).attr("data-expand-date");
        if ($(".expanded_daily_order_summary_tr_" + expand_date).is(":visible")) {
            $(".expanded_daily_order_summary_tr_" + expand_date).hide();
        } else {
            $(".expanded_daily_order_summary_tr").hide();
            $.ajax({
                url: "reports/daily_order_summary_products",
                type: "POST",
                cache: false,
                data: {
                    expand_date: expand_date,
                },
                success: function (data) {
                    if (data.success) {
                        $(".expanded_daily_order_summary_tr_" + expand_date).show();
                        $(".expanded_daily_order_summary_td_" + expand_date).html(
                            data.success
                        );
                    } else if (data.error) alert_float(data.error);
                },
            });
        }
    });

    $(".expand_state_order_summary").on("click", function (e) {
        e.preventDefault();
        var expand_state = $(this).attr("data-expand-state");
        var start_date = $(this).attr("data-start-date");
        var end_date = $(this).attr("data-end-date");
        if ($(".expanded_state_order_summary_tr_" + expand_state).is(":visible")) {
            $(".expanded_state_order_summary_tr_" + expand_state).hide();
        } else {
            $(".expanded_state_order_summary_tr").hide();
            $.ajax({
                url: "reports/state_order_summary_couriers",
                type: "POST",
                cache: false,
                data: {
                    expand_state: expand_state,
                    start_date: start_date,
                    end_date: end_date,
                },
                success: function (data) {
                    if (data.success) {
                        $(".expanded_state_order_summary_tr_" + expand_state).show();
                        $(".expanded_state_order_summary_td_" + expand_state).html(
                            data.success
                        );
                    } else if (data.error) alert_float(data.error);
                },
            });
        }
    });

    $(".fill_bulk_ndr").on("click", function (event) {
        event.preventDefault();
        var ndr_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function () {
            ndr_ids.push($(this).val());
        });

        $(".ndr_action_change option[value='change address']").remove();
        $(".ndr_action_change option[value='change phone']").remove();

        if (ndr_ids.length < 1) {
            alert_float("Please select records");
            return;
        }

        $("#ndr_id").val(ndr_ids);
        $("#ndr_submit_model").modal("show");
    });

    $(".add_remove_tags_button").on("click", function (e) {
        e.preventDefault();
        var action = $(this).attr("data-tag-action");
        var id = $(this).attr("data-id");
        $.ajax({
            url: "../tags/" + action,
            type: "GET",
            cache: false,
            success: function (data) {
                $("#add_remove_tags").html(data);
            },
        });
    });

    $(".add_remove_single_tags_button").on("click", function (e) {
        e.preventDefault();
        var action = $(this).attr("data-tag-action");
        var id = $(this).attr("data-id");
        $.ajax({
            data: {
                id: id,
            },
            datatype: "JSON",
            cache: false,
            url: "../tags/" + action,
            type: "POST",
            cache: false,
            success: function (data) {
                $("#single_add_remove_tags").html(data);
            },
        });
    });

    $(".multiple_checkboxes").change(function () {
        var selected_chkbx = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function () {
            selected_chkbx.push($(this).val());
        });

        if (selected_chkbx.length > 0) {
            $(".multiple_select_count").html(selected_chkbx.length);
            $(".action_row_selected").show();
            $(".action_row_default").hide();
        } else {
            $(".action_row_selected").hide();
            $(".action_row_default").show();
        }
    });
    $("#select_all_checkboxes").change(function () {
        var selected_chkbx = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function () {
            selected_chkbx.push($(this).val());
        });

        if (selected_chkbx.length > 0) {
            $(".multiple_select_count").html(selected_chkbx.length);
            $(".action_row_selected").show();
            $(".action_row_default").hide();
        } else {
            $(".action_row_selected").hide();
            $(".action_row_default").show();
        }
    });
    

    $(".generate-invoice-button").on("click", function (event) {
        event.preventDefault();
        var shipping_ids = [];
        var pageid = $(this).attr("rel");
        if (pageid != "shipping") {
            shipping_ids.push(pageid);
        } else {
            $.each($("input[class='multiple_checkboxes']:checked"), function () {
                shipping_ids.push($(this).val());
            });
        }
        $.ajax({
            url: "../orders/generateinvoice",
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success) window.open(data.success, "_blank");
                else if (data.error) alert_float(data.error);
            },
        });
    });
    $('.coupon_apply_button').on('click', function (e) {
        var ammount = $("#recharge_wallet_amount").val();
        var coupon_code = $("#coupon_code").val();
        // alert_float(ammount);
        if (coupon_code != '' && ammount != '') {
            $.ajax({
                url: 'check_coupon_code',
                type: "POST",
                data: {
                    amount: ammount,
                    coupon_code: coupon_code,
                },
                cache: false,
                success: function (dataResult) {
                    // console.log(dataResult);
                    if (dataResult.statusCode == 200) {
                        $('#apply,#coupon_code').hide();
                        $("#coupon_code_apply").val(coupon_code);
                        $('#message').html(dataResult.success).css("color", "green");
                        $('#message_description').html(dataResult.descriptions).css("font-size", "11px");
                        $('#data_amount button,#recharge_wallet_amount').prop('disabled', true);
                        $('#cancelCode').show();
                    }
                    else if (dataResult.statusCode == 201) {
                        $('#data_amount button, input').prop('disabled', false);
                        $('#message').html(dataResult.error).css("color", "red");
                        $('#message_description').html('');
                        $("#coupon_code_apply").val("");
                        $('#cancelCode').show();
                        $('.recharge_wallet_button').html("Recharge");

                    }
                }
            });
        } else {
            $('#message').html("Enter a valid coupon code !").css("color", "red");
            $("#coupon_code_apply").val("");
            $('#message_description').html("");
            $('#cancelCode').show();
            $('#data_amount button, input').prop('disabled', false);
            $('.recharge_wallet_button').html("Recharge");
        }
    });
  
    $("#coupon_hide_show").click(function () {
        $('#coupon_div_show').show();
        $('#coupon_label_show').hide();
        $('#message,#message_description').html('');
        $('#coupon_code_apply,#coupon_code').val('');
        $('#data_amount button, input').prop('disabled', false);
        $('#apply,#coupon_code').show();
        $('.recharge_wallet_button').html("Recharge");
    });

    $("#cancelCode").click(function () {
        $('#coupon_div_show').hide();
        $('#coupon_label_show').show();
        $('#message,#message_description').html('');
        $('#coupon_code_apply,#coupon_code').val('');
        $('#data_amount button, input').prop('disabled', false); 
        $('.recharge_wallet_button').html("Recharge");
    });
    $('.cancel_cargo').on('click', function(e) {
        var order_id = $(this).attr('data-order-id');

        if (!confirm('are you sure?'))
            return;

        $.ajax({
            url: 'cargo_cancel/',
            type: "POST",
            data: {
                order_id: order_id,
            },
            datatype: "JSON",
            cache: false,
            success: function(data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert_float(data.error);
            }
        });
    });
    
    $(".generate-singlelabel").on("click", function(event) {
        event.preventDefault();

        var shipping_id = $(this).attr("data-shipping-id");
        var shipping_ids = [];
        shipping_ids.push(shipping_id);
        $.ajax({
            url:  baseUrl+"shipping/generate_label",
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function(data) {
                if (data.success) window.open(data.success, "_blank");
                else if (data.error) alert_float(data.error);
            },
        });
    });

    $(".generate-singleinvoice").on("click", function(event) {
        event.preventDefault();
        var shipping_id = $(this).attr("data-shipping-id");
        var shipping_ids = [];
        var pageid = $(this).attr("rel");
        if (pageid != "shipping") {
            shipping_ids.push(pageid);
        } else {
            shipping_ids.push(shipping_id);
        }
        $.ajax({
            url: baseUrl+"orders/generateinvoice",
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function(data) {
                if (data.success) window.open(data.success, "_blank");
                else if (data.error) alert_float(data.error);
            },
        });
    });
   
    $("#cancel-shipment").on("click", function (event) {
        event.preventDefault();
        if (!confirm("are you sure?")) return;
        var shipping_id = $(this).attr("data-shipping-id");
        var shipping_ids = [];

        shipping_ids.push(shipping_id);

        $.ajax({
            url: baseUrl+"shipping/bulk_cancel",
            type: "POST",
            data: {
                shipping_ids: shipping_ids,
            },
            cache: false,
            success: function (data) {
                if (data.success) location.reload();
                else if (data.error) alert_float(data.error);
            },
        });
    });
});

function loader(show_hide = "show") {
    alert_float(show_hide);
    if (show_hide == "show") $(".load_screen").show();
    else $(".load_screen").hide();
}

$(".js-example-tokenizer").select2({
    tags: true,
    tokenSeparators: [","],
});

/*----Remark Text limit in seller escalations----*/
$("#remarktextarea").keyup(function (e) {
    var tval = $("#remarktextarea").val(),
        tlength = tval.length,
        set = 300,
        remain = parseInt(set - tlength);
    $(".countdown").text(remain);
    if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
        $("#remarktextarea").val(tval.substring(0, tlength - 1));
    }
});
function paytm_gateway_recharge() {
    //$('.recharge_wallet_form').modal('hide');
    var amount = $('#recharge_wallet_amount').val();
    $.ajax({
        url: 'create_payment',
        type: "POST",
        data: {
            amount: amount,
            coupon_code_apply: $("#coupon_code_apply").val(),
            payment_mode:'paytm'
        },
        cache: false,
        success: function (data) {
            if (data.error)
                alert_float(data.error);
            if (data.success) {
                payment_id = data.success.payment_id;
                pay_amount = data.success.amount;
                $.ajax({
                    url: 'findPaytmToken',
                    type: "post",
                    data: {
                        payment_id: data.success.payment_id,
                        amount: data.success.amount //$('#recharge_wallet_amount').val(),
                    },
                    cache: false,
                    success: function (data) {
                        
                        if (data.error)
                        {
                            //$('.recharge_wallet_form').modal('show');
                            alert_float(data.error);
                        }
                            if (data.token) {
                            pay_Paytm(data.token, payment_id, amount);
                        }    
                    }
                });
            }
        }
    });
}                            
 


function payment_recharge() {
    var amount = $('#recharge_wallet_amount').val();
    $.ajax({
    url: 'create_payment',
    type: "POST",
    data: {
        amount: amount,
        coupon_code_apply: $("#coupon_code_apply").val(),
        payment_mode: $("#recharge_option").val(),
    },
    cache: false,
    success: function (data) {
        if (data.error){
            $(".recharge_wallet_button").html('Recharge');
            alert_float(data.error);
        }
        if (data.success) {
            payment_id = data.success.payment_id;
            pay_amount = data.success.amount;
            $.ajax({
                //url: 'findPaytmToken',
                url: 'generateLinkData',
                type: "post",
                data: {
                    payment_id: data.success.payment_id,
                    amount: data.success.amount,
                    coupon_code_apply: $("#coupon_code_apply").val(),
                },
                cache: false,
                success: function (data) {
                        $(".recharge_wallet_button").html('Recharge');
                        var easebuzzCheckout = new EasebuzzCheckout('3UDGABY1AP', 'prod'); // for test environment pass "test" 
                        // Replace with your access key
                        var accessKey = data.data; // access key received via Initiate Payment
                        var payment_id = data.payment_id; // access key received via Initiate Payment
                        var options = {
                            access_key: accessKey,
                            onResponse: (response) => {
                                paymentGatewayResponse(response)
                            },
                            theme: "#123456" // color hex
                        };
                        // Automatically initiate payment
                        easebuzzCheckout.initiatePayment(options);
                    }
                });
            }
        }
    });
}

    
 function change_recharge_option()
 {
   var checkd= $("input[name='recharge_option']:checked").val()
        if(checkd=='hdfc')
        {
           hdfc_razorpay_recharge();
           $(".other_gategay_button").hide();
           $(".hdfc_gategay_button").show();
        }
        else
        {
            $(".other_gategay_button").show();
            $(".hdfc_gategay_button").hide();
        }
 }
 

function cancelpaytm_payment()
{
    $('.payment_option, .payment_option_button').show();$('.qrcode_section, .qrcode_section_button').hide();$('.qrcode_section').html('');
    $('#cancelCode').click();
}

function razorpay_recharge() {
    //$('.recharge_wallet_form').modal('hide');
    $.ajax({
        url: "create_payment",
        type: "POST",
        data: {
            amount: $("#recharge_wallet_amount").val(),
            coupon_code_apply: $("#coupon_code_apply").val(),
            payment_mode:'razorpay',
        },
        cache: false,
        success: function (data) {
            if (data.error)
            {
                alert_float(data.error);
                //$('.recharge_wallet_form').modal('show');
            } 
            if (data.success) {
                pay_razorpay(data.success.amount, data.success.payment_id);
            }
        },
    });
}


function hdfc_razorpay_recharge() {
   
    $.ajax({
        url: "create_payment_hdfc",
        type: "POST",
        data: {
            amount: $("#recharge_wallet_amount").val(),
            coupon_code_apply: $("#coupon_code_apply").val(),
            payment_mode:'razorpay',
        },
        cache: false,
        success: function (data) {
            if (data.error)
            {
                alert_float(data.error);
            } 
            if (data.success) {
                $(".hdfc_order_id").val("");
                $(".hdfc_callback_url").val("");
                $(".hdfc_user_name").val("");
                $(".hdfc_user_email").val("");
                $(".hdfc_user_contact").val("");
                $(".hdfc_user_transaction_id").val("");
                $(".hdfc_user_payment_id").val("");
                $(".hdfc_order_amount").val(data.success.amount);
                $(".hdfc_order_id").val(data.success.order_id);
                $(".hdfc_callback_url").val(data.success.callback_url);
                $(".hdfc_user_name").val(data.success.user_name);
                $(".hdfc_user_email").val(data.success.user_email);
                $(".hdfc_user_contact").val(data.success.user_contact);
                $(".hdfc_razorpay_key").val(data.success.hdfc_razorpay_key); 
                $(".hdfc_user_transaction_id").val(data.success.transaction_id);
                $(".hdfc_user_payment_id").val(data.success.payment_id); 
                // hdfc_pay_razorpay(data.success.amount, data.success.payment_id ,data.success.payment_mode,data.success.order_id);
            }
        },
    });
}

function getPaymentStatus(payment_id)
{
    if($('.razorpay-container').css('display') == 'none' && $('.qrcode_section').css('display') != 'none' && $('#paytm-checkoutjs').length==0){
        $.ajax({
            url: "getOrderStatus",
            type: "POST",
            data: {
                amount: $("#recharge_wallet_amount").val(),
                gateway: 'paytm',
                order_id: payment_id,
            },
            cache: false,
            global:false,
            success: function (data) {
                if (data==0) 
                {
                    setTimeout(function(){
                        getPaymentStatus(payment_id);
                        },5000) // call again after 5 seconds
                }
                if (data==1) {
                    location.reload();
                }
            },
        });
    }
  
}

function payment_gateway(){

   // console.log($('input[name="recharge_option"]:checked').val());return false;
        
    if ($('input[name="recharge_option"]:checked').val() == "paytm") {
        paytm_gateway_recharge();
    } else if ($('input[name="recharge_option"]:checked').val() == "razorpay") {
        razorpay_recharge();
    }
    // else if ($('input[name="recharge_option"]:checked').val() == "hdfc") {
    //     hdfc_razorpay_recharge();
    // }

    
}

function disablebutton()
{
    document.getElementById("warehouse_submit").disabled = true;
}

function paymentGatewayResponse(response)
{
    $.ajax({
        url: "../payment/easebuzz_response",
        type: "POST",
        contentType: 'application/json',
        data: JSON.stringify(response),
        cache: false,
        success: function (data) {
            if (data.success) {
                alert_float(data.success);
                window.location.href = '../analytics';
            }
            else if (data.error) alert_float(data.error);
        },
    });
}


// Generate float alert
function alert_float(message, type, timeout=false) {
    if(!type)
        type='error';

    if(type=='error'){
        return $.growl.error({
            message: message
        });
    }
    if(type=='notice'){
        return $.growl.notice({
            message: message
        });
    }
    if(type=='warning'){
        return $.growl.warning({
            message: message
        });
    }
}

let submit_status = true;
$(document).ready(function() {
$(".searchByAwb").on('click', function() {
        var url = $(this).attr("href").replace("#", "");
        //get the selected option value
        $('#search_for').val(url);
        switch (url) {
            case "shipment_b2c":
                    $("#AwbSerach").attr('action', baseUrl+'shipping/all');
                break;
            case "ndr":
                $("#AwbSerach").attr('action', baseUrl+'ndr');
                break;
            case "wallet_history":
                $("#AwbSerach").attr('action', baseUrl+'billing/version/seller_recharge_logs');
                break;
            case "shipping_charges":
                $("#AwbSerach").attr('action', baseUrl+'billing/version/seller_shipping_charges');
                break;
            default:
                $("#AwbSerach").attr('action', baseUrl+'shipping/all');
        }
        $("#AwbSerach").submit();
    });
});
   