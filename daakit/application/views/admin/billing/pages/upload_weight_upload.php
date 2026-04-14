<div class="row m-t-30">
    <div class="col-sm-6">
        <form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="exampleFormControlFile1">Upload Courier Billed File</label>
                    <input type="file" required="" name="importFile"  class="form-control-file" >
                    <small id="passwordHelpBlock" class="form-text">
                        <a href="assets/courier_billed.csv" class="text-info">Download Sample File</a>
                    </small>
                </div>
                <div class="form-group col-sm-6">
                    <button type="submit" style="margin-top: 20px;" class="btn btn-primary">Upload</button>    
                </div>
            </div>
        </form>
    </div>
</div>

