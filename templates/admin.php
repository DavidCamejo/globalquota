<?php
script('globalquota', 'admin-globalquota'); // registra js/admin-globalquota.js
style('globalquota', 'admin-globalquota');  // registra css/admin-globalquota.css
?>

<div id="globalquota" class="section">
	<h2><?php p($l->t('Global Quota')); ?></h2>

	<div class="globalquota-container">
		<div class="globalquota-chart-wrapper">
			<canvas id="globalquota-chart" width="240" height="240" aria-label="<?php p($l->t('Global quota donut chart')); ?>"></canvas>
		</div>

		<div class="globalquota-stats">
			<h3><?php p($l->t('Storage Usage')); ?></h3>
			<ul class="globalquota-list">
				<li>
					<strong><?php p($l->t('Used')); ?>:</strong>
					<span id="quota-used">-</span>
				</li>
				<li>
					<strong><?php p($l->t('Free')); ?>:</strong>
					<span id="quota-free">-</span>
				</li>
				<li>
					<strong><?php p($l->t('Total')); ?>:</strong>
					<span id="quota-total">-</span>
				</li>
				<li>
					<strong><?php p($l->t('Usage')); ?>:</strong>
					<span id="quota-percentage">-</span>
				</li>
			</ul>

			<button id="refresh-quota" class="button"><?php p($l->t('Refresh')); ?></button>
			<div id="quota-error" class="globalquota-error" style="display:none;"></div>
		</div>
	</div>
</div>

<!-- Cargar Chart.js por CDN para asegurar disponibilidad -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
