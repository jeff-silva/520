<?php

cdz_header();

?>

<h2>Newsletter</h2>

<table class="table table-hover table-bordered table-striped">
	<thead>
		<tr>
			<th>E-mail</th>
		</tr>
	</thead>
	<tbody>
		<?php $query = helper_posts('post_type=newsletter&post_status=draft', function($post) {
		$meta = get_post_meta($post->ID, 'meta-action', true);
		?>
		<tr>
			<td><?php echo $meta['email']; ?></td>
		</tr>
		<?php }) ?>

		<?php if (! $query): ?>
		<tr>
			<td class="text-center text-muted">Nenhum resultado encontrado</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>