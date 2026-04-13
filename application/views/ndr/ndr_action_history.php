<table class=" table sub-table mb-0">
    <thead>
        <tr>
            <th>Date</th>
            <th>Action</th>
            <th>Updated Details (If Any)</th>
            <th>Remarks</th>
            <th>Attempt #</th>
            <th>By</th>
            <th>Call Recording</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($history)) {
            foreach ($history as $his) {
        ?>
                <tr>
                    <td><?= date('Y-m-d H:i', $his->created); ?></td>
                    <td>
                        <?php
                        switch (strtolower($his->action)) {
                            case 'ndr':
                                echo 'NDR Raised';
                                break;
                            case 're-attempt':
                                echo 'Re-attempt';
                                break;
                            default:
                                echo ucwords($his->action);
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        switch (strtolower($his->action)) {
                            case 're-attempt':
                                if (!empty($his->re_attempt_date))
                                    echo 'Scheduled Date : ' . date('Y-m-d', $his->re_attempt_date);
                                else
                                    echo 'N/A';

                                break;
                            case 'change address':
                                echo ucwords($his->customer_details_name) . "<br/>";
                                echo $his->customer_details_address_1 . "<br/>";
                                if (!empty($his->customer_details_address_2)) {
                                    echo $his->customer_details_address_2 . "<br/>";
                                }
                                break;
                            case 'change phone':
                                echo $his->customer_contact_phone;
                                break;
                            default:
                                echo 'N/A';
                        }
                        ?>
                    </td>
                    <td><?= $his->remarks; ?></td>
                    <td><?= $his->attempt; ?></td>
                    <td><?= ucwords($his->source); ?></td>
                    <td>
					<?php if($his->call_recording != '') { ?> <audio src="<?php echo $his->call_recording ; ?>" controls></audio> <?php } else { echo "N/A" ; } ?></td>
                </tr>
        <?php
            }
        }
        ?>
    </tbody>
</table>