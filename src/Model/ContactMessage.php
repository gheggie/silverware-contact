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
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverWare\Security\CMSMainPermissions;
use Page;

/**
 * An extension of the data object class for a contact message.
 *
 * @package SilverWare\Contact\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class ContactMessage extends DataObject
{
    use CMSMainPermissions;
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Message';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Messages';
    
    /**
     * Defines the default sort field and order for this object.
     *
     * @var string
     * @config
     */
    private static $default_sort = '"Created" DESC';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_ContactMessage';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'FirstName' => 'Varchar(128)',
        'LastName' => 'Varchar(128)',
        'Email' => 'Varchar(255)',
        'Phone' => 'Varchar(64)',
        'Subject' => 'Varchar(255)',
        'Message' => 'Text',
        'Read' => 'Boolean'
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
     * Defines the many-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $many_many = [
        'Recipients' => ContactRecipient::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'Read' => 0
    ];
    
    /**
     * Defines the summary fields of this object.
     *
     * @var array
     * @config
     */
    private static $summary_fields = [
        'RecipientNames',
        'ReceivedFrom',
        'Received'
    ];
    
    /**
     * Defines the HTML template to use for emails.
     *
     * @var string
     */
    protected $HTMLTemplate = self::class;
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Mark Message as Read:
        
        $this->markAsRead();
        
        // Create Field Tab Set:
        
        $fields = FieldList::create(TabSet::create('Root'));
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create(
                    'RecipientNames',
                    $this->fieldLabel('RecipientNames')
                ),
                TextField::create(
                    'ReceivedFrom',
                    $this->fieldLabel('ReceivedFrom')
                ),
                TextField::create(
                    'Received',
                    $this->fieldLabel('Received')
                ),
                LiteralField::create(
                    'FormattedMessage',
                    sprintf(
                        '<div class="formatted-message">%s</div>',
                        $this->dbObject('Message')
                    )
                )
            ]
        );
        
        // Create Phone Field (if required):
        
        if ($this->Phone) {
            
            $fields->insertAfter(
                TextField::create(
                    'Phone',
                    $this->fieldLabel('Phone')
                ),
                'ReceivedFrom'
            );
            
        }
        
        // Create Subject Field (if required):
        
        if ($this->Subject) {
            
            $fields->insertBefore(
                TextField::create(
                    'Subject',
                    $this->fieldLabel('Subject')
                ),
                'FormattedMessage'
            );
            
        }
        
        // Extend Field Objects:
        
        $this->extend('updateCMSFields', $fields);
        
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
        
        $labels['Phone'] = _t(__CLASS__ . '.PHONE', 'Phone');
        $labels['Subject'] = _t(__CLASS__ . '.SUBJECT', 'Subject');
        $labels['Received'] = _t(__CLASS__ . '.RECEIVED', 'Received');
        $labels['ReceivedFrom'] = _t(__CLASS__ . '.FROM', 'From');
        $labels['RecipientNames'] = _t(__CLASS__ . '.RECIPIENTS', 'Recipient(s)');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers the title of the receiver for the CMS interface.
     *
     * @return string
     */
    public function getTitle()
    {
        return sprintf(
            _t(__CLASS__ . 'CMSTITLE', 'Message from %s received %s'),
            $this->getFullName(),
            $this->getReceived()
        );
    }
    
    /**
     * Answers the full name of the contact.
     *
     * @return string
     */
    public function getFullName()
    {
        $names = [];
        
        if ($this->FirstName) {
            $names[] = $this->FirstName;
        }
        
        if ($this->LastName) {
            $names[] = $this->LastName;
        }
        
        return implode(' ', $names);
    }
    
    /**
     * Answers a nicely formatted string showing the date the message was received.
     *
     * @return string
     */
    public function getReceived()
    {
        return $this->dbObject('Created')->Nice();
    }
    
    /**
     * Answers a string containing the name and email address of the contact.
     *
     * @return string
     */
    public function getReceivedFrom()
    {
        return $this->FullName ? sprintf('%s <%s>', $this->FullName, $this->Email) : $this->Email;
    }
    
    /**
     * Answers a string containing the recipient names of the message.
     *
     * @return string
     */
    public function getRecipientNames()
    {
        if (!$this->Recipients()->count()) {
            return _t(__CLASS__ . 'NONE', 'None');
        }
        
        $recipients = [];
        
        foreach ($this->Recipients() as $recipient) {
            $recipients[] = $recipient->Name;
        }
        
        return implode(', ', $recipients);
    }
    
    /**
     * Adds the given array of recipients to the receiver.
     *
     * @param array|ArrayAccess $recipients
     *
     * @return $this
     */
    public function addRecipients($recipients)
    {
        $this->Recipients()->addMany($recipients);
        
        return $this;
    }
    
    /**
     * Defines the parent page for the receiver.
     *
     * @param Page $page
     *
     * @return $this
     */
    public function setParent(Page $page)
    {
        $this->ParentID = $page->ID;
        
        return $this;
    }
    
    /**
     * Defines the value of the HTMLTemplate attribute.
     *
     * @param string $HTMLTemplate
     *
     * @return $this
     */
    public function setHTMLTemplate($HTMLTemplate)
    {
        $this->HTMLTemplate = (string) $HTMLTemplate;
        
        return $this;
    }
    
    /**
     * Answers the value of the HTMLTemplate attribute.
     *
     * @return string
     */
    public function getHTMLTemplate()
    {
        return $this->HTMLTemplate;
    }
    
    /**
     * Answers true if the message is marked as read.
     *
     * @return boolean
     */
    public function isRead()
    {
        return (boolean) $this->Read;
    }
    
    /**
     * Sends the message as an email to the associated recipients.
     *
     * @return void
     */
    public function send()
    {
        foreach ($this->Recipients() as $recipient) {
            $recipient->receive($this->toEmail());
        }
    }
    
    /**
     * Marks the message as read.
     *
     * @return $this
     */
    public function markAsRead()
    {
        if (!$this->isRead()) {
            $this->Read = 1;
            $this->write();
        }
        
        return $this;
    }
    
    /**
     * Converts the message to an email object.
     *
     * @return Email
     */
    public function toEmail()
    {
        // Create Email Object:
        
        $email = Email::create();
        
        // Define Email Object:
        
        $email->setSubject($this->Subject);
        $email->setHTMLTemplate($this->HTMLTemplate);
        
        // Define Email Template Data:
        
        $email->setData([
           'Message' => $this
        ]);
        
        // Answer Email Object:
        
        return $email;
    }
}
