<?php

/**
 * An extension of the data object class for a contact item.
 */
class ContactItem extends DataObject
{
    private static $singular_name = "Item";
    private static $plural_name   = "Items";
    
    private static $default_sort = "Sort";
    
    private static $db = array(
        'Sort' => 'Int',
        'Title' => 'Varchar(255)',
        'Disabled' => 'Boolean',
        'HideTitle' => 'Boolean'
    );
    
    private static $has_one = array(
        'Component' => 'SilverWareComponent'
    );
    
    private static $defaults = array(
        'Disabled' => 0,
        'HideTitle' => 0
    );
    
    private static $summary_fields = array(
        'Type' => 'Type',
        'Title' => 'Title',
        'Value' => 'Value',
        'Disabled.Nice' => 'Disabled'
    );
    
    private static $extensions = array(
        'SilverWareFontIconExtension'
    );
    
    /**
     * Answers a collection of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Create Field Tab Set:
        
        $fields = FieldList::create(TabSet::create('Root'));
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            array(
                TextField::create(
                    'Title',
                    _t('ContactItem.TITLE', 'Title')
                )
            )
        );
        
        // Create Options Tab:
        
        $fields->findOrMakeTab('Root.Options', _t('ContactItem.OPTIONS', 'Options'));
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            array(
                CheckboxField::create(
                    'HideTitle',
                    _t('ContactItem.HIDETITLE', 'Hide title')
                ),
                CheckboxField::create(
                    'Disabled',
                    _t('ContactItem.DISABLED', 'Disabled')
                )
            )
        );
        
        // Extend Field Objects:
        
        $this->extend('updateCMSFields', $fields);
        
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
        return RequiredFields::create(array('Title'));
    }
    
    /**
     * Answers a string describing the type of item.
     *
     * @return string
     */
    public function getType()
    {
        return $this->i18n_singular_name();
    }
    
    /**
     * Answers the value of the item for the CMS interface (override in subclass).
     *
     * @return string
     */
    public function getValue()
    {
        return "";
    }
    
    /**
     * Answers a string of class names for the HTML template.
     *
     * @return string
     */
    public function getHTMLClass()
    {
        return implode(' ', $this->getClassNames());
    }
    
    /**
     * Answers an array of class names for the receiver.
     *
     * @return array
     */
    public function getClassNames()
    {
        $classes = array('item', strtolower($this->i18n_singular_name()));
        
        if ($this->HideTitle) {
            $classes[] = "no-title";
        }
        
        return $classes;
    }
    
    /**
     * Answers true to use fixed width font icons.
     *
     * @return boolean
     */
    public function getFontIconFixedWidth()
    {
        return true;
    }
    
    /**
     * Renders the receiver for the HTML template.
     *
     * @return string
     */
    public function forTemplate()
    {
        return $this->renderWith('ContactItem');
    }
    
    /**
     * Renders the content of the receiver for the HTML template.
     *
     * @return string
     */
    public function Content()
    {
        return $this->renderWith($this->ClassName);
    }
    
    /**
     * Answers true if the item title is to be shown in the template.
     *
     * @return boolean
     */
    public function ShowTitle()
    {
        return !$this->HideTitle;
    }
}
