<?php cdz_header(); ?>
<h2>Contato</h2>

<table class="table table-hover table-bordered talbe-stripped">
	<thead>
		<tr>
			<th>Mensagens</th>
		</tr>
	</thead>
	<tbody>
		<?php $query = helper_posts('post_type=contact&post_status=draft', function() {
		$meta = get_post_meta(get_the_ID(), 'meta-action', true);
		?>
		<tr>
			<td>
				<?php foreach($meta as $key=>$val): ?>
				<div><strong><?php echo $key; ?></strong>: <?php echo nl2br($val); ?></div>
				<?php endforeach; ?>
			</td>
		</tr>
		<?php }); ?>


		<?php if (! $query): ?>
		<tr>
			<td class="text-center text-muted">Nenhum resultado encontrado</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>