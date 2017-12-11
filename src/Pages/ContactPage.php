<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Contact\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */

namespace SilverWare\Contact\Pages;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\TextField;
use SilverWare\Contact\Forms\GridField\ContactMessageConfig;
use SilverWare\Contact\Model\ContactMessage;
use SilverWare\Contact\Model\ContactRecipient;
use SilverWare\Forms\FieldSection;
use Page;

/**
 * An extension of the page class for a contact page.
 *
 * @package SilverWare\Contact\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class ContactPage extends Page
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Contact Page';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Contact Pages';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A page which displays a contact form to allow a user to send a message';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/contact: admin/client/dist/images/icons/ContactPage.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_ContactPage';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'OnSendMessage' => 'Varchar(255)',
        'RecipientFieldLabel' => 'Varchar(128)',
        'SendViaEmail' => 'Boolean',
        'PhoneRequired' => 'Boolean',
        'ShowPhoneField' => 'Boolean',
        'ShowSubjectField' => 'Boolean',
        'ShowRecipientField' => 'Boolean'
    ];
    
    /**
     * Defines the has-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_many = [
        'Messages' => ContactMessage::class,
        'Recipients' => ContactRecipient::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'SendViaEmail' => 1,
        'PhoneRequired' => 0,
        'ShowPhoneField' => 0,
        'ShowSubjectField' => 0,
        'ShowRecipientField' => 0
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
        
        // Create Messages Tab:
        
        $fields->findOrMakeTab('Root.Messages', $this->fieldLabel('Messages'));
        
        // Add Messages Grid Field to Tab:
        
        $fields->addFieldToTab(
            'Root.Messages',
            GridField::create(
                'Messages',
                $this->fieldLabel('Messages'),
                $this->Messages(),
                ContactMessageConfig::create()
            )
        );
        
        // Create Recipients Tab:
        
        $fields->findOrMakeTab('Root.Recipients', $this->fieldLabel('Recipients'));
        
        // Add Recipients Grid Field to Tab:
        
        $fields->addFieldToTab(
            'Root.Recipients',
            GridField::create(
                'Recipients',
                $this->fieldLabel('Recipients'),
                $this->Recipients(),
                GridFieldConfig_RecordEditor::create()
            )
        );
        
        // Create Options Tab:
        
        $fields->findOrMakeTab('Root.Options', $this->fieldLabel('Options'));
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'ContactOptions',
                    $this->fieldLabel('ContactOptions'),
                    [
                        TextField::create(
                            'OnSendMessage',
                            $this->fieldLabel('OnSendMessage')
                        )->setRightTitle(
                            _t(
                                __CLASS__ . '.ONSENDMESSAGERIGHTTITLE',
                                'Shown to the user after sending a message.'
                            )
                        ),
                        CheckboxField::create(
                            'SendViaEmail',
                            $this->fieldLabel('SendViaEmail')
                        ),
                        CheckboxField::create(
                            'ShowPhoneField',
                            $this->fieldLabel('ShowPhoneField')
                        ),
                        CheckboxField::create(
                            'ShowSubjectField',
                            $this->fieldLabel('ShowSubjectField')
                        ),
                        CheckboxField::create(
                            'ShowRecipientField',
                            $this->fieldLabel('ShowRecipientField')
                        ),
                        CheckboxField::create(
                            'PhoneRequired',
                            $this->fieldLabel('PhoneRequired')
                        ),
                        TextField::create(
                            'RecipientFieldLabel',
                            $this->fieldLabel('RecipientFieldLabel')
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
        
        $labels['Options'] = _t(__CLASS__ . '.OPTIONS', 'Options');
        $labels['Messages'] = _t(__CLASS__ . '.MESSAGES', 'Messages');
        $labels['Recipients'] = _t(__CLASS__ . '.RECIPIENTS', 'Recipients');
        $labels['SendViaEmail'] = _t(__CLASS__ . '.SENDVIAEMAIL', 'Send via email');
        $labels['PhoneRequired'] = _t(__CLASS__ . '.PHONEREQUIRED', 'Phone required');
        $labels['OnSendMessage'] = _t(__CLASS__ . '.ONSENDMESSAGE', 'On Send message');
        $labels['ShowPhoneField'] = _t(__CLASS__ . '.SHOWPHONEFIELD', 'Show phone field');
        $labels['ShowSubjectField'] = _t(__CLASS__ . '.SHOWSUBJECTFIELD', 'Show subject field');
        $labels['ShowRecipientField'] = _t(__CLASS__ . '.SHOWRECIPIENTFIELD', 'Show recipient field');
        $labels['RecipientFieldLabel'] = _t(__CLASS__ . '.RECIPIENTFIELDLABEL', 'Recipient field label');
        $labels['ContactOptions'] = _t(__CLASS__ . '.CONTACT', 'Contact');
        
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
        
        $this->OnSendMessage = _t(
            __CLASS__ . '.DEFAULTONSENDMESSAGE',
            'Thank you for contacting us via our website.'
        );
    }
    
    /**
     * Answers the number of unread messages.
     *
     * @return integer
     */
    public function getUnreadMessageCount()
    {
        return $this->Messages()->filter('Read', 0)->count();
    }
    
    /**
     * Answers an array of number badge data for the CMS tabs.
     *
     * @return array
     */
    public function getNumberBadgeData()
    {
        return [
            'Root.Messages' => $this->getUnreadMessageCount()
        ];
    }
    
    /**
     * Answers the text to display as a site tree number badge.
     *
     * @return integer
     */
    public function getNumberBadgeText()
    {
        return _t(__CLASS__ . '.NUMBERBADGETEXT', '%d unread');
    }
    
    /**
     * Answers the value to display as a site tree number badge.
     *
     * @return integer
     */
    public function getNumberBadgeValue()
    {
        return $this->getUnreadMessageCount();
    }
    
    /**
     * Answers a list of enabled recipients.
     *
     * @return DataList
     */
    public function getEnabledRecipients()
    {
        return $this->Recipients()->filter('Disabled', 0);
    }
    
    /**
     * Answers an array of options for a recipients field.
     *
     * @return DataList
     */
    public function getEnabledRecipientOptions()
    {
        return $this->getEnabledRecipients()->map('ID', 'Name');
    }
}
