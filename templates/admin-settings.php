<?php
script('globalquota', 'admin-settings');
style('globalquota', 'admin-settings');
?>

<div id="globalquota-settings" class="section">
    <h2 class="inlineblock"><?php p($l->t('Global Quota')); ?></h2>
    
    <?php if ($_['serverInfoIntegration']): ?>
        <div class="globalquota-info">
            <p><?php p($l->t('GlobalQuota is integrated with Server Info.')); ?></p>
        </div>
    <?php else: ?>
        <div class="globalquota-chart-container">
            <h3><?php p($l->t('Storage Usage')); ?></h3>
            <div class="globalquota-chart-wrapper">
                <canvas id="globalquota-chart" width="300" height="300"></canvas>
                <div class="globalquota-stats">
                    <div class="stat-item"><span><?php p($l->t('Used:')); ?></span><span id="quota-used">-</span></div>
                    <div class="stat-item"><span><?php p($l->t('Free:')); ?></span><span id="quota-free">-</span></div>
                    <div class="stat-item"><span><?php p($l->t('Total:')); ?></span><span id="quota-total">-</span></div>
                    <div class="stat-item"><span><?php p($l->t('Usage:')); ?></span><span id="quota-percentage">-</span></div>
                </div>
            </div>
            <div class="globalquota-actions">
                <button id="refresh-quota" class="button"><?php p($l->t('Refresh')); ?></button>
            </div>
        </div>
    <?php endif; ?>
</div>
