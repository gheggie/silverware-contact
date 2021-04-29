<?php

/**
 * An extension of the contact item class for a heading.
 */
class ContactHeader extends ContactItem
{
    private static $singular_name = "Header";
    private static $plural_name   = "Headers";
    
    private static $db = array(
        'Text' => 'Varchar(255)',
        'Level' => "Enum('1, 2, 3, 4, 5, 6', '3')"
    );
    
    private static $defaults = array(
        'Title' => 'Header',
        'Level' => 3,
        'HideTitle' => 1
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
                TextField::create(
                    'Text',
                    _t('ContactHeader.TEXT', 'Text')
                ),
                DropdownField::create(
                    'Level',
                    _t('ContactHeader.LEVEL', 'Level'),
                    $this->dbObject('Level')->enumValues()
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
        return $this->dbObject('Text');
    }
    
    /**
     * Answers the appropriate HTML tag for the heading.
     *
     * @return string
     */
    public function Tag()
    {
        return "h{$this->Level}";
    }
}
