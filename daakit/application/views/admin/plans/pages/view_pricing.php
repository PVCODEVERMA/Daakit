<style>
    .verticaltext {
        writing-mode: vertical-rl;
        text-orientation: mixed;
    }
</style>

<div class="row m-t-15">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-12 ">
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
                            <?php foreach ($couriers as $courier) { ?>
                                <tr>
                                    <td rowspan="3" class="align-middle">
                                        <b><?= $courier->name ?></b>
                                        </br><b><?= isset(($courier->courier_alias)) ? " (".ucfirst($courier->courier_alias).")" : "";?></b>
                                    </td>
                                    <td>
                                        Forward
                                    </td>
                                    <?php for ($i = 1; $i <= 5; $i++) {
                                        $zoneMArginString = "getZone{$i}Margin";
                                    ?>
                                        <td class="text-center <?= ($landing_price[$courier->id]['fwd']->{$zoneMArginString}() < 0) ? 'bg-danger text-white' : ''; ?>">
                                            <?php
                                            $string = "getZone{$i}Price";
                                            echo $landing_price[$courier->id]['fwd']->{$string}(); ?>
                                            <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                                                | <span class="<?= ($landing_price[$courier->id]['fwd']->{$zoneMArginString}() < 0) ? 'text-white' : 'text-success'; ?>"><?= $landing_price[$courier->id]['fwd']->{$zoneMArginString}(); ?></span>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                    <td class="text-center <?= ($landing_price[$courier->id]['fwd']->getCodMargin() < 0) ? 'bg-danger text-white' : ''; ?>">
                                        <?= $landing_price[$courier->id]['fwd']->getMinCod(); ?>
                                        <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                                            | <span class="<?= ($landing_price[$courier->id]['fwd']->getCodMargin() < 0) ? 'text-white' : 'text-success'; ?>"><?= $landing_price[$courier->id]['fwd']->getCodMargin(); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center <?= ($landing_price[$courier->id]['fwd']->getCodPercentMargin() < 0) ? 'bg-danger text-white' : ''; ?>">
                                        <?= $landing_price[$courier->id]['fwd']->getCodPercent(); ?>
                                        <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                                            | <span class="<?= ($landing_price[$courier->id]['fwd']->getCodPercentMargin() < 0) ? 'text-white' : 'text-success'; ?>"><?= $landing_price[$courier->id]['fwd']->getCodPercentMargin(); ?></span>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        RTO
                                    </td>
                                    <?php for ($i = 1; $i <= 5; $i++) {
                                        $zoneMArginString = "getZone{$i}Margin";
                                    ?>
                                        <td class="text-center <?= ($landing_price[$courier->id]['rto']->{$zoneMArginString}() < 0) ? 'bg-danger text-white' : ''; ?>">
                                            <?php
                                            $string = "getZone{$i}Price";
                                            echo $landing_price[$courier->id]['rto']->{$string}(); ?>
                                            <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                                                | <span class="<?= ($landing_price[$courier->id]['rto']->{$zoneMArginString}() < 0) ? 'text-white' : 'text-success'; ?>"><?= $landing_price[$courier->id]['rto']->{$zoneMArginString}(); ?></span>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                    <td class="text-center">
                                        <?= $landing_price[$courier->id]['rto']->getMinCod(); ?>
                                        <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                                            | <span class="<?= ($landing_price[$courier->id]['rto']->getCodMargin() < 0) ? 'text-white' : 'text-success'; ?>"><?= $landing_price[$courier->id]['rto']->getCodMargin(); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $landing_price[$courier->id]['rto']->getCodPercent(); ?>
                                        <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                                            | <span class="<?= ($landing_price[$courier->id]['rto']->getCodPercentMargin() < 0) ? 'text-white' : 'text-success'; ?>"><?= $landing_price[$courier->id]['rto']->getCodPercentMargin(); ?></span>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Add. Weight
                                    </td>
                                    <?php for ($i = 1; $i <= 5; $i++) {
                                        $zoneMArginString = "getZone{$i}Margin";
                                    ?>
                                        <td class="text-center <?= ($landing_price[$courier->id]['weight']->{$zoneMArginString}() < 0) ? 'bg-danger text-white' : ''; ?>">
                                            <?php
                                            $string = "getZone{$i}Price";
                                            echo $landing_price[$courier->id]['weight']->{$string}(); ?>
                                            <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                                                | <span class="<?= ($landing_price[$courier->id]['weight']->{$zoneMArginString}() < 0) ? 'text-white' : 'text-success'; ?>"><?= $landing_price[$courier->id]['weight']->{$zoneMArginString}(); ?></span>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                    <td class="text-center">
                                        <?= $landing_price[$courier->id]['weight']->getMinCod(); ?>
                                        <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                                            | <span class="<?= ($landing_price[$courier->id]['weight']->getCodMargin() < 0) ? 'text-white' : 'text-success'; ?>"><?= $landing_price[$courier->id]['weight']->getCodMargin(); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $landing_price[$courier->id]['weight']->getCodPercent(); ?>
                                        <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                                            | <span class="<?= ($landing_price[$courier->id]['weight']->getCodPercentMargin() < 0) ? 'text-white' : 'text-success'; ?>"><?= $landing_price[$courier->id]['weight']->getCodPercentMargin(); ?></span>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <!-- <tr style="border-top-style: solid; border-color:#33D2FF">
                                    <td colspan="9"></td>
                                </tr> -->
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>