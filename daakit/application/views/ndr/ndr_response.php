<!DOCTYPE html>
<html>

<head>
    <title>Response</title>
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/vendor/bootstrap/css/bootstrap.min.css">
    <script src="<?= base_url(); ?>assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-KK53LP4');
    </script>
    <!-- End Google Tag Manager -->
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <p>Order no <b><?= $shipment->order_number; ?></b> from <b><?= $shipment->company_name; ?></b> was un-delivered because of the following reason:</p>
                <p><b><?= $action->remarks; ?></b></p>

                <?php if (!empty($success)) { ?>
                    <div class="alert alert-success fade show" role="alert">
                        <?= $success; ?>
                    </div>
                <?php } ?>
                <?php if (!empty($error)) { ?>
                    <div class="alert alert-danger fade show" role="alert">
                        <?= $error; ?>

                    </div>
                <?php } ?>

                <?php
                switch ($action->ndr_code) {
                    case 'wrong_mobile':
                        include VIEWPATH . 'ndr/forms/wrong_mobile.php';
                        break;
                    case 'wrong_address':
                    case 'restricted':
                    case 'need_details':
                        include VIEWPATH . 'ndr/forms/wrong_address.php';
                        break;
                    case 'customer_cancelled':
                        include VIEWPATH . 'ndr/forms/customer_cancelled.php';
                        break;
                    case 'reschedule':
                    case 'unavailable':
                        include VIEWPATH . 'ndr/forms/reschedule.php';
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</body>


</html>