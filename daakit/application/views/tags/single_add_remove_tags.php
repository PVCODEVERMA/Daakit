<style type="text/css">
    .bootstrap-tagsinput .tag {
        margin-right: 2px;
        color: white !important;
        background-color: #0d6efd;
    }
    .bootstrap-tagsinput {
        width: 100%;
    }
</style>
<form class="single_add_remove_tags_form" method="post">
    <div class="modal-header">
        <h5 class="modal-title" id="mySmallModalLabel1"><?= ucwords($action); ?> Tags</h5>

    </div>

    <div class="modal-body">
    <?php  
           $tags=array_merge(['Call Not Answered/Disconnected','Call Back Later','Order Confirmed','Order Cancelled'],$tags);
           if (!empty($tags)) { ?>
            <div class="row p-t-10 p-b-10">
                <strong><i class="fa fa-bell" aria-hidden="true"></i> Note</strong>
                <p>* Click to add tags</p>
                <div class="col-sm-12 m-t-5">
                    <?php
                    foreach ($tags as $tag) {
                    ?>
                        <button type="button" style="margin-bottom: 8px;" class="btn btn-sm btn-secondary btn-sm btn-tag push_this_tag" data-tag-value="<?= $tag; ?>"><?= ucwords($tag) ?></button>
                    <?php
                    }
                    ?>
                </div>
                <!-- <div class="col-sm-12 m-t-5 fetced_data" >
                </div> -->
            </div>
        <?php } ?>

        <div class="col-sm-12 m-b-10">
            <div class="form-group">
                <label for="inputPassword" class="col-form-label"><b>Tag(s)</b></label>
                <input type="text" class="form-control tagsinput fetchtags" data-role="tagsinput" name="tags" >
            </div>
        </div>

    </div>
   <!-- // !empty($source) ? $source : ''; -->
    <input type="hidden" name="action" value="<?= $action; ?>">
    <input type="hidden" name="type" value="<?= $type; ?>">
    <input type="hidden" id="_ids" name="ids" value="<?= !empty($id) ? $id : ''; ?>">
    <input type="hidden" id="applied_tags" name="applied_tags" value="<?= !empty($applied_tags) ? $applied_tags : ''; ?>">


    <div class="modal-footer">
        <button type="submit" class="btn btn-sm btn-secondary">Save</button>
        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>

    </div>

</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script>
$(document).ready(function() {
        $('.single_add_remove_tags_form').on('submit', function(event) {
            event.preventDefault();
            var selected_ids = '';
            selected_ids = $('#_ids').val();
            var values = {};
            $.each($(this).serializeArray(), function(i, field) {
                values[field.name] = field.value;
            });
            $.ajax({
                url: '<?php echo base_url("tags/single_add_remove");?>',
                type: "POST",
                data: {
                    selected_ids: selected_ids,
                    type: values['type'],
                    action: values['action'],
                    tags: values['tags'],
                },
                cache: false,
                success: function(data) {
                    if (data.success)
                        location.reload();
                    else if (data.error)
                        alert_float(data.error);

                }
            });

        });
        $(".tagsinput ").on('beforeItemAdd', function(event) {
        selected_ids = $('#_ids').val();
        $.ajax({
                url: '<?php echo base_url("tags/fetch_tags");?>',
                type: "POST",
                data: {
                    id: selected_ids,
                    values: event.item,
                },
                cache: false,
                success: function(data) {
                // alert(data)

                    if (data!='0'){
                        var button='<button type="button" class="btn btn-secondary btn-sm btn-tag push_this_tag fetch_tags" style="margin-left: 5px" data-tag-value="'+data+'" >'+data+'</button>';
                        $( ".fetced_data" ).append( button );
                    }


                }
            });
        });
    $('.tagsinput').tagsinput();
    var applied_tags = $('#applied_tags').val();
        $('.tagsinput').tagsinput('add', applied_tags);

    $('.push_this_tag').on('click', function(e) {
        e.preventDefault();
        var tag_value = $(this).attr('data-tag-value');
        $('.tagsinput').tagsinput('add', tag_value);

    });
});
</script>
