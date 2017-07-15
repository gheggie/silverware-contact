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
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;
use SilverWare\Contact\Model\ContactItem;
use SilverWare\Forms\FieldSection;

/**
 * An extension of the contact item class for a Skype item.
 *
 * @package SilverWare\Contact\Items
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class SkypeItem extends ContactItem
{
    /**
     * Define mode constants.
     */
    const MODE_CALL = 'call';
    const MODE_CHAT = 'chat';
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Skype Item';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Skype Items';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A contact item to show a Skype link';
    
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
        'SkypeName' => 'Varchar(32)',
        'SkypeMode' => 'Varchar(16)',
        'VideoEnabled' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'FontIcon' => 'skype',
        'VideoEnabled' => 0
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
                    'SkypeName',
                    $this->fieldLabel('SkypeName')
                ),
                DropdownField::create(
                    'SkypeMode',
                    $this->fieldLabel('SkypeMode'),
                    $this->getModeOptions()
                )
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'SkypeOptions',
                $this->fieldLabel('SkypeOptions'),
                [
                    CheckboxField::create(
                        'VideoEnabled',
                        $this->fieldLabel('VideoEnabled')
                    )
                ]
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
        
        $labels['SkypeName'] = _t(__CLASS__ . '.SKYPENAME', 'Skype name');
        $labels['SkypeMode'] = _t(__CLASS__ . '.SKYPEMODE', 'Skype mode');
        $labels['VideoEnabled'] = _t(__CLASS__ . '.VIDEOENABLED', 'Video enabled');
        $labels['SkypeOptions'] = _t(__CLASS__ . '.SKYPE', 'Skype');
        
        // Answer Field Label:
        
        return $labels;
    }
    
    /**
     * Answers the Skype link for the template.
     *
     * @return string
     */
    public function getSkypeLink()
    {
        return sprintf(
            'skype:%s?%s&video=%s',
            $this->SkypeName,
            $this->SkypeMode,
            $this->dbObject('VideoEnabled')->NiceAsBoolean()
        );
    }
    
    /**
     * Answers a title for the Skype link.
     *
     * @return string
     */
    public function getLinkTitle()
    {
        return sprintf('%s %s', $this->ModeTitle, $this->SkypeName);
    }
    
    /**
     * Answers the appropriate title for the defined Skype mode.
     *
     * @return string
     */
    public function getModeTitle()
    {
        switch ($this->SkypeMode) {
            case self::MODE_CALL:
                return _t(__CLASS__ . '.CALLTO', 'Call to');
            case self::MODE_CHAT:
                return _t(__CLASS__ . '.CHATWITH', 'Chat with');
        }
    }
    
    /**
     * Answers an array of options for the mode field.
     *
     * @return array
     */
    public function getModeOptions()
    {
        return [
            self::MODE_CALL => _t(__CLASS__ . '.CALL', 'Call'),
            self::MODE_CHAT => _t(__CLASS__ . '.CHAT', 'Chat')
        ];
    }
}
