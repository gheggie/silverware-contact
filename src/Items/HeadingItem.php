<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Contact\Items
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */

namespace SilverWare\Contact\Items;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;
use SilverWare\Contact\Components\ContactComponent;
use SilverWare\Contact\Model\ContactItem;

/**
 * An extension of the contact item class for a heading item.
 *
 * @package SilverWare\Contact\Items
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class HeadingItem extends ContactItem
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Heading Item';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Heading Items';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = ContactItem::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Text' => 'Varchar(255)',
        'HeadingLevel' => 'Varchar(2)',
        'ShowFontIcon' => 'Boolean'
    ];
    
    /**
     * Defines the default heading level to use.
     *
     * @var array
     * @config
     */
    private static $heading_level_default = 'h3';
    
    /**
     * Answers a list of field objects for the CMS interface.
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
            [
                TextField::create(
                    'Text',
                    $this->fieldLabel('Text')
                )
            ]
        );
        
        // Define Placeholders:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            CompositeField::create([
                DropdownField::create(
                    'HeadingLevel',
                    $this->fieldLabel('HeadingLevel'),
                    ContactComponent::singleton()->getTitleLevelOptions()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
            ])->setName('HeadingItemStyle')->setTitle($this->i18n_singular_name())
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            CompositeField::create([
                CheckboxField::create(
                    'ShowFontIcon',
                    $this->fieldLabel('ShowFontIcon')
                )
            ])->setName('HeadingItemOptions')->setTitle($this->i18n_singular_name())
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
        
        $labels['Text'] = _t(__CLASS__ . '.TEXT', 'Text');
        $labels['HeadingLevel'] = _t(__CLASS__ . '.HEADINGLEVEL', 'Heading level');
        $labels['ShowFontIcon'] = _t(__CLASS__ . '.SHOWFONTICONINHEADING', 'Show font icon in heading');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Populates the default values for the fields of the receiver.
     *
     * @return void
     */
    public function populateDefaults()
    {
        // Populate Defaults (from parent):
        
        parent::populateDefaults();
        
        // Populate Defaults:
        
        $this->Title = _t(__CLASS__ . '.DEFAULTTITLE', 'Heading');
        
        $this->HideTitle = 1;
        $this->ShowFontIcon = 1;
    }
    
    /**
     * Answers the value of the item for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->Text;
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
}
