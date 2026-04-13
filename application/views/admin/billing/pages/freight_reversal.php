<div class="row m-t-30">
    <div class="col-sm-12 m-b-20">
        <b>Revert All charges for AWBs</b></br>
        <p>Check upload status in file downloaded after update</p>
    </div>

    <div class="col-sm-6">
        <?php if (in_array('freight_reversal', $user_details->permissions)) { ?>
            <form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data" id="formId">
                <div class="form-row">
                <div class="form-group col-sm-6">
                        <label for="exampleFormControlFile1">Select Type</label>
                        <select class="form-control" name="type" >
                            <option value="">Select..</option>
                            <option value="all">All</option>
                            <option value="forward">Forward</option>
                            <option value="rto">RTO</option>


                        </select>
                     
                    </div>
                    <div class="form-group col-sm-6" style="margin-top:10px;padding-left: 50px;">
                        <label for="exampleFormControlFile1"></label>
                        <input type="file" required="" name="importFile" class="form-control-file">
                        <small id="passwordHelpBlock" class="form-text">
                            <a href="assets/reverse_freight.csv?v=1" class="text-info">Download Sample File</a>
                        </small>
                    </div>
                    <div class="form-group col-sm-6">
                        <button type="submit" style="margin-top: 20px;" class="btn btn-primary reloadPage">Upload</button>
                    </div>
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<script>
        $('.reloadPage').on('click', function() {
            $("#formId")[0].submit();
            setTimeout(function(){
                $("#formId")[0].reset();
            }, 300);
        });
</script>