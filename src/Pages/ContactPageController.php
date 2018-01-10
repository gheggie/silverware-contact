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

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ValidationResult;
use SilverWare\Contact\Model\ContactMessage;
use SilverWare\Validator\Validator;
use PageController;

/**
 * An extension of the page controller class for a contact page.
 *
 * @package SilverWare\Contact\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class ContactPageController extends PageController
{
    /**
     * Defines the allowed actions for this controller.
     *
     * @var array
     * @config
     */
    private static $allowed_actions = [
        'Form',
        'doSend'
    ];
    
    /**
     * Answers the form object for the HTML template.
     *
     * @return Form
     */
    public function Form()
    {
        // Create Form Fields:
        
        $fields = FieldList::create(
            FieldGroup::create(
                TextField::create(
                    'FirstName',
                    _t(__CLASS__ . '.FIRSTNAME', 'First Name')
                ),
                TextField::create(
                    'LastName',
                    _t(__CLASS__ . '.LASTNAME', 'Last Name')
                )
            ),
            EmailField::create(
                'Email',
                _t(__CLASS__ . '.EMAIL', 'Email')
            ),
            TextareaField::create(
                'Message',
                _t(__CLASS__ . '.MESSAGE', 'Message')
            )
        );
        
        // Define Required Fields:
        
        $required = [
            'FirstName',
            'LastName',
            'Email',
            'Message'
        ];
        
        // Create Phone Field (if required):
        
        if ($this->ShowPhoneField) {
            
            $fields->insertAfter(
                TextField::create(
                    'Phone',
                    _t(__CLASS__ . '.PHONE', 'Phone')
                ),
                'Email'
            );
            
            if ($this->PhoneRequired) {
                $required[] = 'Phone';
            }
            
        }
        
        // Create Recipient Field (if required):
        
        if ($this->ShowRecipientField) {
            
            $fields->insertBefore(
                DropdownField::create(
                    'RecipientID',
                    $this->getRecipientFieldLabel(),
                    $this->getEnabledRecipientOptions()
                )->setEmptyString(' '),
                'Message'
            );
            
            $required[] = 'RecipientID';
            
        }
        
        // Create Subject Field (if required):
        
        if ($this->ShowSubjectField) {
            
            $fields->insertBefore(
                TextField::create(
                    'Subject',
                    _t(__CLASS__ . '.SUBJECT', 'Subject')
                ),
                'Message'
            );
            
            $required[] = 'Subject';
            
        }
        
        // Create Form Actions:
        
        $actions = FieldList::create(
            FormAction::create('doSend', _t(__CLASS__ . '.SEND', 'Send'))
        );
        
        // Create Form Validator:
        
        $validator = Validator::create()->addRequiredFields($required);
        
        // Create Form Object:
        
        $form = Form::create($this, 'Form', $fields, $actions, $validator);
        
        // Enable Spam Protection (if available):
        
        if ($form->hasMethod('enableSpamProtection')) {
            $form->enableSpamProtection();
        }
        
        // Answer Form Object:
        
        return $form;
    }
    
    /**
     * Handles the submission of the contact form.
     *
     * @param array $data
     * @param Form $form
     * @param HTTPRequest $request
     *
     * @return HTTPResponse
     */
    public function doSend($data, Form $form, HTTPRequest $request)
    {
        // Create Message Object:
        
        $message = ContactMessage::create();
        
        // Define Message Object:
        
        $form->saveInto($message);
        
        // Add Message Recipients:
        
        $message->addRecipients($this->getRecipientIDs($data));
        
        // Associate Message with Page:
        
        $message->setParent($this->data());
        
        // Record Message Object:
        
        $message->write();
        
        // Send Message to Recipient(s):
        
        if ($this->SendViaEmail) {
            $message->send();
        }
        
        // Obtain On Send Message:
        
        $message = $this->OnSendMessage;
        
        if ($this->ShowRecipientField && isset($data['RecipientID'])) {
            
            if ($recipient = $this->data()->Recipients()->byID($data['RecipientID'])) {
                $message = $recipient->OnSendMessage ?: $this->OnSendMessage;
            }
            
        }
        
        // Define Session Message:
        
        $form->sessionMessage($message, ValidationResult::TYPE_GOOD);
        
        // Redirect Back to Form:
        
        return $this->redirectBack();
    }
    
    /**
     * Answers the label for the recipient field.
     *
     * @return string
     */
    public function getRecipientFieldLabel()
    {
        if ($label = $this->data()->RecipientFieldLabel) {
            return $label;
        }
        
        return _t(__CLASS__ . '.RECIPIENT', 'Recipient');
    }
    
    /**
     * Answers an array of recipient IDs for the message object based upon the given form data.
     *
     * @param array $data
     *
     * @return array
     */
    public function getRecipientIDs($data)
    {
        if ($this->ShowRecipientField && isset($data['RecipientID'])) {
            return [$data['RecipientID']];
        }
        
        return $this->getEnabledRecipients()->getIDList();
    }
}
