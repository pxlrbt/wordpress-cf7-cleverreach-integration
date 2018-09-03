<?php
    use Pixelarbeit\CF7Cleverreach\Controllers\FormConfigController;
    use Pixelarbeit\CF7Cleverreach\Config\Config;

    $fcc = FormConfigController::getInstance();
    $options = Config::getOptions($fcc->getCurrentFormId());
    $attributeMapping = Config::getAttributeMapping($fcc->getCurrentFormId());
    $globalAttributeMapping = Config::getGlobalAttributeMapping($fcc->getCurrentFormId());

    $api = $this->plugin->getApi();

    if (isset($options['listId'])) {
        $globalAttributes = $api->getAttributes(0);
        $attributes = $api->getAttributes($options['listId']);
    }
    
?>

<div class="cleverreach-config">
    <h2>Cleverreach Configuration</h2>

    <h3>Options</h3>

    <table class="mapping">
        <thead>
            <tr>
                <td>Option</td>
                <td>Value</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Active</th>
                <td>
                    <input type="checkbox" name="wpcf7-cleverreach_options[active]" <?php if (isset($options['active']) && $options['active'] == true): ?>checked<?php endif; ?>>
                </td>            
            </tr>        
            <tr>
                <th>Group*</th>
                <td>
                    <select name="wpcf7-cleverreach_options[listId]">
                        <option value=""></option>
                        <?php foreach ($api->getGroups() as $group): ?>
                            <option value="<?php echo $group->id; ?>"
                                <?php if (isset($options['listId']) && $group->id == $options['listId']) { echo "selected"; } ?>>
                                <?php echo $group->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>            
            </tr>        
            <tr>
                <th>Form*</th>
                <td>
                    <select name="wpcf7-cleverreach_options[formId]">
                        <option value=""></option>
                        <?php foreach ($api->getForms() as $form): ?>
                            <option value="<?php echo $form->id; ?>"
                                <?php if (isset($options['formId']) && $form->id == $options['formId']) { echo "selected"; } ?>>
                                <?php echo $form->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>            
            </tr>        
            <tr class="hasNote">
                <th>Email Field*</th>
                <td>
                    <select name="wpcf7-cleverreach_options[emailField]">
                        <option></option>
                        <?php foreach ($fcc->getCF7FieldNames() as $field): ?>
                            <option value="<?php echo $field; ?>" <?php if (isset($options['emailField']) && $options['emailField'] == $field): ?>selected<?php endif; ?>>
                                <?php echo $field; ?>
                            </option>                            
                        <?php endforeach; ?>      
                    </select>
                </td>   
            </tr>       
            <tr>
                <td colspan="2">
                    <small>Field that contains email address.</small>
                </td>
            </tr>   
            <tr class="hasNote">
                <th>Require Field</th>
                <td>
                    <select name="wpcf7-cleverreach_options[requireField]">
                        <option></option>
                        <?php foreach ($fcc->getCF7FieldNames() as $field): ?>
                            <option value="<?php echo $field; ?>" <?php if (isset($options['requireField']) && $options['requireField'] == $field): ?>selected<?php endif; ?>>
                                <?php echo $field; ?>
                            </option>                            
                        <?php endforeach; ?>      
                    </select>
                </td>            
            </tr>         
            <tr>
                <td colspan="2">
                    <small>Only send data to cleverreach if this field is set.</small>
                </td>
            </tr>           
        </tbody>
    </table>

    <?php if ($options['listId']): ?>
        <br><br><br>
        <h3>Mapping: List Fields</h3>
        
        <table class="mapping">
            <thead>
                <tr>
                    <td>CF7 Field</td>
                    <td>Cleverreach Attribute</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fcc->getCF7FieldNames() as $field): ?>
                    <tr>
                        <th><?php echo $field; ?></th>
                        <td>
                            <select name="wpcf7-cleverreach_attribute[<?php echo $field; ?>]">
                                <option value=""></option>
                                <?php foreach ($attributes as $attr): ?>
                                    <option value="<?php echo $attr->name; ?>"
                                    <?php if (isset($attributeMapping[$field]) && $attributeMapping[$field] == $attr->name) { echo "selected"; } ?>>
                                        <?php echo $attr->description; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>                       
                    </tr>
                <?php endforeach; ?>            
            </tbody>
        </table>

        <br><br><br>
        <h3>Mapping: Intergroup Fields</h3>
        <table class="mapping">
            <thead>
                <tr>
                    <td>CF7 Field</td>
                    <td>Cleverreach Attribute</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fcc->getCF7FieldNames() as $field): ?>
                    <tr>
                        <th><?php echo $field; ?></th>
                        <td>
                            <select name="wpcf7-cleverreach_global_attribute[<?php echo $field; ?>]">
                                <option value=""></option>
                                <?php foreach ($globalAttributes as $attr): ?>
                                    <option value="<?php echo $attr->name; ?>"
                                    <?php if (isset($globalAttributeMapping[$field]) && $globalAttributeMapping[$field] == $attr->name) { echo "selected"; } ?>>
                                        <?php echo $attr->description; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>            
                    </tr>
                <?php endforeach; ?>            
            </tbody>
        </table>
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