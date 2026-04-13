<div class="row m-t-10">
    <div class="table-responsive">
        <table class="table table-bordered border-bottom dataTable no-footer" id="responsive-datatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Receipt ID</th>
                    <th>Carrier</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>UTR No.</th>
                    <th>Download</th>
                    <th>Report</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($history)) {
                    $i = 1;
                    foreach ($history as $hist) {
                        ?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= date('Y-m-d', $hist->created); ?></td>
                            <td><?= $hist->id; ?></td>
                            <td><?= ucwords($hist->courier_name); ?></td>
                            <td><?= $hist->amount; ?></td>
                            <td><?= date('M d, Y', $hist->payment_date); ?></td>
                            <td><?= $hist->utr_number; ?></td>
                            <td><a href="<?php echo base_url("assets/cod_receipts/$hist->file_name");?>" class="btn btn-sm btn-outline-info"><i class="fa fa-download"></i></a></td>
                            <td><a href="<?php echo base_url("admin/remittance/receipt/$hist->id/awb");?>" class="btn btn-sm btn-outline-primary">Details</a></td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
                ?>

            </tbody>
        </table>
    </div>    
</div>

<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
    </div>
    <div class="col-sm-12 col-md-6">
        <ul class="pagination" style="float: right;
            margin-right: 0px;">
            <?php if (isset($pagination)) { ?>
                <?php echo $pagination ?>
            <?php } ?>
        </ul>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
      $('#responsive-datatable').DataTable({
          "aoColumnDef": [
              null,
              null,
              null,
              null,
              null,
              {
                  "sType": "numeric"
              },
              null,
              {
                  "sType": "string"
              },
              null,
              null,
              null,
              null
          ],
          aoColumnDefs: [{
              orderable: false,
              aTargets: [0]
          }],
          'aaSorting': [
              [3, 'desc']
          ],
          "paging": false, // false to disable pagination (or any other option)
          "filter": false,
          "info": false,
      });

  });
  </script>