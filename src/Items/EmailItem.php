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

use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\EmailField;
use SilverWare\Contact\Model\ContactItem;

/**
 * An extension of the contact item class for a email item.
 *
 * @package SilverWare\Contact\Items
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class EmailItem extends ContactItem
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Email Item';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Email Items';
    
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
        'Email' => 'Varchar(255)',
        'LinkEmail' => 'Boolean',
        'ProtectEmail' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'FontIcon' => 'envelope'
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'Link' => 'HTMLFragment',
        'EmailAddress' => 'HTMLFragment'
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
                EmailField::create(
                    'Email',
                    $this->fieldLabel('Email')
                )
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            CompositeField::create([
                CheckboxField::create(
                    'LinkEmail',
                    $this->fieldLabel('LinkEmail')
                ),
                CheckboxField::create(
                    'ProtectEmail',
                    $this->fieldLabel('ProtectEmail')
                )
            ])->setName('EmailItemOptions')->setTitle($this->i18n_singular_name())
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
        
        $labels['Email'] = _t(__CLASS__ . '.EMAIL', 'Email');
        $labels['LinkEmail'] = _t(__CLASS__ . '.LINKEMAIL', 'Link email');
        $labels['ProtectEmail'] = _t(__CLASS__ . '.PROTECTEMAIL', 'Protect email from spam bots (recommended)');
        
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
        
        $this->Title = _t(__CLASS__ . '.DEFAULTTITLE', 'Email');
        
        $this->LinkEmail = 1;
        $this->ProtectEmail = 1;
    }
    
    /**
     * Answers the value of the item for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->Email;
    }
    
    /**
     * Answers the email link for the template.
     *
     * @return string
     */
    public function getLink()
    {
        return sprintf('mailto:%s', ($this->ProtectEmail ? Email::obfuscate($this->Email, 'hex') : $this->Email));
    }
    
    /**
     * Answers the email address for the template.
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->ProtectEmail ? Email::obfuscate($this->Email, 'direction') : $this->Email;
    }
}
