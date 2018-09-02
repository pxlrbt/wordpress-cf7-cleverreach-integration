<?php
    use Pixelarbeit\CF7Cleverreach\Controllers\FormConfigController;
    use Pixelarbeit\CF7Cleverreach\Config\Config;

    $fcc = FormConfigController::getInstance();
    $options = Config::getOptions($fcc->getCurrentFormId());
    $attributeMapping = Config::getAttributeMapping($fcc->getCurrentFormId());
    $globalAttributeMapping = Config::getGlobalAttributeMapping($fcc->getCurrentFormId());
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
                <th>List ID*</th>
                <td>
                    <input type="text" name="wpcf7-cleverreach_options[listId]" <?php if (isset($options['listId'])): ?>value="<?php echo $options['listId']; ?>"<?php endif; ?>>
                </td>            
            </tr>        
            <tr>
                <th>Form ID*</th>
                <td>
                    <input type="text" name="wpcf7-cleverreach_options[formId]" <?php if (isset($options['formId'])): ?>value="<?php echo $options['formId']; ?>"<?php endif; ?>>
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
                        <input type="text"
                            name="wpcf7-cleverreach_attribute[<?php echo $field; ?>]"
                            value="<?php if (isset($attributeMapping[$field])) { echo $attributeMapping[$field]; } ?>">
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
                        <input type="text"
                            name="wpcf7-cleverreach_global_attribute[<?php echo $field; ?>]"
                            value="<?php if (isset($globalAttributeMapping[$field])) { echo $globalAttributeMapping[$field]; } ?>">
                    </td>            
                </tr>
            <?php endforeach; ?>            
        </tbody>
    </table>
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
    }

</style>