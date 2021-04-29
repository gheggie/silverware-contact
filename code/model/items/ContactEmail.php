<?php

/**
 * An extension of the contact item class for an email address.
 */
class ContactEmail extends ContactItem
{
    private static $singular_name = "Email";
    private static $plural_name   = "Emails";
    
    private static $db = array(
        'Email' => 'Varchar(255)',
        'LinkEmail' => 'Boolean',
        'ProtectEmail' => 'Boolean'
    );
    
    private static $defaults = array(
        'Title' => 'Email',
        'LinkEmail' => 1,
        'ProtectEmail' => 1
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
                EmailField::create(
                    'Email',
                    _t('ContactEmail.EMAIL', 'Email')
                )
            )
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            array(
                CheckboxField::create(
                    'LinkEmail',
                    _t('ContactEmail.LINKEMAIL', 'Link email')
                ),
                CheckboxField::create(
                    'ProtectEmail',
                    _t('ContactEmail.PROTECTEMAIL', 'Protect email from spammers (recommended)')
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
        return parent::getCMSValidator()->addRequiredField('Email');
    }
    
    /**
     * Answers the value of the item for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->dbObject('Email');
    }
    
    /**
     * Answers the email address for the HTML template.
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->ProtectEmail ? Email::obfuscate($this->Email, 'direction') : $this->Email;
    }
    
    /**
     * Answers the email link for the HTML template.
     *
     * @return string
     */
    public function Link()
    {
        return "mailto:" . ($this->ProtectEmail ? Email::obfuscate($this->Email, 'hex') : $this->Email);
    }
}
