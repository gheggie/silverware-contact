<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Contact\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */

namespace SilverWare\Contact\Model;

use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverWare\Extensions\Model\URLSegmentExtension;
use Page;

/**
 * An extension of the data object class for a contact recipient.
 *
 * @package SilverWare\Contact\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class ContactRecipient extends DataObject
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Recipient';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Recipients';
    
    /**
     * Defines the default sort field and order for this object.
     *
     * @var string
     * @config
     */
    private static $default_sort = 'Name';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Name' => 'Varchar(128)',
        'NameTo' => 'Varchar(128)',
        'EmailTo' => 'Varchar(255)',
        'NameFrom' => 'Varchar(128)',
        'EmailFrom' => 'Varchar(255)',
        'EmailSubject' => 'Varchar(255)',
        'Disabled' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'Parent' => Page::class
    ];
    
    /**
     * Defines the reciprocal many-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $belongs_many_many = [
        'Messages' => ContactMessage::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'Disabled' => 0
    ];
    
    /**
     * Defines the summary fields of this object.
     *
     * @var array
     * @config
     */
    private static $summary_fields = [
        'Name',
        'SendTo',
        'SendFrom',
        'Disabled.Nice'
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        URLSegmentExtension::class
    ];
    
    /**
     * Answers a list of field objects for the CMS interface.
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
            [
                TextField::create(
                    'Name',
                    $this->fieldLabel('Name')
                ),
                FieldGroup::create(
                    $this->fieldLabel('SendTo'),
                    [
                        TextField::create(
                            'NameTo',
                            $this->fieldLabel('NameTo')
                        )->setAttribute('size', 30),
                        EmailField::create(
                            'EmailTo',
                            $this->fieldLabel('EmailTo')
                        )->setAttribute('size', 40)
                    ]
                ),
                FieldGroup::create(
                    $this->fieldLabel('SendFrom'),
                    [
                        TextField::create(
                            'NameFrom',
                            $this->fieldLabel('NameFrom')
                        )->setAttribute('size', 30),
                        EmailField::create(
                            'EmailFrom',
                            $this->fieldLabel('EmailFrom')
                        )->setAttribute('size', 40)
                    ]
                ),
                TextField::create(
                    'EmailSubject',
                    $this->fieldLabel('EmailSubject')
                ),
                CheckboxField::create(
                    'Disabled',
                    $this->fieldLabel('Disabled')
                )
            ]
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
        return RequiredFields::create([
            'Name',
            'EmailTo',
            'EmailFrom',
            'EmailSubject'
        ]);
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
        
        $labels['SendTo'] = _t(__CLASS__ . '.SENDTO', 'Send to');
        $labels['SendFrom'] = _t(__CLASS__ . '.SENDFROM', 'Send from');
        $labels['EmailSubject'] = _t(__CLASS__ . '.EMAILSUBJECT', 'Email subject');
        
        $labels['Name'] = $labels['NameTo'] = $labels['NameFrom'] = _t(__CLASS__ . '.NAME', 'Name');
        $labels['Email'] = $labels['EmailTo'] = $labels['EmailFrom'] = _t(__CLASS__ . '.EMAIL', 'Email');
        $labels['Disabled'] = $labels['Disabled.Nice'] =_t(__CLASS__ . '.DISABLED', 'Disabled');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers true if the member can create a new instance of the receiver.
     *
     * @param Member $member Optional member object.
     * @param array $context Context-specific data.
     *
     * @return boolean
     */
    public function canCreate($member = null, $context = [])
    {
       return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
    
    /**
     * Answers true if the member can delete the receiver.
     *
     * @param Member $member
     *
     * @return boolean
     */
    public function canDelete($member = null)
    {
       return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
    
    /**
     * Answers true if the member can edit the receiver.
     *
     * @param Member $member
     *
     * @return boolean
     */
    public function canEdit($member = null)
    {
       return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
    
    /**
     * Answers true if the member can view the receiver.
     *
     * @param Member $member
     *
     * @return boolean
     */
    public function canView($member = null)
    {
       return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
    
    /**
     * Answers a string containing the name and email address for sending to.
     *
     * @return string
     */
    public function getSendTo()
    {
        return $this->NameTo ? sprintf('%s <%s>', $this->NameTo, $this->EmailTo) : $this->EmailTo;
    }
    
    /**
     * Answers a string containing the name and email address for sending from.
     *
     * @return string
     */
    public function getSendFrom()
    {
        return $this->NameFrom ? sprintf('%s <%s>', $this->NameFrom, $this->EmailFrom) : $this->EmailFrom;
    }
    
    /**
     * Sends the given email to the recipient.
     *
     * @param Email $email
     *
     * @return $this
     */
    public function receive(Email $email)
    {
        // Define Email Object:
        
        $email->setTo($this->EmailTo, $this->NameTo);
        $email->setFrom($this->EmailFrom, $this->NameFrom);
        
        // Define Email Subject:
        
        if (!$email->getSubject()) {
            $email->setSubject($this->EmailSubject);
        }
        
        // Define Email Template Data:
        
        $email->addData('Recipient', $this);
        
        // Send Email to Recipient:
        
        $email->send();
    }
}
