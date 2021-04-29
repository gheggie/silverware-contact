<?php

/**
 * An extension of the contact item class for a link.
 */
class ContactLink extends ContactItem
{
    private static $singular_name = "Link";
    private static $plural_name   = "Links";
    
    private static $db = array(
        'Name' => 'Varchar(255)',
        'LinkURL' => 'Varchar(2048)',
        'OpenInNewTab' => 'Boolean'
    );
    
    private static $has_one = array(
        'LinkPage' => 'SiteTree'
    );
    
    private static $defaults = array(
        'OpenInNewTab' => 0
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
                    'Name',
                    _t('ContactLink.NAME', 'Name')
                ),
                TreeDropdownField::create(
                    'LinkPageID',
                    _t('ContactLink.LINKPAGE', 'Link Page'),
                    'SiteTree'
                ),
                TextField::create(
                    'LinkURL',
                    _t('ContactLink.LINKURL', 'Link URL')
                )
            )
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            array(
                CheckboxField::create(
                    'OpenInNewTab',
                    _t('ContactLink.OPENINNEWTAB', 'Open in new tab')
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
        return parent::getCMSValidator()->addRequiredField('Name');
    }
    
    /**
     * Answers the appropriate link for the receiver.
     *
     * @return string
     */
    public function getLink()
    {
        if ($this->LinkURL) {
            return $this->dbObject('LinkURL')->URL();
        }
        
        if ($this->LinkPageID) {
            return $this->LinkPage()->Link();
        }
    }
    
    /**
     * Answers the value of the item for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        $parts = array($this->Name);
        
        if ($Link = $this->Link) {
            $parts[] = "(" . rtrim($this->Link, '/') . ")";
        }
        
        return implode(' ', $parts);
    }
}
