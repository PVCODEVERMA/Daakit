<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Credit Notes - <?= $invoice_prefix.sprintf('%03d', $invoice->inv_no); ?></title>
</head>
<body style="font-family: Arial, sans-serif; margin: 40px;">

  <h2 style="text-align: center; margin-bottom: 5px;">Daakit | Credit Note</h2>
  <h3 style="text-align: center; margin-top: 0; margin-bottom: 20px;">Credit Note</h3>

  <table style="width: 100%; font-size: 14px; margin-bottom: 20px;">
    <tr>
      <td><strong>Acknowledge No:</strong> N/A</td>
      <td style="text-align: right;"><strong>Acknowledge Date:</strong> N/A</td>
    </tr>
    <tr>
      <td><strong>IRN No:</strong> N/A</td>
    </tr>
  </table>
    <?php list($product,$hsn)=explode("(",$product_name['name']);?>
  <table style="width: 100%; font-size: 14px; margin-bottom: 20px;">
    <tr>
      <td style="width: 33.33%; vertical-align: top;">
        <strong>BILL TO:</strong><br><br>
        <?= !empty($company->company_name) ? ucwords($company->company_name) : ''; ?><br>
        <?= !empty($company->cmp_address) ? ucwords($company->cmp_address) : ''; ?><br>
        <?= !empty($company->cmp_city) ? ucwords($company->cmp_city) : ''; ?><br />
        <?= !empty($company->cmp_state) ? ucwords($company->cmp_state) : ''; ?> - <?= !empty($company->cmp_pincode) ? ucwords($company->cmp_pincode) : ''; ?><br />
        <strong>State Code:</strong> <?= !empty($state_code) ? ucwords($state_code) : ''; ?><br />
        <strong>GSTIN:</strong> <?= !empty($company->cmp_gstno) ? ucwords($company->cmp_gstno) : ''; ?><br />
        <!-- Place of supply: <?= !empty($place_of_supply) ? ucwords($place_of_supply) : ''; ?><br />
        <?= !empty($user->phone) ? 'Mobile: ' . $user->phone : ''; ?> -->
      </td>
      <td style="width: 33.33%; vertical-align: top;">
        <strong>SOLD BY:</strong><br><br>
        DAAKIT TECHNOLOGIES PRIVATE LIMITED, <br>
        DC 158, DABUA COLONY, NIT SECTOR 50,<br>
        Faridabad NIT,<br>
        Faridabad- 121001, Haryana<br>
        <strong>GSTN:</strong> 06AAKCD5605P1Z9
      </td>
      <td style="width: 33.33%; vertical-align: top;">
        <strong>CREDIT NOTE DETAILS:</strong><br><br>
        CN NO. #:</strong> <?= $invoice_prefix . sprintf('%03d', $invoice->inv_no); ?><br>
        CN DATE:</b> <?= date('d M Y', $invoice->created); ?><br>
        CN PERIOD:</b> <?= ucwords($invoice->month) ?><br><br>
        <table style="
            background-color: #ffe5e5;
            border: 2px solid #ffe5e5;
            font-size: 12px;
            color: #ff0000;
            text-align: center;
        ">
        <tr>
            <td style="padding: 2px 4px;border-radius:5px;">
            DUE
            </td>
        </tr>
        </table>

      </td>
    </tr>
  </table>

  <table style="width: 100%; font-size: 14px; margin-bottom: 20px;">
    <tr>
      <td></td>
    </tr>
  </table>

  <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; font-size: 14px; margin-bottom: 0;">
    <thead>
      <tr style="background-color: #f2f2f2;">
        <th style="border: 1px solid #000; padding: 8px;">#</th>
        <th style="border: 1px solid #000; padding: 8px;">Particulars</th>
        <th style="border: 1px solid #000; padding: 8px;">HSN/SAC</th>
        <th style="border: 1px solid #000; padding: 8px;">Shipments Count</th>
        <th style="border: 1px solid #000; padding: 8px;">Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">1</td>
        <td style="border: 1px solid #000; padding: 8px;"><?= $product; ?></td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;"><?= $hsn_code = preg_replace("/[^0-9]/", "", $hsn); ?></td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;"><?= $total_shipment; ?></td>
        <td style="border: 1px solid #000; padding: 8px; text-align: right;">&#8377; <?= $invoice->pre_gst; ?></td>
      </tr>
    </tbody>
  </table>

  <!-- Terms + Grand Total side by side -->
  <table style="width: 100%; font-size: 14px; border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; border-collapse: collapse; margin-bottom: 40px;">
    <tr>
      <td style="width: 50%;border: 1px solid #000; padding: 8px;">
        <strong>Terms & Conditions:</strong><br>
        <div style="padding-left: 10px; font-size: 14px;">
            • All Cheques/DD in favour of 'DAAKIT Technologies Pvt. Ltd'.<br>
            • For any queries feel free to contact your account manager.<br>
            • Any dispute subject to Faridabad, Haryana jurisdiction.<br>
            • E. & O.E.<br>
            • This is a computer generated receipt and does not require physical signature.
        </div>
      </td>
      <td style="width: 50%;border: 1px solid #000; padding: 8px;">
        <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
          <tr>
            <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">SUBTOTAL</td>
            <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">&#8377; <?= $invoice->pre_gst; ?></td>
          </tr>
          <?php 
          if($invoice->igst){
            ?>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">IGST (18%)</td>
                <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">&#8377; <?= $invoice->igst; ?></td>
            </tr>
            <?php
          }
          if(!$invoice->igst){
            ?>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">SGST @ 9%</td>
                <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">&#8377; <?= $invoice->sgst; ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">CGST @ 9%</td>
                <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">&#8377; <?= $invoice->cgst; ?></td>
            </tr>

            <?php
            }?>
            <tr style="font-weight: bold;">
                <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">GRAND TOTAL</td>
                <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">&#8377; <?= $invoice->total_amount; ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">Paid / Adjusted</td>
                <td style="padding: 8px; border-bottom: 1px solid #000; text-align: right;">₹ 0.00</td>
            </tr>
          
          <tr style="font-weight: bold;">
            <td style="padding: 8px; text-align: right;">Due Amount</td>
            <td style="padding: 8px; text-align: right;">&#8377; <?= $invoice->total_amount; ?></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- <div style="font-size: 14px; margin-bottom: 20px;">
    <strong>Payment method:</strong> NEFT/RTGS<br>
    <strong>Bank Name:</strong> YES Bank<br>
    <strong>Account #:</strong> 026363200000854<br>
    <strong>IFSC Code:</strong> YESB0000263
  </div> -->

  <!-- Thank you and Authorised Signatory right aligned -->
  <div style="text-align: right; font-size: 14px; margin-top: 50px;">
    <p>Thank you for trusting and doing business with Daakit Technologies Pvt. Ltd.</p>
    <img src="<?= base_url('assets/images/stamp.jpeg'); ?>" style="max-width: 200px; max-height: 150px;">
    <p><strong>Authorised Signatory</strong></p>
  </div>

</body>
</html>
