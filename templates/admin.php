<?php
script('globalquota', 'admin-globalquota');
style('globalquota', 'admin-globalquota');
?>

<div id="globalquota" class="section">
    <h2><?php p($l->t('Global Quota')); ?></h2>

    <div class="quota-container">
        <div class="quota-chart">
            <canvas id="quotaChart" width="200" height="200"></canvas>
        </div>

        <div class="quota-info">
            <div class="quota-item">
                <span class="quota-label"><?php p($l->t('Used:')); ?></span>
                <span id="quota-used" class="quota-value">-</span>
            </div>

            <div class="quota-item">
                <span class="quota-label"><?php p($l->t('Free:')); ?></span>
                <span id="quota-free" class="quota-value">-</span>
            </div>

            <div class="quota-item">
                <span class="quota-label"><?php p($l->t('Total:')); ?></span>
                <span id="quota-total" class="quota-value">-</span>
            </div>

            <div class="quota-item">
                <span class="quota-label"><?php p($l->t('Usage:')); ?></span>
                <span id="quota-percentage" class="quota-value">-</span>
            </div>
        </div>
    </div>

    <div class="quota-actions">
        <button id="refresh-quota" class="button primary">
            <?php p($l->t('Refresh')); ?>
        </button>
    </div>

    <div id="quota-error" class="msg error" style="display: none;"></div>
</div>
