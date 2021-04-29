<?php

/**
 * An extension of the page class for a contact page.
 */
class ContactPage extends Page
{
    private static $singular_name = "Contact Page";
    private static $plural_name   = "Contact Pages";
    
    private static $description = "A page which displays a form allowing the user to send a message";
    
    private static $icon = "silverware-contact/images/icons/ContactPage.png";
    
    private static $db = array(
        'SendViaEmail' => 'Boolean',
        'OnSendMessage' => 'Varchar(255)',
        'ShowPhoneField' => 'Boolean',
        'ShowSubjectField' => 'Boolean',
        'ShowRecipientField' => 'Boolean',
        'RecipientFieldLabel' => 'Varchar(255)'
    );
    
    private static $has_many = array(
        'Messages' => 'ContactMessage',
        'Recipients' => 'ContactRecipient'
    );
    
    private static $defaults = array(
        'SendViaEmail' => 1,
        'ShowPhoneField' => 0,
        'ShowSubjectField' => 0,
        'ShowRecipientField' => 0
    );
    
    /**
     * Shortcode handler for the 'contact_link' shortcode.
     *
     * @param array $arguments
     * @param string $content
     * @param ShortcodeParser $parser
     * @return string
     */
    public static function link_shortcode_handler($arguments, $content = null, $parser = null)
    {
        // Check Arguments:
        
        if (!isset($arguments['id'])) {
            return;
        }
        
        // Obtain Page and Recipient IDs:
        
        list($pid, $rid) = array_pad(explode('-', $arguments['id']), 2, null);
        
        // Obtain Page Object:
        
        if ($Page = self::get()->byID($pid)) {
            
            // Obtain Link:
            
            $Link = $Page->getRecipientLink($rid);
            
            // Answer Link:
            
            if ($content) {
                return sprintf('<a href="%s">%s</a>', $Link, $parser->parse($content));
            } else {
                return $Link;
            }
            
        }
    }
    
    /**
     * Answers a collection of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Messages Tab:
        
        $fields->findOrMakeTab('Root.Messages', $this->getMessagesTabLabel());
        
        // Add Messages Grid Field to Tab:
        
        $fields->addFieldToTab(
            'Root.Messages',
            GridField::create(
                'Messages',
                _t('ContactPage.MESSAGES', 'Messages'),
                $this->Messages(),
                ContactMessage_GridFieldConfig::create()
            )
        );
        
        // Create Recipients Tab:
        
        $fields->findOrMakeTab('Root.Recipients', _t('ContactPage.RECIPIENTS', 'Recipients'));
        
        // Add Recipients Grid Field to Tab:
        
        $fields->addFieldToTab(
            'Root.Recipients',
            GridField::create(
                'Recipients',
                _t('ContactPage.RECIPIENTS', 'Recipients'),
                $this->Recipients(),
                GridFieldConfig_RecordEditor::create()
            )
        );
        
        // Create Options Tab:
        
        $fields->findOrMakeTab('Root.Options', _t('ContactPage.OPTIONS', 'Options'));
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            ToggleCompositeField::create(
                'ContactPageOptions',
                $this->i18n_singular_name(),
                array(
                    TextField::create(
                        'OnSendMessage',
                        _t('ContactPage.ONSENDMESSAGE', 'On Send message')
                    )->setRightTitle(
                        _t(
                            'ContactPage.ONSENDMESSAGERIGHTTITLE',
                            'Shown to the user after sending their message.'
                        )
                    ),
                    TextField::create(
                        'RecipientFieldLabel',
                        _t('ContactPage.RECIPIENTFIELDLABEL', 'Label for recipient field')
                    ),
                    CheckboxField::create(
                        'ShowPhoneField',
                        _t('ContactPage.SHOWPHONEFIELD', 'Show phone field')
                    ),
                    CheckboxField::create(
                        'ShowSubjectField',
                        _t('ContactPage.SHOWSUBJECTFIELD', 'Show subject field')
                    ),
                    CheckboxField::create(
                        'ShowRecipientField',
                        _t('ContactPage.SHOWRECIPIENTFIELD', 'Show recipient field')
                    ),
                    CheckboxField::create(
                        'SendViaEmail',
                        _t('ContactPage.SENDMESSAGESVIAEMAIL', 'Send messages via email')
                    )
                )
            )
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Populates the default values for the attributes of the receiver.
     */
    public function populateDefaults()
    {
        // Populate Defaults (from parent):
        
        parent::populateDefaults();
        
        // Populate Defaults:
        
        $this->OnSendMessage = _t('ContactPage.DEFAULTONSENDMESSAGE', 'Thank you for contacting us via our website.');
        $this->RecipientFieldLabel = _t('ContactPage.DEFAULTRECIPIENTFIELDLABEL', 'Recipient');
    }
    
    /**
     * Answers a link to the specified contact page recipient (or the page link if none specified).
     *
     * @param integer $id
     * @return string
     */
    public function getRecipientLink($id = null)
    {
        if ($recipient = $this->getRecipientByID($id)) {
            return $recipient->Link();
        }
        
        return $this->Link();
    }
    
    /**
     * Answers a contact recipient with the given ID.
     *
     * @param integer $id
     * @return ContactRecipient
     */
    public function getRecipientByID($id)
    {
        return $this->Recipients()->byID($id);
    }
    
    /**
     * Answers a list of enabled recipients.
     *
     * @return DataList
     */
    public function getEnabledRecipients()
    {
        return $this->Recipients()->filter(array('Disabled' => 0));
    }

