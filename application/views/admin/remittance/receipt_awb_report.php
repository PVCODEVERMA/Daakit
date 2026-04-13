<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered border-bottom dataTable no-footer">
            <thead>
                <tr>
                    <th>#</th>
                    <th>AWB Number</th>
                    <th>Amount</th>
                    <th>Seller</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($records)) {
                    $i = 1;
                    foreach ($records as $record) {
                        ?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= $record->awb_number; ?></td>
                            <td><?= $record->receipt_amount; ?></td>
                            <td><?= !empty($record->user_company) ? $record->user_company : $record->user_fname . ' ' . $record->user_lname; ?></td>
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