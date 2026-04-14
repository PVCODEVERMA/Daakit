<style>
    .verticaltext {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        margin-left: 25px;
    }

    .form-control.is-invalid {
        background-image: none;
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
                                <tr class="p-t-10 sticky-top" style="z-index: 9;">
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
                                <?php foreach ($couriers as $courier) {
                                      $courier_alias= isset(($courier->courier_alias)) ? " (".ucfirst($courier->courier_alias).")" : "";
                                     ?>
                                    <tr>
                                        <td rowspan="3" class="align-middle bg-gray-400 text-right">
                                            <div class="verticaltext ">
                                                <b><?= $courier->name ?></b>
                                                <br><b><?= $courier_alias ?></b>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gray-400">Forward</span>
                                                </div>
                                            </div>
                                        </td>
                                        <?php
                                        if($plan_type == 'per_dispatch') {
                                            $price = new \App\Lib\Pricing\PerDispatchPlanPrice($plan_details->id, $courier->id, 'fwd');
                                        } else {
                                            $price = new \App\Lib\Pricing\PlanPrice($plan_details->id, $courier->id, 'fwd');
                                        }
                                        for ($i = 1; $i <= 5; $i++) {
                                        ?>
                                            <td>
                                                <div class="input-group">
                                                    <input required name="pricing[<?= $courier->id ?>][fwd][z<?= $i ?>]" type="text" value="<?php echo $field_value =  set_value("pricing[{$courier->id}][fwd][z{$i}]", (!empty($landing_price[$courier->id]['fwd']['zone' . $i]) ? $landing_price[$courier->id]['fwd']['zone' . $i] : '0')) ?>" class="form-control <?= ($field_value < 0) ? 'is-invalid' : '' ?>">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text bg-gray-400"><?php $string = "getZone{$i}Price"; echo  $price->{$string}(); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                        <?php } ?>
                                        <td>
                                            <div class="input-group ">
                                                <input required name="pricing[<?= $courier->id ?>][fwd][min_cod]" type="text" value="<?php echo $field_value =   set_value("pricing[{$courier->id}][fwd][min_cod]", (!empty($landing_price[$courier->id]['fwd']['min_cod']) ? $landing_price[$courier->id]['fwd']['min_cod'] : '0')) ?>" class="form-control <?= ($field_value < 0) ? 'is-invalid' : '' ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-gray-400"><?= $price->getMinCod(); ?></span>
                                                </div>
                                            </div>

                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input required name="pricing[<?= $courier->id ?>][fwd][cod_percent]" type="text" value="<?php echo $field_value =   set_value("pricing[{$courier->id}][fwd][cod_percent]", (!empty($landing_price[$courier->id]['fwd']['cod_percent']) ? $landing_price[$courier->id]['fwd']['cod_percent'] : '0')) ?>" class="form-control <?= ($field_value < 0) ? 'is-invalid' : '' ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-gray-400"><?= $price->getCodPercent(); ?></span>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gray-400">RTO</span>
                                                </div>
                                            </div>
                                        </td>
                                        <?php
                                        if($plan_type == 'per_dispatch') {
                                            $price = new \App\Lib\Pricing\PerDispatchPlanPrice($plan_details->id, $courier->id, 'rto');
                                        } else {
                                            $price = new \App\Lib\Pricing\PlanPrice($plan_details->id, $courier->id, 'rto');
                                        }
                                        for ($i = 1; $i <= 5; $i++) { ?>
                                            <td>
                                                <div class="input-group">
                                                    <input required name="pricing[<?= $courier->id ?>][rto][z<?= $i ?>]" type="text" value="<?php echo $field_value = set_value("pricing[{$courier->id}][rto][z{$i}]", (!empty($landing_price[$courier->id]['rto']['zone' . $i]) ? $landing_price[$courier->id]['rto']['zone' . $i] : '0')) ?>" class="form-control <?= ($field_value < 0) ? 'is-invalid' : '' ?>">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text bg-gray-400"><?php $string = "getZone{$i}Price"; echo  $price->{$string}(); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gray-400">Add Weight</span>
                                                </div>
                                            </div>
                                        </td>
                                        <?php
                                        if($plan_type == 'per_dispatch') {
                                            $price = new \App\Lib\Pricing\PerDispatchPlanPrice($plan_details->id, $courier->id, 'weight');
                                        } else {
                                            $price = new \App\Lib\Pricing\PlanPrice($plan_details->id, $courier->id, 'weight');
                                        }
                                        for ($i = 1; $i <= 5; $i++) { ?>
                                            <td>
                                                <div class="input-group">
                                                    <input required name="pricing[<?= $courier->id ?>][weight][z<?= $i ?>]" type="text" value="<?php echo $field_value = set_value("pricing[{$courier->id}][weight][z{$i}]", (!empty($landing_price[$courier->id]['weight']['zone' . $i]) ? $landing_price[$courier->id]['weight']['zone' . $i] : '0')) ?>" class="form-control <?= ($field_value < 0) ? 'is-invalid' : '' ?>">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text bg-gray-400"><?php $string = "getZone{$i}Price"; echo  $price->{$string}(); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <tr style="border-top-style: solid; border-color:#33D2FF">
                                        <td colspan="9"></td>
                                    </tr>
                                    <tr>
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