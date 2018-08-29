<input type="text"
    class="regular-text"
    id="<?php echo $args['slug']; ?>"
    name="<?php echo $args['slug']; ?>"
    value="<?php echo isset($value) ? esc_attr($value) : ''; ?>">

<?php if (isset($field->args['description'])) : ?>
    <p class="description">
        <?php echo $field->args['description']; ?>
    </p>
<?php endif; ?>
