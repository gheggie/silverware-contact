<?php

/**
 * An extension of the contact item class for a Skype link.
 */
class ContactSkype extends ContactItem
{
    private static $singular_name = "Skype";
    private static $plural_name   = "Skype";
    
    private static $db = array(
        'SkypeName' => 'Varchar(32)',
        'SkypeMode' => "Enum('Call, Chat', 'Call')",
        'VideoEnabled' => 'Boolean'
    );
    
    private static $defaults = array(
        'Title' => 'Skype',
        'FontIcon' => 'fa-skype',
        'VideoEnabled' => 0
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
                    'SkypeName',
                    _t('ContactSkype.SKYPENAME', 'Skype Name')
                ),
                DropdownField::create(
                    'SkypeMode',
                    _t('ContactSkype.SKYPEMODE', 'Skype Mode'),
                    $this->dbObject('SkypeMode')->enumValues()
                )
            )
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            array(
                CheckboxField::create(
                    'VideoEnabled',
                    _t('ContactSkype.VIDEOENABLED', 'Video enabled')
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
        return parent::getCMSValidator()->addRequiredField('SkypeName');
    }
    
    /**
     * Answers the value of the item for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->dbObject('SkypeName');
    }
    
    /**
     * Answers the link for the HTML template.
     *
     * @return string
     */
    public function Link()
    {
        $video = $this->dbObject('VideoEnabled')->NiceAsBoolean();
        
        return "skype:" . $this->SkypeName . "?" . strtolower($this->SkypeMode) . "&video=" . $video;
    }
    
    /**
     * Answers a title for the link.
     *
     * @return string
     */
    public function LinkTitle()
    {
        if ($this->SkypeMode == 'Call') {
            return _t('ContactSkype.CALLTO', 'Call to') . " " . $this->SkypeName;
        } else {
            return _t('ContactSkype.CHATWITH', 'Chat with') . " " . $this->SkypeName;
        }
    }
}
