<?php

namespace pxlrbt\Cf7Cleverreach\Config;

class Config
{
    public $formId = null;

    public static function getOptions($formId)
    {
        $data = get_post_meta($formId, '_wpcf7-cleverreach_options', true);
        return is_array($data) ? $data : [];
    }

    public static function saveOptions($formId, $options)
    {
        return update_post_meta($formId, '_wpcf7-cleverreach_options', $options);
    }

    public static function getAttributeMapping($formId)
    {
        $data = get_post_meta($formId, '_wpcf7-cleverreach_attribute_mapping', true);
        return is_array($data) ? $data : [];
    }

    public static function saveAttributeMapping($formId, $mapping)
    {
        return update_post_meta($formId, '_wpcf7-cleverreach_attribute_mapping', $mapping);
    }

    public static function getGlobalAttributeMapping($formId)
    {
        $data = get_post_meta($formId, '_wpcf7-cleverreach_global_attribute_mapping', true);
        return is_array($data) ? $data : [];
    }

    public static function saveGlobalAttributeMapping($formId, $mapping)
    {
        return update_post_meta($formId, '_wpcf7-cleverreach_global_attribute_mapping', $mapping);
    }

    public static function deleteConfig($formId)
    {
        delete_post_meta($formId, '_wpcf7-cleverreach_options_mapping');
        delete_post_meta($formId, '_wpcf7-cleverreach_field_mapping');
        delete_post_meta($formId, '_wpcf7-cleverreach_option_mapping');
    }
}
