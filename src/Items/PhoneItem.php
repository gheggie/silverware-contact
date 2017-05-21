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
use SilverStripe\Forms\TextField;
use SilverWare\Contact\Model\ContactItem;

/**
 * An extension of the contact item class for a phone item.
 *
 * @package SilverWare\Contact\Items
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class PhoneItem extends ContactItem
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Phone Item';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Phone Items';
    
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
        'PhoneNumber' => 'Varchar(64)',
        'CallToNumber' => 'Varchar(64)',
        'LinkNumber' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'FontIcon' => 'phone'
    ];
    
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
                    'PhoneNumber',
                    $this->fieldLabel('PhoneNumber')
                ),
                TextField::create(
                    'CallToNumber',
                    $this->fieldLabel('CallToNumber')
                )->setRightTitle(
                    _t(
                        __CLASS__ . '.CALLTONUMBERRIGHTTITLE',
                        'Overrides the above phone number for linking (optional).'
                    )
                )
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            CompositeField::create([
                CheckboxField::create(
                    'LinkNumber',
                    $this->fieldLabel('LinkNumber')
                )
            ])->setName('PhoneItemOptions')->setTitle($this->i18n_singular_name())
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
        
        $labels['PhoneNumber'] = _t(__CLASS__ . '.PHONENUMBER', 'Phone number');
        $labels['CallToNumber'] = _t(__CLASS__ . '.CALLTONUMBER', 'Call to number');
        $labels['LinkNumber'] = _t(__CLASS__ . '.LINKNUMBER', 'Link number');
        
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
        
        $this->Title = _t(__CLASS__ . '.DEFAULTTITLE', 'Phone');
        
        $this->LinkNumber = 1;
    }
    
    /**
     * Answers the value of the item for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->PhoneNumber;
    }
    
    /**
     * Answers a phone number suitable for linking.
     *
     * @return string
     */
    public function getLinkableNumber()
    {
        return preg_replace('/[^0-9\+]+/', '', ($this->CallToNumber ? $this->CallToNumber : $this->PhoneNumber));
    }
    
    /**
     * Answers the phone number link for the template.
     *
     * @return string
     */
    public function getLink()
    {
        return sprintf('callto:%s', $this->getLinkableNumber());
    }
}
