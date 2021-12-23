<?php

    use pxlrbt\Cf7Cleverreach\Cleverreach\ApiCredentials;
    use pxlrbt\Cf7Cleverreach\ContactForm7\FormConfig;
    use pxlrbt\Cf7Cleverreach\ContactForm7\Helpers as Cf7;
    use pxlrbt\Cf7Cleverreach\Container;

    $currentFormId = Cf7::currentFormId();
    $options = FormConfig::getOptions($currentFormId);
    $attributeMapping = FormConfig::getAttributeMapping($currentFormId);
    $globalAttributeMapping = FormConfig::getGlobalAttributeMapping($currentFormId);

    $api = Container::getInstance()->getApi();

    try {
        $groups = $api->getGroups();
        $forms = $api->getForms();

        if (isset($options['listId'])) {
            $globalAttributes = $api->getAttributes(0);
            $attributes = $api->getAttributes($options['listId']);
        }
    } catch (\Exception $e) {
    }
?>

<div class="cleverreach-config">
    <h2><?php _e('CleverReach Configuration', 'wpcf7-cleverreach'); ?></h2>

    <?php if (ApiCredentials::token() === null): ?>
        <p>
            <?php _e('Please acquire a CleverReach token first.', 'wpcf7-cleverreach'); ?>
            <a href="<?php echo admin_url('/admin.php?page=cf7-cleverreach'); ?>"><?php _e('Go to settings.', 'wpcf7-cleverreach'); ?></a>
        </p>
    <?php else: ?>
        <h3><?php _e('Options', 'wpcf7-cleverreach'); ?></h3>

        <table class="mapping">
            <thead>
                <tr>
                    <td><?php _e('Option', 'wpcf7-cleverreach'); ?></td>
                    <td><?php _e('Value', 'wpcf7-cleverreach'); ?></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th><?php _e('Active', 'wpcf7-cleverreach'); ?></th>
                    <td>
                        <input type="checkbox" name="wpcf7-cleverreach_options[active]" <?php if (isset($options['active']) && $options['active'] == true): ?>checked<?php endif; ?>>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Group', 'wpcf7-cleverreach'); ?>*</th>
                    <td>
                        <select name="wpcf7-cleverreach_options[listId]">
                            <option value=""></option>
                            <?php if (is_array($groups)): ?>
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?php echo $group->id; ?>"
                                        <?php if (isset($options['listId']) && $group->id == $options['listId']) { echo "selected"; } ?>>
                                        <?php echo $group->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Form', 'wpcf7-cleverreach'); ?>*</th>
                    <td>
                        <select name="wpcf7-cleverreach_options[formId]">
                            <option value=""></option>
                            <?php if (is_array($forms)): ?>
                                <?php foreach ($forms as $form): ?>
                                    <option value="<?php echo $form->id; ?>"
                                        <?php if (isset($options['formId']) && $form->id == $options['formId']) { echo "selected"; } ?>>
                                        <?php echo $form->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr class="hasNote">
                    <th><?php _e('Email field', 'wpcf7-cleverreach'); ?>*</th>
                    <td>
                        <select name="wpcf7-cleverreach_options[emailField]">
                            <option></option>
                            <?php foreach (Cf7::fieldNames() as $field): ?>
                                <option value="<?php echo $field; ?>" <?php if (isset($options['emailField']) && $options['emailField'] == $field): ?>selected<?php endif; ?>>
                                    <?php echo $field; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <small><?php _e('Field that contains email address.', 'wpcf7-cleverreach'); ?></small>
                    </td>
                </tr>
                <tr class="hasNote">
                    <th><?php _e('Require field', 'wpcf7-cleverreach'); ?></th>
                    <td>
                        <select name="wpcf7-cleverreach_options[requireField]">
                            <option></option>
                            <?php foreach (Cf7::fieldNames() as $field): ?>
                                <option value="<?php echo $field; ?>" <?php if (isset($options['requireField']) && $options['requireField'] == $field): ?>selected<?php endif; ?>>
                                    <?php echo $field; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <small><?php _e('Only send data to CleverReach if this field is set.', 'wpcf7-cleverreach'); ?></small>
                    </td>
                </tr>
                <tr class="hasNote">
                    <th><?php _e('Double Opt-In', 'wpcf7-cleverreach'); ?></th>
                    <td>
                        <input type="checkbox" name="wpcf7-cleverreach_options[doubleOptIn]" <?php if (isset($options['doubleOptIn']) == false || $options['doubleOptIn'] == true): ?>checked<?php endif; ?>>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <small><?php _e('Creates recipient as inactive and sends an confirmation email (GDPR compliant)', 'wpcf7-cleverreach'); ?></small>
                    </td>
                </tr>
                <tr class="hasNote">
                    <th><?php _e('Source', 'wpcf7-cleverreach'); ?></th>
                    <td>
                        <input type="text" name="wpcf7-cleverreach_options[source]" <?php if (isset($options['source'])) { echo 'value="' . $options['source'] . '"'; } ?>>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <small><?php _e('Value for CleverReachs internal tag field.', 'wpcf7-cleverreach'); ?></small>
                    </td>
                </tr>
                <tr class="hasNote">
                    <th><?php _e('Tags', 'wpcf7-cleverreach'); ?></th>
                    <td>
                        <input type="text" name="wpcf7-cleverreach_options[tags]" <?php if (isset($options['tags'])) { echo 'value="' . $options['tags'] . '"'; } ?>>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <small><?php _e('Comma seperated list tags for CleverReachs internal tag field.', 'wpcf7-cleverreach'); ?></small>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php if (isset($options['listId'])): ?>
            <br><br><br>
            <h3><?php _e('Mapping', 'wpcf7-cleverreach'); ?></h3>

            <table class="mapping">
                <thead>
                    <tr>
                        <td><?php _e('Contact Form 7 Field', 'wpcf7-cleverreach'); ?></td>
                        <td><?php _e('CleverReach Attribute', 'wpcf7-cleverreach'); ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (Cf7::fieldNames() as $field): ?>
                        <tr>
                            <th><?php echo $field; ?></th>
                            <td>
                                <select name="wpcf7-cleverreach_mapping[<?php echo $field; ?>]">
                                    <option value=""></option>
                                    <optgroup label="List Fields">
                                        <?php foreach ($attributes as $attr): ?>
                                            <option value="local--<?php echo $attr->name; ?>"
                                            <?php if (isset($attributeMapping[$field]) && $attributeMapping[$field] == $attr->name) { echo "selected"; } ?>>
                                                <?php echo $attr->description; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <optgroup label="Intergroup Fields">
                                        <?php foreach ($globalAttributes as $attr): ?>
                                            <option value="global--<?php echo $attr->name; ?>"
                                            <?php if (isset($globalAttributeMapping[$field]) && $globalAttributeMapping[$field] == $attr->name) { echo "selected"; } ?>>
                                                <?php echo $attr->description; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
    .cleverreach-config h4 {
        font-size: 1.2em;
        margin-top: 3em;
    }

    .mapping {
        width: 100%;
        text-align: left;
        border-collapse: collapse;
        margin-bottom: 1.5em;
    }

    .mapping select {
        text-align: right;
        min-width: 150px;
    }

    .mapping th, .mapping td {
        padding: .5em;
        vertical-align: top;
        border-bottom: 1px solid #ccc;
    }

    .mapping th {
        line-height: 2em;
    }

    .mapping thead td {
        font-style: italic;
    }
    .mapping table td,
    .mapping table th {
        border-bottom: none;
        padding: 0 .5em;
        vertical-align: middle;
    }

    .mapping tr.hasNote th, .mapping tr.hasNote td {
        border-bottom: none;
        padding-bottom: 0;
    }

</style>
