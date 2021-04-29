<?php

/**
 * An extension of the data object class for a contact recipient.
 */
class ContactRecipient extends DataObject
{
    private static $singular_name = "Recipient";
    private static $plural_name   = "Recipients";
    
    private static $default_sort = "Name";
    
    private static $db = array(
        'Name' => 'Varchar(255)',
        'NameTo' => 'Varchar(255)',
        'EmailTo' => 'Varchar(255)',
        'NameFrom' => 'Varchar(255)',
        'EmailFrom' => 'Varchar(255)',
        'EmailSubject' => 'Varchar(255)',
        'Disabled' => 'Boolean'
    );
    
    private static $has_one = array(
        'ParentPage' => 'Page'
    );
    
    private static $belongs_many_many = array(
        'Messages' => 'ContactMessage'
    );
    
    private static $defaults = array(
        'Disabled' => 0
    );
    
    private static $summary_fields = array(
        'Name' => 'Name',
        'SendTo' => 'To',
        'SendFrom' => 'From',
        'Disabled.Nice' => 'Disabled'
    );
    
    private static $extensions = array(
        'URLSegmentExtension'
    );
    
    /**
     * Answers a collection of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Create Field Tab Set:
        
        $fields = FieldList::create(TabSet::create('Root'));
        
        // Create Field Objects:
        
        $fields->addFieldsToTab(
            'Root.Main',
            array(
                TextField::create(
                    'Name',
                    _t('ContactRecipient.NAME', 'Name')
                ),
                FieldGroup::create(
                    _t('ContactRecipient.SENDEMAILTO', 'Send email to'),
                    array(
                        TextField::create(
                            'NameTo',
                            ''
                        )->setAttribute('placeholder', _t('ContactRecipient.NAME', 'Name')),
                        EmailField::create(
                            'EmailTo',
                            ''
                        )
                        ->setAttribute('placeholder', _t('ContactRecipient.EMAIL', 'Email'))
                        ->setAttribute('size', 40)
                    )
                ),
                FieldGroup::create(
                    _t('ContactRecipient.SENDEMAILFROM', 'Send email from'),
                    array(
                        TextField::create(
                            'NameFrom',
                            ''
                        )->setAttribute('placeholder', _t('ContactRecipient.NAME', 'Name')),
                        EmailField::create(
                            'EmailFrom',
                            ''
                        )
                        ->setAttribute('placeholder', _t('ContactRecipient.EMAIL', 'Email'))
                        ->setAttribute('size', 40)
                    )
                ),
                TextField::create(
                    'EmailSubject',
                    _t('ContactRecipient.EMAILSUBJECT', 'Email subject')
                ),
                CheckboxField::create(
                    'Disabled',
                    _t('ContactRecipient.DISABLED', 'Disabled')
                )
            )
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
        return RequiredFields::create(
            array(
                'Name',
                'EmailTo',
                'EmailFrom',
                'EmailSubject'
            )
        );
    }
    
    /**
     * Answers a string containing the name (optional) and email address for sending to.
     *
     * @return string
     */
    public function getSendTo()
    {
        return $this->NameTo ? sprintf('%s <%s>', $this->NameTo, $this->EmailTo) : $this->EmailTo;
    }
    
    /**
     * Answers a string containing the name (optional) and email address for sending from.
     *
     * @return string
     */
    public function getSendFrom()
    {
        return $this->NameFrom ? sprintf('%s <%s>', $this->NameFrom, $this->EmailFrom) : $this->EmailFrom;
    }
    
    /**
     * Answers a string containing the recipient name and send to value.
     *
     * @return string
     */
    public function getNameAndSendTo()
    {
        return sprintf('%s: %s', $this->Name, $this->SendTo);
    }
    
    /**
     * Answers a string containing the recipient name and send from value.
     *
     * @return string
     */
    public function getNameAndSendFrom()
    {
        return sprintf('%s: %s', $this->Name, $this->SendFrom);
    }
    
    /**
     * Sends the given email to the recipient.
     *
     * @param Email $Email
     */
    public function receive(Email $Email)
    {
        // Define Email Object:
        
        $Email->setTo($this->SendTo);
        $Email->setFrom($this->SendFrom);
        
        // Define Email Subject:
        
        if (!$Email->Subject()) {
            $Email->setSubject($this->EmailSubject);
        }
        
        // Populate Email Template:
        
        $Email->populateTemplate(
            array(
                'Recipient' => $this
            )
        );
        
        // Send Email to Recipient:
        
        $Email->send();
    }
    
    /**
     * Answers a link to send a message to this recipient via the contact page.
     *
     * @return string
     */
    public function Link()
    {
        return $this->ParentPage()->Link(sprintf('#%s', $this->URLSegment));
    }
}
