<style>
    .verticaltext {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        margin-left: 25px;
    }

    .form-control {
        background-image: none;
        width: 100px;
    }
    .bg-gray-400{
        font-weight:400;
    }
</style>
<div class="row m-t-15">
    <div class="col-sm-12">

        <div class="row">
            <div class="col-sm-12 ">
                <form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
                    <div class="table-responsive">
                        <table class="table table-bordered border-bottom dataTable no-footer">
                            <thead>
                                <tr class="p-t-10 sticky-top">
                                    <th>Carrier</th>
                                    <th class="text-center">Mode</th>
                                    <th class="text-center">Z1</th>
                                    <th class="text-center">Z2</th>
                                    <th class="text-center">Z3</th>
                                    <th class="text-center">Z4</th>
                                    <th class="text-center">Z5</th>
                                    <th class="text-center">MIN COD</th>
                                    <th class="text-center">COD %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($couriers as $courier) { ?>
                                    <tr>
                                        <td rowspan="3" class="align-middle bg-gray-400 text-right">
                                            <div class="verticaltext ">
                                                <b><?= $courier->name ?> </br><?= isset(($courier->courier_alias)) ? " (".ucfirst($courier->courier_alias).")" : "";?></b>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="bg-gray-400">Forward</span>
                                                </div>
                                            </div>
                                        </td>
                                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                                            <td>
                                                <input required name="pricing[<?= $courier->id ?>][fwd][z<?= $i ?>]" type="text" value="<?= set_value("pricing[{$courier->id}][fwd][z{$i}]", (!empty($landing_price[$courier->id]['fwd']['zone' . $i]) ? $landing_price[$courier->id]['fwd']['zone' . $i] : '0')) ?>" class="form-control">
                                            </td>
                                        <?php } ?>
                                        <td>
                                            <input required name="pricing[<?= $courier->id ?>][fwd][min_cod]" type="text" value="<?= set_value("pricing[{$courier->id}][fwd][min_cod]", (!empty($landing_price[$courier->id]['fwd']['min_cod']) ? $landing_price[$courier->id]['fwd']['min_cod'] : '0')) ?>" class="form-control">
                                        </td>
                                        <td>
                                            <input required name="pricing[<?= $courier->id ?>][fwd][cod_percent]" type="text" value="<?= set_value("pricing[{$courier->id}][fwd][cod_percent]", (!empty($landing_price[$courier->id]['fwd']['cod_percent']) ? $landing_price[$courier->id]['fwd']['cod_percent'] : '0')) ?>" class="form-control">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="bg-gray-400">RTO</span>
                                                </div>
                                            </div>
                                        </td>
                                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                                            <td>
                                                <input required name="pricing[<?= $courier->id ?>][rto][z<?= $i ?>]" type="text" value="<?= set_value("pricing[{$courier->id}][rto][z{$i}]", (!empty($landing_price[$courier->id]['rto']['zone' . $i]) ? $landing_price[$courier->id]['rto']['zone' . $i] : '0')) ?>" class="form-control">
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="bg-gray-400">Add Weight</span>
                                                </div>
                                            </div>
                                        </td>
                                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                                            <td>
                                                <input required name="pricing[<?= $courier->id ?>][weight][z<?= $i ?>]" type="text" value="<?= set_value("pricing[{$courier->id}][weight][z{$i}]", (!empty($landing_price[$courier->id]['weight']['zone' . $i]) ? $landing_price[$courier->id]['weight']['zone' . $i] : '0')) ?>" class="form-control">
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>

                        </table>
                    </div>

                    <div class="form-group text-right">
                        <button type="submit" style="margin-top: 20px;" class="btn btn-primary">Save</button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>