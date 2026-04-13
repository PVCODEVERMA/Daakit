<style>.btn-tag {
    font-size: .7875rem;
    line-height: 0.9;
    padding: 0.2rem 0.75rem;
    border-radius: 0.2rem;
}
 </style>
<form class="add_remove_tags_form" method="post">
    <div class="modal-header">
        <h5 class="modal-title" id="mySmallModalLabel"><?= ucwords($action); ?> Tags</h5>

    </div>

    <div class="modal-body">
        <div class="m-b-10">
            <div class="form-group">
                <label for="inputPassword" class="col-form-label">Tag(s)</label>
                <input type="text" class="form-control tagsinput" data-role="tagsinput" required="" name="tags">
            </div>
        </div>
        <?php if (!empty($tags)) { ?>
            <div class="row border-top p-t-10 p-b-10">
                <div class="col-sm-12">
                    <b>Existing Tags</b>
                </div>
                <div class="col-sm-12 m-t-5">
                    <?php
                    foreach ($tags as $tag) {
                    ?>
                        <button type="button" class="btn btn-secondary btn-sm btn-tag push_this_tag" data-tag-value="<?= $tag; ?>"><?= ucwords($tag) ?></button>
                    <?php
                    }
                    ?>

                </div>
            </div>
        <?php } ?>
    </div>
    <input type="hidden" name="action" value="<?= $action; ?>">
    <input type="hidden" name="type" value="<?= $type; ?>">

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

    </div>

</form>

<script>
    $('.tagsinput').tagsinput();
    $('.push_this_tag').on('click', function(e) {
        e.preventDefault();
        var tag_value = $(this).attr('data-tag-value');
        $('.tagsinput').tagsinput('add', tag_value);
    });
    
    $(".add_remove_tags_form").submit(function(event) {
        event.preventDefault();

        var selected_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function() {
            selected_ids.push($(this).val());
        });


        var values = {};
        $.each($(this).serializeArray(), function(i, field) {
            values[field.name] = field.value;
        });

        $.ajax({
            url: 'tags/add_remove',
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
                    alert(data.error);

            }
        });

    });
</script>