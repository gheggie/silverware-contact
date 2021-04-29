<?php

/**
 * An extension of the base component class for a contact component.
 */
class ContactComponent extends BaseComponent
{
    private static $singular_name = "Contact Component";
    private static $plural_name   = "Contact Components";
    
    private static $description = "A component to show contact information";
    
    private static $icon = "silverware-contact/images/icons/ContactComponent.png";
    
    private static $hide_ancestor = "BaseComponent";
    
    private static $allowed_children = "none";
    
    private static $db = array(
        'RowsWidth' => 'Int',
        'LabelWidth' => 'Int',
        'ItemLayout' => "Enum('Rows, Columns', 'Rows')",
        'ItemTitleFontSize' => 'Decimal(3,2,1)',
        'ItemTitleFontUnit' => "Enum('px, em, rem, pt, cm, in', 'rem')",
        'ItemTitleMarginBottom' => 'Decimal(3,2,1)',
        'ItemTitleMarginBottomUnit' => "Enum('px, em, rem, pt, cm, in', 'rem')"
    );
    
    private static $defaults = array(
        'RowsWidth' => 400,
        'LabelWidth' => 200,
        'ItemLayout' => 'Rows',
        'ItemTitleFontSize' => 2.4,
        'ItemTitleFontUnit' => 'rem',
        'ItemTitleMarginBottom' => 1,
        'ItemTitleMarginBottomUnit' => 'rem'
    );
    
    private static $has_many = array(
        'Items' => 'ContactItem'
    );
    
    private static $required_themed_css = array(
        'contact-component'
    );
    
    private static $identifier_mappings = array(
        'Items' => array(
            'Address' => 'ContactAddress',
            'Email' => 'ContactEmail',
            'Fax' => 'ContactFax',
            'Header' => 'ContactHeader',
            'Link' => 'ContactLink',
            'Phone' => 'ContactPhone',
            'Skype' => 'ContactSkype',
            'Text' => 'ContactText'
        )
    );
    
    protected $customItems;
    
    /**
     * Answers a collection of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Insert Items Tab:
        
        $fields->insertAfter(
            Tab::create(
                'Items',
                _t('ContactComponent.ITEMS', 'Items')
            ),
            'Main'
        );
        
        // Add Items Grid Field to Tab:
        
        $fields->addFieldToTab(
            'Root.Items',
            $items = GridField::create(
                'Items',
                _t('ContactComponent.ITEMS', 'Items'),
                $this->Items(),
                GridFieldConfig_MultiClassEditor::create()->useDescendantsOf('ContactItem')
            )
        );
        
        // Create Style Fields:
        
        $fields->addFieldsToTab(
            'Root.Style',
            array(
                ToggleCompositeField::create(
                    'ContactComponentStyle',
                    $this->i18n_singular_name(),
                    array(
                        DropdownField::create(
                            'ItemLayout',
                            _t('ContactComponent.ITEMLAYOUT', 'Item layout'),
                            $this->dbObject('ItemLayout')->enumValues()
                        )->setRightTitle(
                            _t(
                                'ContactComponent.ITEMLAYOUTRIGHTTITLE',
                                'Items will automatically display as rows when space is limited.'
                            )
                        ),
                        FieldGroup::create(
                            _t('ContactComponent.ITEMTITLEFONTSIZE', 'Item title font size'),
                            array(
                                NumericField::create('ItemTitleFontSize', '')->setAttribute(
                                    'placeholder',
                                    _t('ContactComponent.SIZE', 'Size')
                                )->setMaxLength(5),
                                DropdownField::create(
                                    'ItemTitleFontUnit',
                                    '',
                                    $this->dbObject('ItemTitleFontUnit')->enumValues()
                                )
                            )
                        ),
                        FieldGroup::create(
                            _t('ContactComponent.ITEMTITLEBOTTOMMARGIN', 'Item title bottom margin'),
                            array(
                                NumericField::create('ItemTitleMarginBottom', '')->setAttribute(
                                    'placeholder',
                                    _t('ContactComponent.SIZE', 'Size')
                                )->setMaxLength(5),
                                DropdownField::create(
                                    'ItemTitleMarginBottomUnit',
                                    '',
                                    $this->dbObject('ItemTitleMarginBottomUnit')->enumValues()
                                )
                            )
                        )
                    )
                )
            )
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            array(
                ToggleCompositeField::create(
                    'ContactComponentOptions',
                    $this->i18n_singular_name(),
                    array(
                        NumericField::create(
                            'RowsWidth',
                            _t('ContactComponent.ROWSWIDTH', 'Rows width (in pixels)')
                        )->setRightTitle(
                            _t(
                                'ContactComponent.ROWSWIDTHRIGHTTITLE',
                                'Specifies the container width when the item layout changes from columns to rows.'
                            )
                        ),
                        NumericField::create(
                            'LabelWidth',
                            _t('ContactComponent.LABELWIDTH', 'Label width (in pixels)')
                        )->setRightTitle(
                            _t(
                                'ContactComponent.LABELWIDTHRIGHTTITLE',
                                'Specifies the width of labels when the item layout is in columns mode.'
                            )
                        )
                    )
                )
            )
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Defines a list of custom items for the component.
     *
     * @param SS_List $items
     * @return ContactComponent
     */
    public function setCustomItems(SS_List $items)
    {
        $this->customItems = $items;
        
        return $this;
    }
    
    /**
     * Answers a list of custom items for the component.
     *
     * @return SS_List|null
     */
    public function getCustomItems()
    {
        return $this->customItems;
    }
    
    /**
     * Answers a string of class names for the item wrapper.
     *
     * @return string
     */
    public function getItemWrapperClass()
    {
        return implode(' ', $this->getItemWrapperClassNames());
    }
    
    /**
     * Answers an array of class names for the item wrapper.
     *
     * @return array
     */
    public function getItemWrapperClassNames()
    {
        return array('items', strtolower($this->ItemLayout));
    }
    
    /**
     * Answers the CSS string for the item title font size style.
     *
     * @return string
     */
    public function getItemTitleFontSizeCSS()
    {
        return $this->ItemTitleFontSize . $this->ItemTitleFontUnit;
    }
    
    /**
     * Answers the CSS string for the item title font size style.
     *
     * @return string
     */
    public function getItemTitleMarginBottomCSS()
    {
        return $this->ItemTitleMarginBottom . $this->ItemTitleMarginBottomUnit;
    }
    
    /**
     * Answers a data list which contains only the items which are not disabled.
     *
     * @return DataList
     */
    public function EnabledItems()
    {
        $items = $this->Items();
        
        if ($this->customItems) {
            $items = $this->customItems;
        }
        
        return $items->filter('Disabled', false);
    }
}

/**
 * An extension of the base component controller class for a contact component.
 */
class ContactComponent_Controller extends BaseComponent_Controller
{
    /**
     * Defines the allowed actions for this controller.
     */
    private static $allowed_actions = array(
        
    );
    
    /**
     * Performs initialisation before any action is called on the receiver.
     */
    public function init()
    {
        // Initialise Parent:
        
        parent::init();
    }
}
