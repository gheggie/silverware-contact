<?php

/**
 * An extension of the contact item class for an address.
 */
class ContactAddress extends ContactItem
{
    private static $singular_name = "Address";
    private static $plural_name   = "Addresses";
    
    private static $db = array(
        'Street' => 'Varchar(255)',
        'Suburb' => 'Varchar(255)',
        'StateTerritory' => 'Varchar(128)',
        'PostalCode' => 'Varchar(32)',
        'Country' => 'Varchar(2)'
    );
    
    private static $casting = array(
        'FullAddress' => 'Text'
    );
    
    private static $defaults = array(
        'Title' => 'Address'
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
                    'Street',
                    _t('ContactAddress.STREET', 'Street')
                ),
                TextField::create(
                    'Suburb',
                    _t('ContactAddress.SUBURB', 'Suburb')
                ),
                TextField::create(
                    'StateTerritory',
                    _t('ContactAddress.STATETERRITORY', 'State/Territory')
                ),
                TextField::create(
                    'PostalCode',
                    _t('ContactAddress.POSTALCODE', 'Postal code')
                ),
                $country = CountryDropdownField::create(
                    'Country',
                    _t('ContactAddress.COUNTRY', 'Country')
                )->setEmptyString(' ')
            )
        );
        
        // Define Field Objects:
        
        $country->config()->default_country = "";
        $country->config()->default_to_locale = 0;
        
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
            RequiredFields::create(
                'Street',
                'Suburb',
                'StateTerritory',
                'PostalCode',
                'Country'
            )
        );
    }
    
    /**
     * Answers the value of the item for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->obj('FullAddress')->LimitCharacters(40);
    }
    
    /**
     * Answers the full address as a string.
     *
     * @return string
     */
    public function getFullAddress()
    {
        $address = array();
        
        if ($this->Street) {
            
            $address[] = $this->Street;
            
        }
        
        if ($this->Suburb || $this->PostalCode || $this->StateTerritory) {
            
            $line = array();
            
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
            
            $address[] = $this->getCountryName();
            
        }
        
        return implode("\n", $address);
    }
    
    /**
     * Answers the name of the country defined for the receiver.
     *
     * @return string
     */
    public function getCountryName()
    {
        return SilverWareTools::get_country_name($this->Country);
    }
}
