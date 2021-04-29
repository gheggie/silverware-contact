<?php

/**
 * An extension of the contact item class for a phone number.
 */
class ContactPhone extends ContactItem
{
    private static $singular_name = "Phone";
    private static $plural_name   = "Phones";
    
    private static $db = array(
        'PhoneNumber' => 'Varchar(64)',
        'CallToNumber' => 'Varchar(64)',
        'LinkNumber' => 'Boolean'
    );
    
    private static $defaults = array(
        'Title' => 'Phone',
        'LinkNumber' => 1
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
                    'PhoneNumber',
                    _t('ContactPhone.PHONENUMBER', 'Phone number')
                ),
                TextField::create(
                    'CallToNumber',
                    _t('ContactPhone.CALLTONUMBER', 'Call to number')
                )->setRightTitle(
                    _t(
                        'ContactPhone.CALLTONUMBERRIGHTTITLE',
                        'The call to number overrides the phone number for linking (optional).'
                    )
                )
            )
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            array(
                CheckboxField::create(
                    'LinkNumber',
                    _t('ContactEmail.LINKNUMBER', 'Link number')
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
        return parent::getCMSValidator()->addRequiredField('PhoneNumber');
    }
    
    /**
     * Answers the value of the item for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->dbObject('PhoneNumber');
    }
    
    /**
     * Answers a phone number suitable for linking.
     *
     * @return string
     */
    public function getLinkableNumber()
    {
        return preg_replace('/[^0-9\+]+/', '', $this->CallToNumber ? $this->CallToNumber : $this->PhoneNumber);
    }
    
    /**
     * Answers the phone link for the HTML template.
     *
     * @return string
     */
    public function Link()
    {
        return "callto:" . $this->getLinkableNumber();
    }
}
