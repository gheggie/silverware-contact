<?php

/**
 * An extension of the contact item class for a string of text.
 */
class ContactText extends ContactItem
{
    private static $singular_name = "Text";
    private static $plural_name   = "Text";
    
    private static $db = array(
        'Text' => 'Text'
    );
    
    private static $defaults = array(
        'Title' => 'Text'
    );
    
    /**
     * Answers a collection of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            array(
                TextareaField::create(
                    'Text',
                    _t('ContactText.TEXT', 'Text')
                )
            )
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers a validator for the CMS interface.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return parent::getCMSValidator()->addRequiredField('Text');
    }
    
    /**
     * Answers the value of the item for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->dbObject('Text')->LimitCharacters(40);
    }
}