    /**
     * Answers an array of enabled recipients for a dropdown field.
     *
     * @return array
     */
    public function getEnabledRecipientsMap()
    {
        return $this->getEnabledRecipients()->map('ID', 'Name');
    }
    
    /**
     * Answers the number of unread messages.
     *
     * @return integer
     */
    public function getUnreadMessageCount()
    {
        return $this->Messages()->filter(array('Read' => 0))->count();
    }
    
    /**
     * Answers the number to display as a site tree badge.
     *
     * @return integer
     */
    public function getBadgeNumber()
    {
        return $this->getUnreadMessageCount();
    }
    
    /**
     * Answers the text to display as a site tree badge.
     *
     * @return string
     */
    public function getBadgeText()
    {
        return _t('ContactPage.NUMBERUNREAD', '{num} unread');
    }
    
    /**
     * Answers the label for the messages tab.
     *
     * @return string
     */
    protected function getMessagesTabLabel()
    {
        $label = _t('ContactPage.MESSAGES', 'Messages');
        
        if ($num = $this->getBadgeNumber()) {
            $label .= ' <span class="badge-number">' . $num . '</span>';
        }
        
        return $label;
    }
}

/**
 * An extension of the page controller class for a contact page.
 */
class ContactPage_Controller extends Page_Controller
{
    /**
     * Defines the allowed actions for this controller.
     */
    private static $allowed_actions = array(
        'Form',
        'doSend'
    );
    
    /**
     * Performs initialisation before any action is called on the receiver.
     */
    public function init()
    {
        // Initialise Parent:
        
        parent::init();
        
        // Load Requirements:
        
        Requirements::themedCSS('contact-page', SILVERWARE_CONTACT_DIR);
        
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js');
        Requirements::javascript(SILVERWARE_CONTACT_DIR . '/javascript/contact-page.js');
    }
    
    /**
     * Answers the form object for the template.
     *
     * @return Form
     */
    public function Form()
    {
        // Create Form Fields:
        
        $fields = FieldList::create(
            TextField::create(
                'FirstName',
                _t('ContactPage.FIRSTNAME', 'First Name')
            ),
            TextField::create(
                'LastName',
                _t('ContactPage.LASTNAME', 'Last Name')
            ),
            EmailField::create(
                'Email',
                _t('ContactPage.EMAILADDRESS', 'Email Address')
            ),
            TextareaField::create(
                'Message',
                _t('ContactPage.MESSAGE', 'Message')
            )->setAttribute(
                'placeholder',
                _t(
                    'ContactPage.MESSAGEPLACEHOLDER',
                    'Enter your message'
                )
            )
        );
        
        // Create Form Actions:
        
        $actions = FieldList::create(
            FormAction::create('doSend', 'Send')
        );
        
        // Define Required Fields:
        
        $required = array(
            'FirstName',
            'LastName',
            'Email',
            'Message'
        );
        
        // Create Phone Field (if required):
        
        if ($this->ShowPhoneField) {
            
            $fields->insertAfter(
                TextField::create(
                    'Phone',
                    _t('ContactMessage.PHONENUMBER', 'Phone Number')
                ),
                'Email'
            );
            
        }
        
        // Create Recipient Field (if required):
        
        if ($this->ShowRecipientField) {
            
            $fields->insertBefore(
                DropdownField::create(
                    'RecipientID',
                    $this->RecipientFieldLabel,
                    $this->getEnabledRecipientsMap()
                )->setEmptyString(' '),
                'Message'
            );
            
            $required[] = "RecipientID";
            
        }
        
        // Create Subject Field (if required):
        
        if ($this->ShowSubjectField) {
            
            $fields->insertBefore(
                TextField::create(
                    'Subject',
                    _t('ContactMessage.SUBJECT', 'Subject')
                ),
                'Message'
            );
            
            $required[] = "Subject";
            
        }
        
        // Create Form Validator:
        
        if (class_exists('ZenValidator')) {
            $validator = ZenValidator::create()->addRequiredFields($required);
        } else {
            $validator = RequiredFields::create($required);
        }
        
        // Create Form Object:
        
        $form = Form::create($this, 'Form', $fields, $actions, $validator);
        
        // Define Form Object:
        
        $form->setAttribute(
            'data-recipients',
            $this->getRecipientJSON()
        );
        
        $form->addExtraClass('contact-form');
        
        // Enable Spam Protection (if installed):
        
        if ($form->hasExtension('FormSpamProtectionExtension')) {
            $form->enableSpamProtection();
        }
        
        // Answer Form Object:
        
        return $form;
    }
    
    /**
     * Answers the recipient JSON for the contact form.
     *
     * @return string
     */
    public function getRecipientJSON()
    {
        return Convert::array2json($this->getEnabledRecipients()->map('URLSegment', 'ID')->toArray());
    }
    
    /**
     * Handles the submission of the contact form.
     *
     * @param array $data
     * @param Form $form
     */
    public function doSend($data, $form)
    {
        // Create Message Object:
        
        $message = ContactMessage::create();
        
        // Define Message Object:
        
        $form->saveInto($message);
        
        // Link Message to Page:
        
        $message->ParentPageID = $this->ID;
        
        // Add Recipient(s) to Message:
        
        $message->addRecipients($this->dataRecord, $data);
        
        // Write Message Object:
        
        $message->write();
        
        // Send Message to Recipients:
        
        if ($this->SendViaEmail) {
            $message->send();
        }
        
        // Define Session Message:
        
        $form->sessionMessage($this->OnSendMessage, 'good');
        
        // Redirect Back to Form:
        
        $this->redirectBack();
    }
}
