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

use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\i18n\i18n;
use SilverWare\Contact\Model\ContactItem;
use SilverWare\Countries\Forms\CountryDropdownField;
use SilverWare\Forms\FieldSection;

/**
 * An extension of the contact item class for an address item.
 *
 * @package SilverWare\Contact\Items
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class AddressItem extends ContactItem
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Address Item';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Address Items';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A contact item to show an address';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_Contact_AddressItem';
    
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
        'Street' => 'Varchar(255)',
        'StreetLine2' => 'Varchar(255)',
        'Suburb' => 'Varchar(255)',
        'StateTerritory' => 'Varchar(128)',
        'PostalCode' => 'Varchar(32)',
        'Country' => 'Varchar(2)'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'FontIcon' => 'building'
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'FullAddress' => 'Text'
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
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNSELECT', 'Select');
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                FieldSection::create(
                    'AddressSection',
                    $this->fieldLabel('Address'),
                    [
                        CompositeField::create([
                            TextField::create(
                                'Street',
                                ''
                            ),
                            TextField::create(
                                'StreetLine2',
                                ''
                            )->addExtraClass('street-line-2')
                        ])->setName('StreetWrapper')->setTitle($this->fieldLabel('Street')),
                        TextField::create(
                            'Suburb',
                            $this->fieldLabel('Suburb')
                        ),
                        TextField::create(
                            'StateTerritory',
                            $this->fieldLabel('StateTerritory')
                        ),
                        TextField::create(
                            'PostalCode',
                            $this->fieldLabel('PostalCode')
                        ),
                        CountryDropdownField::create(
                            'Country',
                            $this->fieldLabel('Country')
                        )
                    ]
                )
            ]
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
        return parent::getCMSValidator()->appendRequiredFields(
            RequiredFields::create([
                'Street',
                'Suburb',
                'StateTerritory',
                'PostalCode',
                'Country'
            ])
        );
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
        
        $labels['Street'] = _t(__CLASS__ . '.STREET', 'Street');
        $labels['Suburb'] = _t(__CLASS__ . '.SUBURB', 'Suburb');
        $labels['Address'] = _t(__CLASS__ . '.ADDRESS', 'Address');
        $labels['Country'] = _t(__CLASS__ . '.COUNTRY', 'Country');
        $labels['PostalCode'] = _t(__CLASS__ . '.POSTALCODE', 'Postal code');
        $labels['StateTerritory'] = _t(__CLASS__ . '.STATETERRITORY', 'State/Territory');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Event method called before the receiver is written to the database.
     *
     * @return void
     */
    public function onBeforeWrite()
    {
        // Call Parent Event:
        
        parent::onBeforeWrite();
        
        // Correct Country Value:
        
        $this->Country = strtolower($this->Country);
    }
    
    /**
     * Answers the full name of the country.
     *
     * @return string
     */
    public function getCountryName()
    {
        return i18n::getData()->countryName($this->Country);
    }
    
    /**
     * Answers the full address as a string.
     *
     * @return string
     */
    public function getFullAddress()
    {
        $address = [];
        
        if ($this->Street) {
            $address[] = $this->Street;
        }
        
        if ($this->StreetLine2) {
            $address[] = $this->StreetLine2;
        }
        
        if ($this->Suburb || $this->PostalCode || $this->StateTerritory) {
            
            $line = [];
            
            if ($this->Suburb) {
                $line[] = $this->Suburb;
            }
            
            if ($this->PostalCode) {
                $line[] = $this->PostalCode;
            }
            
            if ($this->StateTerritory) {
                $line[] = $this->StateTerritory;
            }
            
            $address[] = implode(' ', $line);
            
        }
        
        if ($this->Country) {
            $address[] = $this->CountryName;
        }
        
        return implode("\n", $address);
    }
}
