<?php

/**
 * An extension of the contact item class for a fax number.
 */
class ContactFax extends ContactItem
{
    private static $singular_name = "Fax";
    private static $plural_name   = "Faxes";
    
    private static $db = array(
        'FaxNumber' => 'Varchar(64)'
    );
    
    private static $defaults = array(
        'Title' => 'Fax'
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
                    'FaxNumber',
                    _t('ContactFax.FAXNUMBER', 'Fax number')
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
        return parent::getCMSValidator()->addRequiredField('FaxNumber');
    }
    
    /**
     * Answers the value of the item for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->dbObject('FaxNumber');
    }
}
