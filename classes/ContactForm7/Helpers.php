<?php

namespace pxlrbt\Cf7Cleverreach\ContactForm7;

use WPCF7_ContactForm;

class Helpers
{
    public static function currentForm()
    {
        return WPCF7_ContactForm::get_current();
    }

    public static function currentFormId()
    {
        $form = WPCF7_ContactForm::get_current();
        return isset($form) ? $form->id() : null;
    }

    public static function fieldNames()
    {
        $fields = [];
        $tags = self::currentForm()->scan_form_tags();

        foreach ($tags as $tag) {
            if (!empty($tag['name'])) {
                $fields[] = $tag['name'];
            }
        }

        return $fields;
    }
}
