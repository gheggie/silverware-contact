<?php

/**
 * An extension of the data object class for a contact message.
 */
class ContactMessage extends DataObject
{
    private static $singular_name = "Message";
    private static $plural_name   = "Messages";
    
    private static $default_sort = "\"Created\" DESC";
    
    private static $db = array(
        'FirstName' => 'Varchar(128)',
        'LastName' => 'Varchar(128)',
        'Email' => 'Varchar(255)',
        'Phone' => 'Varchar(64)',
        'Subject' => 'Varchar(255)',
        'Message' => 'Text',
        'Read' => 'Boolean'
    );
    
    private static $has_one = array(
        'ParentPage' => 'Page'
    );
    
    private static $many_many = array(
        'Recipients' => 'ContactRecipient'
    );
    
    private static $defaults = array(
        'Read' => 0
    );
    
    private static $summary_fields = array(
        'RecipientNamesAsString' => 'Recipient(s)',
        'ReceivedFrom' => 'From',
        'Created.Nice' => 'Received'
    );
    
    /**
     * @var string
     */
    protected $template = "ContactMessage_Email";
    
    /**
     * Answers a collection of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Mark Message as Read:
        
        $this->markAsRead();
        
        // Create Field Tab Set:
        
        $fields = FieldList::create(TabSet::create('Root'));
        
        // Create Field Objects:
        
        $fields->addFieldsToTab(
            'Root.Main',
            array(
                TextField::create(
                    'RecipientsAsString',
                    _t('ContactMessage.TO', 'To')
                ),
                TextField::create(
                    'ReceivedFrom',
                    _t('ContactMessage.FROM', 'From')
                ),
                TextField::create(
                    'Received',
                    _t('ContactMessage.RECEIVED', 'Received'),
                    $this->dbObject('Created')->Nice()
                ),
                LiteralField::create(
                    'FormattedMessage',
                    sprintf(
                        '<div class="formatted-message">%s</div>',
                        $this->dbObject('Message')
                    )
                )
            )
        );
        
        // Create Phone Field (if required):
        
        if ($this->Phone) {
            
            $fields->insertAfter(
                TextField::create(
                    'Phone',
                    _t('ContactMessage.PHONENUMBER', 'Phone Number')
                ),
                'Email'
            );
            
        }
        
        // Create Subject Field (if required):
        
        if ($this->Subject) {
            
            $fields->insertBefore(
                TextField::create(
                    'Subject',
                    _t('ContactMessage.SUBJECT', 'Subject')
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
     * Defines the value of the template attribute.
     *
     * @param string $template
     * @return ContactMessage
     */
    public function setTemplate($template)
    {
        $this->template = (string) $template;
        
        return $this;
    }
    
    /**
     * Answers the value of the template attribute.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }
    
    /**
     * Answers the title of the receiver for the CMS interface.
     *
     * @return string
     */
    public function getTitle()
    {
        return _t(
            'ContactMessage.TITLE',
            'Message from {name} received {date}',
            array(
                'name' => $this->getFullName(),
                'date' => $this->dbObject('Created')->Nice()
            )
        );
    }
    
    /**
     * Adds recipients to the receiver from either the given page or form data array.
     *
     * @return ReturnType
     */
    public function addRecipients(ContactPage $Page, $data)
    {
        if ($Page->ShowRecipientField) {
            
            if (isset($data['RecipientID'])) {
                $this->Recipients()->add($data['RecipientID']);
            }
            
        } else {
            
            $this->Recipients()->addMany($Page->getEnabledRecipients());
            
        }
    }
    
    /**
     * Answers the full name of the contact.
     *
     * @return string
     */
    public function getFullName()
    {
        $names = array();
        
        if ($this->FirstName) {
            $names[] = $this->FirstName;
        }
        
        if ($this->LastName) {
            $names[] = $this->LastName;
        }
        
        return implode(' ', $names);
    }
    
    /**
     * Answers a string containing the name and email address the message was received from.
     *
     * @return string
     */
    public function getReceivedFrom()
    {
        return $this->FullName ? sprintf('%s <%s>', $this->FullName, $this->Email) : $this->Email;
    }
    
    /**
     * Answers a string containing the recipients of the message.
     *
     * @return string
     */
    public function getRecipientsAsString()
    {
        $recipients = array();
        
        foreach ($this->Recipients() as $Recipient) {
            $recipients[] = $Recipient->SendTo;
        }
        
        return implode(', ', $recipients);
    }
    
    /**
     * Answers a string containing the recipient names of the message.
     *
     * @return string
     */
    public function getRecipientNamesAsString()
    {
        $recipients = array();
        
        foreach ($this->Recipients() as $Recipient) {
            $recipients[] = $Recipient->Name;
        }
        
        return implode(', ', $recipients);
    }
    
    /**
     * Sends the message as an email to the associated recipients.
     */
    public function send()
    {
        foreach ($this->Recipients() as $Recipient) {
            $Recipient->receive($this->toEmail());
        }
    }
    
    /**
     * Marks the message as read.
     */
    public function markAsRead()
    {
        if (!$this->Read) {
            $this->Read = 1;
            $this->write();
        }
    }
    
    /**
     * Answers an email object for sending the message to a recipient.
     *
     * @return Email
     */
    public function toEmail()
    {
        // Create Email Object:
        
        $Email = Email::create();
        
        // Define Email Subject:
        
        $Email->setSubject($this->Subject);
        
        // Define Email Template:
        
        $Email->setTemplate($this->Template);
        
        // Populate Email Template:
        
        $Email->populateTemplate(
            array(
                'Message' => $this
            )
        );
        
        // Answer Email Object:
        
        return $Email;
    }
}

/**
 * An extension of the grid field config record viewer class for the contact message grid field.
 */
class ContactMessage_GridFieldConfig extends GridFieldConfig_RecordViewer
{
    /**
     * Constructs the object upon instantiation.
     *
     * @param integer $itemsPerPage
     */
    public function __construct($itemsPerPage = null)
    {
        // Construct Parent:
        
        parent::__construct($itemsPerPage);
        
        // Construct Object:
        
        $this->addComponent(new GridFieldDeleteAction());
        
        // Apply Formatting:
        
        $formatting = array();
        
        foreach (singleton('ContactMessage')->summaryFields() as $key => $value) {
            
            $formatting[$key] = function ($val, $item) {
                return '<span class="' . ($item->Read ? 'read' : 'unread') . '">' . $val . '</span>';
            };
            
        }
        
        $this->getComponentByType('GridFieldDataColumns')->setFieldFormatting($formatting);
        
        // Apply Extensions:
        
        $this->extend('updateConfig');
    }
}
