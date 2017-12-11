<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Contact\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */

namespace SilverWare\Contact\Components;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverWare\Components\BaseComponent;
use SilverWare\Contact\Model\ContactItem;
use SilverWare\Extensions\Style\AlignmentStyle;
use SilverWare\Forms\FieldSection;

/**
 * An extension of the base component class for a contact component.
 *
 * @package SilverWare\Contact\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class ContactComponent extends BaseComponent
{
    /**
     * Define constants.
     */
    const ITEM_MODE_BLOCK  = 'block';
    const ITEM_MODE_INLINE = 'inline';
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Contact Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Contact Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which shows contact information';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/contact: admin/client/dist/images/icons/ContactComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_ContactComponent';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'HeadingLevel' => 'Varchar(2)',
        'ItemMode' => 'Varchar(8)',
        'ShowIcons' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ShowIcons' => 1
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        ContactItem::class
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        AlignmentStyle::class
    ];
    
    /**
     * Defines the default item mode to use.
     *
     * @var string
     * @config
     */
    private static $default_item_mode = 'block';
    
    /**
     * Defines the default heading level to use.
     *
     * @var array
     * @config
     */
    private static $heading_level_default = 'h4';
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Style Fields:
        
        $fields->addFieldsToTab(
            'Root.Style',
            [
                FieldSection::create(
                    'ContactStyle',
                    $this->fieldLabel('ContactStyle'),
                    [
                        DropdownField::create(
                            'HeadingLevel',
                            $this->fieldLabel('HeadingLevel'),
                            $this->getTitleLevelOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            'ItemMode',
                            $this->fieldLabel('ItemMode'),
                            $this->getItemModeOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
                    ]
                )
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'ContactOptions',
                    $this->fieldLabel('ContactOptions'),
                    [
                        CheckboxField::create(
                            'ShowIcons',
                            $this->fieldLabel('ShowIcons')
                        )
                    ]
                )
            ]
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['ItemMode'] = _t(__CLASS__ . '.ITEMMODE', 'Item mode');
        $labels['ShowIcons'] = _t(__CLASS__ . '.SHOWICONS', 'Show icons');
        $labels['HeadingLevel'] = _t(__CLASS__ . '.HEADINGLEVEL', 'Heading level');
        $labels['ContactStyle'] = $labels['ContactOptions'] = _t(__CLASS__ . '.CONTACT', 'Contact');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers an array of wrapper class names for the HTML template.
     *
     * @return array
     */
    public function getWrapperClassNames()
    {
        $classes = ['items'];
        
        $classes[] = $this->getItemModeClass();
        
        $this->extend('updateWrapperClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers the item mode class for the receiver.
     *
     * @return string
     */
    public function getItemModeClass()
    {
        return sprintf('item-mode-%s', $this->ItemMode ? $this->ItemMode : $this->config()->default_item_mode);
    }
    
    /**
     * Answers a list of all items within the receiver.
     *
     * @return DataList
     */
    public function getItems()
    {
        return $this->getAllChildren();
    }
    
    /**
     * Answers a list of the enabled items within the receiver.
     *
     * @return ArrayList
     */
    public function getEnabledItems()
    {
        return $this->getItems()->filterByCallback(function ($item) {
            return $item->isEnabled();
        });
    }
    
    /**
     * Answers the first enabled item found matching the given class name.
     *
     * @param string $class
     *
     * @return ContactItem
     */
    public function getEnabledItemByClass($class)
    {
        return $this->getEnabledItems()->find('ClassName', $class);
    }
    
    /**
     * Answers the heading tag for the receiver.
     *
     * @return string
     */
    public function getHeadingTag()
    {
        if ($tag = $this->getField('HeadingLevel')) {
            return $tag;
        }
        
        return $this->config()->heading_level_default;
    }
    
    /**
     * Answers an array of custom CSS required for the template.
     *
     * @return array
     */
    public function getCustomCSS()
    {
        $css = parent::getCustomCSS();
        
        foreach ($this->getEnabledItems() as $item) {
            $css = array_merge($css, $item->getCustomCSS());
        }
        
        return $css;
    }
    
    /**
     * Answers true if the object is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->getEnabledItems()->exists()) {
            return true;
        }
        
        return parent::isDisabled();
    }
    
    /**
     * Answers an array of options for the item mode field.
     *
     * @return array
     */
    public function getItemModeOptions()
    {
        return [
            self::ITEM_MODE_BLOCK  => _t(__CLASS__ . '.BLOCK', 'Block'),
            self::ITEM_MODE_INLINE => _t(__CLASS__ . '.INLINE', 'Inline')
        ];
    }
}
